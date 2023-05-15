<?php

namespace App\Http\Controllers;

use App\CapacityPartInjection;
use App\CodeGenerator;
use App\Employee;
use App\EmployeeSync;
use App\InjectionCleaning;
use App\InjectionDryer;
use App\InjectionDryerLog;
use App\InjectionHistoryMoldingLog;
use App\InjectionHistoryMoldingTemp;
use App\InjectionHistoryMoldingWorks;
use App\InjectionInventory;
use App\InjectionMachineLog;
use App\InjectionMachineMaster;
use App\InjectionMachineWork;
use App\InjectionMachineWorkingLog;
use App\InjectionMaintenanceMoldingLog;
use App\InjectionMaintenanceMoldingTemp;
use App\InjectionMaintenanceMoldingWork;
use App\InjectionMoldingLog;
use App\InjectionMoldingMaster;
use App\InjectionProcessLog;
use App\InjectionProcessTemp;
use App\InjectionResin;
use App\InjectionScheduleLog;
use App\InjectionTag;
use App\InjectionTransaction;
use App\InjectionVisualCheck;
use App\InjectionVisualPointCheck;
use App\Inventory;
use App\Mail\SendEmail;
use App\MaterialPlantDataList;
use App\MesinLogInjection;
use App\PlanMesinInjection;
use App\PlanMesinInjectionTmp;
use App\PushBlockMaster;
use App\RcKensaInitial;
use App\TrainingReport;
use App\TransactionPartInjection;
use App\User;
use App\WeeklyCalendar;
use App\WorkingMesinInjection;
use Carbon\Carbon;
use DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Response;

class InjectionsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }
        $this->mesin = [
            'Mesin 1',
            'Mesin 2',
            'Mesin 3',
            'Mesin 4',
            'Mesin 5',
            'Mesin 6',
            'Mesin 7',
            'Mesin 8',
            'Mesin 9',
            'Mesin 10',
            'Mesin 11',
            'Mesin 12',
            'Mesin 13',
            'Mesin 14',
            'Mesin 15',
            'Mesin 16',
            'Mesin 17',
            'Mesin 18',
            'Mesin 19',
        ];

        $this->hour = [
            '08:00:00 - 10:00:00',
            '10:00:00 - 12:00:00',
            '12:00:00 - 14:00:00',
            '14:00:00 - 16:00:00',
            '16:00:00 - 18:00:00',
            '18:00:00 - 20:00:00',
            '20:00:00 - 22:00:00',
            '22:00:00 - 23:59.59',
            '00:00:01 - 02:00:00',
            '02:00:00 - 04:00:00',
            '04:00:00 - 06:00:00',
            '06:00:00 - 08:00:00',
        ];

        $this->mesin_molding = [
            'Mesin 1',
            'Mesin 2',
            'Mesin 3',
            'Mesin 4',
            'Mesin 5',
            'Mesin 6',
            'Mesin 7',
            'Mesin 8',
            'Mesin 9',
            'Mesin 10',
            'Mesin 11',
            'Mesin 12',
            'Mesin 13',
            'Mesin 14',
            'Mesin 15',
            'Mesin 16',
            'Mesin 17',
            'Mesin 18',
            'Mesin 19',
        ];

        $this->dryer = [
            '1',
            '2 & 6',
            '3',
            '4',
            '5',
            '7',
            '8',
            '9',
            '10',
            '11',
            '12',
            '13',
            '14',
            '16',
            '17 (Tangki)',
            '18 (Tangki)',
            '19 (Tangki)',
        ];

        $this->color = [
            'BEIGE',
            'IVORY',
            'SKELTON',
        ];

        $this->part = [
            'MJB',
            'MJG',
            'BJ',
            'FJ',
            'HJ',
            'A YRF B',
            'A YRF H',
            'A YRF S',
        ];

        $this->part_molding = [
            'MJB',
            'MJG',
            'BJ',
            'FJ',
            'HJ',
            'A YRF B',
            'A YRF H',
            'A YRF S',
            'UPPER',
            'LOWER',
            'BARREL',
            'BELL',
            'SMALL 01',
            'SMALL 03',
            'JOINT D',
            'JOINT E 01',
            'JOINT E 02',
            'MP LARGE 02',
            'MP LARGE 03',
            'MP LARGE 04',
            'SOPRAN',
            'ALTO UPPER',
            'ALTO LOWER',
            'TENOR UPPER',
            'TENOR LOWER',
            'C KEY',
            'C# KEY',
            'D KEY',
            'D# KEY',
            'Cover',
            'KEY POST',
            'MP CAP',
            'NECK',
            'OCTKEY',
            'OCTPIPE',
            'REED',
            'THUMBCAP',
            'A KEY',
            'Bb KEY',
            'F KEY',
            'F# KEY',
            'G KEY',
            'G# KEY',
            'KEY POST A',
            'KEY POST B',
            'NECKPART',
            'CL-2',
            'OCL',
            'BSB',
            'CL-1 ',
            'BCL',
            'BS-1',
            'SS',
            'AS-1',
            'AS-2',
            'ECL-',
            'TS',
            'E-1 / E-6 KEY',
            'E-2 / H-2 KEY',
            'E-7 / F-3 KEY',
            'G-1 / G-2 KEY',
            'H-3 / H-5 KEY',
            'H-1 / F-1 KEY',
            'E-4 / F-2 KEY',
            'NECK PART',
            'CLR',
            'ASR',
            'TSR',
            'D / D# KEY',
            'E / F-F# KEY',
            'OCTAV KEY',
            'THUMB CAP / OCT PIPE',
            'C / C# KEY',
            'KEYPOST A/B',
        ];

        $this->product = [
            'YRS24BUK',
            'YRS24B',
            'YRS23',
            'YRS20GP',
            'YRS20GG',
            'YRS20GB',
            'YRS20BR',
            'YRS20BP',
            'YRS20BG',
            'YRS20BB',
            'YRF21',
            'CL BODY',
            'PIANIKA',
            'DSI BODY VENOVA',
            'KUNCI VENOVA SOPRAN',
            'KUNCI VENOVA ALTO',
            'MOUTH PIECE',
            'KUNCI YDS',
            'SYNTHETIC REED',
            'KUNCI VENOVA TENOR',
        ];

        $this->model = [
            'YRS',
            'YRF',
        ];

        $this->jam_istirahat_biasa_1 = array(
            'ist1_start' => '08:00:00',
            'ist1_end' => '08:40:00',
            'ist2_start' => '11:15:00',
            'ist2_end' => '12:40:00',
            'ist3_start' => '13:20:00',
            'ist3_end' => '14:00:00',
        );
        $this->jam_istirahat_jumat_1 = array(
            'ist1_start' => '08:00:00',
            'ist1_end' => '08:40:00',
            'ist2_start' => '11:15:00',
            'ist2_end' => '13:10:00',
            'ist3_start' => '13:50:00',
            'ist3_end' => '14:30:00',
        );

        $this->jam_istirahat_2 = array(
            'ist1_start' => '17:35:00',
            'ist1_end' => '18:10:00',
            'ist2_start' => '20:00:00',
            'ist2_end' => '21:10:00',
            'ist3_start' => '22:20:00',
            'ist3_end' => '22:50:00',
        );
    }

    public function index()
    {

        return view('injection.index')->with('page', 'Injection')->with('jpn', '成形');

    }

    public function in()
    {

        return view('injection.in')->with('page', 'Injection Stock In')->with('jpn', '???');

    }

    public function scanPartInjeksi(Request $request)
    {
        $part = CapacityPartInjection::where('capacity_part_injections.rfid', '=', $request->get('serialNumber'))->get();

        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Scan Part Success',
        );
        return Response::json($response);
    }

    public function scanNewTagInjeksi(Request $request)
    {
        $tag = InjectionTag::where('tag', '=', $request->get('tag'))->where('operator_id', '=', null)->first();

        if ($tag != null) {
            $qty = 0;
            $part = DB::SELECT("SELECT capacity FROM injection_parts where gmc = '" . $tag->material_number . "' and deleted_at is null and remark = 'injection'");
            if (count($part) > 0) {
                $qty = $part[0]->capacity;
            }
            $response = array(
                'status' => true,
                'tag' => $tag,
                'qty' => $qty,
                'message' => 'Scan Product Tag Success',
            );
        } else {
            $response = array(
                'status' => false,
                'message' => 'Tag Invalid',
            );
            return Response::json($response);
        }
        return Response::json($response);
    }

    public function scanPartMolding(Request $request)
    {
        $part = InjectionMoldingMaster::where('status_mesin', '=', $request->get('mesin'))->first();

        if ($part != null) {
            $product = DB::SELECT("SELECT id,part_name,CONCAT(SUBSTRING_INDEX(part_name, ' ', 1),'-',UPPER(part_code),'-',UPPER(color),'-',UPPER(gmc)) as product FROM `injection_parts` where remark = 'injection' and color = '" . $request->get('color') . "' and part_code = '" . $part->product . "' and deleted_at is null ORDER BY part_name desc");

            $response = array(
                'status' => true,
                'part' => $part,
                'product' => $product,
                'message' => 'Scan Molding Tag Success',
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Molding Invalid',
            );
            return Response::json($response);
        }
    }

    public function scanInjectionOperator(Request $request)
    {

        $nik = $request->get('employee_id');

        if (str_contains(strtoupper($nik), 'PI')) {
            $employee = db::table('employees')->where('employee_id', 'like', '%' . strtoupper($nik) . '%')->first();
        } else {
            if (strlen($nik) > 9) {
                $nik = substr($nik, 0, 9);
            }
            $employee = db::table('employees')->where('tag', 'like', '%' . $nik . '%')->first();
        }

        if ($employee != null) {
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

    public function getNewProductCavity(Request $request)
    {
        try {
            $product = DB::SELECT("SELECT id,part_name,CONCAT(SUBSTRING_INDEX(part_name, ' ', 1),'-',UPPER(part_code),'-',UPPER(color),'-',UPPER(gmc)) as product FROM `injection_parts` where remark = 'injection' and color = '" . $request->get('color') . "' and part_code = '" . $request->get('part') . "' and deleted_at is null ORDER BY part_name desc");

            $response = array(
                'status' => true,
                'product' => $product,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => 'Data Tidak Tersedia',
            );
            return Response::json($response);
        }
    }

    public function getDataIn(Request $request)
    {
        $date = date('Y-m-d');
        $query = "SELECT gmc,part,total, created_at as tgl_in from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d')='" . $date . "' and `status` ='" . $request->get('proces') . "' order by created_at desc";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function sendPart(Request $request)
    {
        $gmc1 = $request->get('gmc');
        $id = Auth::id();

        for ($i = 0; $i < sizeof($gmc1); $i++) {
            $part = db::table('capacity_part_injections')
                ->where('gmc', '=', $gmc1[$i])
                ->get();

            $part2 = new TransactionPartInjection([
                'gmc' => $part[0]->gmc,
                'part' => $part[0]->part_name,
                'total' => $part[0]->capacity,
                'status' => $request->get('process'),
                'created_by' => $id,
            ]);
            $part2->save();
        }

        $response = array(
            'status' => true,

        );
        return Response::json($response);
    }

    public function getDataInOut()
    {

        $moth = date('Y-m');
        $day = date('Y-m-d');
        $first = date('Y-m-01');
        $yesterday = date('Y-m-d', strtotime(Carbon::yesterday()));

        $query = "
    SELECT stock_all.*,in_out_part_all.stock_in, in_out_part_all.stock_out, ((stock_all.stock +in_out_part_all.stock_in)-in_out_part_all.stock_out) as total  from (
    SELECT in_out2.gmc, in_out2.part_name, ((stock.stock_akhir + in_out2.stock_in)-in_out2.stock_out ) as stock from (
    SELECT * from (
    SELECT * from (
    SELECT inPart.gmc, inPart.part_name, COALESCE(inPart.stock_in,0) as  stock_in from(
    SELECT * from capacity_part_injections
    LEFT JOIN(
    SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in from transaction_part_injections WHERE `status` ='IN' and DATE_FORMAT(created_at,'%Y-%m-%d') <='" . $first . "' and DATE_FORMAT(created_at,'%Y-%m-%d') >='" . $yesterday . "' GROUP BY part,gmc
    ) stock_in on capacity_part_injections.part_name = stock_in.part_in
    ) inPart
    ) inpart

    LEFT JOIN (

    SELECT outPart.gmc as gmc_out, outPart.part_name as part_name_out, COALESCE(outPart.stock_in,0) as  stock_out from(
    SELECT * from capacity_part_injections
    LEFT JOIN(
    SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in from transaction_part_injections WHERE `status` ='OUT' and DATE_FORMAT(created_at,'%Y-%m-%d') <='" . $first . "' and DATE_FORMAT(created_at,'%Y-%m-%d') >='" . $yesterday . "' GROUP BY part,gmc
    ) stock_in on capacity_part_injections.part_name = stock_in.part_in
    ) outPart

    ) as outPart on inpart.part_name = outPart.part_name_out

    ) as in_out

    ) as in_out2

    LEFT JOIN (
    SELECT part, stock_akhir from stock_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m')='" . $moth . "'
    ) as stock on in_out2.part_name = stock.part
    ) as stock_all

    LEFT JOIN (

    SELECT * from (
    SELECT inpart.gmc, inpart.part_name,inpart.stock_in,outpart.stock_out from (
    SELECT inPart.gmc, inPart.part_name, COALESCE(inPart.stock_in,0) as  stock_in from(
    SELECT * from capacity_part_injections
    LEFT JOIN(
    SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in from transaction_part_injections WHERE `status` ='IN'  and DATE_FORMAT(created_at,'%Y-%m-%d') >='" . $day . "' GROUP BY part,gmc
    ) stock_in on capacity_part_injections.part_name = stock_in.part_in
    ) inPart
    ) inpart

    LEFT JOIN (

    SELECT outPart.gmc as gmc_out, outPart.part_name as part_name_out, COALESCE(outPart.stock_in,0) as  stock_out from(
    SELECT * from capacity_part_injections
    LEFT JOIN(
    SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in from transaction_part_injections WHERE `status` ='OUT'  and DATE_FORMAT(created_at,'%Y-%m-%d') >='" . $day . "' GROUP BY part,gmc
    ) stock_in on capacity_part_injections.part_name = stock_in.part_in
    ) outPart

    ) as outPart on inpart.part_name = outPart.part_name_out
    ) as in_out_part

    ) as in_out_part_all on stock_all.part_name = in_out_part_all.part_name order by part_name
    ";
        $part = DB::select($query);
        return DataTables::of($part)
            ->make(true);
    }

    // -------------- end in

    // -------------- out

    public function out()
    {

        return view('injection.out')->with('page', 'Injection Stock Out')->with('jpn', '???');

    }

    // -------------- end out

    // --------------  dailyStock

    public function dailyStock()
    {

        return view('injection.dailyStock')->with('page', 'Injection Stock Out')->with('jpn', '???');

    }

    public function getDailyStock(Request $request)
    {
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl . '-d';
        $tgl2 = $tgl . '-01';

        if ($tgl != "") {
            $moth = $request->get('tgl');
            $day = date($tgl1, strtotime(carbon::now()->endOfMonth()));
            $first = date($tgl2);
        } else {
            $moth = date('Y-m');
            $day = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
            $first = date('Y-m-01');
        }

        if ($location == "Blue") {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        } elseif ($location == "Green") {
            $reg = "YRS20BG|YRS20GG";
            $reg2 = "YRS20BG|YRS20GG|YRS20GGK";
        } elseif ($location == "Pink") {
            $reg = "YRS20BP|YRS20GP";
            $reg2 = "YRS20BP|YRS20GP|YRS20GPK";
        } elseif ($location == "Red") {
            $reg = "YRS20BR";
            $reg2 = "YRS20BR";
        } elseif ($location == "Brown") {
            $reg = "YRS24BUK";
            $reg2 = "YRS24BUK";
        } elseif ($location == "Ivory") {
            $reg = "YRS23|YRS24B MIDDLE";
            $reg2 = "YRS23|YRS23BR|YRS23CA|YRS23K|YRS27III|YRS24B|YRS24BBR|YRS24BCA|YRS24BK|YRS28BIII";
        } elseif ($location == "Yrf") {
            $reg = "YRF21";
            $reg2 = "YRF21|YRF21K";
        } else {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }

        $query22 = "select * from (
    SELECT date_all.week_date, date_all.part, COALESCE(stock.stock_in,0) as stock from (
    SELECT * from (
    SELECT DISTINCT part FROM detail_part_injections WHERE  part REGEXP '" . $reg . "'
    ) a
    cross JOIN (
    SELECT week_date FROM weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(week_date,'%Y-%m-%d') <='" . $day . "'
    ) as date
    ) date_all
    LEFT JOIN (
    SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from transaction_part_injections WHERE  `status` ='IN' and DATE_FORMAT(created_at,'%Y-%m') ='" . $moth . "' GROUP BY part,gmc,DATE_FORMAT(created_at,'%Y-%m-%d')
    ) as stock on date_all.week_date = stock.tgl and date_all.part = stock.part_in  ORDER BY part, week_date
    ) as aa GROUP BY week_date, part
    ";

        $query = "select * from (
    SELECT date_all.week_date, date_all.part, COALESCE(stock.stock_in,0) as stock from (
    SELECT * from (
    SELECT DISTINCT part FROM detail_part_injections WHERE  part REGEXP '" . $reg . "'
    ) a
    cross JOIN (

    SELECT week_date from ympimis.weekly_calendars WHERE
    week_date not in ( SELECT tanggal from  ftm.kalender)
    and DATE_FORMAT(week_date,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(week_date,'%Y-%m-%d') <='" . $day . "'
    ) as date
    ) date_all
    LEFT JOIN (
    SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from transaction_part_injections WHERE  `status` ='IN' and DATE_FORMAT(created_at,'%Y-%m') ='" . $moth . "' GROUP BY part,gmc,DATE_FORMAT(created_at,'%Y-%m-%d')
    ) as stock on date_all.week_date = stock.tgl and date_all.part = stock.part_in  ORDER BY part, week_date
    ) as aa GROUP BY week_date, part
    ";

        $query2 = "SELECT DISTINCT part FROM detail_part_injections WHERE  part REGEXP '" . $reg . "' ";

        $query3 = "SELECT week_date, COALESCE(target,0) as target from (
    SELECT a.*, SUM(quantity) as target from (
    SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
    SELECT material_number,due_date,quantity from production_schedules WHERE
    material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
    DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $day . "'
    ) target
    LEFT JOIN materials on target.material_number = materials.material_number
    where model REGEXP '" . $reg2 . "' ORDER BY due_date
    ) a GROUP BY due_date
    ) aa
    RIGHT JOIN (
    SELECT week_date from ympimis.weekly_calendars WHERE
    week_date not in ( SELECT tanggal from  ftm.kalender)
    and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-30'
    ) as date on aa.due_date = date.week_date ORDER BY week_date
    ";

        $part = DB::select($query);
        $model = DB::select($query2);
        $assy = DB::select($query3);
        $response = array(
            'status' => true,
            'part' => $part,
            'model' => $model,
            'assy' => $assy,
            'message' => 'Get Part Success',
            'asas' => $tgl1,
        );
        return Response::json($response);
    }

    // -------------- end dailyStock

    // ------------------------- shchedule

    public function schedule()
    {

        return view('injection.schedule')->with('page', 'Injection Stock Out')->with('jpn', '???');

    }

    public function getSchedule(Request $request)
    {

        $from = $request->get('from');
        $to = $request->get('toa');

        $day = date('Y-m-d');

        $first = date('Y-m-01');
        $last = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
        $month = date('Y-m');
        $years = date('Y');

        $query2week = "SELECT target_all.material_number,  target_all.model, target_all.part, target_all.part_code, target_all.color,target_all.target,target_all.stock,target_all.max_day,target_all.qty_hako, target_all.cycle, target_all.shoot, CEILING(ROUND((target - stock) / qty_hako,2)) as target_hako, CEILING((CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako)/ mesin) as target_hako_qty ,COALESCE(mesin.mesin,0) mesin, COALESCE(working,'-') working, target_all.due_date  FROM (
    SELECT total_all.material_number, total_all.due_date, total_all.model, total_all.part, total_all.part_code, total_all.color,
    (total_all.total+(total_all.total / 10))  as target, stock.stock as stock, total_all.max_day,total_all.qty_hako, total_all.cycle, total_all.shoot from (


    SELECT target.*,cycle_time_mesin_injections.cycle, cycle_time_mesin_injections.shoot, cycle_time_mesin_injections.qty,
    ROUND((82800  / cycle_time_mesin_injections.cycle  )*cycle_time_mesin_injections.shoot,0) as max_day,cycle_time_mesin_injections.qty_hako  from (
    SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color, SUM(quantity) as total from (
    SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
    SELECT material_number,due_date,quantity from production_schedules WHERE
    material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
    DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $last . "'
    ) target
    LEFT JOIN materials on target.material_number = materials.material_number
    ) as target_model
    CROSS join  detail_part_injections on target_model.model = detail_part_injections.model
    WHERE due_date in ( SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='" . $from . "' and DATE_FORMAT(week_date,'%Y-%m-%d')<='" . $to . "' and DATE_FORMAT(week_date,'%Y')='" . $years . "')
    GROUP BY part,color,part_code ORDER BY due_date
    ) target

    LEFT JOIN cycle_time_mesin_injections
    on target.part_code = cycle_time_mesin_injections.part
    and target.color = cycle_time_mesin_injections.color
    ORDER BY part


    ) total_all

    LEFT JOIN (
    SELECT  part, (( SUM(stock_akhir) + SUM(total_in) )-SUM(total_out)) stock from (
    SELECT  part, stock_akhir, 0 as total_in, 0 as total_out from stock_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m')='" . $month . "'
    UNION all
    SELECT part,0 as stock_akhir ,total as total_in, 0 as total_out from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(created_at,'%Y-%m-%d') <='" . $last . "' and `status` ='IN'
    UNION all
    SELECT part,0 as stock_akhir ,0 as total_in, total as total_out from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(created_at,'%Y-%m-%d') <='" . $last . "' and `status` ='OUT'
    ) as stock GROUP BY part

    ) as stock on total_all.part = stock.part

    ) as target_all

    LEFT JOIN (
    SELECT part,color, SUM(qty) as mesin, GROUP_CONCAT(working_mesin_injections.mesin) as working from working_mesin_injections
    LEFT JOIN status_mesin_injections on working_mesin_injections.mesin = status_mesin_injections.mesin
    where status_mesin_injections.`status` !='OFF'
    GROUP BY part,color ORDER BY mesin
    ) as mesin on target_all.part_code = mesin.part and target_all.color = mesin.color


    WHERE (CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako) > 0
    ORDER BY due_date
    ";

        $querymonth = "SELECT target_all.material_number,  target_all.model, target_all.part, target_all.part_code, target_all.color,target_all.target,target_all.stock,target_all.max_day,target_all.qty_hako, target_all.cycle, target_all.shoot, CEILING(ROUND((target - stock) / qty_hako,2)) as target_hako, CEILING((CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako)/ mesin) as target_hako_qty ,COALESCE(mesin.mesin,0) mesin, COALESCE(working,'-') working, target_all.due_date  FROM (
    SELECT total_all.material_number, total_all.due_date, total_all.model, total_all.part, total_all.part_code, total_all.color,
    (total_all.total)  as target, stock.stock as stock, total_all.max_day,total_all.qty_hako, total_all.cycle, total_all.shoot from (


    SELECT target.*,cycle_time_mesin_injections.cycle, cycle_time_mesin_injections.shoot, cycle_time_mesin_injections.qty,
    ROUND((82800  / cycle_time_mesin_injections.cycle  )*cycle_time_mesin_injections.shoot,0) as max_day,cycle_time_mesin_injections.qty_hako  from (
    SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color, SUM(quantity) as total from (
    SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
    SELECT material_number,due_date,quantity from production_schedules WHERE
    material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
    DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $last . "'
    ) target
    LEFT JOIN materials on target.material_number = materials.material_number
    ) as target_model
    CROSS join  detail_part_injections on target_model.model = detail_part_injections.model
    WHERE due_date in ( SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='" . $from . "' and DATE_FORMAT(week_date,'%Y-%m-%d')<='" . $to . "' and DATE_FORMAT(week_date,'%Y')='" . $years . "')
    GROUP BY part,color,part_code,DAYOFWEEK(due_date) ORDER BY due_date
    ) target

    LEFT JOIN cycle_time_mesin_injections
    on target.part_code = cycle_time_mesin_injections.part
    and target.color = cycle_time_mesin_injections.color
    ORDER BY part


    ) total_all

    LEFT JOIN (
    SELECT  part, (( SUM(stock_akhir) + SUM(total_in) )-SUM(total_out)) stock from (
    SELECT  part, stock_akhir, 0 as total_in, 0 as total_out from stock_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m')='" . $month . "'
    UNION all
    SELECT part,0 as stock_akhir ,total as total_in, 0 as total_out from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(created_at,'%Y-%m-%d') <='" . $last . "' and `status` ='IN'
    UNION all
    SELECT part,0 as stock_akhir ,0 as total_in, total as total_out from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(created_at,'%Y-%m-%d') <='" . $last . "' and `status` ='OUT'
    ) as stock GROUP BY part

    ) as stock on total_all.part = stock.part

    ) as target_all

    LEFT JOIN (
    SELECT part,color, SUM(qty) as mesin, GROUP_CONCAT(working_mesin_injections.mesin) as working from working_mesin_injections
    LEFT JOIN status_mesin_injections on working_mesin_injections.mesin = status_mesin_injections.mesin
    where status_mesin_injections.`status` !='OFF'
    GROUP BY part,color ORDER BY mesin
    ) as mesin on target_all.part_code = mesin.part and target_all.color = mesin.color


    WHERE (CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako) > 0
    ORDER BY due_date
    ";

        $query = "SELECT target_all.material_number,  target_all.model, target_all.part, target_all.part_code, target_all.color,target_all.target,target_all.stock,target_all.max_day,target_all.qty_hako, target_all.cycle, target_all.shoot, CEILING(ROUND((target - stock) / qty_hako,2)) as target_hako, CEILING((CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako)/ mesin) as target_hako_qty ,COALESCE(mesin.mesin,0) mesin, COALESCE(working,'-') working, target_all.due_date  FROM (
    SELECT total_all.material_number, total_all.due_date, total_all.model, total_all.part, total_all.part_code, total_all.color,
    (total_all.total)  as target, stock.stock as stock, total_all.max_day,total_all.qty_hako, total_all.cycle, total_all.shoot from (


    SELECT target.*,cycle_time_mesin_injections.cycle, cycle_time_mesin_injections.shoot, cycle_time_mesin_injections.qty,
    ROUND((82800  / cycle_time_mesin_injections.cycle  )*cycle_time_mesin_injections.shoot,0) as max_day,cycle_time_mesin_injections.qty_hako  from (
    SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color, quantity as total from (
    SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
    SELECT material_number,due_date,quantity from production_schedules WHERE
    material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
    DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $last . "'
    ) target
    LEFT JOIN materials on target.material_number = materials.material_number
    ) as target_model
    CROSS join  detail_part_injections on target_model.model = detail_part_injections.model
    WHERE due_date in ( SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='" . $from . "' and DATE_FORMAT(week_date,'%Y-%m-%d')<='" . $to . "' and DATE_FORMAT(week_date,'%Y')='" . $years . "')
    ORDER BY due_date
    ) target

    LEFT JOIN cycle_time_mesin_injections
    on target.part_code = cycle_time_mesin_injections.part
    and target.color = cycle_time_mesin_injections.color
    ORDER BY part


    ) total_all

    LEFT JOIN (
    SELECT  part, (( SUM(stock_akhir) + SUM(total_in) )-SUM(total_out)) stock from (
    SELECT  part, stock_akhir, 0 as total_in, 0 as total_out from stock_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m')='" . $month . "'
    UNION all
    SELECT part,0 as stock_akhir ,total as total_in, 0 as total_out from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(created_at,'%Y-%m-%d') <='" . $last . "' and `status` ='IN'
    UNION all
    SELECT part,0 as stock_akhir ,0 as total_in, total as total_out from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(created_at,'%Y-%m-%d') <='" . $last . "' and `status` ='OUT'
    ) as stock GROUP BY part

    ) as stock on total_all.part = stock.part

    ) as target_all

    LEFT JOIN (
    SELECT part,color, SUM(qty) as mesin, GROUP_CONCAT(working_mesin_injections.mesin) as working from working_mesin_injections
    LEFT JOIN status_mesin_injections on working_mesin_injections.mesin = status_mesin_injections.mesin
    where status_mesin_injections.`status` !='OFF'
    GROUP BY part,color ORDER BY mesin
    ) as mesin on target_all.part_code = mesin.part and target_all.color = mesin.color


    WHERE (CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako) > 0
    ORDER BY due_date
    ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'a' => $to,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function getStatusMesin(Request $request)
    {

        $query = "SELECT mesin,`status` from status_mesin_injections";

        $mesin = DB::select($query);
        $response = array(
            'status' => true,
            'mesin' => $mesin,
            'message' => 'Get Mesin Success',

        );
        return Response::json($response);
    }

    public function getDateWorking(Request $request)
    {

        $max = $request->get('max');

        $date = date('Y-m-d');

        $query = "SELECT week_date from ympimis.weekly_calendars WHERE
    week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' ORDER BY week_date asc limit " . $max . "";

        $mesin = DB::select($query);
        $response = array(
            'status' => true,
            'tgl' => $mesin,
            'message' => 'Get Date Success',

        );
        return Response::json($response);
    }

    public function saveSchedule(Request $request)
    {

        $id = Auth::id();
        try {

            // m1

            $m1s = $request->get('PostMESIN1');

            foreach ($m1s as $m1) {
                if (strlen($m1) > 0) {
                    $m1 = explode("#", $m1);

                    $plan = new PlanMesinInjection([
                        'mesin' => 'Mesin 1',
                        'part' => $m1[2],
                        'qty' => $m1[3],
                        'color' => $m1[1],
                        'due_date' => $m1[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m1

            // m2

            $m2s = $request->get('PostMESIN2');

            foreach ($m2s as $m2) {
                if (strlen($m2) > 0) {
                    $m2 = explode("#", $m2);

                    $plan = new PlanMesinInjection([
                        'mesin' => 'Mesin 2',
                        'part' => $m2[2],
                        'qty' => $m2[3],
                        'color' => $m2[1],
                        'due_date' => $m2[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m2

            // m3

            $m3s = $request->get('PostMESIN3');

            foreach ($m3s as $m3) {
                if (strlen($m3) > 0) {
                    $m3 = explode("#", $m3);

                    $plan = new PlanMesinInjection([
                        'mesin' => 'Mesin 3',
                        'part' => $m3[2],
                        'qty' => $m3[3],
                        'color' => $m3[1],
                        'due_date' => $m3[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m3

            // m4

            $m4s = $request->get('PostMESIN4');

            foreach ($m4s as $m4) {
                if (strlen($m4) > 0) {
                    $m4 = explode("#", $m4);

                    $plan = new PlanMesinInjection([
                        'mesin' => 'Mesin 4',
                        'part' => $m4[2],
                        'qty' => $m4[3],
                        'color' => $m4[1],
                        'due_date' => $m4[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m4

            // m5

            $m5s = $request->get('PostMESIN5');

            foreach ($m5s as $m5) {
                if (strlen($m5) > 0) {
                    $m5 = explode("#", $m5);

                    $plan = new PlanMesinInjection([
                        'mesin' => 'Mesin 5',
                        'part' => $m5[2],
                        'qty' => $m5[3],
                        'color' => $m5[1],
                        'due_date' => $m5[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m5

            // m6

            $m6s = $request->get('PostMESIN6');

            foreach ($m6s as $m6) {
                if (strlen($m6) > 0) {
                    $m6 = explode("#", $m6);

                    $plan = new PlanMesinInjection([
                        'mesin' => 'Mesin 6',
                        'part' => $m6[2],
                        'qty' => $m6[3],
                        'color' => $m6[1],
                        'due_date' => $m6[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m6

            // m7

            $m7s = $request->get('PostMESIN7');

            foreach ($m7s as $m7) {
                if (strlen($m7) > 0) {
                    $m7 = explode("#", $m7);

                    $plan = new PlanMesinInjection([
                        'mesin' => 'Mesin 7',
                        'part' => $m7[2],
                        'qty' => $m7[3],
                        'color' => $m7[1],
                        'due_date' => $m7[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m7

            // m8

            $m8s = $request->get('PostMESIN8');

            foreach ($m8s as $m8) {
                if (strlen($m8) > 0) {
                    $m8 = explode("#", $m8);

                    $plan = new PlanMesinInjection([
                        'mesin' => 'Mesin 8',
                        'part' => $m8[2],
                        'qty' => $m8[3],
                        'color' => $m8[1],
                        'due_date' => $m8[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m8

            // m9

            $m9s = $request->get('PostMESIN9');

            foreach ($m9s as $m9) {
                if (strlen($m9) > 0) {
                    $m9 = explode("#", $m9);

                    $plan = new PlanMesinInjection([
                        'mesin' => 'Mesin 9',
                        'part' => $m9[2],
                        'qty' => $m9[3],
                        'color' => $m9[1],
                        'due_date' => $m9[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m9

            // m11

            $m11s = $request->get('PostMESIN11');

            foreach ($m11s as $m11) {
                if (strlen($m11) > 0) {
                    $m11 = explode("#", $m11);

                    $plan = new PlanMesinInjection([
                        'mesin' => 'Mesin 11',
                        'part' => $m11[2],
                        'qty' => $m11[3],
                        'color' => $m11[1],
                        'due_date' => $m11[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m11

            $response = array(
                'status' => true,
                'message' => 'Make Schedule Success',

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

    public function getChartPlan(Request $request)
    {

        $from = $request->get('from');
        $to = $request->get('toa');

        $first = date('Y-m-01');
        $last = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));

        $querytarget = " select target.*, SUM(quantity) as qty from (
  SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color from (
  SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $first . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $last . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  ) as target_model
  CROSS join  detail_part_injections on target_model.model = detail_part_injections.model
  WHERE due_date >='" . $from . "' and due_date <='" . $to . "'
  ORDER BY due_date asc

  ) as target GROUP BY due_date
  ";

        $query = "SELECT week_date, SUM(blue) blue, SUM(green) green, SUM(pink) pink, SUM(red) red, SUM(brown) brown, SUM(ivory) ivory, SUM(yrf) yrf from (
  SELECT date.week_date, COALESCE(quantity,0) as blue, 0 as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $from . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $to . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  UNION ALL

  SELECT date.week_date, 0 as blue, COALESCE(quantity,0) as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $from . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $to . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BG|YRS20GG|YRS20GGK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  UNION ALL

  SELECT date.week_date, 0 as blue, 0 as green, COALESCE(quantity,0) as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $from . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $to . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BP|YRS20GP|YRS20GPK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  UNION ALL

  SELECT date.week_date, 0 as blue, 0 as green, 0 as pink, COALESCE(quantity,0) as red, 0 as brown, 0 as ivory, 0 as yrf     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $from . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $to . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BR'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  UNION ALL

  SELECT date.week_date, 0 as blue, 0 as green, 0 as pink, 0 as red, COALESCE(quantity,0) as brown, 0 as ivory, 0 as yrf     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $from . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $to . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS24BUK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  UNION ALL

  SELECT date.week_date, 0 as blue, 0 as green, 0 as pink, 0 as red, 0 as brown, COALESCE(quantity,0) as ivory, 0 as yrf     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $from . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $to . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS23|YRS23BR|YRS23CA|YRS23K|YRS27III|YRS24B|YRS24BBR|YRS24BCA|YRS24BK|YRS28BIII'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  UNION ALL

  SELECT date.week_date, 0 as blue, 0 as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, COALESCE(quantity,0) as yrf     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $from . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $to . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRF21|YRF21K'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  ) total GROUP BY week_date

  ";

        $query2 = "SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "' ORDER BY week_date asc ";

        $query3mesin = "SELECT a.*, SUM(qty) as total from (
  SELECT color,part,due_date,qty from plan_mesin_injection_tmps
  ) a GROUP BY due_date
  ";

        $query3 = "SELECT week_date, SUM(blue) blue, SUM(green) green, SUM(pink) pink, SUM(red) red, SUM(brown) brown, SUM(ivory) ivory, SUM(yrf) yrf from (                                                                SELECT week_date, COALESCE(target,0) as blue,  0 as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  UNION all

  SELECT week_date, 0 as blue,  COALESCE(target,0) as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf                                                                FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BG|YRS20GG' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  UNION all

  SELECT week_date, 0 as blue,  0 as green, COALESCE(target,0) as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf                                                                FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BP|YRS20GP' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  UNION all

  SELECT week_date, 0 as blue,  0 as green, 0 as pink, COALESCE(target,0) as red, 0 as brown, 0 as ivory, 0 as yrf                                                                FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BR' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  UNION all

  SELECT week_date, 0 as blue,  0 as green, 0 as pink, 0 as red, COALESCE(target,0) as brown, 0 as ivory, 0 as yrf                                                                FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS24BUK' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  UNION all

  SELECT week_date, 0 as blue,  0 as green, 0 as pink, 0 as red, 0 as brown, COALESCE(target,0) as ivory, 0 as yrf                                                                FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS23|YRS24B MIDDLE' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  UNION all

  SELECT week_date, 0 as blue,  0 as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, COALESCE(target,0) as yrf                                                                FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRF21' AND
  color like 'A YRF S%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date
  ) total GROUP BY week_date

  ";

        // $query4 ="SELECT mesin,due_date, SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) as color_p,SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 2) as part_p
        // from plan_mesin_injection_tmps  ORDER BY  id
        // ";

        $query5 = "
  SELECT * from (
  SELECT *, COUNT(mesin) total from (
  SELECT mesin,GROUP_CONCAT(due_date)due_date , SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) as color_p,
  SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 2) as part_p
  from plan_mesin_injection_tmps WHERE SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) !='OFF'  GROUP BY due_date,mesin,color_p,part_p ORDER BY  id
  ) a GROUP BY mesin
  ) aa WHERE total > 1
  ";

        $query4 = "SELECT a.due_date, COALESCE(total,1) as total from (
  SELECT * from (
  SELECT c.*, COUNT(c.mesin) total from (
  SELECT b.due_date,b.mesin from (
  SELECT a.*, GROUP_CONCAT(a.due_date) due_date_2 from (
  SELECT mesin,due_date , SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) as color_p,
  SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 2) as part_p
  from plan_mesin_injection_tmps WHERE SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) !='OFF'

  ) a  GROUP BY mesin,color_p,part_p
  ) b
  ) c GROUP BY mesin

  ) d WHERE total > 1
  ) target

  RIGHT JOIN (
  SELECT DISTINCT(due_date) from plan_mesin_injection_tmps
  ) a on target.due_date = a.due_date
  ";

        $query4a = "SELECT aa.due_date, SUM(aa.total)total from (
  SELECT a.due_date, COALESCE(total,0) as total from (
  SELECT * from (
  SELECT c.*, COUNT(c.mesin) total from (
  SELECT RIGHT(b.due_date_2,10) as due_date_all,b.mesin from (
  SELECT a.*, GROUP_CONCAT(a.due_date) due_date_2 from (
  SELECT mesin,due_date , SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) as color_p,
  SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 2) as part_p
  from plan_mesin_injection_tmps WHERE SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) !='OFF'

  ) a  GROUP BY mesin,color_p,part_p
  ) b
  ) c GROUP BY mesin

  ) d WHERE total > 1
  ) target

  RIGHT JOIN (
  SELECT DISTINCT(due_date) from plan_mesin_injection_tmps
  ) a on target.due_date_all = a.due_date
  ) aa GROUP BY due_date

  ";

        $part = DB::select($query);
        $tgl = DB::select($query2);
        $plan = DB::select($query3);
        $molding = DB::select($query4);
        $response = array(
            'status' => true,
            'part' => $part,
            'tgl' => $tgl,
            'plan' => $plan,
            'molding' => $molding,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    // ------------------------- end shchedule

    // ------------------------------- operator mesin

    public function injection_machine()
    {
        $title = 'Injection Machine';
        $title_jp = '成形機';
        $ng_lists = DB::table('ng_lists')->where('location', '=', 'Recorder')->get();

        return view('injection.machine_injection', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'ng_lists' => $ng_lists,
            'mesin' => $this->mesin,
            'dryer' => $this->dryer,
            'name' => Auth::user()->name,
        ))->with('page', 'Injection Machine');
    }

    public function getDataMesinShootLog(Request $request)
    {

        $time = date('H:m:s');
        if ($time > '07:10:00') {
            $date = date('Y-m-d');
            $last = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
        } else {
            $date = date('Y-m-d', strtotime(Carbon::yesterday()));
            $last = date('Y-m-d');
        }

        $query = "SELECT mesin, color, part, SUM(target) as target, SUM(act) as act, (SUM(act) - SUM(target)  ) as minus  from (
  SELECT mesin,color,part,qty as target, 0 as act from plan_mesin_injections
  WHERE mesin='Mesin 1' and due_date='" . $date . "'
  UNION all
  SELECT mesin, color, part,0 as target, qty as act from log_shoot_mesin_injections
  WHERE created_at >= '" . $date . " 00:00:00' and created_at <= '" . $last . " 07:10:00'
) a GROUP BY part,mesin,color";

        $target = DB::select($query);
        $response = array(
            'status' => true,
            'target' => $target,
            'message' => 'Get Target Success',

        );
        return Response::json($response);
    }

    public function getDataMesinStatusLog(Request $request)
    {

        $mesin = $request->get('mesin');
        $date = date('Y-m-d');

        $query = "SELECT `status`, reason, DATE_FORMAT(created_at,'%H:%i:%s') as start_time, COALESCE(DATE_FORMAT(deleted_at,'%H:%i:%s'),'-') as end_time  from mesin_log_injections WHERE mesin ='" . $mesin . "' and DATE_FORMAT(created_at,'%Y-%m-%d')='" . $date . "' ORDER BY created_at desc";

        $log = DB::select($query);
        $response = array(
            'status' => true,
            'log' => $log,
            'message' => 'Get Log Status Success',

        );
        return Response::json($response);
    }

    public function inputStatusMesin(Request $request)
    {
        $mesin = $request->get('mesin');
        $statusa = $request->get('statusa');
        $Reason = $request->get('Reason');
        $id = Auth::id();

        $master = MesinLogInjection::where('mesin', '=', $mesin)
            ->delete();

        try {
            $plan = new MesinLogInjection([
                'mesin' => $mesin,
                'status' => $statusa,
                'reason' => $Reason,
                'created_by' => $id,
            ]);

            $plan->save();

            $response = array(
                'status' => true,
                'message' => 'Change Status Mesin Success',

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

    public function getStatusMesian(Request $request)
    {

        $time = date('H:m:s');
        if ($time > '07:10:00') {
            $date = date('Y-m-d');
            $last = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
        } else {
            $date = date('Y-m-d', strtotime(Carbon::yesterday()));
            $last = date('Y-m-d');
        }

        $query = "SELECT mesin, color, part, SUM(target) as target, SUM(act) as act, (SUM(act) - SUM(target)  ) as minus  from (
  SELECT mesin,color,part,qty as target, 0 as act from plan_mesin_injections
  WHERE mesin='Mesin 1' and due_date='" . $date . "'
  UNION all
  SELECT mesin, color, part,0 as target, qty as act from log_shoot_mesin_injections
  WHERE created_at >= '" . $date . " 00:00:00' and created_at <= '" . $last . " 07:10:00'
) a GROUP BY part,mesin,color";

        $target = DB::select($query);
        $response = array(
            'status' => true,
            'target' => $target,
            'message' => 'Get Target Success',

        );
        return Response::json($response);
    }

    public function create_temp(Request $request)
    {
        try {
            $id_user = Auth::id();
            $injection = InjectionProcessTemp::create([
                // 'tag_product' => $request->get('tag_product'),
                'tag_molding' => $request->get('tag_molding'),
                'operator_id' => $request->get('operator_id'),
                'start_time' => date('Y-m-d H:i:s'),
                'mesin' => $request->get('mesin'),
                'part_name' => $request->get('part_name'),
                'part_type' => $request->get('part_type'),
                'color' => $request->get('color'),
                'cavity' => $request->get('cavity'),
                'molding' => $request->get('molding'),
                'material_number' => $request->get('material_number'),
                'dryer' => $request->get('dryer'),
                'dryer_lot_number' => $request->get('dryer_lot_number'),
                'dryer_color' => $request->get('dryer_color'),
                'created_by' => $id_user,
            ]);

            $machinework = InjectionMachineWork::where('mesin', $request->get('mesin'))->where('tag_molding', null)->first();
            if ($machinework != null) {
                $machinework->part_name = $request->get('part_name');
                $machinework->part_type = $request->get('part_type');
                $machinework->color = $request->get('color');
                $machinework->cavity = $request->get('cavity');
                $machinework->molding = $request->get('molding');
                $machinework->start_time = $request->get('start_time');
                $machinework->tag_molding = $request->get('tag_molding');
                $machinework->material_number = $request->get('material_number');
                $machinework->dryer = $request->get('dryer');
                $machinework->dryer_lot_number = $request->get('dryer_lot_number');
                $machinework->dryer_color = $request->get('dryer_color');
                $machinework->status = 'Working';
                $machinework->created_by = $id_user;
                $machinework->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Memulai Proses',
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

    public function update_tag(Request $request)
    {
        try {
            $id = Auth::id();

            $tag = InjectionTag::where('tag', $request->get('tag'))->first();
            $tag->part_name = $request->get('part_name');
            $tag->operator_id = $request->get('operator_id');
            $tag->part_type = $request->get('part_type');
            $tag->color = $request->get('color');
            $tag->cavity = $request->get('cavity');
            $tag->location = $request->get('location');
            $tag->material_number = $request->get('material_number');
            $tag->availability = 1;
            $tag->save();

            $machinestatus = InjectionMachineMaster::where('mesin', $request->get('location'))->first();
            $machinestatus->status = 'Work';
            $machinestatus->save();

            $response = array(
                'status' => true,
                'message' => 'Tag Updated',
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

    public function inputReasonIdleTrouble(Request $request)
    {
        try {
            $reason = InjectionMachineWorkingLog::create([
                'mesin' => $request->get('mesin'),
                'status' => $request->get('status'),
                'reason' => $request->get('reason'),
                'color' => $request->get('color'),
                'cavity' => $request->get('cavity'),
                'molding' => $request->get('molding'),
                'material_number' => $request->get('material_number'),
                'dryer' => $request->get('dryer'),
                'dryer_lot_number' => $request->get('dryer_lot_number'),
                'start_time' => date('Y-m-d H:i:s'),
                'remark' => 'Open',
                'created_by' => Auth::id(),
            ]);

            $temp = InjectionMachineWork::where('mesin', $request->get('mesin'))->first();
            $temp->status = $request->get('status');
            $temp->remark = $request->get('reason');
            $temp->save();

            $response = array(
                'status' => true,
                'message' => '',
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

    public function changeReasonIdleTrouble(Request $request)
    {
        try {
            $reason = InjectionMachineWorkingLog::where('mesin', $request->get('mesin'))->where('remark', 'Open')->first();
            $reason->end_time = date('Y-m-d H:i:s');
            $reason->remark = 'Close';
            $reason->save();

            $temp = InjectionMachineWork::where('mesin', $request->get('mesin'))->first();
            $temp->status = 'Working';
            $temp->remark = null;
            $temp->save();

            $response = array(
                'status' => true,
                'message' => '',
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

    public function get_temp(Request $request)
    {
        // $tgl = $request->get('tgl');
        // $tag_product = $request->get('tag_product');
        // $tag_molding = $request->get('tag_molding');

        $temp = InjectionProcessTemp::where('mesin', $request->get('mesin'))->first();
        $temp_machine = InjectionMachineWork::where('mesin', $request->get('mesin'))->where('tag_molding', '!=', null)->first();

        if ($temp_machine != null) {
            $response = array(
                'status' => true,
                'datas' => $temp,
                'data_mesin' => $temp_machine,
                'message' => 'Success get Temp',
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }
    }

    public function update_temp(Request $request)
    {
        try {
            $id_user = Auth::id();

            $temp = InjectionProcessTemp::where('mesin', $request->get('mesin'))
                ->where('tag_molding', $request->get('tag_molding'))
                ->first();

            if ($request->get('running_shot') == "") {
                if ($request->get('shot') != $temp->shot) {
                    $shot = $temp->shot;
                    $total = $request->get('shot');
                    $temp->shot = $total;
                } else {
                    $total = $temp->shot;
                }
            } else {
                $shot = $temp->shot;
                $total = $request->get('shot') + $shot;
                $temp->shot = $total;
            }

            $temp->ng_name = $request->get('ng_name');
            $temp->ng_count = $request->get('ng_count');
            $temp->save();

            $response = array(
                'status' => true,
                'total_shot' => $total,
                'message' => 'Temp Updated',
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

    public function create_log(Request $request)
    {
        try {
            $id_user = Auth::id();
            $injection = InjectionProcessLog::create([
                'tag_product' => $request->get('tag_product'),
                'tag_molding' => $request->get('tag_molding'),
                'operator_id' => $request->get('operator_id'),
                'start_time' => $request->get('start_time'),
                'end_time' => date('Y-m-d H:i:s'),
                'mesin' => $request->get('mesin'),
                'part_name' => $request->get('part_name'),
                'part_type' => $request->get('part_type'),
                'color' => $request->get('color'),
                'cavity' => $request->get('cavity'),
                'molding' => $request->get('molding'),
                'shot' => $request->get('shot'),
                'material_number' => $request->get('material_number'),
                'ng_name' => $request->get('ng_name'),
                'ng_count' => $request->get('ng_count'),
                'dryer' => $request->get('dryer'),
                'dryer_lot_number' => $request->get('dryer_lot_number'),
                'dryer_color' => $request->get('dryer_color'),
                'created_by' => $id_user,
            ]);

            $tag = InjectionTag::where('tag', $request->get('tag_product'))->first();
            $tag->shot = $request->get('shot');
            $tag->location = $request->get('mesin');
            $tag->part_name = $request->get('part_name');
            $tag->operator_id = $request->get('operator_id');
            $tag->part_type = $request->get('part_type');
            $tag->color = $request->get('color');
            $tag->cavity = $request->get('cavity');
            $tag->material_number = $request->get('material_number');
            $tag->availability = 1;
            $tag->save();

            $temp = InjectionProcessTemp::where('mesin', $request->get('mesin'))->delete();

            $molding_master = InjectionMoldingMaster::where('tag', $request->get('tag_molding'))->first();

            $last = $molding_master->last_counter;
            $new = $last + $request->get('shot');
            $molding_master->last_counter = $new;

            $last_ng = $molding_master->ng_count;
            $new_ng = $last_ng + $request->get('ng_counting');
            $molding_master->ng_count = $new_ng;

            $molding_master->save();

            $molding_log = InjectionMoldingLog::where('tag_molding', $request->get('tag_molding'))->where('status', 'Running')->orderBy('id', 'desc')->first();
            if ($molding_log) {
                $total_running_shot = $molding_log->total_running_shot;
                $total = $total_running_shot + $request->get('shot');
                $molding_log->status = 'Close';
                $molding_log->save();
            } else {
                $total = $request->get('shot');
            }

            InjectionMoldingLog::create([
                'tag_molding' => $request->get('tag_molding'),
                'mesin' => $request->get('mesin'),
                'part' => $request->get('molding'),
                'color' => $request->get('color'),
                'cavity' => $request->get('cavity'),
                'start_time' => $request->get('start_time'),
                'end_time' => date('Y-m-d H:i:s'),
                'running_shot' => $request->get('shot'),
                'total_running_shot' => $total,
                'ng_name' => $request->get('ng_name'),
                'ng_count' => $request->get('ng_count'),
                'status' => 'Running',
                'status_maintenance' => 'Running',
                'created_by' => $id_user,
            ]);

            //send Inventories
            // $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC11']);
            // $inventory->quantity = ($inventory->quantity+$request->get('shot'));
            // $inventory->save();

            // //send Inj Inventories
            // $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC11']);
            // $injectionInventory->quantity = ($injectionInventory->quantity+$request->get('shot'));
            // $injectionInventory->save();

            // //Transaction
            // InjectionTransaction::create([
            //     'tag' => $request->get('tag_product'),
            //     'material_number' => $request->get('material_number'),
            //     'location' => 'RC11',
            //     'quantity' => $request->get('shot'),
            //     'status' => 'IN',
            //     'operator_id' =>  $request->get('operator_id'),
            //     'created_by' => $id_user
            // ]);

            //COMPLETION KITTO

            // $material = db::connection('mysql2')->table('materials')
            //     ->where('material_number', '=', $request->get('material_number'))
            //     ->first();

            // $completion = db::connection('mysql2')->table('histories')->insert([
            //         "category" => "completion",
            //         "completion_barcode_number" => "",
            //         "completion_description" => $material->description,
            //         "completion_location" => 'RC11',
            //         "completion_issue_plant" => "8190",
            //         "completion_material_id" => $material->id,
            //         "completion_reference_number" => "",
            //         "lot" => $request->get('shot'),
            //         "synced" => 0,
            //         'user_id' => "1",
            //         'created_at' => date("Y-m-d H:i:s"),
            //         'updated_at' => date("Y-m-d H:i:s")
            //     ]);

            //SEMENTARA
            // $transaction = InjectionTag::where('tag',$request->get('tag_product'))->first();
            // $transaction->location = 'RC91';
            // $transaction->availability = 2;
            // $transaction->height_check = 'Uncheck';
            // $transaction->push_pull_check = 'Uncheck';
            // $transaction->torque_check = 'Uncheck';
            // $transaction->save();

            // $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC11']);
            // $inventory->quantity = ($inventory->quantity-$request->get('shot'));
            // $inventory->save();

            // $inventory2 = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC91']);
            // $inventory2->quantity = ($inventory2->quantity+$request->get('shot'));
            // $inventory2->save();

            // //send Inj Inventories
            // $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC11']);
            // $injectionInventory->quantity = ($injectionInventory->quantity-$request->get('shot'));
            // $injectionInventory->save();

            // $injectionInventory2 = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC91']);
            // $injectionInventory2->quantity = ($injectionInventory2->quantity+$request->get('shot'));
            // $injectionInventory2->save();

            // InjectionTransaction::create([
            //     'material_number' => $request->get('material_number'),
            //     'location' => 'RC11',
            //     'quantity' => $request->get('shot'),
            //     'status' => 'OUT',
            //     'operator_id' => $request->get('operator_id'),
            //     'created_by' => $id_user
            // ]);

            // InjectionTransaction::create([
            //     'material_number' => $request->get('material_number'),
            //     'location' => 'RC91',
            //     'quantity' => $request->get('shot'),
            //     'status' => 'IN',
            //     'operator_id' => $request->get('operator_id'),
            //     'created_by' => $id_user
            // ]);

            $response = array(
                'status' => true,
                'message' => 'Log Created',
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

    public function inputMesinLog(Request $request)
    {
        try {
            InjectionMachineLog::create([
                'mesin' => $request->get('mesin'),
                'material_number' => $request->get('material_number'),
                'part_name' => $request->get('part_name'),
                'part_type' => $request->get('part_type'),
                'color' => $request->get('color'),
                'cavity' => $request->get('cavity'),
                'molding' => $request->get('molding'),
                'dryer' => $request->get('dryer'),
                'dryer_lot_number' => $request->get('dryer_lot_number'),
                'dryer_color' => $request->get('dryer_color'),
                'start_time' => $request->get('start_time'),
                'end_time' => date('Y-m-d H:i:s'),
                'created_by' => Auth::id(),
            ]);

            $machinework = InjectionMachineWork::where('mesin', $request->get('mesin'))->first();
            if ($machinework != null) {
                $machinework->tag_molding = null;
                $machinework->part_name = null;
                $machinework->part_type = null;
                $machinework->color = null;
                $machinework->cavity = null;
                $machinework->molding = null;
                $machinework->start_time = null;
                $machinework->tag_molding = null;
                $machinework->material_number = null;
                $machinework->dryer = null;
                $machinework->dryer_lot_number = null;
                $machinework->dryer_color = null;
                $machinework->status = null;
                $machinework->save();
            }

            InjectionProcessTemp::where('mesin', $request->get('mesin'))->forceDelete();

            $response = array(
                'status' => true,
                'message' => 'Proses Injeksi Selesai',
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

    // ------------------------------- end operator mesin

    public function saveScheduleTmp(Request $request)
    {

        $id = Auth::id();
        $date = date('Y-m-d');
        $master = PlanMesinInjectionTmp::whereNull('deleted_at')
            ->forceDelete();
        try {

            // m1

            $m1s = $request->get('PostMESIN1');

            foreach ($m1s as $m1) {
                if (strlen($m1) > 0) {
                    $m1 = explode("#", $m1);

                    $plan = new PlanMesinInjectionTmp([
                        'mesin' => 'Mesin 1',
                        'part' => $m1[2],
                        'qty' => $m1[3],
                        'color' => $m1[1],
                        'due_date' => $m1[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m1

            // m2

            $m2s = $request->get('PostMESIN2');

            foreach ($m2s as $m2) {
                if (strlen($m2) > 0) {
                    $m2 = explode("#", $m2);

                    $plan = new PlanMesinInjectionTmp([
                        'mesin' => 'Mesin 2',
                        'part' => $m2[2],
                        'qty' => $m2[3],
                        'color' => $m2[1],
                        'due_date' => $m2[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m2

            // m3

            $m3s = $request->get('PostMESIN3');

            foreach ($m3s as $m3) {
                if (strlen($m3) > 0) {
                    $m3 = explode("#", $m3);

                    $plan = new PlanMesinInjectionTmp([
                        'mesin' => 'Mesin 3',
                        'part' => $m3[2],
                        'qty' => $m3[3],
                        'color' => $m3[1],
                        'due_date' => $m3[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m3

            // m4

            $m4s = $request->get('PostMESIN4');

            foreach ($m4s as $m4) {
                if (strlen($m4) > 0) {
                    $m4 = explode("#", $m4);

                    $plan = new PlanMesinInjectionTmp([
                        'mesin' => 'Mesin 4',
                        'part' => $m4[2],
                        'qty' => $m4[3],
                        'color' => $m4[1],
                        'due_date' => $m4[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m4

            // m5

            $m5s = $request->get('PostMESIN5');

            foreach ($m5s as $m5) {
                if (strlen($m5) > 0) {
                    $m5 = explode("#", $m5);

                    $plan = new PlanMesinInjectionTmp([
                        'mesin' => 'Mesin 5',
                        'part' => $m5[2],
                        'qty' => $m5[3],
                        'color' => $m5[1],
                        'due_date' => $m5[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m5

            // m6

            $m6s = $request->get('PostMESIN6');

            foreach ($m6s as $m6) {
                if (strlen($m6) > 0) {
                    $m6 = explode("#", $m6);

                    $plan = new PlanMesinInjectionTmp([
                        'mesin' => 'Mesin 6',
                        'part' => $m6[2],
                        'qty' => $m6[3],
                        'color' => $m6[1],
                        'due_date' => $m6[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m6

            // m7

            $m7s = $request->get('PostMESIN7');

            foreach ($m7s as $m7) {
                if (strlen($m7) > 0) {
                    $m7 = explode("#", $m7);

                    $plan = new PlanMesinInjectionTmp([
                        'mesin' => 'Mesin 7',
                        'part' => $m7[2],
                        'qty' => $m7[3],
                        'color' => $m7[1],
                        'due_date' => $m7[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m7

            // m8

            $m8s = $request->get('PostMESIN8');

            foreach ($m8s as $m8) {
                if (strlen($m8) > 0) {
                    $m8 = explode("#", $m8);

                    $plan = new PlanMesinInjectionTmp([
                        'mesin' => 'Mesin 8',
                        'part' => $m8[2],
                        'qty' => $m8[3],
                        'color' => $m8[1],
                        'due_date' => $m8[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m8

            // m9

            $m9s = $request->get('PostMESIN9');

            foreach ($m9s as $m9) {
                if (strlen($m9) > 0) {
                    $m9 = explode("#", $m9);

                    $plan = new PlanMesinInjectionTmp([
                        'mesin' => 'Mesin 9',
                        'part' => $m9[2],
                        'qty' => $m9[3],
                        'color' => $m9[1],
                        'due_date' => $m9[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m9

            // m11

            $m11s = $request->get('PostMESIN11');

            foreach ($m11s as $m11) {
                if (strlen($m11) > 0) {
                    $m11 = explode("#", $m11);

                    $plan = new PlanMesinInjectionTmp([
                        'mesin' => 'Mesin 11',
                        'part' => $m11[2],
                        'qty' => $m11[3],
                        'color' => $m11[1],
                        'due_date' => $m11[0],
                        'created_by' => $id,
                    ]);

                    $plan->save();
                }
            }

            // end  m11

            $response = array(
                'status' => true,
                'message' => 'Make Schedule Success',

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

    // ------------- monhtly report

    public function indexMonhtlyStock()
    {
        return view('injection.monthlyStock')->with('page', 'Injection Monhtly Target')->with('jpn', '???');
    }

    public function MonhtlyStock(Request $request)
    {
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl . '-d';
        $tgl2 = $tgl . '-01';

        $model2 = "AND color like 'MJ%'";

        if ($tgl != "") {
            $moth = $request->get('tgl');
            $day = date($tgl1, strtotime(carbon::now()->endOfMonth()));
            $first = date($tgl2);
        } else {
            $moth = date('Y-m');
            $day = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
            $first = date('Y-m-01');
        }

        if ($location == "Blue") {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        } elseif ($location == "Green") {
            $reg = "YRS20BG|YRS20GG";
            $reg2 = "YRS20BG|YRS20GG|YRS20GGK";
        } elseif ($location == "Pink") {
            $reg = "YRS20BP|YRS20GP";
            $reg2 = "YRS20BP|YRS20GP|YRS20GPK";
        } elseif ($location == "Red") {
            $reg = "YRS20BR";
            $reg2 = "YRS20BR";
        } elseif ($location == "Brown") {
            $reg = "YRS24BUK";
            $reg2 = "YRS24BUK";
        } elseif ($location == "Ivory") {
            $reg = "YRS23|YRS24B MIDDLE";
            $reg2 = "YRS23|YRS23BR|YRS23CA|YRS23K|YRS27III|YRS24B|YRS24BBR|YRS24BCA|YRS24BK|YRS28BIII";
        } elseif ($location == "Yrf") {
            $reg = "YRF21";
            $reg2 = "YRF21|YRF21K";
            $model2 = "AND color like '%A YRF B%'";
        } else {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP '" . $reg2 . "'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '" . $reg . "' " . $model2 . "
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function MonhtlyStockHead(Request $request)
    {
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl . '-d';
        $tgl2 = $tgl . '-01';

        $model2 = "AND color like 'HJ%'";

        if ($tgl != "") {
            $moth = $request->get('tgl');
            $day = date($tgl1, strtotime(carbon::now()->endOfMonth()));
            $first = date($tgl2);
        } else {
            $moth = date('Y-m');
            $day = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
            $first = date('Y-m-01');
        }

        if ($location == "Blue") {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        } elseif ($location == "Green") {
            $reg = "YRS20BG|YRS20GG";
            $reg2 = "YRS20BG|YRS20GG|YRS20GGK";
        } elseif ($location == "Pink") {
            $reg = "YRS20BP|YRS20GP";
            $reg2 = "YRS20BP|YRS20GP|YRS20GPK";
        } elseif ($location == "Red") {
            $reg = "YRS20BR";
            $reg2 = "YRS20BR";
        } elseif ($location == "Brown") {
            $reg = "YRS24BUK";
            $reg2 = "YRS24BUK";
        } elseif ($location == "Ivory") {
            $reg = "YRS23|YRS24B MIDDLE";
            $reg2 = "YRS23|YRS23BR|YRS23CA|YRS23K|YRS27III|YRS24B|YRS24BBR|YRS24BCA|YRS24BK|YRS28BIII";
        } elseif ($location == "Yrf") {
            $reg = "YRF21";
            $reg2 = "YRF21|YRF21K";
            $model2 = "AND color like '%A YRF H%'";
        } else {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP '" . $reg2 . "'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '" . $reg . "' " . $model2 . "
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function MonhtlyStockAll(Request $request)
    {
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl . '-d';
        $tgl2 = $tgl . '-01';

        $model2 = "AND color like 'FJ%'";

        if ($tgl != "") {
            $moth = $request->get('tgl');
            $day = date($tgl1, strtotime(carbon::now()->endOfMonth()));
            $first = date($tgl2);
        } else {
            $moth = date('Y-m');
            $day = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
            $first = date('Y-m-01');
        }

        if ($location == "Blue") {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        } elseif ($location == "Green") {
            $reg = "YRS20BG|YRS20GG";
            $reg2 = "YRS20BG|YRS20GG|YRS20GGK";
        } elseif ($location == "Pink") {
            $reg = "YRS20BP|YRS20GP";
            $reg2 = "YRS20BP|YRS20GP|YRS20GPK";
        } elseif ($location == "Red") {
            $reg = "YRS20BR";
            $reg2 = "YRS20BR";
        } elseif ($location == "Brown") {
            $reg = "YRS24BUK";
            $reg2 = "YRS24BUK";
        } elseif ($location == "Ivory") {
            $reg = "YRS23|YRS24B MIDDLE";
            $reg2 = "YRS23|YRS23BR|YRS23CA|YRS23K|YRS27III|YRS24B|YRS24BBR|YRS24BCA|YRS24BK|YRS28BIII";
        } elseif ($location == "Yrf") {
            $reg = "YRF21";
            $reg2 = "YRF21|YRF21K";
            $model2 = "AND color like '%A YRF S%'";
        } else {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP '" . $reg2 . "'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as mj from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '" . $reg . "' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as block from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '" . $reg . "' AND
  color like 'BJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as head from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '" . $reg . "' AND
  color like 'HJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as foot from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '" . $reg . "' AND
  color like 'FJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";
        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function MonhtlyStockAllYrf(Request $request)
    {
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl . '-d';
        $tgl2 = $tgl . '-01';

        if ($tgl != "") {
            $moth = $request->get('tgl');
            $day = date($tgl1, strtotime(carbon::now()->endOfMonth()));
            $first = date($tgl2);
        } else {
            $moth = date('Y-m');
            $day = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
            $first = date('Y-m-01');
        }

        $query = "SELECT week_date, SUM(assy) assy, SUM(b) b, SUM(s) s, SUM(h) h from (
  SELECT date.week_date, COALESCE(quantity,0) as assy, 0 as b, 0 as s, 0 as h    from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRF21|YRF21K'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(b,0) b,  0 as s, 0 as h  FROM (
  SELECT * from (
  select a.*, SUM(qty) as b from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
  color like 'A YRF B%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as b,  COALESCE(s,0) as s, 0 as h  FROM (
  SELECT * from (
  select a.*, SUM(qty) as s from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
  color like 'A YRF S%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as b,  0 as s, COALESCE(h,0) as h  FROM (
  SELECT * from (
  select a.*, SUM(qty) as h from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
  color like 'A YRF H%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on aa.due_date = date.week_date
  ) as a GROUP BY week_date
  ";
        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function MonhtlyStockFoot(Request $request)
    {
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl . '-d';
        $tgl2 = $tgl . '-01';

        $model2 = "AND color like 'FJ%'";

        if ($tgl != "") {
            $moth = $request->get('tgl');
            $day = date($tgl1, strtotime(carbon::now()->endOfMonth()));
            $first = date($tgl2);
        } else {
            $moth = date('Y-m');
            $day = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
            $first = date('Y-m-01');
        }

        if ($location == "Blue") {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        } elseif ($location == "Green") {
            $reg = "YRS20BG|YRS20GG";
            $reg2 = "YRS20BG|YRS20GG|YRS20GGK";
        } elseif ($location == "Pink") {
            $reg = "YRS20BP|YRS20GP";
            $reg2 = "YRS20BP|YRS20GP|YRS20GPK";
        } elseif ($location == "Red") {
            $reg = "YRS20BR";
            $reg2 = "YRS20BR";
        } elseif ($location == "Brown") {
            $reg = "YRS24BUK";
            $reg2 = "YRS24BUK";
        } elseif ($location == "Ivory") {
            $reg = "YRS23|YRS24B MIDDLE";
            $reg2 = "YRS23|YRS23BR|YRS23CA|YRS23K|YRS27III|YRS24B|YRS24BBR|YRS24BCA|YRS24BK|YRS28BIII";
        } elseif ($location == "Yrf") {
            $reg = "YRF21";
            $reg2 = "YRF21|YRF21K";
            $model2 = "AND color like '%A YRF S%'";
        } else {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP '" . $reg2 . "'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '" . $reg . "' " . $model2 . "
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";
        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function MonhtlyStockBlock(Request $request)
    {
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl . '-d';
        $tgl2 = $tgl . '-01';

        $model2 = "AND color like 'BJ%'";

        if ($tgl != "") {
            $moth = $request->get('tgl');
            $day = date($tgl1, strtotime(carbon::now()->endOfMonth()));
            $first = date($tgl2);
        } else {
            $moth = date('Y-m');
            $day = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
            $first = date('Y-m-01');
        }

        if ($location == "Blue") {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        } elseif ($location == "Green") {
            $reg = "YRS20BG|YRS20GG";
            $reg2 = "YRS20BG|YRS20GG|YRS20GGK";
        } elseif ($location == "Pink") {
            $reg = "YRS20BP|YRS20GP";
            $reg2 = "YRS20BP|YRS20GP|YRS20GPK";
        } elseif ($location == "Red") {
            $reg = "YRS20BR";
            $reg2 = "YRS20BR";
        } elseif ($location == "Brown") {
            $reg = "YRS24BUK";
            $reg2 = "YRS24BUK";
        } elseif ($location == "Ivory") {
            $reg = "YRS23|YRS24B MIDDLE";
            $reg2 = "YRS23|YRS23BR|YRS23CA|YRS23K|YRS27III|YRS24B|YRS24BBR|YRS24BCA|YRS24BK|YRS28BIII";
        } elseif ($location == "Yrf") {
            $reg = "YRF21";
            $reg2 = "YRF21|YRF21K";
            $model2 = "AND color like '%A YRF S%'";
        } else {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP '" . $reg2 . "'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '" . $reg . "' " . $model2 . "
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    // ------------- end monhtly report

    // ------------- start daily ng report

    public function indexDailyNG()
    {
        return view('injection.dailyNG')->with('title', 'Daily Injection NG Monitoring')->with('title_jp', '???');
    }

    public function dailyNG(Request $request)
    {
        // $tgl = $request->get('tgl');
        if ($request->get('tgl') == '') {
            $tgl = date('Y-m-d');
        } else {
            $tgl = $request->get('tgl');
        }

        $query = "SELECT IF(mesin = 'MESIN1',
  'Mesin 1',
  IF(mesin = 'MESIN2',
  'Mesin 2',
  IF(mesin = 'MESIN3',
  'Mesin 3',
  IF(mesin = 'MESIN4',
  'Mesin 4',
  IF(mesin = 'MESIN5',
  'Mesin 5',
  IF(mesin = 'MESIN6',
  'Mesin 6',
  IF(mesin = 'MESIN7',
  'Mesin 7',
  IF(mesin = 'MESIN8',
  'Mesin 8',
  IF(mesin = 'MESIN9',
  'Mesin 9',
  IF(mesin = 'MESIN11',
  'Mesin 11',
  0))))))))))
  as mesin,
  IF(mesin = 'MESIN1',
  COALESCE((select SUM(SUM_OF_LIST(ng_count))
  from ng_log_mesin_injections
  where mesin = 'Mesin 1'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN2',
  COALESCE((select SUM(SUM_OF_LIST(ng_count))
  from ng_log_mesin_injections
  where mesin = 'Mesin 2'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN3',
  COALESCE((select SUM(SUM_OF_LIST(ng_count))
  from ng_log_mesin_injections
  where mesin = 'Mesin 3'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN4',
  COALESCE((select SUM(SUM_OF_LIST(ng_count))
  from ng_log_mesin_injections
  where mesin = 'Mesin 4'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN5',
  COALESCE((select SUM(SUM_OF_LIST(ng_count))
  from ng_log_mesin_injections
  where mesin = 'Mesin 5'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN6',
  COALESCE((select SUM(SUM_OF_LIST(ng_count))
  from ng_log_mesin_injections
  where mesin = 'Mesin 6'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN7',
  COALESCE((select SUM(SUM_OF_LIST(ng_count))
  from ng_log_mesin_injections
  where mesin = 'Mesin 7'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN8',
  COALESCE((select SUM(SUM_OF_LIST(ng_count))
  from ng_log_mesin_injections
  where mesin = 'Mesin 8'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN9',
  COALESCE((select SUM(SUM_OF_LIST(ng_count))
  from ng_log_mesin_injections
  where mesin = 'Mesin 9'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN11',
  COALESCE((select SUM(SUM_OF_LIST(ng_count))
  from ng_log_mesin_injections
  where mesin = 'Mesin 11'
  and DATE(created_at) = '" . $tgl . "'),0),0))))))))))
  as jumlah_ng,
  IF(mesin = 'MESIN1',
  COALESCE((select SUM(jumlah_shot)
  from ng_log_mesin_injections
  where mesin = 'Mesin 1'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN2',
  COALESCE((select SUM(jumlah_shot)
  from ng_log_mesin_injections
  where mesin = 'Mesin 2'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN3',
  COALESCE((select SUM(jumlah_shot)
  from ng_log_mesin_injections
  where mesin = 'Mesin 3'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN4',
  COALESCE((select SUM(jumlah_shot)
  from ng_log_mesin_injections
  where mesin = 'Mesin 4'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN5',
  COALESCE((select SUM(jumlah_shot)
  from ng_log_mesin_injections
  where mesin = 'Mesin 5'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN6',
  COALESCE((select SUM(jumlah_shot)
  from ng_log_mesin_injections
  where mesin = 'Mesin 6'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN7',
  COALESCE((select SUM(jumlah_shot)
  from ng_log_mesin_injections
  where mesin = 'Mesin 7'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN8',
  COALESCE((select SUM(jumlah_shot)
  from ng_log_mesin_injections
  where mesin = 'Mesin 8'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN9',
  COALESCE((select SUM(jumlah_shot)
  from ng_log_mesin_injections
  where mesin = 'Mesin 9'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN11',
  COALESCE((select SUM(jumlah_shot)
  from ng_log_mesin_injections
  where mesin = 'Mesin 11'
  and DATE(created_at) = '" . $tgl . "'),0),0))))))))))
  as jumlah_shot,
  IF(mesin = 'MESIN1',
  COALESCE((select (SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100
  from ng_log_mesin_injections
  where mesin = 'Mesin 1'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN2',
  COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
  from ng_log_mesin_injections
  where mesin = 'Mesin 2'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN3',
  COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
  from ng_log_mesin_injections
  where mesin = 'Mesin 3'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN4',
  COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
  from ng_log_mesin_injections
  where mesin = 'Mesin 4'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN5',
  COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
  from ng_log_mesin_injections
  where mesin = 'Mesin 5'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN6',
  COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
  from ng_log_mesin_injections
  where mesin = 'Mesin 6'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN7',
  COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
  from ng_log_mesin_injections
  where mesin = 'Mesin 7'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN8',
  COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
  from ng_log_mesin_injections
  where mesin = 'Mesin 8'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN9',
  COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
  from ng_log_mesin_injections
  where mesin = 'Mesin 9'
  and DATE(created_at) = '" . $tgl . "'),0),
  IF(mesin = 'MESIN11',
  COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
  from ng_log_mesin_injections
  where mesin = 'Mesin 11'
  and DATE(created_at) = '" . $tgl . "'),0),0))))))))))
  as persen_ng
  FROM `status_mesin_injections`
  ";

        $dailyNG = DB::select($query);
        $response = array(
            'status' => true,
            'datas' => $dailyNG,
        );
        return Response::json($response);
    }

    public function detailDailyNG(Request $request)
    {
        // if($request->get('tgl') == ''){
        //     $tanggal = date('Y-m-d');
        // }
        // else{
        $tanggal = $request->get('tanggal');
        // }
        $mesin = $request->get("mesin");

        $query = "select * from ng_log_mesin_injections where mesin = '" . $mesin . "' and date(created_at) = '" . $tanggal . "'";

        $detail = db::select($query);

        $response = array(
            'status' => true,
            'lists' => $detail,
        );
        return Response::json($response);
    }

    // ------------- end daily ng report

    // ------------- start molding monitoring

    public function index_molding_monitoring($condition)
    {
        return view('injection.molding_monitoring')
            ->with('title', 'Molding Maintenance Monitoring')
            ->with('title_jp', '金型保全管理')
            ->with('condition', $condition);
    }

    public function molding_monitoring(Request $request)
    {

        $query_pasang = DB::SELECT("SELECT * FROM `injection_molding_masters` where `status` = 'PASANG' order by CAST(SPLIT_STRING(status_mesin, ' ', 2) as INT)");

        $query_ready = DB::SELECT("SELECT
            *
    FROM
    `injection_molding_masters`
    WHERE
    STATUS = 'LEPAS'
    ORDER BY
    CAST(SPLIT_STRING(status_mesin, ' ', 2) as INT)");

        $query_not_ready = DB::SELECT("SELECT
            *
    FROM
    `injection_molding_masters`
    WHERE
    ( status = 'DIPERBAIKI' )
    OR
    ( status = 'HARUS MAINTENANCE' )
    OR (status = 'LEPAS'
    AND last_counter >= 15000)");
        $query_maintenance = DB::SELECT("SELECT
            *
    FROM
    `injection_molding_masters`
    WHERE
    ( STATUS = 'DIPERBAIKI' )
    OR (STATUS = 'HARUS MAINTENANCE')");
        $response = array(
            'status' => true,
            'query_pasang' => $query_pasang,
            'query_ready' => $query_ready,
            'query_not_ready' => $query_not_ready,
            'query_maintenance' => $query_maintenance,
        );
        return Response::json($response);
    }

    public function index_molding_schedule()
    {
        return view('injection.molding_schedule')->with('title', 'Molding Schedule')->with('title_jp', '');
    }

    public function molding_schedule(Request $request)
    {
        // $tgl = $request->get('tgl');
        // if($request->get('tgl') == ''){
        //     $tgl = date('Y-m-d');
        // }
        // else{
        //     $tgl = $request->get('tgl');
        // }

        $schedules = DB::SELECT("SELECT
    a.date,
    a.date_name,
    GROUP_CONCAT( a.schedules ) AS schedule_isi
    FROM
    (
    SELECT
    week_date AS date,
    DATE_FORMAT( week_date, '%d %b %Y' ) AS date_name,
    (
    SELECT
    GROUP_CONCAT(
    CONCAT(
    machine,
    '_',
    part,
    '_',
    color,
    '_',
    DATE_FORMAT( start_time, '%H:%i:%s' ),
    '_',(
    SELECT
    GROUP_CONCAT( part SEPARATOR ' - ' )
    FROM
    injection_molding_masters
    WHERE
    injection_molding_masters.product = injection_schedule_molding_logs.part
    AND `status` = 'LEPAS'
    )
    ))
    FROM
    injection_schedule_molding_logs
    WHERE
    date( start_time ) = week_date
    ) AS schedules
    FROM
    weekly_calendars
    WHERE
    remark != 'H'
    AND week_date >= '" . date('Y-m-01') . "'
    AND week_date <= '" . date('Y-m-t') . "' UNION ALL
    SELECT
    week_date AS date,
    DATE_FORMAT( week_date, '%d %b %Y' ) AS date_name,
    (
    SELECT
    GROUP_CONCAT(
    CONCAT(
    machine,
    '_',
    part,
    '_',
    color,
    '_',
    DATE_FORMAT( start_time, '%H:%i:%s' ),
    '_',(
    SELECT
    GROUP_CONCAT( part SEPARATOR ' - ' )
    FROM
    injection_molding_masters
    WHERE
    injection_molding_masters.product = injection_schedule_moldings.part
    AND `status` = 'LEPAS'
    )
    ))
    FROM
    injection_schedule_moldings
    WHERE
    date( start_time ) = week_date
    ) AS schedules
    FROM
    weekly_calendars
    WHERE
    remark != 'H'
    AND week_date >= '" . date('Y-m-01') . "'
    AND week_date <= '" . date('Y-m-t') . "'
    ) a
    GROUP BY
    a.date,
    a.date_name");

        $response = array(
            'status' => true,
            'schedules' => $schedules,
        );
        return Response::json($response);
    }

    // ------------- end molding monitoring

    // -------------------- start persen mesin

    public function chartWorkingMachine(Request $request)
    {

        $query = "SELECT week_date, SUM(total_1) total_1, SUM(total_2) total_2, SUM(total_3)total_3, SUM(total_4) total_4, SUM(total_5) total_5, SUM(total_6) total_6, SUM(total_7) total_7, SUM(total_8) total_8, SUM(total_9) total_9, SUM(total_11) total_11 from (
  SELECT mesin,week_date, SUM(qty) as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps
  LEFT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
  ) as date on plan_mesin_injection_tmps.due_date = date.week_date
  WHERE mesin='Mesin 1'
  GROUP BY week_date,mesin

  UNION all

  SELECT mesin,week_date, 0 as total_1, SUM(qty) as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps
  LEFT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
  ) as date on plan_mesin_injection_tmps.due_date = date.week_date
  WHERE mesin='Mesin 2'
  GROUP BY week_date,mesin

  UNION all

  SELECT mesin,week_date, 0 as total_1, 0 as total_2, SUM(qty) as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps
  LEFT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
  ) as date on plan_mesin_injection_tmps.due_date = date.week_date
  WHERE mesin='Mesin 3'
  GROUP BY week_date,mesin

  UNION all

  SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, SUM(qty) as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps
  LEFT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
  ) as date on plan_mesin_injection_tmps.due_date = date.week_date
  WHERE mesin='Mesin 4'
  GROUP BY week_date,mesin

  UNION all

  SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, SUM(qty) as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps
  LEFT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
  ) as date on plan_mesin_injection_tmps.due_date = date.week_date
  WHERE mesin='Mesin 5'
  GROUP BY week_date,mesin

  UNION all

  SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, SUM(qty) as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps
  LEFT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
  ) as date on plan_mesin_injection_tmps.due_date = date.week_date
  WHERE mesin='Mesin 6'
  GROUP BY week_date,mesin

  UNION all

  SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, SUM(qty) as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps
  LEFT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
  ) as date on plan_mesin_injection_tmps.due_date = date.week_date
  WHERE mesin='Mesin 7'
  GROUP BY week_date,mesin

  UNION all

  SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, SUM(qty) as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps
  LEFT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
  ) as date on plan_mesin_injection_tmps.due_date = date.week_date
  WHERE mesin='Mesin 8'
  GROUP BY week_date,mesin

  UNION all

  SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, SUM(qty) as total_9, 0 as total_11 from plan_mesin_injection_tmps
  LEFT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
  ) as date on plan_mesin_injection_tmps.due_date = date.week_date
  WHERE mesin='Mesin 9'
  GROUP BY week_date,mesin

  UNION all

  SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, SUM(qty) as total_11 from plan_mesin_injection_tmps
  LEFT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
  ) as date on plan_mesin_injection_tmps.due_date = date.week_date
  WHERE mesin='Mesin 11'
  GROUP BY week_date,mesin
  ) as total GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function percenMesin(Request $request)
    {

        $query = "SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
  SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 1' GROUP BY due_date,mesin
  ) a GROUP BY mesin

  union all

  SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
  SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 2' GROUP BY due_date,mesin
  ) a GROUP BY mesin

  union all

  SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
  SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 3' GROUP BY due_date,mesin
  ) a GROUP BY mesin

  union all

  SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
  SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 4' GROUP BY due_date,mesin
  ) a GROUP BY mesin

  union all

  SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
  SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 5' GROUP BY due_date,mesin
  ) a GROUP BY mesin

  union all

  SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
  SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 6' GROUP BY due_date,mesin
  ) a GROUP BY mesin

  union all

  SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
  SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 7' GROUP BY due_date,mesin
  ) a GROUP BY mesin

  union all

  SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
  SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 8' GROUP BY due_date,mesin
  ) a GROUP BY mesin

  union all

  SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
  SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 9' GROUP BY due_date,mesin
  ) a GROUP BY mesin

  union all

  SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
  SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 11' GROUP BY due_date,mesin
  ) a GROUP BY mesin
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    // -------------------- end persen mesin

    // -------------------- start mj mesin

    public function detailPartMJBlue(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('toa');

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $from . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $to . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    // -------------------- end mj mesin

    // -------------------- start hj mesin

    public function detailPartHeadBlue(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('toa');

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $from . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $to . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND color like 'HJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyGreen(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('toa');

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BG|YRS20GG|YRS20GGK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as mj from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BG|YRS20GG' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as block from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BG|YRS20GG' AND
  color like 'BJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as head from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BG|YRS20GG' AND
  color like 'HJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as foot from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BG|YRS20GG' AND
  color like 'FJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyBlue(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('toa');

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as mj from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as block from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND
  color like 'BJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as head from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND
  color like 'HJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as foot from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND
  color like 'FJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyPink(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('toa');

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BP|YRS20GP|YRS20GPK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as mj from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BP|YRS20GP' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as block from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BP|YRS20GP' AND
  color like 'BJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as head from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BP|YRS20GP' AND
  color like 'HJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as foot from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BP|YRS20GP' AND
  color like 'FJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyRed(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('toa');

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BR'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as mj from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BR' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as block from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BR' AND
  color like 'BJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as head from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BR' AND
  color like 'HJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as foot from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BR' AND
  color like 'FJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyBrown(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('toa');

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS24BUK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as mj from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS24BUK' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as block from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS24BUK' AND
  color like 'BJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as head from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS24BUK' AND
  color like 'HJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as foot from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS24BUK' AND
  color like 'FJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyIvory(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('toa');

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS23|YRS23BR|YRS23CA|YRS23K|YRS27III|YRS24B|YRS24BBR|YRS24BCA|YRS24BK|YRS28BIII'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as mj from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS23|YRS24B MIDDLE' AND
  color like 'MJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as block from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS23|YRS24B MIDDLE' AND
  color like 'BJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as head from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS23|YRS24B MIDDLE' AND
  color like 'HJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (
  SELECT * from (
  select a.*, SUM(qty) as foot from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS23|YRS24B MIDDLE' AND
  color like 'FJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyYrf(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('toa');

        $query = "SELECT week_date, SUM(assy) assy, SUM(b) b, SUM(s) s, SUM(h) h from (
  SELECT date.week_date, COALESCE(quantity,0) as assy, 0 as b, 0 as s, 0 as h    from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRF21|YRF21K'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(b,0) b,  0 as s, 0 as h  FROM (
  SELECT * from (
  select a.*, SUM(qty) as b from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
  color like 'A YRF B%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as b,  COALESCE(s,0) as s, 0 as h  FROM (
  SELECT * from (
  select a.*, SUM(qty) as s from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
  color like 'A YRF S%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, 0 as b,  0 as s, COALESCE(h,0) as h  FROM (
  SELECT * from (
  select a.*, SUM(qty) as h from (
  SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
  color like 'A YRF H%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date
  ) as a GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    // -------------------- end hj mesin

    // -------------------- start fj mesin

    public function detailPartFootBlue(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('toa');

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $from . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $to . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND color like 'FJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    // -------------------- end fj mesin

    // -------------------- start fj mesin

    public function detailPartBlockBlue(Request $request)
    {

        $from = $request->get('from');
        $to = $request->get('toa');

        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (
  SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
  SELECT target.due_date,target.quantity  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='" . $from . "' and DATE_FORMAT(due_date,'%Y-%m-%d') <='" . $to . "'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
  ) target
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on target.due_date = date.week_date

  union all

  SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (
  SELECT * from (
  select a.*, SUM(qty) as target from (
  SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND color like 'BJ%'
  ) a GROUP BY due_date
  ) target
  ) as aa
  RIGHT JOIN (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='" . $from . "' and week_date <='" . $to . "'
  ) as date on aa.due_date = date.week_date

  ) TARGET GROUP BY week_date
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    // -------------------- end fj mesin

    // ---------------------- master working machine

    public function masterMachine()
    {
        $mesin = $this->mesin;
        $color = $this->color;
        $part = $this->part;
        $model = $this->model;

        return view('injection.masterMachine', array(
            'mesin' => $mesin,
            'color' => $color,
            'part' => $part,
            'model' => $model,
        ))->with('page', 'Machine Injection')->with('jpn', '???');

    }

    public function fillMasterMachine(Request $request)
    {
        $op = "select id,mesin,part,color,model from working_mesin_injections";
        $ops = DB::select($op);
        return DataTables::of($ops)

            ->addColumn('edit', function ($ops) {
                return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editop(id)" id="' . $ops->id . '"><i class="fa fa-edit"></i></a>';
            })
            ->rawColumns(['edit' => 'edit'])

            ->make(true);
    }

    public function editMasterMachine(Request $request)
    {
        $id_op = WorkingMesinInjection::where('id', '=', $request->get('id'))->get();

        $response = array(
            'status' => true,
            'id_op' => $id_op,
        );
        return Response::json($response);
    }

    public function updateMasterMachine(Request $request)
    {
        $id_user = Auth::id();

        try {
            $op = WorkingMesinInjection::where('id', '=', $request->get('id'))
                ->first();
            $op->mesin = $request->get('mesin');
            $op->part = $request->get('part');
            $op->color = $request->get('color');
            $op->model = $request->get('model');
            $op->created_by = $id_user;

            $op->save();

            $response = array(
                'status' => true,
                'message' => 'Update Success',
            );
            return redirect('/index/masterMachine')->with('status', 'Update Machine success')->with('page', 'Master Operator');
        } catch (QueryException $e) {
            return redirect('/index/masterMachine')->with('error', $e->getMessage())->with('page', 'Master Operator');
        }

    }

    public function addMasterMachine(Request $request)
    {
        $id_user = Auth::id();

        try {

            $head = new WorkingMesinInjection([
                'mesin' => $request->get('mesin3'),
                'part' => $request->get('part3'),
                'color' => $request->get('color3'),
                'model' => $request->get('model3'),
                'qty' => '1',
                'created_by' => $id_user,
            ]);
            $head->save();

            $response = array(
                'status' => true,
                'message' => 'Add Machine Success',
            );
            return redirect('/index/masterMachine')->with('status', 'Update Machine success')->with('page', 'Master Operator');
        } catch (QueryException $e) {
            return redirect('/index/masterMachine')->with('error', $e->getMessage())->with('page', 'Master Operator');
        }

    }

    public function chartMasterMachine(Request $request)
    {

        $query = "SELECT a.*, CONVERT(SPLIT_STRING(mesin,'N',2),SIGNED INTEGER) as a FROM (
  SELECT mesin, COUNT(part) as working from working_mesin_injections GROUP BY mesin
  )a ORDER BY a
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    // ---------------- end

    // ---------------------- master cycle machine

    public function masterCycleMachine()
    {
        $mesin = $this->mesin;
        $color = $this->color;
        $part = $this->part;
        $model = $this->model;

        return view('injection.masterCycleMachine', array(
            'mesin' => $mesin,
            'color' => $color,
            'part' => $part,
            'model' => $model,
        ))->with('page', 'Cycle Machine Injection')->with('jpn', '???');

    }

    public function fillMasterCycleMachine(Request $request)
    {
        $op = "select id,part,model,cycle,shoot,qty,qty_hako,qty_mesin,color from cycle_time_mesin_injections";
        $ops = DB::select($op);
        return DataTables::of($ops)

            ->addColumn('edit', function ($ops) {
                return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editop(id)" id="' . $ops->id . '"><i class="fa fa-edit"></i></a>';
            })
            ->rawColumns(['edit' => 'edit'])

            ->make(true);
    }

    public function chartMasterCycleMachine(Request $request)
    {

        $query = "SELECT mesin.*, cycle,shoot,qty_hako from (
  SELECT part,color,COUNT(part) as total from working_mesin_injections
  GROUP BY part,color
  ) as mesin
  LEFT JOIN cycle_time_mesin_injections
  ON mesin.part = cycle_time_mesin_injections.part and mesin.color = cycle_time_mesin_injections.color
  ORDER BY part
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function workingPartMesin(Request $request)
    {

        $mesin = $request->get('mesin');

        $query = "SELECT part,color,model from working_mesin_injections WHERE mesin='" . $mesin . "'
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function workingPartMesina(Request $request)
    {

        $mesin = $request->get('mesin');

        $query = "SELECT part,color,model from working_mesin_injections WHERE mesin='" . $mesin . "'
  ";

        $part = DB::select($query);
        $response = array(
            'status' => true,
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    // ------------------- sock 3 hari

    public function indexPlanAll()
    {

        return view('injection.shedule_3_hari')->with('page', 'Injection')->with('jpn', '???');

    }

    public function getPlanAll(Request $request)
    {

        $mesin = $request->get('mesin');

        $queryfjivory = "SELECT COALESCE(part,'-')part,COALESCE(color,'-')color, COALESCE(quantity,0)quantity, COALESCE(quantity2,0)quantity2, COALESCE(total2,0)total2, COALESCE(total_22,0)total_all,  week_date from (
  SELECT a.*, SUM(total)as total2, SUM(total_2)as total_22, SUM(quantity)as quantity2 from (
  SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color, (quantity * 3) as total, (quantity * 2) as total_2  from (
  SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  ) as target_model
  CROSS join  detail_part_injections on target_model.model = detail_part_injections.model
  WHERE due_date in ( SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d')<='2019-11-30' and DATE_FORMAT(week_date,'%Y')='2019')
  and part_code like 'FJ%'
  and color ='ivory'
  ORDER BY due_date
  ) a GROUP BY  due_date, color
  ) target
  RIGHT JOIN (
  SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='2019-11-01' and
  DATE_FORMAT(week_date,'%Y-%m-%d')<='2019-11-30'
  )weekd
  on target.due_date = weekd.week_date
  union all
  SELECT part,color,qty,qty2,total2,total_all,week_date as due_date from (
  SELECT 0 as part,0 as color,0 as qty,0 as qty2, 0 as total2,0 as total_all ,week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender)
  and week_date <'2019-11-01' ORDER BY week_date desc limit 2

  ) a
  ORDER BY week_date
  ";

        $queryfjSkelton = "SELECT COALESCE(part,'-')part,COALESCE(color,'-')color, COALESCE(quantity,0)quantity, COALESCE(quantity2,0)quantity2, COALESCE(total2,0)total2, COALESCE(total_22,0)total_all, due_date from (
  SELECT a.*, SUM(total)as total2, SUM(quantity)as quantity2,SUM(total_2)as total_22 from (
  SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color, (quantity * 3) as total , (quantity * 2) as total_2 from (
  SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
  SELECT material_number,due_date,quantity from production_schedules WHERE
  material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and
  DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
  ) target
  LEFT JOIN materials on target.material_number = materials.material_number
  ) as target_model
  CROSS join  detail_part_injections on target_model.model = detail_part_injections.model
  WHERE due_date in ( SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d')<='2019-11-30' and DATE_FORMAT(week_date,'%Y')='2019')
  and part_code like 'FJ%'
  and color ='ivory'
  ORDER BY due_date
  ) a GROUP BY  due_date, color
  ) target
  union all
  SELECT part,color,qty,qty2,total2,total3,week_date as due_date from (
  SELECT 0 as part,0 as color,0 as qty,0 as qty2, 0 as total2 , 0 as total3,week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender)
  and week_date <'2019-11-01' ORDER BY week_date desc limit 2

  ) a
  ORDER BY due_date
  ";

        $query2 = "SELECT * from (
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender)
  and week_date <='2019-11-01' ORDER BY week_date desc limit 3
  ) a
  union all
  SELECT week_date from ympimis.weekly_calendars WHERE
  week_date not in ( SELECT tanggal from  ftm.kalender)
  and week_date >='2019-11-01' and week_date <='2019-11-31' GROUP BY week_date
  ORDER BY week_date
  ";

        $queryfjivory = DB::select($queryfjSkelton);
        $FJSkelton = DB::select($queryfjSkelton);

        $tgl = DB::select($query2);
        $response = array(
            'status' => true,
            'partFJI' => $queryfjivory,

            'FJSkelton' => $FJSkelton,
            'tgl' => $tgl,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function molding()
    {
        $title = 'Molding Setup';
        $title_jp = '金型設定';
        $molding = InjectionMoldingLog::where('status_maintenance', 'Running')->get();

        $mesin_moldings = $this->mesin_molding;

        $mesins = [];
        $mesins_lepas = [];

        for ($i = 0; $i < count($mesin_moldings); $i++) {
            $mesin = InjectionMoldingMaster::where('status', 'PASANG')->where('status_mesin', $mesin_moldings[$i])->first();
            if ($mesin != null) {
                array_push($mesins, $mesin_moldings[$i]);
            } else {
                array_push($mesins_lepas, $mesin_moldings[$i]);
            }
        }

        return view('injection.molding', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'molding' => $molding,
            'mesin' => $mesins,
            'mesin_lepas' => $mesins_lepas,
            'part' => $this->part_molding,
            'product' => $this->product,
            'name' => Auth::user()->name,
        ))->with('page', 'Molding Setup');
    }

    public function get_molding(Request $request)
    {
        // $tgl = $request->get('tgl');
        $mesin = $request->get('mesin');

        $molding = DB::SELECT("SELECT
    injection_molding_masters.part,
    injection_molding_masters.product,
    status_mesin AS mesin,
    ( SELECT pic FROM injection_history_molding_logs WHERE injection_history_molding_logs.mesin = status_mesin ORDER BY created_at DESC LIMIT 1 ) AS pic,
    COALESCE ( injection_molding_masters.last_counter / injection_molding_masters.qty_shot, 0 ) AS shot
    FROM
    injection_molding_masters
    WHERE
                -- remark = 'RC'
                -- AND
                injection_molding_masters.STATUS = 'PASANG'
                AND status_mesin = '" . $mesin . "'");

        $response = array(
            'status' => true,
            'datas' => $molding,
            'message' => 'Success get Molding Log',
        );
        return Response::json($response);
    }

    public function get_molding_pasang(Request $request)
    {
        // $tgl = $request->get('tgl');
        $mesin = substr($request->get('mesin'), 6);

        $molding = InjectionMoldingMaster::where('status', 'LEPAS')->
            where('mesin', 'like', '%' . $mesin . '%')
        // ->where('remark','=','RC')
            ->get();

        $molding2 = InjectionMoldingMaster::where('status', 'PASANG')->where('status_mesin', '=', $request->get('mesin'))->get();

        $pesan = '';
        if (count($molding2) > 0) {
            $pesan = $request->get('mesin') . ' Sudah Terpasang Molding!';
        }

        $response = array(
            'status' => true,
            'datas' => $molding,
            'pesan' => $pesan,
            'message' => 'Success get Molding Log Pasang',
        );
        return Response::json($response);
    }

    public function fetch_molding(Request $request)
    {
        // $tgl = $request->get('tgl');
        $id = $request->get('id');

        $molding = InjectionMoldingLog::find($id);

        $response = array(
            'status' => true,
            'datas' => $molding,
            // 'message' => 'Success get Molding Log'
        );
        return Response::json($response);
    }

    public function fetch_molding_pasang(Request $request)
    {
        // $tgl = $request->get('tgl');
        $id = $request->get('id');

        $molding = InjectionMoldingMaster::find($id);

        $response = array(
            'status' => true,
            'datas' => $molding,
            // 'message' => 'Success get Molding Log'
        );
        return Response::json($response);
    }

    public function store_history_temp(Request $request)
    {
        try {
            $id_user = Auth::id();

            InjectionHistoryMoldingTemp::create([
                'molding_code' => $request->get('molding_code'),
                'type' => $request->get('type'),
                'pic' => $request->get('pic'),
                'mesin' => $request->get('mesin'),
                'part' => $request->get('part'),
                'part_name' => $request->get('part_name'),
                'part_type' => $request->get('part_type'),
                'color' => $request->get('color'),
                'total_shot' => $request->get('total_shot'),
                'start_time' => $request->get('start_time'),
                'created_by' => $id_user,
            ]);

            $molding = InjectionMoldingLog::where('mesin', $request->get('mesin'))->where('part', $request->get('part'))->where('color', $request->get('color'))->where('status_maintenance', 'Running')->get();

            if (count($molding) == 0) {

            } else {
                foreach ($molding as $molding) {
                    $id_molding = $molding->id;
                    $molding2 = InjectionMoldingLog::find($id_molding);
                    $molding2->status_maintenance = 'Maintenance';
                    $molding2->save();
                }
            }

            $response = array(
                'status' => true,
                'start_time' => $request->get('start_time'),
                'molding_code' => $request->get('molding_code'),
            );
            // return redirect('index/interview/details/'.$interview_id)
            // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function get_history_temp(Request $request)
    {
        // $tgl = $request->get('tgl');
        $mesin = $request->get('mesin');

        $molding = InjectionHistoryMoldingTemp::where('mesin', $mesin)->get();

        $response = array(
            'status' => true,
            'datas' => $molding,
            'message' => 'Success get History Temp',
        );
        return Response::json($response);
    }

    public function update_history_temp(Request $request)
    {
        // $tgl = $request->get('tgl');
        $mesin = $request->get('mesin');
        $type = $request->get('type');

        $history_temp = InjectionHistoryMoldingTemp::where('mesin', $mesin)->where('type', $type)->get();
        foreach ($history_temp as $key) {
            $id_history_temp = $key->id;
        }
        $history_temp2 = InjectionHistoryMoldingTemp::find($id_history_temp);
        $history_temp2->note = $request->get('note');
        $history_temp2->save();

        $response = array(
            'status' => true,
            'message' => 'Success Update Temp',
        );
        return Response::json($response);
    }

    public function cancel_history_molding(Request $request)
    {
        try {
            InjectionHistoryMoldingTemp::where('mesin', $request->get('mesin'))->where('type', $request->get('type'))->delete();

            if ($request->get('type') == 'LEPAS') {
                $molding_master = InjectionMoldingLog::where('part', $request->get('part'))->where('status_maintenance', "Maintenance")->get();
                foreach ($molding_master as $molding_master) {
                    $id_molding_master = $molding_master->id;
                    $molding3 = InjectionMoldingLog::find($id_molding_master);
                    $molding3->status_maintenance = "Running";
                    $molding3->save();
                }
            }

            $response = array(
                'status' => true,
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

    public function store_history_molding(Request $request)
    {
        try {
            $id_user = Auth::id();
            $start_time = $request->get('start_time');
            $end_time = $request->get('end_time');
            $running_time = $request->get('running_time');

            $temps = InjectionHistoryMoldingTemp::where('molding_code', $request->get('molding_code'))->first();

            if (date('D') == 'Fri') {
                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_jumat_1['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_jumat_1['ist1_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist2_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-40 minutes', strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist3_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($end_time)));
                }
            } else {
                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist1_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist2_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-40 minutes', strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist3_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($end_time)));
                }
            }
            if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist1_end']) {
                $end_time = date('Y-m-d H:i:s', strtotime('-15 minutes', strtotime($end_time)));
            }

            if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist2_end']) {
                $end_time = date('Y-m-d H:i:s', strtotime('-25 minutes', strtotime($end_time)));
            }

            if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist3_end']) {
                $end_time = date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($end_time)));
            }

            $timenow = strtotime($end_time) - strtotime($start_time);

            $years = floor($timenow / (365 * 60 * 60 * 24));

            $months = floor(($timenow - $years * 365 * 60 * 60 * 24)
                / (30 * 60 * 60 * 24));

            $days = floor(($timenow - $years * 365 * 60 * 60 * 24 -
                $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

            $hours = floor(($timenow - $years * 365 * 60 * 60 * 24
                 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24)
                / (60 * 60));

            $minutes = floor(($timenow - $years * 365 * 60 * 60 * 24
                 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24
                 - $hours * 60 * 60) / 60);

            $seconds = floor(($timenow - $years * 365 * 60 * 60 * 24
                 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24
                 - $hours * 60 * 60 - $minutes * 60));

            $length = 2;

            $running_time = str_pad($hours, $length, "0", STR_PAD_LEFT) . ':' . str_pad($minutes, $length, "0", STR_PAD_LEFT) . ':' . str_pad($seconds, $length, "0", STR_PAD_LEFT);

            if ($request->get('pic_2') != '-') {
                $pic = $request->get('pic_1') . ', ' . $request->get('pic_2');
            }

            if ($request->get('pic_3') != '-') {
                $pic = $request->get('pic_1') . ', ' . $request->get('pic_2') . ', ' . $request->get('pic_3');
            }

            if ($request->get('pic_2') == '-' && $request->get('pic_3') == '-') {
                $pic = $request->get('pic_1');
            }

            InjectionHistoryMoldingLog::create([
                'molding_code' => $request->get('molding_code'),
                'type' => $request->get('type'),
                'pic' => $pic,
                'mesin' => $request->get('mesin'),
                'part' => $request->get('part'),
                'color' => $request->get('color'),
                'total_shot' => $request->get('total_shot'),
                'start_time' => $request->get('start_time'),
                'end_time' => $request->get('end_time'),
                'part_name' => $request->get('part_name'),
                'part_type' => $request->get('part_type'),
                'running_time' => $running_time,
                'note' => $request->get('notelepas'),
                'decision' => $request->get('decision'),
                'status_cek_visual' => $temps->status_cek_visual,
                'status_approval_qa' => $temps->status_approval_qa,
                'status_purging' => $temps->status_purging,
                'status_setting_robot' => $temps->status_setting_robot,
                'status_parameter' => $temps->status_parameter,
                'created_by' => $id_user,
            ]);

            InjectionHistoryMoldingTemp::where('mesin', $request->get('mesin'))->delete();

            if ($request->get('type') == 'LEPAS') {
                $molding = InjectionMoldingLog::where('mesin', $request->get('mesin'))->where('part', $request->get('part'))->where('color', $request->get('color'))->where('status', 'Running')->get();

                if (count($molding) == 0) {

                } else {
                    foreach ($molding as $molding) {
                        $id_molding = $molding->id;
                        $molding2 = InjectionMoldingLog::find($id_molding);
                        $molding2->status_maintenance = 'Close';
                        $molding2->status = 'Close';
                        $molding2->save();
                    }
                }
                $molding_master = InjectionMoldingMaster::where('part', $request->get('part'))->get();
                foreach ($molding_master as $molding_master) {
                    $id_molding_master = $molding_master->id;
                    $molding3 = InjectionMoldingMaster::find($id_molding_master);
                    if ($request->get('decision') == 'MAINTENANCE') {
                        $molding3->status = 'HARUS MAINTENANCE';
                    } else {
                        $molding3->status = $request->get('reason');
                    }
                    $molding3->status_mesin = null;
                    $molding3->save();
                }
            }

            if ($request->get('type') == 'PASANG') {
                $molding_master = InjectionMoldingMaster::where('part', $request->get('part'))->get();
                foreach ($molding_master as $molding_master) {
                    $id_molding_master = $molding_master->id;
                    $molding3 = InjectionMoldingMaster::find($id_molding_master);
                    $molding3->status = 'PASANG';
                    $molding3->status_mesin = $request->get('mesin');
                    $molding3->save();
                }
            }

            $response = array(
                'status' => true,
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

    public function molding_maintenance()
    {
        $title = 'Molding Maintenance';
        $title_jp = '金型保全';
        // $molding = MoldingInjectionLog::where('status_maintenance','Running')->get();
        return view('injection.molding_maintenance', array(
            'title' => $title,
            'title_jp' => $title_jp,
            // 'molding' => $molding,
            'username' => Auth::user()->username,
            'name' => Auth::user()->name,
        ))->with('page', 'Molding Maintenance');
    }

    public function get_molding_master(Request $request)
    {
        // $tgl = $request->get('tgl');
        // $tag = $request->get('tag');

        $molding = DB::SELECT("SELECT
    injection_molding_masters.id AS id_molding,
    injection_molding_masters.product,
    injection_molding_masters.part,
    injection_molding_masters.last_counter,
    injection_molding_masters.qty_shot,
    COALESCE ( status_mesin, '-' ) AS mesin,
    COALESCE ( injection_molding_logs.color, '-' ) AS color,
    injection_molding_masters.status,
    COALESCE ( ROUND(injection_molding_logs.total_running_shot / injection_machine_cycle_times.shoot,0), 0 ) AS shot
    FROM
    injection_molding_masters
    LEFT JOIN injection_molding_logs ON injection_molding_logs.tag_molding = injection_molding_masters.tag and injection_molding_logs.status = 'Running'
    LEFT JOIN injection_machine_cycle_times ON injection_molding_masters.product = injection_machine_cycle_times.part
    AND injection_molding_logs.color = injection_machine_cycle_times.color
    WHERE
            -- remark = 'RC'
            injection_molding_masters.status != 'PASANG' ");

        $response = array(
            'status' => true,
            'datas' => $molding,
            'message' => 'Success get Molding Log',
        );
        return Response::json($response);
    }

    public function fetch_molding_master(Request $request)
    {
        // $tgl = $request->get('tgl');
        $id = $request->get('id');

        $molding = InjectionMoldingMaster::find($id);

        $response = array(
            'status' => true,
            'datas' => $molding,
            // 'message' => 'Success get Molding Log'
        );
        return Response::json($response);
    }

    public function store_maintenance_temp(Request $request)
    {
        try {
            $id_user = Auth::id();

            InjectionMaintenanceMoldingTemp::create([
                'maintenance_code' => $request->get('maintenance_code'),
                'pic' => $request->get('pic'),
                'mesin' => $request->get('mesin'),
                'part' => $request->get('part'),
                'product' => $request->get('product'),
                'status' => $request->get('status'),
                'last_counter' => $request->get('last_counter'),
                'start_time' => $request->get('start_time'),
                'created_by' => $id_user,
            ]);

            $molding = InjectionMoldingMaster::where('part', $request->get('part'))->where('product', $request->get('product'))->get();

            foreach ($molding as $key) {
                $id_molding = $key->id;
                $molding2 = InjectionMoldingMaster::find($id_molding);
                $molding2->status = 'DIPERBAIKI';
                $molding2->save();
            }

            $response = array(
                'status' => true,
                'start_time' => $request->get('start_time'),
            );
            // return redirect('index/interview/details/'.$interview_id)
            // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function get_maintenance_temp(Request $request)
    {
        // $tgl = $request->get('tgl');
        $part = $request->get('part');

        $molding = InjectionMaintenanceMoldingTemp::where('part', $part)->get();

        $response = array(
            'status' => true,
            'datas' => $molding,
            'message' => 'Success get Maintenance Temp',
        );
        return Response::json($response);
    }

    public function update_maintenance_temp(Request $request)
    {
        // $tgl = $request->get('tgl');
        $maintenance_code = $request->get('maintenance_code');

        $maintenance_temp = InjectionMaintenanceMoldingTemp::where('maintenance_code', $maintenance_code)->get();
        foreach ($maintenance_temp as $key) {
            $id_maintenance_temp = $key->id;
        }
        $maintenance_temp2 = InjectionMaintenanceMoldingTemp::find($id_maintenance_temp);
        $maintenance_temp2->note = $request->get('note');
        $maintenance_temp2->save();

        $response = array(
            'status' => true,
            'message' => 'Success Update Temp',
        );
        return Response::json($response);
    }

    public function store_maintenance_molding(Request $request)
    {
        try {
            $id_user = Auth::id();
            $start_time = $request->get('start_time');
            $end_time = $request->get('end_time');
            $running_time = $request->get('running_time');

            if (date('D') == 'Fri') {
                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_jumat_1['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_jumat_1['ist1_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist2_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-40 minutes', strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist3_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($end_time)));
                }
            } else {
                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist1_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist2_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-40 minutes', strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist3_end']) {
                    $end_time = date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($end_time)));
                }
            }
            if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist1_end']) {
                $end_time = date('Y-m-d H:i:s', strtotime('-15 minutes', strtotime($end_time)));
            }

            if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist2_end']) {
                $end_time = date('Y-m-d H:i:s', strtotime('-25 minutes', strtotime($end_time)));
            }

            if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist3_end']) {
                $end_time = date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($end_time)));
            }

            $timenow = strtotime($end_time) - strtotime($start_time);

            $years = floor($timenow / (365 * 60 * 60 * 24));

            $months = floor(($timenow - $years * 365 * 60 * 60 * 24)
                / (30 * 60 * 60 * 24));

            $days = floor(($timenow - $years * 365 * 60 * 60 * 24 -
                $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

            $hours = floor(($timenow - $years * 365 * 60 * 60 * 24
                 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24)
                / (60 * 60));

            $minutes = floor(($timenow - $years * 365 * 60 * 60 * 24
                 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24
                 - $hours * 60 * 60) / 60);

            $seconds = floor(($timenow - $years * 365 * 60 * 60 * 24
                 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24
                 - $hours * 60 * 60 - $minutes * 60));

            $length = 2;

            $running_time = str_pad($hours, $length, "0", STR_PAD_LEFT) . ':' . str_pad($minutes, $length, "0", STR_PAD_LEFT) . ':' . str_pad($seconds, $length, "0", STR_PAD_LEFT);

            InjectionMaintenanceMoldingLog::create([
                'maintenance_code' => $request->get('maintenance_code'),
                'pic' => $request->get('pic'),
                'mesin' => $request->get('mesin'),
                'part' => $request->get('part'),
                'product' => $request->get('product'),
                'status' => $request->get('status'),
                'last_counter' => $request->get('last_counter'),
                'start_time' => $request->get('start_time'),
                'end_time' => $request->get('end_time'),
                'running_time' => $running_time,
                'note' => $request->get('note'),
                'created_by' => $id_user,
            ]);

            $molding = InjectionMoldingMaster::where('part', $request->get('part'))->where('product', $request->get('product'))->get();

            $molding3 = InjectionMaintenanceMoldingTemp::where('part', $request->get('part'))->where('product', $request->get('product'))->delete();

            foreach ($molding as $key) {
                $id_molding = $key->id;
                $molding2 = InjectionMoldingMaster::find($id_molding);
                if ($molding2->status_mesin != null) {
                    $molding2->status = 'PASANG';
                } else {
                    $molding2->status = 'LEPAS';
                    $molding2->last_counter = 0;
                    $molding2->ng_count = 0;
                }
                $molding2->save();
            }

            $response = array(
                'status' => true,
                'start_time' => $request->get('start_time'),
            );
            // return redirect('index/interview/details/'.$interview_id)
            // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    // ---------------- end

    public function transaction($status)
    {
        $title = 'Injection Transaction';
        if (strtoupper($status) == 'IN') {
            $title_jp = '成形品の受け渡し（IN）';
        } else {
            $title_jp = '成形品の受け渡し（OUT）';
        }

        return view('injection.transaction_injection', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'status' => strtoupper($status),
            'name' => Auth::user()->name,
        ))->with('page', 'Injection Transaction');
    }

    public function scanProduct(Request $request)
    {
        try {
            if ($request->get('status') == "IN") {
                $tag = DB::SELECT("SELECT * FROM `injection_tags` where tag = '" . $request->get('tag') . "' and location = 'RC11'");
            } else {
                $tag = DB::SELECT("SELECT * FROM `injection_tags` left join injection_process_logs on tag = tag_product and injection_tags.material_number = injection_process_logs.material_number and injection_tags.cavity = injection_process_logs.cavity where tag = '" . $request->get('tag') . "' and location = 'RC91' and injection_process_logs.remark is null");
            }
            if (count($tag) > 0) {
                $response = array(
                    'status' => true,
                    'data' => $tag,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
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

    public function fetchTransaction(Request $request)
    {
        try {
            if ($request->get('status') == 'IN') {
                $transaction = DB::SELECT("SELECT
        injection_transactions.id,
        injection_transactions.tag,
        injection_transactions.material_number,
        SUBSTRING_INDEX( injection_parts.part_name, ' ', 1 ) AS part_name,
        injection_parts.part_name as material_description,
        injection_parts.part_code,
        injection_parts.part_type,
        injection_parts.color,
        injection_transactions.location,
        injection_transactions.quantity,
        injection_transactions.created_at,
        injection_transactions.status,
        injection_process_logs.mesin,
        injection_process_logs.cavity,
        employee_syncs.employee_id,
        employee_syncs.`name`,
        injection_process_logs.updated_at,
        injection_process_logs.ng_name,
        injection_process_logs.ng_count
        FROM
        injection_transactions
        LEFT JOIN injection_parts ON injection_transactions.material_number = injection_parts.gmc
        LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_transactions.tag
        AND injection_process_logs.updated_at = injection_transactions.created_at
        LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_transactions.operator_id
        WHERE
        injection_transactions.status = '" . $request->get('status') . "'
        AND injection_transactions.location = 'RC11'
        AND DATE( injection_transactions.created_at ) BETWEEN DATE(
        NOW()) - INTERVAL 14 DAY
        AND DATE(
        NOW())
        AND injection_parts.deleted_at IS NULL
        ORDER BY
        injection_transactions.created_at DESC");
            } else {
                $transaction = DB::SELECT("SELECT
        injection_transactions.id,
        injection_transactions.tag,
        injection_transactions.material_number,
        SUBSTRING_INDEX( injection_parts.part_name, ' ', 1 ) AS part_name,
        injection_parts.part_name as material_description,
        injection_parts.part_code,
        injection_parts.part_type,
        injection_parts.color,
        injection_transactions.location,
        injection_transactions.quantity,
        injection_transactions.created_at,
        injection_transactions.status,
        injection_process_logs.mesin,
        injection_process_logs.cavity,
        employee_syncs.employee_id,
        employee_syncs.`name`,
        injection_process_logs.updated_at,
        injection_process_logs.ng_name,
        injection_process_logs.ng_count
        FROM
        injection_transactions
        LEFT JOIN injection_parts ON injection_transactions.material_number = injection_parts.gmc
        LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_transactions.tag
        AND injection_process_logs.updated_at = injection_transactions.created_at
        LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_transactions.operator_id
        WHERE
        injection_transactions.status = 'IN'
        AND injection_transactions.location = 'RC91'
        AND DATE( injection_transactions.created_at ) BETWEEN DATE(
        NOW()) - INTERVAL 14 DAY
        AND DATE(
        NOW())
        AND injection_parts.deleted_at IS NULL
        ORDER BY
        injection_transactions.created_at DESC");
            }

            $response = array(
                'status' => true,
                'data' => $transaction,
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

    public function fetchDetailTransaction(Request $request)
    {
        try {
            $transaction = DB::SELECT("SELECT
      injection_transactions.id,
      injection_transactions.tag,
      injection_transactions.material_number,
      injection_parts.part_name as material_description,
      SUBSTRING_INDEX( injection_parts.part_name, ' ', 1 ) AS part_name,
      injection_parts.part_code,
      injection_parts.part_type,
      injection_parts.color,
      injection_transactions.location,
      injection_transactions.quantity,
      injection_transactions.created_at,
      injection_transactions.status,
      injection_process_logs.mesin,
      injection_process_logs.cavity,
      employee_syncs.employee_id,
      employee_syncs.`name`,
      injection_process_logs.updated_at,
      injection_process_logs.ng_name,
      injection_process_logs.ng_count,
      empambil.employee_id as empidambil,
      empambil.name as nameambil
      FROM
      injection_transactions
      LEFT JOIN injection_parts ON injection_transactions.material_number = injection_parts.gmc
      LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_transactions.tag
      AND injection_process_logs.updated_at = injection_transactions.created_at
      LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_process_logs.operator_id
      LEFT JOIN employee_syncs empambil ON empambil.employee_id = injection_transactions.operator_id
      WHERE
      injection_transactions.status = 'IN'
      AND injection_transactions.location = 'RC91'
      AND DATE( injection_transactions.created_at ) BETWEEN DATE(
      NOW()) - INTERVAL 3 DAY
      AND DATE(
      NOW())
      AND injection_parts.deleted_at IS NULL
      AND injection_transactions.id = '" . $request->get('id') . "'
      ORDER BY
      injection_transactions.created_at DESC");

            $response = array(
                'status' => true,
                'data' => $transaction,
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

    public function fetchCheckInjections(Request $request)
    {
        try {
            $operator_id = "";
            if ($request->get('status') == 'IN') {
                if ($request->get('tag_product') == '') {
                    $remark = 'antenna_1';
                    $transaction = DB::SELECT("SELECT
                        -- ympirfid.injection_lists.tag
                        *,ympimis.injection_tags.id as injection_id,ympimis.injection_tags.tag as tag_rfid
                        FROM
                        ympirfid.injection_lists
                        JOIN ympimis.injection_tags ON ympimis.injection_tags.concat_kanban = ympirfid.injection_lists.tag
                        JOIN employee_syncs ON ympimis.employee_syncs.employee_id = ympimis.injection_tags.operator_id
                        WHERE
                        ympirfid.injection_lists.remark = '" . $remark . "'
                        AND ympimis.injection_tags.availability = 1");
                    $operator_id = "";
                } else {
                    $transaction = DB::SELECT("SELECT
                        *,ympimis.injection_tags.id as injection_id,ympimis.injection_tags.tag as tag_rfid
          FROM
          ympimis.injection_tags
          JOIN employee_syncs ON ympimis.employee_syncs.employee_id = ympimis.injection_tags.operator_id
          WHERE
          ympimis.injection_tags.tag = '" . $request->get('tag_product') . "'
          AND ympimis.injection_tags.availability = 1");
                }
            } else {
                if ($request->get('tag_product') == '') {
                    $remark = 'antenna_2';
                    $transaction = DB::SELECT("SELECT
                        -- ympirfid.injection_lists.tag
                        *,ympimis.injection_tags.id as injection_id,ympimis.injection_tags.tag as tag_rfid,ympimis.injection_process_logs.id as process_id
                        FROM
                        ympirfid.injection_lists
                        JOIN ympimis.injection_tags ON ympimis.injection_tags.concat_kanban = ympirfid.injection_lists.tag
                        LEFT JOIN  ympimis.injection_process_logs ON ympimis.injection_tags.tag = tag_product and ympimis.injection_tags.material_number = ympimis.injection_process_logs.material_number and ympimis.injection_tags.cavity = ympimis.injection_process_logs.cavity AND ympimis.injection_process_logs.remark is null
                        JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = ympimis.injection_tags.operator_id
                        WHERE
                        ympirfid.injection_lists.remark = '" . $remark . "'
                        AND ympimis.injection_tags.availability = 2");
                    $operator_id = DB::SELECT("SELECT * from ympirfid.injection_lists left join ympimis.employee_syncs on ympimis.employee_syncs.employee_id = ympirfid.injection_lists.tag where tag like '%PI%'");
                } else {
                    $transaction = DB::SELECT("SELECT
                        *,ympimis.injection_tags.id as injection_id,ympimis.injection_tags.tag as tag_rfid
          FROM
          ympimis.injection_tags
          JOIN employee_syncs ON ympimis.employee_syncs.employee_id = ympimis.injection_tags.operator_id
          WHERE
          ympimis.injection_tags.tag = '" . $request->get('tag_product') . "'
          AND ympimis.injection_tags.availability = 2");
                    $operator_id = "";
                }
            }

            if ($operator_id == "") {
                $response = array(
                    'status' => true,
                    'data' => $transaction,
                );
            } else {
                $response = array(
                    'status' => true,
                    'data' => $transaction,
                    'operator' => $operator_id,
                );
            }
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchCheckNg(Request $request)
    {
        try {
            $ng = DB::SELECT("select * from injection_process_logs where id = '" . $request->get('id') . "'");

            $response = array(
                'status' => true,
                'data' => $ng,
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

    public function completion(Request $request)
    {
        try {
            $id_user = Auth::id();
            if ($request->get('status') == 'IN') {
                $transaction = InjectionTag::where('tag', $request->get('tag'))->first();
                $concat_kanban = $transaction->concat_kanban;
                if (str_contains($concat_kanban,'RC')) {
                    $transaction->location = 'RC11';
                }else{
                    $transaction->location = 'VN11';
                }
                $transaction->availability = 2;
                // $transaction->height_check = 'Uncheck';
                // $transaction->push_pull_check = 'Uncheck';
                // $transaction->torque_check = 'Uncheck';
                $transaction->save();

                $mpdl = MaterialPlantDataList::where('material_number', '=', $request->get('material_number'))->first();

                //YMES COMPLETION ITEM PACKED NEW
                $category = 'production_result';
                $function = 'completion';
                $action = 'production_result';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $concat_kanban;
                $serial_number = null;
                $material_number = $request->get('material_number');
                $material_description = $mpdl->material_description;
                $issue_location = $mpdl->storage_location;
                $mstation = 'W' . $mpdl->mrpc . 'S10';
                $quantity = $request->get('qty');
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->production_result(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                //YMES COMPLETION END

                // $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC11']);
                // $inventory->quantity = ($inventory->quantity-$request->get('qty'));
                // $inventory->save();

                // $inventory2 = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC91']);
                // $inventory2->quantity = ($inventory2->quantity+$request->get('qty'));
                // $inventory2->save();

                //send Inj Inventories
                // $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC11']);
                // $injectionInventory->quantity = ($injectionInventory->quantity-$request->get('qty'));
                // $injectionInventory->save();

                // $injectionInventory2 = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC91']);
                // $injectionInventory2->quantity = ($injectionInventory2->quantity+$request->get('qty'));
                // $injectionInventory2->save();

                // InjectionTransaction::create([
                //     'tag' => $request->get('tag'),
                //     'material_number' => $request->get('material_number'),
                //     'location' => 'RC11',
                //     'quantity' => $request->get('qty'),
                //     'status' => 'OUT',
                //     'operator_id' => $request->get('operator_id'),
                //     'created_by' => $id_user
                // ]);

                // InjectionTransaction::create([
                //     'tag' => $request->get('tag'),
                //     'material_number' => $request->get('material_number'),
                //     'location' => 'RC91',
                //     'quantity' => $request->get('qty'),
                //     'status' => 'IN',
                //     'operator_id' => $request->get('operator_id'),
                //     'created_by' => $id_user
                // ]);

                $locs = 'RC11';

                if (str_contains($concat_kanban,'RC')) {
                    $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC11']);
                }else{
                    $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'VN11']);
                    $locs = 'VN11';
                }
                $inventory->quantity = ($inventory->quantity + $request->get('qty'));
                $inventory->save();

                //send Inj Inventories
                $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => $locs]);
                $injectionInventory->quantity = ($injectionInventory->quantity + $request->get('qty'));
                $injectionInventory->save();

                //Transaction
                InjectionTransaction::create([
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'location' => $locs,
                    'quantity' => $request->get('qty'),
                    'status' => 'IN',
                    'operator_id' => $request->get('operator_id'),
                    'created_by' => $id_user,
                ]);

                $deleteInjList = DB::SELECT("DELETE FROM ympirfid.injection_lists where tag = '" . $concat_kanban . "'");

            } else {
                $transaction = InjectionTag::where('tag', $request->get('tag'))->first();
                $transaction->operator_id = $request->get('operator_id');
                // $transaction->operator_id = null;
                // $transaction->part_name = null;
                // $transaction->part_type = null;
                // $transaction->color = null;
                // $transaction->cavity = null;
                // $transaction->location = null;
                $concat_kanban = $transaction->concat_kanban;
                $locs = 'RC11';
                $locs2 = 'RC91';
                if (str_contains($concat_kanban,'RC')) {
                    $transaction->location = 'RC91';
                }else{
                    $transaction->location = 'VN91';
                    $locs = 'VN11';
                    $locs2 = 'VN91';
                }
                // $transaction->shot = null;
                // $transaction->availability = null;
                $transaction->availability = 3;
                // $transaction->height_check = null;
                // $transaction->push_pull_check = null;
                // $transaction->torque_check = null;
                // $transaction->remark = null;
                $transaction->save();

                $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => $locs]);
                $inventory->quantity = ($inventory->quantity - $request->get('qty'));
                $inventory->save();

                $inventory2 = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => $locs2]);
                $inventory2->quantity = ($inventory2->quantity + $request->get('qty'));
                $inventory2->save();

                //send Inj Inventories
                $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => $locs]);
                $injectionInventory->quantity = ($injectionInventory->quantity - $request->get('qty'));
                $injectionInventory->save();

                $injectionInventory2 = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => $locs2]);
                $injectionInventory2->quantity = ($injectionInventory2->quantity + $request->get('qty'));
                $injectionInventory2->save();

                InjectionTransaction::create([
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'location' => $locs,
                    'quantity' => $request->get('qty'),
                    'status' => 'OUT',
                    'operator_id' => $request->get('operator_id'),
                    'created_by' => $id_user,
                ]);

                InjectionTransaction::create([
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'location' => $locs2,
                    'quantity' => $request->get('qty'),
                    'status' => 'IN',
                    'operator_id' => $request->get('operator_id'),
                    'created_by' => $id_user,
                ]);

                // InjectionTransaction::create([
                //     'tag' => $request->get('tag'),
                //     'material_number' => $request->get('material_number'),
                //     'location' => 'RC91',
                //     'quantity' => $request->get('qty'),
                //     'status' => 'OUT',
                //     'operator_id' => $request->get('operator_id'),
                //     'created_by' => $id_user
                // ]);

                // $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC91']);
                // $injectionInventory->quantity = ($injectionInventory->quantity-$request->get('qty'));
                // $injectionInventory->save();

                $process = InjectionProcessLog::where('tag_product', $request->get('tag'))->where('material_number', $request->get('material_number'))->where('cavity', $request->get('cavity'))->where('remark', null)->first();
                if ($process) {
                    $process->remark = 'Close';
                    $process->save();
                }

                // $bom = BomComponent::where('material_parent',$request->get('material_number'))->

                //YMES TRANSFER
                $category = 'goods_movement';
                $function = 'completion';
                $action = 'goods_movement';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $transaction->concat_kanban;
                $serial_number = null;
                $material_number = $request->get('material_number');
                $material_description = $transaction->mat_desc;
                $issue_location = $locs;
                $receive_location = $locs2;
                $quantity = $request->get('qty');
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->goods_movement(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                //YMES TRANSFER END

                // $deleteInjList = DB::SELECT("DELETE FROM ympirfid.injection_lists where tag = '".$concat_kanban."'");

                // $material = db::connection('mysql2')->table('materials')
                // ->where('material_number', '=', $request->get('material_number'))
                // ->first();

                // $transfer = db::connection('mysql2')->table('histories')->insert([
                //     "category" => "transfer",
                //     "transfer_barcode_number" => "",
                //     "transfer_document_number" => "8190",
                //     "transfer_material_id" => $material->id,
                //     "transfer_issue_location" => 'RC11',
                //     "transfer_issue_plant" => "8190",
                //     "transfer_receive_plant" => "8190",
                //     "transfer_receive_location" => 'RC91',
                //     "transfer_cost_center" => "",
                //     "transfer_gl_account" => "",
                //     "transfer_transaction_code" => "MB1B",
                //     "transfer_movement_type" => "9I3",
                //     "transfer_reason_code" => "",
                //     "lot" => $request->get('qty'),
                //     "synced" => 0,
                //     'user_id' => "1",
                //     'created_at' => date("Y-m-d H:i:s"),
                //     'updated_at' => date("Y-m-d H:i:s")
                // ]);
            }

            $response = array(
                'status' => true,
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

    public function cancelCompletion(Request $request)
    {
        try {
            $delete = DB::CONNECTION('rfid')->SELECT("DELETE FROM ympirfid.injection_lists WHERE tag = '" . $request->get('concat_kanban') . "'");
            $response = array(
                'status' => true,
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

    public function indexMachineMonitoring()
    {
        return view('injection.machine_monitoring')
            ->with('mesin', $this->mesin)
            ->with('title', 'Injection Machine Monitoring')
            ->with('title_jp', '成形機の監視');
    }

    public function fetchMachineMonitoring(Request $request)
    {
        try {
            $id_user = Auth::id();

            $data = DB::SELECT("SELECT
      mesin,
      COALESCE (( SELECT part_name FROM injection_process_temps WHERE mesin = injection_machine_works.mesin AND injection_process_temps.deleted_at IS NULL ), '' ) AS part,
      COALESCE ((
      SELECT
      CONCAT( '<br>(', part_type, ' - ', color, ')<br>', cavity )
      FROM
      injection_process_temps
      WHERE
      mesin = injection_machine_works.mesin
      AND injection_process_temps.deleted_at IS NULL
      ),
      ''
      ) AS type,
      COALESCE (( SELECT shot FROM injection_process_temps WHERE mesin = injection_machine_works.mesin AND injection_process_temps.deleted_at IS NULL ), 0 ) AS shot_mesin,
      COALESCE ((
      SELECT COALESCE
      ( ROUND( last_counter / injection_molding_masters.qty_shot ), 0 ) AS shot
      FROM
      injection_molding_masters
      WHERE
      injection_molding_masters.status_mesin = injection_machine_works.mesin
      ),
      0
      ) AS shot_molding,
      COALESCE (( SELECT part FROM injection_molding_masters WHERE injection_molding_masters.status_mesin = injection_machine_works.mesin ), '-' ) AS molding,
      COALESCE (( SELECT ng_count FROM injection_process_temps WHERE mesin = injection_machine_works.mesin AND injection_process_temps.deleted_at IS NULL ), '' ) AS ng_count,
      COALESCE ((
      SELECT
      CONCAT( operator_id, '<br>', employee_syncs.`name` )
      FROM
      injection_process_temps
      LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_process_temps.operator_id
      WHERE
      mesin = injection_machine_works.mesin
      AND injection_process_temps.deleted_at IS NULL
      ),
      ''
      ) AS operator,
      status,
      remark
      FROM
      injection_machine_works");

            $response = array(
                'status' => true,
                'data' => $data,
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

    public function indexStockMonitoring()
    {
        $color = DB::SELECT('SELECT DISTINCT(color) FROM `injection_parts`');

        return view('injection.stock_monitoring2')
            ->with('mesin', $this->mesin)
            ->with('color', $color)
            ->with('title', 'Injection Stock Monitoring')
            ->with('title_jp', '成形品在庫の監視');
    }

    public function fetchStockMonitoring(Request $request)
    {
        try {
            $id_user = Auth::id();

            if ($request->get('color') == "" || $request->get('color') == "All") {
                $color = "";
            } else {
                $color = "AND TRIM(
      RIGHT(
      c.part,
      (LENGTH(c.part) - LOCATE('(',c.part))
    )) =  '" . $request->get('color') . ")'";
            }

            $j = 2;
            $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $nextdayplus1) {
                    if ($key->remark == 'H') {
                        $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                    }
                }
            }

            $j = 3;
            $nextdayplus2 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $nextdayplus2) {
                    if ($key->remark == 'H') {
                        $nextdayplus2 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                    }
                }
            }

            $j = 1;
            $nextday = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $nextday) {
                    if ($key->remark == 'H') {
                        $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                    }
                }
            }

            $first = date('Y-m-01');
            $now = date('Y-m-d');

            $yesterday = date('Y-m-d', strtotime(' -1 days'));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars` order by week_date desc");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $yesterday) {
                    if ($key->remark == 'H') {
                        $yesterday = date('Y-m-d', strtotime($yesterday . '-1 days'));
                    }
                }
            }

            $nextmonth = date('Y-m-t', strtotime('+ 1 MONTH'));
            $data_skeleton = DB::SELECT("SELECT
    c.part,
    TRIM(
    RIGHT(
    c.part,
    (LENGTH(c.part) - LOCATE('(',c.part))
    )
    ) as color,
    SUM( c.stock ) AS stock,
    SUM( c.stock_assy ) AS stock_assy,
    SUM( c.plan ) AS plan
    FROM
    (
    SELECT
    CONCAT( UPPER( injection_parts.part_code ), ' (', injection_parts.color, ')' ) AS part,
    COALESCE (( SELECT quantity FROM injection_inventories WHERE location = 'RC11' AND material_number = gmc and injection_inventories.deleted_at is null), 0 ) AS stock,
    COALESCE (( SELECT quantity FROM injection_inventories WHERE location = 'RC91' AND material_number = gmc and injection_inventories.deleted_at is null ), 0 ) AS stock_assy,
    0 AS plan
    FROM
    injection_parts where remark = 'injection'
    GROUP BY injection_parts.part_code,injection_parts.color,gmc,color  UNION ALL
    SELECT
    part,
    0 AS stock,
    0 AS stock_assy,
    sum( a.plan )- sum( a.stamp ) AS plan
    FROM
    (
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    SUM( quantity ) AS plan,
    0 AS stamp
    FROM
    production_schedules
    LEFT JOIN materials ON materials.material_number = production_schedules.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND production_schedules.due_date BETWEEN '" . $first . "'
    AND '" . $nextdayplus1 . "'
    GROUP BY
    part_code,color UNION ALL
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    0 AS plan,
    SUM( quantity ) AS stamp
    FROM
    flo_details
    LEFT JOIN materials ON materials.material_number = flo_details.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND DATE( flo_details.created_at ) BETWEEN '" . $first . "'
    AND '" . $now . "'
    GROUP BY
    part_code,color
    ) a
    GROUP BY
    a.part
    ) c
    where c.part not like '%YRF%' AND
    c.part not like '%IVORY%' AND
    c.part not like '%BEIGE%'
    " . $color . "
    GROUP BY
    c.part ORDER BY color");

            $data_ivory = DB::SELECT("SELECT
    c.part,
    TRIM(
    RIGHT(
    c.part,
    (LENGTH(c.part) - LOCATE('(',c.part))
    )
    ) as color,
    SUM( c.stock ) AS stock,
    SUM( c.stock_assy ) AS stock_assy,
    SUM( c.plan ) AS plan
    FROM
    (
    SELECT
    CONCAT( UPPER( injection_parts.part_code ), ' (', injection_parts.color, ')' ) AS part,
    COALESCE (( SELECT quantity FROM injection_inventories WHERE location = 'RC11' AND material_number = gmc and injection_inventories.deleted_at is null), 0 ) AS stock,
    COALESCE (( SELECT quantity FROM injection_inventories WHERE location = 'RC91' AND material_number = gmc and injection_inventories.deleted_at is null), 0 ) AS stock_assy,
    0 AS plan
    FROM
    injection_parts where remark = 'injection'
    GROUP BY injection_parts.part_code,injection_parts.color,gmc,color  UNION ALL
    SELECT
    part,
    0 AS stock,
    0 AS stock_assy,
    sum( a.plan )- sum( a.stamp ) AS plan
    FROM
    (
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    SUM( quantity ) AS plan,
    0 AS stamp
    FROM
    production_schedules
    LEFT JOIN materials ON materials.material_number = production_schedules.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND production_schedules.due_date BETWEEN '" . $first . "'
    AND '" . $nextdayplus1 . "'
    GROUP BY
    part_code,color UNION ALL
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    0 AS plan,
    SUM( quantity ) AS stamp
    FROM
    flo_details
    LEFT JOIN materials ON materials.material_number = flo_details.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND DATE( flo_details.created_at ) BETWEEN '" . $first . "'
    AND '" . $now . "'
    GROUP BY
    part_code,color
    ) a
    GROUP BY
    a.part
    ) c
    where c.part not like '%PINK%' AND
    c.part not like '%RED%' AND
    c.part not like '%BLUE%' AND
    c.part not like '%BROWN%' AND
    c.part not like '%GREEN%'
    " . $color . "
    GROUP BY
    c.part ORDER BY part");

            $datas_skeleton = [];
            $datas_ivory = [];

            foreach ($data_ivory as $key) {
                $datas_ivory[] = array(
                    'part' => $key->part,
                    'color' => $key->color,
                    'stock' => $key->stock,
                    'stock_assy' => $key->stock_assy,
                    'plan' => $key->plan);
            }

            foreach ($data_skeleton as $key) {
                $datas_skeleton[] = array(
                    'part' => $key->part,
                    'color' => $key->color,
                    'stock' => $key->stock,
                    'stock_assy' => $key->stock_assy,
                    'plan' => $key->plan);
            }

            $plan_day = DB::SELECT("select * from
    (SELECT
    'RC91' AS location,
    a.part,
    sum( a.plan )- sum( a.stamp ) AS qty,
    'No' AS late_stock
    FROM
    (
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    SUM( quantity ) AS plan,
    0 AS stamp
    FROM
    production_schedules
    LEFT JOIN materials ON materials.material_number = production_schedules.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND production_schedules.due_date BETWEEN '" . $first . "'
    AND '" . $now . "'
    GROUP BY
    part,
    part_code,color UNION ALL
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    0 AS plan,
    SUM( quantity ) AS stamp
    FROM
    flo_details
    LEFT JOIN materials ON materials.material_number = flo_details.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND DATE( flo_details.created_at ) BETWEEN '" . $first . "'
    AND '" . $now . "'
    GROUP BY
    part,
    part_code,
    color
    ) a
    GROUP BY
    a.part UNION ALL
    SELECT
    'RC11' AS location,
    b.part,
    sum( b.plan )- sum( b.stamp ) AS qty,
    'No' AS late_stock
    FROM
    (
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    SUM( quantity ) AS plan,
    0 AS stamp
    FROM
    production_schedules
    LEFT JOIN materials ON materials.material_number = production_schedules.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND production_schedules.due_date BETWEEN '" . $nextday . "'
    AND '" . $nextdayplus1 . "'
    GROUP BY
    part,
    part_code,
    color
    ) b
    GROUP BY
    b.part UNION ALL
    SELECT
    'RC91' AS location,
    c.part,
    SPLIT_STRING ( c.plan, '_', 1 ) AS qty,
    SPLIT_STRING ( c.plan, '_', 2 ) AS late_stock
    FROM
    (
    SELECT DISTINCT
    (
    CONCAT( part_code, ' (', color, ')' )) AS part,
    (
    SELECT
    CONCAT(
    COALESCE ( quantity, 0 ),
    '_',
    COALESCE (  DATEDIFF( due_date, '" . $now . "' ), 0 )) AS plan
    FROM
    production_schedules
    LEFT JOIN materials ON materials.material_number = production_schedules.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND production_schedules.due_date BETWEEN '" . $nextdayplus2 . "'
    AND '" . $nextmonth . "'
    AND CONCAT( part_code, ' (', color, ')' ) = CONCAT( p.part_code, ' (', p.color, ')' )
    LIMIT 1
    ) AS plan,
    0 AS stamp
    FROM
    production_schedules a
    LEFT JOIN materials m ON m.material_number = a.material_number
    LEFT JOIN injection_part_details p ON p.model = m.model
    WHERE
    m.category = 'FG'
    AND m.origin_group_code = '072'
    ) c
    GROUP BY
    c.part,
    c.plan ) d
    where d.qty != 0");

            $response = array(
                'status' => true,
                'datas_skeleton' => $datas_skeleton,
                'datas_ivory' => $datas_ivory,
                'plan_day' => $plan_day,
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

    public function indexDryerResin()
    {
        $dryer = DB::SELECT('select * from injection_dryers');

        return view('injection.index_dryer')
            ->with('mesin', $this->mesin)
            ->with('dryer', $dryer)
            ->with('title', 'Injection Dryer')
            ->with('title_jp', '成形乾燥機');
    }

    public function fetchListResin(Request $request)
    {
        try {
            $id_user = Auth::id();

            $data = DB::SELECT("Select * from injection_parts where remark = 'abs'");

            $response = array(
                'status' => true,
                'datas' => $data,
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

    public function fetchResumeResin(Request $request)
    {
        try {
            $id_user = Auth::id();

            $data = DB::SELECT("SELECT
      injection_dryer_logs.*,
      employee_syncs.`name`,
      injection_dryer_logs.created_at AS created,
      DATE(
      NOW()) AS now,
      DATE(
      NOW()) - INTERVAL 7 DAY AS week_ago,
      cond_before.material_number AS matnumbefore,
      cond_before.material_description AS matdesbefore,
      cond_before.color AS colorbefore,
      cond_before.qty AS qtybefore,
      cond_before.lot_number AS lotbefore,
      cond_before.employee_id AS empbefore,
      employeenefore.`name` AS namebefore,
    IF
      ( cond_before.material_number = injection_dryer_logs.material_number, 'TETAP', 'GANTI' ) AS `status`
    FROM
      `injection_dryer_logs`
      LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_dryer_logs.employee_id
      LEFT JOIN injection_dryer_logs AS cond_before ON cond_before.id = injection_dryer_logs.dryer_before_id
      LEFT JOIN employee_syncs AS employeenefore ON employeenefore.employee_id = cond_before.employee_id
    WHERE
      DATE( injection_dryer_logs.created_at ) BETWEEN DATE(
      NOW()) - INTERVAL 7 DAY
      AND DATE(
      NOW())
    ORDER BY
      injection_dryer_logs.created_at DESC");

            $response = array(
                'status' => true,
                'datas' => $data,
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

    public function inputResin(Request $request)
    {
        try {
            $id_user = Auth::id();

            $dryer_log = InjectionDryerLog::where('dryer', $request->get('dryer'))->orderBy('id', 'desc')->first();
            $id = null;
            if ($dryer_log) {
                $id = $dryer_log->id;
            }

            $resin = InjectionDryerLog::create([
                'dryer_before_id' => $id,
                'dryer' => $request->get('dryer'),
                'material_number' => $request->get('material_number'),
                'material_description' => $request->get('material_description'),
                'color' => $request->get('color'),
                'qty' => $request->get('qty'),
                'lot_number' => $request->get('lot_number'),
                'type' => 'IN',
                'employee_id' => $request->get('employee_id'),
                'created_by' => $id_user,
            ]);

            $dryer = InjectionDryer::firstOrNew(['dryer' => $request->get('dryer')]);
            $dryer->material_number = $request->get('material_number');
            $dryer->material_description = $request->get('material_description');
            $dryer->color = $request->get('color');

            $dryer->lot_number = $request->get('lot_number');
            $dryer->qty = $request->get('qty');
            $dryer->created_by = $id_user;
            $dryer->save();

            $resin = InjectionResin::create([
                'qty' => $request->get('qty'),
                'lot_number' => $request->get('lot_number'),
                'created_by' => $id_user,
            ]);

            $response = array(
                'status' => true,
                'message' => 'Input Resin Success',
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

    public function fetchDryer(Request $request)
    {
        try {
            if ($request->get('dryer') != null) {
                $dryer = InjectionDryer::where('dryer', $request->get('dryer'))->first();
            } elseif ($request->get('machine') != null) {
                $dryer = InjectionDryer::where('machine', $request->get('machine'))->first();
            }

            if ($dryer != null) {
                $response = array(
                    'status' => true,
                    'dryer' => $dryer,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Dryer Not Found',
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

    public function updateDryer(Request $request)
    {
        try {
            $dryer_all = InjectionDryer::get();
            $machines = [];
            foreach ($dryer_all as $key) {
                $machines[] = $key->machine;
            }
            if (in_array($request->get('machine'), $machines)) {
                $status = false;
                $message = 'Mesin sudah terpakai';
            } else {
                $dryer = InjectionDryer::find($request->get('id'));
                $dryer->machine = $request->get('machine');
                $dryer->save();

                $status = true;
                $message = 'Adjustment Success';
            }

            $response = array(
                'status' => $status,
                'message' => $message,
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

    public function indexInjectionSchedule()
    {
        $title = 'Injection Schedule';
        $title_jp = '???';
        return view('injection.schedule_view', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'auth' => Auth::user()->username,
            'role' => Auth::user()->role_code,
        ))->with('page', 'Injection Schedule View')->with('jpn', '???');
    }

    public function fetchInjectionSchedule()
    {
        $last = date('Y-m-t');
        $first = date('Y-m-01');

        $schedule = db::select("SELECT
    injection_schedule_logs.*,
    'material' AS type
    FROM
    injection_schedule_logs UNION ALL
    SELECT
    injection_schedule_molding_logs.*,
    'molding' AS type
    FROM
    injection_schedule_molding_logs UNION ALL
    SELECT
    injection_schedules.*,
    'material' AS type
    FROM
    injection_schedules UNION ALL
    SELECT
    injection_schedule_moldings.*,
    'molding' AS type
    FROM
    injection_schedule_moldings");

        $response = array(
            'status' => true,
            'schedule' => $schedule,
            'mesin' => $this->mesin,
            'first' => $first,
            'last' => $last,
        );
        return Response::json($response);

    }

    public function fetchInjectionScheduleAdjustment(Request $request)
    {
        try {
            $schedule = DB::SELECT("SELECT DISTINCT
      ( id_schedule ),
      injection_schedule_logs.*,
      DATE( injection_schedule_logs.start_time ) AS start_date,
      TIME( injection_schedule_logs.start_time ) AS start_times,
      SPLIT_STRING ( injection_molding_masters.mesin, ',', 1 ) AS machine_1,
      IF
      ( SPLIT_STRING ( injection_molding_masters.mesin, ',', 2 ) != '', SPLIT_STRING ( injection_molding_masters.mesin, ',', 2 ), 0 ) AS machine_2,
      IF
      ( SPLIT_STRING ( injection_molding_masters.mesin, ',', 3 ) != '', SPLIT_STRING ( injection_molding_masters.mesin, ',', 3 ), 0 ) AS machine_3
      FROM
      injection_schedule_logs
      LEFT JOIN injection_molding_masters ON injection_molding_masters.product = injection_schedule_logs.part
      WHERE
      injection_schedule_logs.id = '" . $request->get('id_schedule') . "'");
            $response = array(
                'status' => true,
                'schedule' => $schedule,
                'mesin' => $this->mesin,
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

    public function adjustInjectionScheduleAdjustment(Request $request)
    {
        try {
            $id_schedule = $request->get('id_schedule');
            $start_date = $request->get('start_date');
            $start_time = $request->get('start_time');
            $machine = $request->get('machine');
            $reason = $request->get('reason');
            $quantity_awal = $request->get('quantity_awal');
            $quantity_adj = $request->get('quantity_adj');
            $new_schedule = $request->get('new_schedule');

            $start = explode(':', $start_time);
            $starts = $start_date . ' ' . sprintf('%02d', $start[0]) . ':' . $start[1] . ':00';

            $schedule = InjectionScheduleLog::select('injection_schedule_logs.*', 'injection_machine_cycle_times.shoot', 'injection_machine_cycle_times.cycle')
                ->join('injection_machine_cycle_times', function ($join) {
                    $join->on('injection_machine_cycle_times.part', '=', 'injection_schedule_logs.part');
                    $join->on('injection_machine_cycle_times.color', '=', 'injection_schedule_logs.color');
                })
                ->where('injection_schedule_logs.id', $id_schedule)
                ->first();

            $cek_mesin = DB::SELECT("SELECT
                  *
      FROM
      injection_schedule_logs
      WHERE
      machine = '" . $machine . "'
      AND
      start_time <= '" . $starts . "'
      AND end_time >= '" . $starts . "'
      and id != '" . $id_schedule . "'");
            if (count($cek_mesin) > 0) {
                $status = false;
                $message = 'Ada schedule di mesin dan jam yang dipilih.';
            } else if ($starts >= $start_date . ' 01:00:00' && $starts < $start_date . ' 07:00:00') {
                $status = false;
                $message = 'Tidak bisa memilih pada Shift 3.';
            } else {
                $new_schedule_status = '';
                if ($quantity_adj != $quantity_awal) {
                    if ($quantity_adj < $quantity_awal) {
                        if ($new_schedule == 'Tidak Buat Schedule Baru') {
                            $quantity_new = $quantity_adj;
                            $new_schedule_status = '';
                        } else {
                            $quantity_new = $quantity_adj;
                            $quantity_new_adj = $quantity_awal - $quantity_adj;
                            $new_schedule_status = 'Yes';
                        }
                    } else {
                        $quantity_new = $quantity_adj;
                    }
                } else {
                    $quantity_new = $quantity_awal;
                }
                $cycle = $quantity_new * $schedule->cycle;
                $schedule->machine = $machine;
                $schedule->start_time = $starts;
                $schedule->end_time = date('Y-m-d H:i:s', strtotime($starts) + $cycle);
                $schedule->qty = $quantity_new;
                $schedule->save();

                $nextday = date('Y-m-d', strtotime($start_date . ' +1 day'));

                if ($new_schedule_status != '') {
                    $new_schedules = DB::table('injection_schedule_logs')->insert([
                        'material_number' => $schedule->material_number,
                        'material_description' => $schedule->material_description,
                        'part' => $schedule->part,
                        'color' => $schedule->color,
                        'qty' => $quantity_new_adj,
                        'start_time' => date("Y-m-d H:i:s", strtotime($nextday . ' 07:00:00')),
                        'end_time' => date("Y-m-d H:i:s", strtotime($nextday . ' 07:00:00') + ($quantity_new_adj * $schedule->cycle)),
                        'machine' => $schedule->machine,
                        'created_by' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                $status = true;
                $message = 'Sukses Mengubah Schedule';
            }

            $response = array(
                'status' => $status,
                'message' => $message,
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

    public function indexInputStock()
    {
        $materials = DB::SELECT("SELECT *,gmc as material_number, part_name as material_description FROM `injection_parts` where remark = 'injection' and deleted_at is null");

        $title = 'Input Daily Stock Recorder';
        $title_jp = '???';
        return view('injection.input_stock', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'materials' => $materials,
        ))->with('page', 'Input Daily Stock Recorder')->with('jpn', '???');
    }

    public function inputStock(Request $request)
    {
        try {
            $id_user = Auth::id();

            $injection_inventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC91']);
            $injection_inventory->quantity = $request->get('quantity');
            $injection_inventory->save();

            InjectionTransaction::create([
                'material_number' => $request->get('material_number'),
                'location' => 'RC91',
                'quantity' => $request->get('quantity'),
                'status' => 'INPUT STOCK',
                'operator_id' => Auth::user()->username,
                'created_by' => $id_user,
            ]);

            $response = array(
                'status' => true,
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

    public function fetchInputStock(Request $request)
    {
        try {

            $stock = InjectionInventory::select('*', 'part_name as material_description')->join('injection_parts', 'injection_parts.gmc', 'injection_inventories.material_number')->where('location', 'RC91')->where('injection_parts.deleted_at', null)->where('injection_parts.remark', 'injection')->get();

            $response = array(
                'status' => true,
                'stock' => $stock,
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

    public function indexInjectionTag()
    {

        $material = DB::SELECT("SELECT *,gmc as material_number, part_name as material_description FROM `injection_parts` where remark = 'injection' and deleted_at is null");

        $title = 'Injection Tag';
        $title_jp = '成形タグ';
        return view('injection.tag', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'material' => $material,
            'material2' => $material,
        ))->with('page', 'Injection Tag')->with('jpn', '成形タグ');
    }

    public function fetchInjectionTag(Request $request)
    {
        try {
            $tag = DB::SELECT('SELECT
      injection_tags.*,
      injection_parts.*,
      employee_syncs.*,
      concat( injection_tags.part_name, "<br>", injection_tags.part_type, " - ", injection_tags.color, "<br>", injection_tags.cavity ) AS partsall,
      injection_parts.part_name AS material_description,
      injection_tags.updated_at AS last_update,
      injection_tags.id AS id_tag
      FROM
      injection_tags
      LEFT JOIN injection_parts ON injection_parts.gmc = injection_tags.material_number
      LEFT JOIN employee_syncs ON injection_tags.operator_id = employee_syncs.employee_id
      WHERE
      injection_parts.deleted_at IS NULL');

            $response = array(
                'status' => true,
                'tag' => $tag,
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

    public function fetchInjectionMaterial(Request $request)
    {
        try {
            $material = InjectionTag::join('injection_parts', 'injection_parts.gmc', 'injection_tags.material_number')->where('material_number', $request->get('material_number'))->orderBy('injection_tags.id', 'desc')->first();

            $materialall = InjectionTag::orderBy('id', 'desc')->first();

            $response = array(
                'status' => true,
                'material' => $material,
                'materialall' => $materialall,
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

    public function fetchInjectionMaterialEdit(Request $request)
    {
        try {
            $material = InjectionTag::join('injection_parts', 'injection_parts.gmc', 'injection_tags.material_number')->where('material_number', $request->get('material_number'))->orderBy('injection_tags.id', 'desc')->first();

            $materialall = InjectionTag::orderBy('id', 'desc')->first();

            $response = array(
                'status' => true,
                'material' => $material,
                'materialall' => $materialall,
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

    public function inputInjectionTag(Request $request)
    {
        try {
            $id_user = Auth::id();
            InjectionTag::create([
                'material_number' => $request->get('material_number'),
                'no_kanban' => $request->get('no_kanban'),
                'concat_kanban' => $request->get('concat_kanban'),
                'tag' => $request->get('tag'),
                'mat_desc' => $request->get('mat_desc'),
                'created_by' => $id_user,
            ]);

            $response = array(
                'status' => true,
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

    public function editInjectionTag(Request $request)
    {
        try {
            $tag = InjectionTag::where('injection_tags.id', $request->get('id'))->first();

            $response = array(
                'status' => true,
                'tag' => $tag,
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

    public function updateInjectionTag(Request $request)
    {
        try {
            $id_user = Auth::id();
            $tag = InjectionTag::where('injection_tags.id', $request->get('id_tag'))->first();
            $tag->material_number = $request->get('material_number');
            $tag->mat_desc = $request->get('mat_desc');
            $tag->no_kanban = $request->get('no_kanban');
            $tag->tag = $request->get('tag');
            $tag->save();

            $response = array(
                'status' => true,
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

    public function deleteInjectionTag($id)
    {
        try {
            InjectionTag::where('id', $id)->forceDelete();
            return redirect('index/injection/tag');
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function removeInjectionTag(Request $request)
    {
        try {
            $transaction = InjectionTag::where('tag', $request->get('tag'))->where('availability', '3')->first();

            if ($transaction) {
                $process = InjectionProcessLog::where('tag_product', $request->get('tag'))->where('material_number', $transaction->material_number)->where('cavity', $transaction->cavity)->where('remark', null)->first();

                if ($process != null) {
                    $process->remark = 'Close';
                    $process->save();
                }

                $transaction->operator_id = null;
                $transaction->part_name = null;
                $transaction->part_type = null;
                $transaction->color = null;
                $transaction->cavity = null;
                $transaction->location = null;
                $transaction->shot = null;
                $transaction->availability = null;
                $transaction->height_check = null;
                $transaction->push_pull_check = null;
                $transaction->torque_check = null;
                $transaction->remark = null;
                $transaction->save();

                $response = array(
                    'status' => true,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Material belum ditransaksikan. Masih milik RC11.',
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

    public function fetchInjectionCleanKanban()
    {
        try {
            $process = InjectionProcessLog::where('injection_process_logs.remark', 'Close')
                ->select('injection_process_logs.material_number', 'injection_process_logs.part_type', 'injection_process_logs.color', 'injection_process_logs.shot', 'injection_process_logs.cavity', 'injection_tags.mat_desc', 'injection_tags.no_kanban', 'injection_process_logs.updated_at as created')
                ->whereDate('injection_process_logs.updated_at', '>=', date('Y-m-01'))
                ->whereDate('injection_process_logs.updated_at', '<=', date('Y-m-d'))
                ->leftjoin('injection_tags', 'injection_tags.tag', 'injection_process_logs.tag_product')
                ->orderBy('injection_process_logs.updated_at', 'desc')
                ->get();

            $response = array(
                'status' => true,
                'datas' => $process,
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

    public function indexInjectionTraceability()
    {
        $title = 'Injection Traceability';
        $title_jp = '成形トレーサビリティ';
        return view('injection.traceability', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Injection Traceability')->with('jpn', '成形トレーサビリティ');
    }

    public function fetchInjectionTraceability(Request $request)
    {
        try {
            if (is_numeric($request->get('tag'))) {
                $injection_process = DB::SELECT("SELECT
        injection_process_logs.tag_product,
        injection_tags.material_number,
        injection_tags.mat_desc,
        injection_tags.no_kanban,
        injection_process_logs.start_time,
        injection_process_logs.end_time,
        injection_process_logs.mesin,
        injection_process_logs.part_name,
        injection_process_logs.part_type,
        injection_process_logs.color,
        injection_process_logs.cavity,
        injection_process_logs.molding,
        injection_process_logs.shot as qty,
        injection_process_logs.dryer,
        injection_process_logs.dryer_lot_number,
        injection_process_logs.ng_name,
        injection_process_logs.ng_count,
        opmesin.employee_id,
        opmesin.name
        FROM
        injection_tags
        LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
        LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
        WHERE
        injection_process_logs.tag_product = '" . $request->get('tag') . "'
        ORDER BY
        injection_process_logs.created_at DESC
        LIMIT 1
        ");

                $molding = DB::SELECT("SELECT
        injection_history_molding_logs.pic,
        injection_history_molding_logs.mesin AS mesin,
        injection_history_molding_logs.part,
        injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
        injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
        injection_history_molding_logs.start_time,
        injection_history_molding_logs.end_time,
        injection_history_molding_logs.running_time,
        injection_history_molding_logs.note,
        COALESCE(injection_history_molding_logs.decision,'Tidak Ada') as decision
        FROM
        injection_tags
        LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
        LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
        LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
        LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part
        left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
        WHERE
        injection_process_logs.tag_product = '" . $request->get('tag') . "'
        AND injection_history_molding_logs.created_at <= injection_process_logs.start_time
        AND injection_history_molding_logs.type = 'PASANG'
        ORDER BY
        injection_process_logs.created_at DESC,
        injection_history_molding_logs.created_at DESC,
        injection_molding_logs.created_at DESC
        LIMIT 1");

                $dryer = DB::SELECT("SELECT
        injection_dryer_logs.material_number,
        injection_dryer_logs.material_description,
        injection_dryer_logs.dryer,
        injection_dryer_logs.color,
        injection_dryer_logs.qty,
        injection_dryer_logs.lot_number,
        injection_dryer_logs.created_at,
        opresin.employee_id,
        opresin.name
        FROM
        injection_tags
        LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
        LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number
        AND injection_dryer_logs.dryer = injection_process_logs.dryer
        LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id
        WHERE
        injection_process_logs.tag_product = '" . $request->get('tag') . "'
        AND injection_dryer_logs.created_at <= injection_process_logs.start_time
        ORDER BY
        injection_process_logs.created_at DESC,
        injection_dryer_logs.created_at DESC
        LIMIT 1");

                $transaction = DB::SELECT("
        SELECT
        injection_transactions.material_number,
        injection_tags.mat_desc,
        injection_transactions.location,
        injection_transactions.quantity,
        injection_transactions.status,
        opinjeksi.employee_id,
        opinjeksi.name,
        injection_transactions.created_at
        FROM
        injection_tags
        LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
        LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
        LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id
        WHERE
        injection_process_logs.tag_product = '" . $request->get('tag') . "'
        AND injection_transactions.created_at >= injection_process_logs.end_time
        ORDER BY
        injection_process_logs.created_at DESC,
        injection_transactions.created_at ASC
        LIMIT 3");

                $response = array(
                    'status' => true,
                    'injection_process' => $injection_process,
                    'molding' => $molding,
                    'dryer' => $dryer,
                    'transaction' => $transaction,
                );
            } else {
                $tag = $request->get('tag');

                $datas = DB::SELECT('SELECT DISTINCT
        ( rc_kensa_initials.serial_number ),
        rc_kensa_initials.*,
        rc_kensa_initials.ng_name AS ng_name_injection,
        rc_kensa_initials.ng_count AS ng_count_injection,
        (
        SELECT
        GROUP_CONCAT( rc_kensas.ng_name )
        FROM
        rc_kensas
        LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
        WHERE
        rc_kensas.kensa_initial_code = rc_kensa_initials.kensa_initial_code
        AND ng_name IS NOT NULL
        AND injection_parts.deleted_at IS NULL
        AND injection_parts.remark = "injection"
        ) AS ng_name_kensa,
        (
        SELECT
        GROUP_CONCAT( rc_kensas.ng_count )
        FROM
        rc_kensas
        LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
        WHERE
        rc_kensas.kensa_initial_code = rc_kensa_initials.kensa_initial_code
        AND ng_name IS NOT NULL
        AND injection_parts.deleted_at IS NULL
        AND injection_parts.remark = "injection"
        ) AS ng_count_kensa,
        emp_injection.employee_id AS employee_id_injection,
        emp_injection.NAME AS name_injection,
        emp_resin.employee_id AS employee_id_resin,
        emp_resin.NAME AS name_resin,
        emp_kensa.employee_id AS employee_id_kensa,
        emp_kensa.NAME AS name_kensa,
        part_injection.gmc AS material_number,
        part_injection.part_name AS part_name,
        part_resin.gmc AS material_number_resin,
        part_resin.part_name AS mat_desc_resin,
        rc_kensa_initials.part_type AS parts
        FROM
        rc_kensa_initials
        LEFT JOIN employee_syncs ON employee_syncs.employee_id = rc_kensa_initials.operator_kensa
        LEFT JOIN injection_parts part_injection ON part_injection.gmc = rc_kensa_initials.material_number
        LEFT JOIN injection_parts part_resin ON part_resin.gmc = rc_kensa_initials.material_resin
        LEFT JOIN employee_syncs emp_injection ON emp_injection.employee_id = operator_injection
        LEFT JOIN employee_syncs emp_resin ON emp_resin.employee_id = operator_resin
        LEFT JOIN employee_syncs emp_kensa ON emp_kensa.employee_id = rc_kensa_initials.operator_kensa
        WHERE
        rc_kensa_initials.serial_number = "' . $tag . '"
        AND part_resin.deleted_at IS NULL
        AND part_injection.deleted_at IS NULL');

                $data_kensa = DB::SELECT("SELECT
                  * ,
        rc_kensa_initials.created_at AS create_kensa
        FROM
        rc_kensa_initials
        LEFT JOIN employee_syncs ON employee_syncs.employee_id = operator_kensa
        LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensa_initials.material_number
        WHERE
        serial_number = '" . $tag . "'
        AND injection_parts.deleted_at IS NULL
        ORDER BY
        rc_kensa_initials.id");

                if (count($datas) > 0 && count($data_kensa) > 0) {
                    $response = array(
                        'status' => true,
                        'datas' => $datas,
                        'data_kensa' => $data_kensa,
                    );
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Not Found',
                    );
                    return Response::json($response);
                }
            }
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexInjectionInventories($loc)
    {
        $title = 'Injection Inventories ' . strtoupper($loc);
        $title_jp = '成形在庫';
        return view('injection.inventory', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'loc' => $loc,
        ))->with('page', 'Injection Inventories')->with('jpn', '成形在庫');
    }

    public function fetchInjectionInventories(Request $request)
    {
        try {
            $data = InjectionInventory::select('*', 'injection_inventories.updated_at as update_inventories', 'injection_inventories.id as id_inventory')->join('injection_parts', 'injection_parts.gmc', 'injection_inventories.material_number')->where('injection_parts.deleted_at', null)->where('injection_inventories.location', $request->get('loc'))->get();

            $response = array(
                'status' => true,
                'datas' => $data,
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

    public function updateInjectionInventories(Request $request)
    {
        try {
            $invent = InjectionInventory::where('id', $request->get('id'))->first();
            $invent->quantity = $request->get('quantity');
            $invent->save();

            $response = array(
                'status' => true,
                'message' => 'Success Update Inventories',
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

    public function inputReasonPause(Request $request)
    {
        try {
            $reason = InjectionHistoryMoldingWorks::create([
                'molding_code' => $request->get('molding_code'),
                'status' => $request->get('status'),
                'type' => $request->get('type'),
                'pic' => $request->get('pic'),
                'mesin' => $request->get('mesin'),
                'part' => $request->get('part'),
                'start_time' => $request->get('start_time'),
                'reason' => $request->get('reason'),
                'created_by' => Auth::id(),
            ]);

            $temp = InjectionHistoryMoldingTemp::where('molding_code', $request->get('molding_code'))->first();
            $temp->remark = $request->get('status');
            $temp->reason = $request->get('reason');
            $temp->save();

            $response = array(
                'status' => true,
                'message' => '',
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

    public function inputApprovalCek(Request $request)
    {
        try {
            $datawork = InjectionHistoryMoldingTemp::where('molding_code', $request->get('molding_code'))->first();
            if ($datawork->remark != null) {

                if ($request->get('status') == 'CEK VISUAL & DIMENSI') {
                    $work = InjectionHistoryMoldingWorks::where('molding_code', $request->get('molding_code'))->where('end_time', null)->first();
                    $work->end_time = date('Y-m-d H:i:s');
                    $work->save();

                    $temp = InjectionHistoryMoldingTemp::where('molding_code', $request->get('molding_code'))->where('remark', '!=', null)->first();
                    $temp->remark = null;
                    $temp->reason = null;
                    $temp->status_cek_visual = date('Y-m-d H:i:s') . '_' . $request->get('pic');
                    $temp->save();
                } else if ($request->get('status') == 'PURGING') {
                    $work = InjectionHistoryMoldingWorks::where('molding_code', $request->get('molding_code'))->where('end_time', null)->first();
                    $work->end_time = date('Y-m-d H:i:s');
                    $work->save();

                    $temp = InjectionHistoryMoldingTemp::where('molding_code', $request->get('molding_code'))->where('remark', '!=', null)->first();
                    $temp->remark = null;
                    $temp->reason = null;
                    $temp->status_purging = date('Y-m-d H:i:s') . '_' . $request->get('pic');
                    $temp->save();
                } else if ($request->get('status') == 'SETTING ROBOT & CAMERA') {
                    $work = InjectionHistoryMoldingWorks::where('molding_code', $request->get('molding_code'))->where('end_time', null)->first();
                    $work->end_time = date('Y-m-d H:i:s');
                    $work->save();

                    $temp = InjectionHistoryMoldingTemp::where('molding_code', $request->get('molding_code'))->where('remark', '!=', null)->first();
                    $temp->remark = null;
                    $temp->reason = null;
                    $temp->status_setting_robot = date('Y-m-d H:i:s') . '_' . $request->get('pic');
                    $temp->save();
                } else if ($request->get('status') == 'APPROVAL QA' && $request->get('status_qa') != null) {
                    $work = InjectionHistoryMoldingWorks::where('molding_code', $request->get('molding_code'))->where('end_time', null)->first();
                    $work->end_time = date('Y-m-d H:i:s');
                    $work->save();

                    $temp = InjectionHistoryMoldingTemp::where('molding_code', $request->get('molding_code'))->where('remark', '!=', null)->first();
                    $temp->remark = null;
                    $temp->reason = null;
                    $temp->status_approval_qa = date('Y-m-d H:i:s') . '_' . $request->get('pic_molding') . '_' . $request->get('pic_qa');
                    $temp->save();
                }

                $datawork = InjectionHistoryMoldingTemp::where('molding_code', $request->get('molding_code'))->first();

                $response = array(
                    'status' => true,
                    'statusApprovalCek' => 'Selesai',
                    'datawork' => $datawork,
                );
                return Response::json($response);
            } else {
                $reason = InjectionHistoryMoldingWorks::create([
                    'molding_code' => $request->get('molding_code'),
                    'status' => $request->get('status'),
                    'type' => $request->get('type'),
                    'pic' => $request->get('pic'),
                    'mesin' => $request->get('mesin'),
                    'part' => $request->get('part'),
                    'start_time' => $request->get('start_time'),
                    'reason' => $request->get('reason'),
                    'created_by' => Auth::id(),
                ]);

                $temp = InjectionHistoryMoldingTemp::where('molding_code', $request->get('molding_code'))->first();
                $temp->remark = $request->get('status');
                $temp->reason = $request->get('reason');
                $temp->save();

                $response = array(
                    'status' => true,
                    'statusApprovalCek' => 'Mulai',
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

    public function changeReasonPause(Request $request)
    {
        try {
            $work = InjectionHistoryMoldingWorks::where('molding_code', $request->get('molding_code'))->where('end_time', null)->first();
            $work->end_time = date('Y-m-d H:i:s');
            $work->save();

            $temp = InjectionHistoryMoldingTemp::where('molding_code', $request->get('molding_code'))->where('remark', '!=', null)->first();
            $temp->remark = null;
            $temp->reason = null;
            $temp->save();

            $response = array(
                'status' => true,
                'message' => '',
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

    public function inputReasonPauseMaintenance(Request $request)
    {
        try {
            $reason = InjectionMaintenanceMoldingWork::create([
                'maintenance_code' => $request->get('maintenance_code'),
                'type' => $request->get('type'),
                'pic' => $request->get('pic'),
                'product' => $request->get('product'),
                'part' => $request->get('part'),
                'start_time' => $request->get('start_time'),
                'reason' => $request->get('reason'),
                'status' => $request->get('status'),
                'last_counter' => $request->get('last_counter'),
                'created_by' => Auth::id(),
            ]);

            $temp = InjectionMaintenanceMoldingTemp::where('maintenance_code', $request->get('maintenance_code'))->first();
            $temp->remark = 'PAUSE';
            $temp->reason = $request->get('reason');
            $temp->save();

            $response = array(
                'status' => true,
                'message' => '',
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

    public function changeReasonPauseMaintenance(Request $request)
    {
        try {
            $work = InjectionMaintenanceMoldingWork::where('maintenance_code', $request->get('maintenance_code'))->where('end_time', null)->first();
            $work->end_time = date('Y-m-d H:i:s');
            $work->save();

            $temp = InjectionMaintenanceMoldingTemp::where('maintenance_code', $request->get('maintenance_code'))->where('remark', 'PAUSE')->first();
            $temp->remark = null;
            $temp->reason = null;
            $temp->save();

            $response = array(
                'status' => true,
                'message' => '',
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

    public function indexReportSetupMolding()
    {
        $title = 'Setup Molding History';
        $title_jp = '金型セットアップ履歴';
        return view('injection.report_setup_molding', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Setup Molding History')->with('jpn', '成形在庫');
    }

    public function fetchReportSetupMolding(Request $request)
    {
        try {
            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            if ($date_from == '') {
                if ($date_to == '') {
                    $where = "WHERE DATE(injection_history_molding_logs.created_at) BETWEEN CONCAT(DATE_FORMAT(NOW(),'%Y-%m-01')) AND DATE(NOW())";
                } else {
                    $where = "WHERE DATE(injection_history_molding_logs.created_at) BETWEEN CONCAT(DATE_FORMAT(NOW(),'%Y-%m-01')) AND '" . $date_to . "'";
                }
            } else {
                if ($date_to == '') {
                    $where = "WHERE DATE(injection_history_molding_logs.created_at) BETWEEN '" . $date_from . "' AND DATE(NOW())";
                } else {
                    $where = "WHERE DATE(injection_history_molding_logs.created_at) BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
                }
            }

            $data = DB::SELECT("SELECT
  injection_history_molding_logs.molding_code,
  injection_history_molding_logs.pic,
  injection_history_molding_logs.type,
  injection_history_molding_logs.mesin,
  injection_history_molding_logs.part,
  injection_history_molding_logs.color,
  injection_history_molding_logs.created_at AS created,
  ROUND( total_shot / qty_shot, 0 ) AS last_shot,
  injection_history_molding_logs.start_time,
  injection_history_molding_logs.end_time,
  TIMESTAMPDIFF(second,start_time,end_time)/60 AS duration,
  injection_history_molding_logs.note,
  injection_history_molding_logs.decision
  FROM
  `injection_history_molding_logs`
  LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part and injection_history_molding_logs.color = injection_molding_masters.product
  " . $where . "
  order by injection_history_molding_logs.id desc");

            $dataall = [];
            $dataworkall = [];

            foreach ($data as $key) {
                $work = DB::SELECT("SELECT *,TIMESTAMPDIFF(second,start_time,end_time)/60 as duration FROM `injection_history_molding_works` where molding_code = '" . $key->molding_code . "'");

                if (count($work) > 0) {
                    foreach ($work as $val) {
                        $datawork = array(
                            'molding_code' => $val->molding_code,
                            'status' => $val->status,
                            'start_time' => $val->start_time,
                            'end_time' => $val->end_time,
                            'duration' => $val->duration,
                            'reason' => $val->reason);
                        $dataworkall[] = join("+", $datawork);
                    }
                }

            }
            $response = array(
                'status' => true,
                'datas' => $data,
                'dataworkall' => $dataworkall,
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

    public function fetchReportSetupMoldingTimeline(Request $request)
    {
        try {

            $molding_code = $request->get('molding_code');
            $timeline = InjectionHistoryMoldingWorks::where('molding_code', $molding_code)->get();
            $response = array(
                'status' => true,
                'timeline' => $timeline,
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

    public function indexInjectionTransaction()
    {
        $title = 'Transaction History';
        $title_jp = '';
        return view('injection.transaction_history', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Transaction History')->with('jpn', '');
    }

    public function fetchInjectionTransaction(Request $request)
    {
        try {
            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            if ($date_from == '') {
                if ($date_to == '') {
                    $wheredate = "AND DATE(injection_transactions.created_at) BETWEEN CONCAT(DATE_FORMAT(NOW(),'%Y-%m-01')) AND DATE(NOW())";
                } else {
                    $wheredate = "AND DATE(injection_transactions.created_at) BETWEEN CONCAT(DATE_FORMAT(NOW(),'%Y-%m-01')) AND '" . $date_to . "'";
                }
            } else {
                if ($date_to == '') {
                    $wheredate = "AND DATE(injection_transactions.created_at) BETWEEN '" . $date_from . "' AND DATE(NOW())";
                } else {
                    $wheredate = "AND DATE(injection_transactions.created_at) BETWEEN '" . $date_from . "' AND '" . $date_to . "'";
                }
            }

            if ($request->get('location') == "") {
                $location = '';
            } else {
                $location = "AND injection_transactions.location = '" . $request->get('location') . "'";
            }

            if ($request->get('status') == "") {
                $status = '';
            } else {
                $status = "AND injection_transactions.status = '" . $request->get('status') . "'";
            }
            $data = DB::SELECT("SELECT
                *,
  injection_transactions.created_at AS created
  FROM
  `injection_transactions`
  LEFT JOIN employee_syncs ON operator_id = employee_syncs.employee_id
  LEFT JOIN injection_parts ON injection_parts.gmc = injection_transactions.material_number
  WHERE
  `status` != 'INPUT STOCK'
  AND injection_parts.deleted_at IS NULL
  AND injection_parts.remark = 'injection'
  " . $wheredate . "
  " . $location . "
  " . $status . "
  order by injection_transactions.id desc");
            $response = array(
                'status' => true,
                'datas' => $data,
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

    public function indexInjectionNgRate()
    {
        $title = 'QUALITY MONITORING OF INJECTION EMPLOYEES';
        $title_jp = '';
        return view('injection.ng_rate_injection', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'QUALITY MONITORING OF INJECTION EMPLOYEES')->with('jpn', '');
    }

    public function fetchInjectionNgRate(Request $request)
    {
        try {
            if ($request->get('tanggal') == "") {
                $now = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime("-1 days"));
            } else {
                $now = date('Y-m-d', strtotime($request->get('tanggal')));
                $yesterday = date('Y-m-d', strtotime("-1 days", strtotime($now)));
            }

            $j = 1;
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $yesterday) {
                    if ($key->remark == 'H') {
                        $yesterday = date('Y-m-d', strtotime("-" . ++$j . " days", strtotime($yesterday)));
                    }
                }
            }

            $emp = DB::SELECT("SELECT DISTINCT
      ( rc_kensa_initials.operator_injection ) AS employee_id,
      employee_syncs.`name`
      FROM
      rc_kensa_initials
      LEFT JOIN employee_syncs ON employee_syncs.employee_id = rc_kensa_initials.operator_injection");

            $resumes = DB::SELECT("SELECT DISTINCT
      ( rc_kensa_initials.operator_injection ),
      employee_syncs.`name`,
      rc_kensa_initials.ng_name,
      rc_kensa_initials.ng_count,
      empkensa.`name` AS name_kensa,
      operator_kensa AS emp_kensa,
      serial_number,
      rc_kensa_initials.product,
      rc_kensa_initials.material_number,
      part_code,
      injection_parts.part_name,
      rc_kensa_initials.cavity,
      (
      SELECT
      GROUP_CONCAT( rc_kensas.ng_name )
      FROM
      rc_kensas
      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
      WHERE
      rc_kensas.kensa_initial_code = rc_kensa_initials.kensa_initial_code
      AND ng_name IS NOT NULL
      AND DATE( rc_kensas.created_at ) = '" . $now . "'
      AND injection_parts.deleted_at IS NULL
      AND injection_parts.remark = 'injection'
      AND injection_parts.part_code NOT LIKE '%MJ%'
      AND injection_parts.part_code NOT LIKE '%BJ%'
      ) AS ng_name_kensa,
      (
      SELECT
      GROUP_CONCAT( rc_kensas.ng_count )
      FROM
      rc_kensas
      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
      WHERE
      rc_kensas.kensa_initial_code = rc_kensa_initials.kensa_initial_code
      AND ng_name IS NOT NULL
      AND DATE( rc_kensas.created_at ) = '" . $now . "'
      AND injection_parts.deleted_at IS NULL
      AND injection_parts.remark = 'injection'
      AND injection_parts.part_code NOT LIKE '%MJ%'
      AND injection_parts.part_code NOT LIKE '%BJ%'
      ) AS ng_count_kensa
      FROM
      rc_kensa_initials
      LEFT JOIN employee_syncs ON employee_syncs.employee_id = rc_kensa_initials.operator_injection
      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensa_initials.material_number
      LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = rc_kensa_initials.operator_kensa
      WHERE
      rc_kensa_initials.ng_name IS NOT NULL
      AND rc_kensa_initials.part_type NOT LIKE '%MJ%'
      AND rc_kensa_initials.part_type NOT LIKE '%BJ%'
      AND injection_parts.deleted_at IS NULL
      AND injection_parts.remark = 'injection'
      AND DATE( rc_kensa_initials.created_at ) = '" . $now . "'  ");

            $dateTitle = date('d M Y', strtotime($now));

            $firstweek = DB::SELECT("SELECT
      week_date
      FROM
      weekly_calendars
      WHERE
      week_name = ( SELECT week_name FROM weekly_calendars WHERE week_date = '" . $now . "' - INTERVAL 7 DAY )
      AND fiscal_year = (
      SELECT
      fiscal_year
      FROM
      weekly_calendars
      WHERE
      week_date = '" . $now . "')
      ORDER BY
      week_date ASC
      LIMIT 1 ");

            foreach ($firstweek as $key) {
                $firstdayweek = $key->week_date;
            }

            $lastweek = DB::SELECT("SELECT
      week_date
      FROM
      weekly_calendars
      WHERE
      week_name = ( SELECT week_name FROM weekly_calendars WHERE week_date = '" . $now . "' - INTERVAL 7 DAY )
      AND fiscal_year = (
      SELECT
      fiscal_year
      FROM
      weekly_calendars
      WHERE
      week_date = DATE( NOW() ))
      ORDER BY
      week_date DESC
      LIMIT 1 ");

            foreach ($lastweek as $key) {
                $lastdayweek = $key->week_date;
            }

            // $kensa = RcKensaInitial::whereDate('created_at','>=',$firstdayweek)->whereDate('created_at','<=',$lastdayweek)->get();

            // if ($kensa[0]->counceled_employee == null) {
            //     var_dump(date('Y-m-d', strtotime("-7 day", strtotime($firstdayweek))));
            //     var_dump(date('Y-m-d', strtotime("-7 day", strtotime($lastdayweek))));
            //     die();
            // }

            $resumeweek = DB::SELECT("SELECT DISTINCT
      ( rc_kensa_initials.operator_injection ),
      employee_syncs.`name`,
      users.avatar,
      rc_kensa_initials.ng_name,
      rc_kensa_initials.ng_count,
      serial_number,
      (
      SELECT
      GROUP_CONCAT( rc_kensas.ng_name )
      FROM
      rc_kensas
      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
      WHERE
      rc_kensas.kensa_initial_code = rc_kensa_initials.kensa_initial_code
      AND ng_name IS NOT NULL
      AND DATE( rc_kensas.created_at ) >= '" . $firstdayweek . "'
      AND DATE( rc_kensas.created_at ) <= '" . $lastdayweek . "'
      AND injection_parts.deleted_at IS NULL
      AND injection_parts.remark = 'injection'
      AND injection_parts.part_code NOT LIKE '%MJ%'
      AND injection_parts.part_code NOT LIKE '%BJ%'
      ) AS ng_name_kensa,
      (
      SELECT
      GROUP_CONCAT( rc_kensas.ng_count )
      FROM
      rc_kensas
      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
      WHERE
      rc_kensas.kensa_initial_code = rc_kensa_initials.kensa_initial_code
      AND ng_name IS NOT NULL
      AND DATE( rc_kensas.created_at ) >= '" . $firstdayweek . "'
      AND DATE( rc_kensas.created_at ) <= '" . $lastdayweek . "'
      AND injection_parts.deleted_at IS NULL
      AND injection_parts.remark = 'injection'
      AND injection_parts.part_code NOT LIKE '%MJ%'
      AND injection_parts.part_code NOT LIKE '%BJ%'
      ) AS ng_count_kensa,
      rc_kensa_initials.counceled_employee,
      rc_kensa_initials.counceled_by,
      rc_kensa_initials.counceled_at,
      rc_kensa_initials.counceled_image
      FROM
      rc_kensa_initials
      LEFT JOIN employee_syncs ON employee_syncs.employee_id = rc_kensa_initials.operator_injection
      LEFT JOIN users ON users.username = employee_syncs.employee_id
      WHERE
      rc_kensa_initials.ng_name IS NOT NULL
      AND rc_kensa_initials.part_type NOT LIKE '%MJ%'
      AND rc_kensa_initials.part_type NOT LIKE '%BJ%'
      AND DATE( rc_kensa_initials.created_at ) >= '" . $firstdayweek . "'
      AND DATE( rc_kensa_initials.created_at ) <= '" . $lastdayweek . "'  ");

            $resumeyesterday = DB::SELECT("SELECT DISTINCT
      ( rc_kensa_initials.operator_injection ),
      employee_syncs.`name`,
      rc_kensa_initials.ng_name,
      rc_kensa_initials.ng_count,
      empkensa.`name` AS name_kensa,
      operator_kensa AS emp_kensa,
      serial_number,
      rc_kensa_initials.product,
      rc_kensa_initials.material_number,
      part_code,
      injection_parts.part_name,
      rc_kensa_initials.cavity,
      (
      SELECT
      GROUP_CONCAT( rc_kensas.ng_name )
      FROM
      rc_kensas
      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
      WHERE
      rc_kensas.kensa_initial_code = rc_kensa_initials.kensa_initial_code
      AND ng_name IS NOT NULL
      AND DATE( rc_kensas.created_at ) = '" . $yesterday . "'
      AND injection_parts.deleted_at IS NULL
      AND injection_parts.remark = 'injection'
      AND injection_parts.part_code NOT LIKE '%MJ%'
      AND injection_parts.part_code NOT LIKE '%BJ%'
      ) AS ng_name_kensa,
      (
      SELECT
      GROUP_CONCAT( rc_kensas.ng_count )
      FROM
      rc_kensas
      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
      WHERE
      rc_kensas.kensa_initial_code = rc_kensa_initials.kensa_initial_code
      AND ng_name IS NOT NULL
      AND DATE( rc_kensas.created_at ) = '" . $yesterday . "'
      AND injection_parts.deleted_at IS NULL
      AND injection_parts.remark = 'injection'
      AND injection_parts.part_code NOT LIKE '%MJ%'
      AND injection_parts.part_code NOT LIKE '%BJ%'
      ) AS ng_count_kensa
      FROM
      rc_kensa_initials
      LEFT JOIN employee_syncs ON employee_syncs.employee_id = rc_kensa_initials.operator_injection
      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensa_initials.material_number
      LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = rc_kensa_initials.operator_kensa
      WHERE
      rc_kensa_initials.ng_name IS NOT NULL
      AND rc_kensa_initials.part_type NOT LIKE '%MJ%'
      AND rc_kensa_initials.part_type NOT LIKE '%BJ%'
      AND injection_parts.deleted_at IS NULL
      AND injection_parts.remark = 'injection'
      AND DATE( rc_kensa_initials.created_at ) = '" . $yesterday . "'");

            $response = array(
                'status' => true,
                'emp' => $emp,
                'resumes' => $resumes,
                'resumeweek' => $resumeweek,
                'resumeyesterday' => $resumeyesterday,
                'dateTitle' => $dateTitle,
                'firstdayweek' => $firstdayweek,
                'lastdayweek' => $lastdayweek,
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

    public function inputInjectionCounceling(Request $request)
    {
        try {
            $id_user = Auth::id();
            // $tujuan_upload = 'data_file/injection/counceling';
            // $file = $request->file('fileData');
            // $filename = md5($request->input('counceled_employee').$request->input('counceled_by').date('YmdHisa')).'.'.$request->input('extension');
            // $file->move($tujuan_upload,$filename);

            $emp = explode('-', $request->input('counceled_employee'));
            $leader = explode('-', $request->input('counceled_by'));

            $kensa = RcKensaInitial::whereDate('created_at', '>=', $request->input('first_date'))->whereDate('created_at', '<=', $request->input('last_date'))->get();
            foreach ($kensa as $key) {
                $kensainitial = RcKensaInitial::where('id', $key->id)->first();
                $kensainitial->counceled_employee = $emp[0];
                $kensainitial->counceled_by = $leader[0];
                $kensainitial->counceled_at = date('Y-m-d H:i:s');
                // $kensainitial->counceled_image = $filename;
                $kensainitial->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Konseling Berhasil',
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

    public function inputInjectionDocument()
    {
        $id_user = Auth::id();
        $fy = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
        $training = TrainingReport::create([
            'activity_list_id' => 477,
            'department' => 'Educational Instrument (EI) Department',
            'section' => 'Recorder Proces',
            'product' => 'Recorder',
            'periode' => $fy->fiscal_year,
            'date' => date('Y-m-d'),
            'time' => '00:30:00',
            'trainer' => 'M. Afif Fahamsyah',
            'theme' => 'Training NG Rate Operator Injeksi',
            'isi_training' => 'Training NG Rate Operator Injeksi',
            'tujuan' => 'Evaluasi Hasil NG Per Operator Injeksi',
            'standard' => '-',
            'leader' => 'M. Afif',
            'foreman' => 'Eko Prasetyo Wicaksono',
            'notes' => '-',
            'remark' => date('Y-m-d H:i:s'),
            'created_by' => $id_user,
        ]);
        $id = $training->id;

        return redirect('index/training_report/details/' . $id . '/injeksi')
            ->with('page', 'Training Report')->with('status', 'Training Berhasil Dibuat.')->with('session_training', 'injeksi');
    }

    public function inputInjectionDocumentQa()
    {
        $id_user = Auth::id();
        $fy = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
        $training = TrainingReport::create([
            'activity_list_id' => 624,
            'department' => 'Educational Instrument (EI) Department',
            'section' => 'Recorder Proces',
            'product' => 'Recorder',
            'periode' => $fy->fiscal_year,
            'date' => date('Y-m-d'),
            'time' => '00:30:00',
            'trainer' => 'M. Afif Fahamsyah',
            'theme' => 'Training Audit QA',
            'isi_training' => 'Training Audit QA',
            'tujuan' => 'Evaluasi Hasil Audit QA',
            'standard' => '-',
            'leader' => 'M. Afif',
            'foreman' => 'Eko Prasetyo Wicaksono',
            'notes' => '-',
            'remark' => date('Y-m-d H:i:s'),
            'created_by' => $id_user,
        ]);
        $id = $training->id;

        return redirect('index/training_report/details/' . $id . '/injeksi')
            ->with('page', 'Training Report')->with('status', 'Training Berhasil Dibuat.')->with('session_training', 'injeksi');
    }

    public function scanInjectionCounceledEmployee(Request $request)
    {
        try {
            $emp = Employee::where('tag', $request->get('employee_id'))->first();

            if ($emp != null) {
                $response = array(
                    'status' => true,
                    'employee' => $emp,
                    'message' => 'Scan Sukses',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Scan Failed',
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

    public function scanInjectionCounceledBy(Request $request)
    {
        try {
            $emp = Employee::where('tag', $request->get('employee_id'))->first();

            if ($emp != null) {
                $response = array(
                    'status' => true,
                    'employee' => $emp,
                    'message' => 'Scan Sukses',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Scan Failed',
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

    public function indexVisualCheck()
    {

        foreach ($this->hour as $hour) {
            if (date('H:i:s') >= explode(' - ', $hour)[0] && date('H:i:s') < explode(' - ', $hour)[1]) {
                $hour_check = $hour;
            }
        }
        $title = 'Injection Visual Check';
        $title_jp = '';
        return view('injection.visual_check', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'hour_check' => $hour_check,
            'mesin' => $this->mesin,
            'hour' => $this->hour,
        ))->with('page', 'Injection Visual Check')->with('jpn', '');
    }

    public function fetchMacineWork(Request $request)
    {
        try {
            $machine = InjectionMachineWork::where('mesin', $request->get('mesin'))->first();

            $point_check = null;
            $cavity = null;

            if ($machine->tag_molding != null) {
                if (strpos($machine->part_name, 'YRS') !== false) {
                    if (strpos($machine->part_type, 'HJ') !== false) {
                        $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_HJ')->get();
                        $cavity = PushBlockMaster::where('type', 'head')->where('no_cavity', $machine->cavity)->get();
                    } else if (strpos($machine->part_type, 'FJ') !== false) {
                        $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_FJ')->get();
                        $cavity = PushBlockMaster::where('type', 'foot')->where('no_cavity', $machine->cavity)->get();
                    } else if (strpos($machine->part_type, 'MJ') !== false) {
                        $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_MJ')->get();
                        $cavity = PushBlockMaster::where('type', 'middle')->where('no_cavity', $machine->cavity)->get();
                    } else if (strpos($machine->part_type, 'BJ') !== false) {
                        $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_BJ')->get();
                        $cavity = PushBlockMaster::where('type', 'block')->where('no_cavity', $machine->cavity)->get();
                    }
                }

                if (strpos($machine->part_name, 'YRF') !== false) {
                    if (strpos($machine->part_type, 'A YRF H') !== false) {
                        $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_HJ')->get();
                        $cavity = PushBlockMaster::where('type', 'head')->where('no_cavity', $machine->cavity)->get();
                    } else if (strpos($machine->part_type, 'A YRF B') !== false) {
                        $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_MJ')->get();
                        $cavity = PushBlockMaster::where('type', 'body')->where('no_cavity', $machine->cavity)->get();
                    } else if (strpos($machine->part_type, 'A YRF S') !== false) {
                        $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_BJ')->get();
                        $cavity = PushBlockMaster::where('type', 'stopper')->where('no_cavity', $machine->cavity)->get();
                    }
                }
            }
            $response = array(
                'status' => true,
                'machine' => $machine,
                'point_check' => $point_check,
                'cavity' => $cavity,
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

    public function inputVisualCheck(Request $request)
    {
        try {
            $machine = $request->get('machine');
            $hour_check = $request->get('hour_check');
            $material_number = $request->get('material_number');
            $part_name = $request->get('part_name');
            $part_type = $request->get('part_type');
            $cavity = $request->get('cavity');
            $color = $request->get('color');
            $molding = $request->get('molding');
            $tag_molding = $request->get('tag_molding');
            $dryer = $request->get('dryer');
            $lot_number = $request->get('lot_number');
            $cav_detail = $request->get('cav_detail');
            $cavity_total = $request->get('cavity_total');
            $point_check_total = $request->get('point_check_total');
            $point_check = $request->get('point_check');
            $result_check = $request->get('result_check');
            $pic_check = $request->get('pic_check');
            $note = $request->get('note');
            $description = $request->get('description');
            $action_now = $request->get('action_now');
            $cause = $request->get('cause');
            $action = $request->get('action');

            $injection_parts = DB::SELECT("SELECT
      part_name
      FROM
      injection_parts
      WHERE
      injection_parts.deleted_at IS NULL
      AND remark = 'injection'
      AND gmc = '" . $material_number . "'");

            $material_description = $injection_parts[0]->part_name;

            $total_check = 0;

            $data_emails = [];

            for ($i = 0; $i < $point_check_total; $i++) {
                for ($j = 0; $j < $cavity_total; $j++) {
                    $visual_check = InjectionVisualCheck::create([
                        'machine' => $machine,
                        'hour_check' => $hour_check,
                        'material_number' => $material_number,
                        'part_name' => $part_name,
                        'part_type' => $part_type,
                        'cavity' => $cavity,
                        'color' => $color,
                        'molding' => $molding,
                        'tag_molding' => $tag_molding,
                        'dryer' => $dryer,
                        'lot_number' => $lot_number,
                        'cav_detail' => $cav_detail[$j],
                        'point_check' => $point_check[$i],
                        'result_check' => $result_check[$total_check],
                        'pic_check' => $pic_check,
                        'material_description' => $material_description,
                        'car_description' => $description[$j],
                        'car_action_now' => $action_now[$j],
                        'car_cause' => $cause[$j],
                        'car_action' => $action[$j],
                        'note' => $note[$j],
                        'created_by' => Auth::user()->id,
                    ]);

                    if ($result_check[$total_check] == 'NG' || $result_check[$total_check] == 'NS') {
                        $empsync = EmployeeSync::where('employee_id', $pic_check)->first();
                        $data_email = array(
                            'id' => $visual_check->id,
                            'machine' => $machine,
                            'hour_check' => $hour_check,
                            'material_number' => $material_number,
                            'part_name' => $part_name,
                            'part_type' => $part_type,
                            'cavity' => $cavity,
                            'color' => $color,
                            'molding' => $molding,
                            'tag_molding' => $tag_molding,
                            'dryer' => $dryer,
                            'lot_number' => $lot_number,
                            'cav_detail' => $cav_detail[$j],
                            'point_check' => $point_check[$i],
                            'result_check' => $result_check[$total_check],
                            'pic_check' => $pic_check,
                            'pic_name' => $empsync->name,
                            'material_description' => $material_description,
                            'car_description' => $description[$j],
                            'car_action_now' => $action_now[$j],
                            'car_cause' => $cause[$j],
                            'car_action' => $action[$j],
                            'note' => $note[$j],
                            'created_by' => Auth::user()->id,
                        );
                        array_push($data_emails, $data_email);
                    }
                    $total_check++;
                }
            }

            $mail_to = [];
            $cc = [];
            array_push($mail_to, 'eko.prasetyo.wicaksono@music.yamaha.com');
            array_push($mail_to, 'andik.yayan@music.yamaha.com');

            array_push($cc, 'imbang.prasetyo@music.yamaha.com');
            array_push($cc, 'susilo.basri@music.yamaha.com');

            if (count($data_emails) > 0) {
                Mail::to($mail_to)->cc($cc, 'cc')
                    ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                    ->send(new SendEmail($data_emails, 'car_injection'));
            }

            $response = array(
                'status' => true,
                'message' => 'Success Save Data',
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

    public function indexReportVisualCheck()
    {
        $title = 'Report Injection Visual Check';
        $title_jp = '';
        return view('injection.report_visual_check', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'mesin' => $this->mesin,
            'part' => $this->part,
            'hour' => $this->hour,
        ))->with('page', 'Report Injection Visual Check')->with('jpn', '');
    }

    public function fetchReportVisualCheck(Request $request)
    {
        try {

            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = "DATE( NOW() )";
                    $last = "DATE( NOW() )";
                } else {
                    $first = "DATE( NOW() ) ";
                    $last = "'" . $date_to . "'";
                }
            } else {
                if ($date_to == "") {
                    $first = "'" . $date_from . "'";
                    $last = "DATE( NOW() )";
                } else {
                    $first = "'" . $date_from . "'";
                    $last = "'" . $date_to . "'";
                }
            }
            $machine = "";
            if ($request->get('machine') != "") {
                $machine = "AND machine = '" . $request->get('machine') . "'";
            }

            $hour_check = "";
            if ($request->get('hour_check') != "") {
                $hour_check = "AND hour_check = '" . $request->get('hour_check') . "'";
            }
            $visual_check = DB::SELECT("SELECT
  injection_visual_checks.*,
  employee_syncs.`name`
  FROM
  `injection_visual_checks`
  LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_visual_checks.pic_check
  WHERE
  date( injection_visual_checks.created_at ) >= " . $first . " AND date( injection_visual_checks.created_at ) <= " . $last . "
  " . $machine . " " . $hour_check . "");
            $response = array(
                'status' => true,
                'visual_check' => $visual_check,
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

    public function pdfReportVisualCheck($date, $part_type)
    {
        $percentage = DB::SELECT("SELECT DISTINCT
    ( part_type ),
    hour_check,
    machine,
    part_name,
    cavity,
    color,
    molding,
    material_number,
    material_description,
    pic_check,
    employee_syncs.`name`,
    DATE(injection_visual_checks.created_at) AS created,
    (
    SELECT
    GROUP_CONCAT( CONCAT( a.point_check, '-', a.cav_detail, '-', a.result_check ) SEPARATOR '_' )
    FROM
    injection_visual_checks a
    WHERE
    a.part_type = injection_visual_checks.part_type
    AND a.hour_check = injection_visual_checks.hour_check
    AND DATE( a.created_at ) = DATE( injection_visual_checks.created_at )
    ) AS result_all,
    (
    SELECT
    GROUP_CONCAT(
    DISTINCT ( a.note ))
    FROM
    injection_visual_checks a
    WHERE
    a.part_type = injection_visual_checks.part_type
    AND a.hour_check = injection_visual_checks.hour_check
    AND DATE( a.created_at ) = DATE( injection_visual_checks.created_at )
    ) AS note,
    (
    SELECT
    GROUP_CONCAT(
    DISTINCT ( a.cav_detail ))
    FROM
    injection_visual_checks a
    WHERE
    a.part_type = injection_visual_checks.part_type
    AND a.hour_check = injection_visual_checks.hour_check
    AND DATE( a.created_at ) = DATE( injection_visual_checks.created_at )
    ) AS cav_detail
    FROM
    `injection_visual_checks`
    LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_visual_checks.pic_check
    WHERE
    DATE( injection_visual_checks.created_at ) = '" . $date . "'
    AND part_type = '" . $part_type . "'");

        if (count($percentage) > 0) {
            if (strpos($percentage[0]->part_name, 'YRS') !== false) {
                if (strpos($percentage[0]->part_type, 'HJ') !== false) {
                    $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_HJ')->get();
                } else if (strpos($percentage[0]->part_type, 'FJ') !== false) {
                    $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_FJ')->get();
                } else if (strpos($percentage[0]->part_type, 'MJ') !== false) {
                    $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_MJ')->get();
                } else if (strpos($percentage[0]->part_type, 'BJ') !== false) {
                    $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_BJ')->get();
                }
            }

            if (strpos($percentage[0]->part_name, 'YRF') !== false) {
                if (strpos($percentage[0]->part_type, 'A YRF H') !== false) {
                    $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_HJ')->get();
                } else if (strpos($percentage[0]->part_type, 'A YRF B') !== false) {
                    $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_MJ')->get();
                } else if (strpos($percentage[0]->part_type, 'A YRF S') !== false) {
                    $point_check = InjectionVisualPointCheck::where('part_type', 'YRS_BJ')->get();
                }
            }
            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'landscape');

            return view('injection.pdf_visual_check', array(
                'percentage' => $percentage,
                'point_check' => $point_check,
            ));
        }
    }

    public function indexVisualCheckMonitoring()
    {

        $title = 'Injection Visual Check Monitoring';
        $title_jp = '';
        return view('injection.visual_check_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'mesin' => $this->mesin,
            'hour' => $this->hour,
        ))->with('page', 'Injection Visual Check')->with('jpn', '');
    }

    public function fetchVisualCheckMonitoring(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = "'" . date('Y-m-01') . "'";
                    $last = "'" . date('Y-m-t') . "'";
                    $dateTitleFirst = date('d M Y', strtotime(date('Y-m-01')));
                    $dateTitleLast = date('d M Y', strtotime(date('Y-m-t')));
                } else {
                    $first = "'" . date('Y-m-01') . "'";
                    $last = "'" . $date_to . "'";
                    $dateTitleFirst = date('d M Y', strtotime(date('Y-m-01')));
                    $dateTitleLast = date('d M Y', strtotime($date_to));
                }
            } else {
                if ($date_to == "") {
                    $first = "'" . $date_from . "'";
                    $last = "'" . date('Y-m-t') . "'";
                    $dateTitleFirst = date('d M Y', strtotime($date_from));
                    $dateTitleLast = date('d M Y', strtotime(date('Y-m-t')));
                } else {
                    $first = "'" . $date_from . "'";
                    $last = "'" . $date_to . "'";
                    $dateTitleFirst = date('d M Y', strtotime($date_from));
                    $dateTitleLast = date('d M Y', strtotime($date_to));
                }
            }

            $visual_check = DB::SELECT("SELECT
  week_date AS date,
  ( SELECT GROUP_CONCAT( hour_check ) FROM injection_visual_check_schedules ) AS schedules,
  ( SELECT GROUP_CONCAT( DISTINCT ( a.hour_check )) FROM injection_visual_checks a WHERE DATE( a.created_at ) = weekly_calendars.week_date ) AS hour_check
  FROM
  weekly_calendars
  WHERE
  week_date >= " . $first . "
  AND week_date <= " . $last . "
  AND remark != 'H'");

            $response = array(
                'status' => true,
                'visual_check' => $visual_check,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
                'hour' => $this->hour,
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

    public function fetchDetailVisualCheckMonitoring(Request $request)
    {
        try {
            $details = DB::SELECT("SELECT DISTINCT
      ( hour_check ),
      material_number,
      material_description,
      part_name,
      part_type,
      machine,
      color,
      cavity,
      molding,
      dryer,
      lot_number,
      (
      SELECT
      min( a.created_at )
      FROM
      injection_visual_checks a
      WHERE
      DATE( a.created_at ) = DATE( injection_visual_checks.created_at )
      AND a.hour_check = injection_visual_checks.hour_check
      ) AS created,
      (
      SELECT
      sum( CASE WHEN a.result_check = 'OK' THEN 1 ELSE 0 END )
      FROM
      injection_visual_checks a
      WHERE
      DATE( a.created_at ) = DATE( injection_visual_checks.created_at )
      AND a.hour_check = injection_visual_checks.hour_check
      ) AS ok,
      (
      SELECT
      sum( CASE WHEN a.result_check = 'NG' THEN 1 ELSE 0 END )
      FROM
      injection_visual_checks a
      WHERE
      DATE( a.created_at ) = DATE( injection_visual_checks.created_at )
      AND a.hour_check = injection_visual_checks.hour_check
      ) AS ng,
      (
      SELECT
      sum( CASE WHEN a.result_check = 'NS' THEN 1 ELSE 0 END )
      FROM
      injection_visual_checks a
      WHERE
      DATE( a.created_at ) = DATE( injection_visual_checks.created_at )
      AND a.hour_check = injection_visual_checks.hour_check
      ) AS ns,
      employee_syncs.`name`,
      pic_check
      FROM
      `injection_visual_checks`
      LEFT JOIN employee_syncs ON employee_syncs.employee_id = pic_check
      WHERE
      date( injection_visual_checks.created_at ) = '" . $request->get('date') . "'
      AND employee_syncs.end_date IS NULL");

            $dateTitle = date('d M Y', strtotime($request->get('date')));
            $response = array(
                'status' => true,
                'details' => $details,
                'dateTitle' => $dateTitle,
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

    public function approvalVisualCheck($id)
    {
        $id_all = explode('_', $id);
        for ($i = 0; $i < count($id_all); $i++) {
            $visual = InjectionVisualCheck::where('id', $id_all[$i])->first();
            $visual->car_approver_id = Auth::user()->username;
            $visual->car_approver_name = Auth::user()->name;
            $visual->car_approved_at = date('Y-m-d H:i:s');
            $visual->save();
        }
        return view('injection.visual_check_approval')->with('head', 'Injection Visual Check Approval')->with('message', 'Visual Check CAR has been approved.')->with('page', 'Injection Visual Check Approval');
    }

    public function indexCleaning()
    {

        $title = 'Injection Equipment Cleaning';
        $title_jp = '';
        $dayname = date('D');
        return view('injection.cleaning', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'dayname' => $dayname,
        ))->with('page', 'Injection Equipment Cleaning')->with('jpn', '');
    }

    public function fetchCleaningPoint(Request $request)
    {
        try {
            $point = DB::SELECT("SELECT DISTINCT
      (
      CONCAT( point_check_type, ' ', point_check_machine )) AS point,
      CONCAT( point_check_type, '_', point_check_machine ) AS point_id
      FROM
      `injection_cleaning_points`
      WHERE
      point_check_type = '" . $request->get('point') . "'");

            $response = array(
                'status' => true,
                'point' => $point,
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

    public function fetchCleaningPointDetail(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', 'injection_cleaning')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $cleaning_id = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();
            if ($request->get('ada') == 'Ada Pengecekan') {
                $check = DB::connection('ympimis_2')->table('injection_cleaning_timelines')->where('point_check_type', $request->get('tools'))->where('point_check_machine', $request->get('point'))->where('finished_at', null)->first();
                if (!$check) {
                    $timeline_start = DB::connection('ympimis_2')->table('injection_cleaning_timelines')->insert([
                        'cleaning_id' => $cleaning_id,
                        'point_check_type' => $request->get('tools'),
                        'point_check_machine' => $request->get('point'),
                        'started_at' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $started_at = date('Y-m-d H:i:s');
                } else {
                    $started_at = $check->started_at;
                }
                $point = DB::SELECT("SELECT *
        FROM
        `injection_cleaning_points`
        WHERE
        point_check_type = '" . $request->get('tools') . "'
        and point_check_machine = '" . $request->get('point') . "'");
                $response = array(
                    'status' => true,
                    'point' => $point,
                    'started_at' => $started_at,
                    'check_time' => date('Y-m-d H:i:s'),
                );
                return Response::json($response);
            } else {
                $point = DB::SELECT("SELECT *
        FROM
        `injection_cleaning_points`
        WHERE
        point_check_type = '" . $request->get('tools') . "'
        and point_check_machine = '" . $request->get('point') . "'");
                for ($i = 0; $i < count($point); $i++) {
                    InjectionCleaning::create([
                        'cleaning_id' => $cleaning_id,
                        'check_time' => date('Y-m-d H:i:s'),
                        'point_check_type' => $point[$i]->point_check_type,
                        'point_check_index' => $point[$i]->point_check_index,
                        'point_check_machine' => $point[$i]->point_check_machine,
                        'pic_check' => $request->get('pic_check'),
                        'point_check_name' => $point[$i]->point_check_name,
                        'point_check_standard' => $point[$i]->point_check_standard,
                        'result_check' => '-',
                        'note' => 'Tidak Ada Pengecekan',
                        'created_by' => Auth::user()->id,
                    ]);
                }
                $timeline = DB::connection('ympimis_2')->table('injection_cleaning_timelines')->insert([
                    'cleaning_id' => $cleaning_id,
                    'point_check_type' => $request->get('tools'),
                    'point_check_machine' => $request->get('point'),
                    'started_at' => date('Y-m-d H:i:s'),
                    'finished_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $response = array(
                    'status' => true,
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

    public function deleteCleaningTimeline(Request $request)
    {
        try {
            $check = DB::connection('ympimis_2')->table('injection_cleaning_timelines')->where('point_check_type', $request->get('point_check_type'))->where('point_check_machine', $request->get('point_check_machine'))->where('finished_at', null)->first();
            if ($check) {
                $delete = DB::connection('ympimis_2')->table('injection_cleaning_timelines')->where('point_check_type', $request->get('point_check_type'))->where('point_check_machine', $request->get('point_check_machine'))->where('finished_at', null)->delete();
            }

            $response = array(
                'status' => true,
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

    public function updateCleaningTimeline(Request $request)
    {
        try {
            $check = DB::connection('ympimis_2')->table('injection_cleaning_timelines')->where('point_check_type', $request->input('point_check_type'))->where('point_check_machine', $request->input('point_check_machine'))->where('finished_at', null)->first();
            $code_generator = CodeGenerator::where('note', '=', 'injection_cleaning')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $cleaning_id = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;

            if ($check) {
                $update = DB::connection('ympimis_2')->table('injection_cleaning_timelines')->where('point_check_type', $request->input('point_check_type'))->where('point_check_machine', $request->input('point_check_machine'))->update([
                    'finished_at' => date('Y-m-d H:i:s'),
                ]);
                $cleaning_id = $check->cleaning_id;
            } else {
                $timeline = DB::connection('ympimis_2')->table('injection_cleaning_timelines')->insert([
                    'cleaning_id' => $cleaning_id,
                    'point_check_type' => $request->input('point_check_type'),
                    'point_check_machine' => $request->input('point_check_machine'),
                    'started_at' => date('Y-m-d H:i:s'),
                    'finished_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $code_generator->save();
            }
            $response = array(
                'status' => true,
                'cleaning_id' => $cleaning_id,
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

    public function inputCleaning(Request $request)
    {
        try {
            if ($request->file('fileData') != null) {
                $tujuan_upload = 'data_file/injection/cleaning';
                $file = $request->file('fileData');
                $filename = md5($request->input('id_point') . $request->input('check_time') . date('YmdHisa')) . '.' . $request->input('extension');
                $file->move($tujuan_upload, $filename);

                InjectionCleaning::create([
                    'cleaning_id' => $request->input('cleaning_id'),
                    'check_time' => $request->input('check_time'),
                    'point_check_type' => $request->input('point_check_type'),
                    'point_check_machine' => $request->input('point_check_machine'),
                    'pic_check' => $request->input('pic_check'),
                    'point_check_name' => $request->input('point_check_name'),
                    'point_check_standard' => $request->input('point_check_standard'),
                    'result_check' => $request->input('result_check'),
                    'note' => $request->input('note'),
                    'point_check_index' => $request->input('point_check_index'),
                    'result_image' => $filename,
                    'created_by' => Auth::user()->id,
                ]);

                $response = array(
                    'status' => true,
                );
                return Response::json($response);
            } else {
                // $response = array(
                //     'status' => false,
                //     'message' => 'Upload Photo on Point '.$request->input('point_check_index')
                // );
                // return Response::json($response);
                InjectionCleaning::create([
                    'cleaning_id' => $request->input('cleaning_id'),
                    'check_time' => $request->input('check_time'),
                    'point_check_type' => $request->input('point_check_type'),
                    'point_check_machine' => $request->input('point_check_machine'),
                    'pic_check' => $request->input('pic_check'),
                    'point_check_name' => $request->input('point_check_name'),
                    'point_check_standard' => $request->input('point_check_standard'),
                    'result_check' => $request->input('result_check'),
                    'note' => $request->input('note'),
                    'point_check_index' => $request->input('point_check_index'),
                    'created_by' => Auth::user()->id,
                ]);

                $response = array(
                    'status' => true,
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

    public function indexReportCleaning()
    {
        $point = DB::SELECT("SELECT DISTINCT
    (
    CONCAT( point_check_type, ' ', point_check_machine )) AS point,
    CONCAT( point_check_type, '_', point_check_machine ) AS point_id
    FROM
    `injection_cleaning_points`");
        $title = 'Injection Cleaning Report';
        $title_jp = '';
        return view('injection.report_cleaning', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'point' => $point,
        ))->with('page', 'Injection Cleaning Report')->with('jpn', '');
    }

    public function fetchReportCleaning(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = "'" . date('Y-m-01') . "'";
                    $last = "'" . date('Y-m-t') . "'";
                    $dateTitleFirst = date('d M Y', strtotime(date('Y-m-01')));
                    $dateTitleLast = date('d M Y', strtotime(date('Y-m-t')));
                } else {
                    $first = "'" . date('Y-m-01') . "'";
                    $last = "'" . $date_to . "'";
                    $dateTitleFirst = date('d M Y', strtotime(date('Y-m-01')));
                    $dateTitleLast = date('d M Y', strtotime($date_to));
                }
            } else {
                if ($date_to == "") {
                    $first = "'" . $date_from . "'";
                    $last = "'" . date('Y-m-t') . "'";
                    $dateTitleFirst = date('d M Y', strtotime($date_from));
                    $dateTitleLast = date('d M Y', strtotime(date('Y-m-t')));
                } else {
                    $first = "'" . $date_from . "'";
                    $last = "'" . $date_to . "'";
                    $dateTitleFirst = date('d M Y', strtotime($date_from));
                    $dateTitleLast = date('d M Y', strtotime($date_to));
                }
            }

            if ($request->get('point_check') == '') {
                $point_check = '';
            } else {
                $type = explode('_', $request->get('point_check'))[0];
                $machine = explode('_', $request->get('point_check'))[1];
                $point_check = "AND point_check_type = '" . $type . "' and point_check_machine = '" . $machine . "'";
            }

            $cleaning = DB::SELECT("SELECT
  injection_cleanings.*,
  employee_syncs.`name`
  FROM
  injection_cleanings
  LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_cleanings.pic_check
  WHERE
  DATE( check_time ) >= " . $first . "
  AND DATE( check_time ) <= " . $last . "
  " . $point_check . "
  ORDER BY
  injection_cleanings.created_at desc,
  injection_cleanings.point_check_index");

            $response = array(
                'status' => true,
                'cleaning' => $cleaning,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
            );
            return Response::json($response);
            $response = array(
                'status' => true,
                'cleaning' => $cleaning,
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

    public function indexCleaningMonitoring()
    {
        return view('injection.cleaning_monitoring')
            ->with('title', 'Cleaning Monitoring')
            ->with('title_jp', '')
            ->with('page', 'Cleaning Monitoring');
    }

    public function fetchCleaningMonitoring(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = "'" . date('Y-m-01') . "'";
                    $last = "'" . date('Y-m-d') . "'";
                    $dateTitleFirst = date('d M Y', strtotime(date('Y-m-01')));
                    $dateTitleLast = date('d M Y', strtotime(date('Y-m-d')));
                } else {
                    $first = "'" . date('Y-m-01') . "'";
                    $last = "'" . $date_to . "'";
                    $dateTitleFirst = date('d M Y', strtotime(date('Y-m-01')));
                    $dateTitleLast = date('d M Y', strtotime($date_to));
                }
            } else {
                if ($date_to == "") {
                    $first = "'" . $date_from . "'";
                    $last = "'" . date('Y-m-d') . "'";
                    $dateTitleFirst = date('d M Y', strtotime($date_from));
                    $dateTitleLast = date('d M Y', strtotime(date('Y-m-d')));
                } else {
                    $first = "'" . $date_from . "'";
                    $last = "'" . $date_to . "'";
                    $dateTitleFirst = date('d M Y', strtotime($date_from));
                    $dateTitleLast = date('d M Y', strtotime($date_to));
                }
            }

            $week_date = DB::SELECT("SELECT
  week_date,
  remark
  FROM
  weekly_calendars
  WHERE
  week_date <= " . $last . " AND week_date >= " . $first . "
  and remark != 'H'
  ");
            $cleanings = [];
            for ($i = 0; $i < count($week_date); $i++) {
                $today = $week_date[$i]->week_date;
                $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
                foreach ($weekly_calendars as $key) {
                    if ($key->week_date == $week_date[$i]->week_date) {
                        if ($key->remark == 'H') {
                            $today = date('Y-m-d', strtotime("+1 day", strtotime($today)));
                        }
                    }
                }
                $cleaning = DB::SELECT("SELECT
    '" . $week_date[$i]->week_date . "' AS week_date,
    SUM( a.qty_check )- SUM( a.qty_not_act ) AS qty_check,
    sum( a.qty_act )- SUM( a.qty_not_act ) AS qty_act
    FROM
    (
    SELECT
    COUNT(
    DISTINCT (
    CONCAT( point_check_type, point_check_machine ))) AS qty_check,
    0 AS qty_act,
    0 AS qty_not_act
    FROM
    `injection_cleaning_points`
    WHERE
    point_check_schedule = 'daily' UNION ALL
    SELECT
    IF
    (
    DAYNAME( '" . $week_date[$i]->week_date . "' ) = 'Monday'
    OR DAYNAME( '" . $week_date[$i]->week_date . "' ) = 'Wednesday'
    OR DAYNAME( '" . $week_date[$i]->week_date . "' ) = 'Friday',(
    COUNT(
    DISTINCT (
    CONCAT( point_check_type, point_check_machine )))),
    0
    ) AS qty_check,
    0 AS qty_act,
    0 AS qty_not_act
    FROM
    `injection_cleaning_points`
    WHERE
    point_check_schedule = 'weekly' 
    UNION ALL
    SELECT
    IF
        (
            DAYNAME( '" . $week_date[$i]->week_date . "' ) = 'Monday' 
            ,(
                COUNT(
                    DISTINCT (
                    CONCAT( point_check_type, point_check_machine )))),
            0 
        ) AS qty_check,
        0 AS qty_act,
        0 AS qty_not_act 
    FROM
        `injection_cleaning_points` 
    WHERE
        point_check_schedule = 'early_week'
        UNION ALL
    SELECT
    IF
        (
            '" . $today . "' <= '".date('Y-m',strtotime($week_date[$i]->week_date))."-03' AND '" . $today . "' >= '".date('Y-m',strtotime($week_date[$i]->week_date))."-01'
            ,(
                COUNT(
                    DISTINCT (
                    CONCAT( point_check_type, point_check_machine )))),
            0 
        ) AS qty_check,
        0 AS qty_act,
        0 AS qty_not_act 
    FROM
        `injection_cleaning_points` 
    WHERE
        point_check_schedule = 'monthly'
        UNION ALL
    SELECT
    0 AS qty_check,
    COUNT(
    DISTINCT (
    CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine ))) AS qty_act,
    0 AS qty_not_act
    FROM
    injection_cleanings
    JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine )
    WHERE
    injection_cleaning_points.point_check_schedule = 'daily'
    AND DATE( injection_cleanings.created_at ) = '" . $week_date[$i]->week_date . "' UNION ALL
    SELECT
    0 AS qty_check,
    IF
    (
    DAYNAME( '" . $week_date[$i]->week_date . "' ) = 'Monday'
    OR DAYNAME( '" . $week_date[$i]->week_date . "' ) = 'Wednesday'
    OR DAYNAME( '" . $week_date[$i]->week_date . "' ) = 'Friday',
    COUNT(
    DISTINCT (
    CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine ))),
    0
    ) AS qty_act,
    0 AS qty_not_act
    FROM
    injection_cleanings
    JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine )
    WHERE
    injection_cleaning_points.point_check_schedule = 'weekly'
    AND DATE( injection_cleanings.created_at ) = '" . $week_date[$i]->week_date . "' UNION ALL
    SELECT
    0 AS qty_check,
    0 AS qty_act,
    COUNT(
    DISTINCT (
    CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine ))) AS qty_not_act
    FROM
    injection_cleanings
    JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine )
    WHERE
    injection_cleaning_points.point_check_schedule = 'daily'
    AND DATE( injection_cleanings.created_at ) = '" . $week_date[$i]->week_date . "'
    AND injection_cleanings.note = 'Tidak Ada Pengecekan' UNION ALL
    SELECT
    0 AS qty_check,
    0 AS qty_act,
    IF
    (
    DAYNAME( '" . $week_date[$i]->week_date . "' ) = 'Monday'
    OR DAYNAME( '" . $week_date[$i]->week_date . "' ) = 'Wednesday'
    OR DAYNAME( '" . $week_date[$i]->week_date . "' ) = 'Friday',
    COUNT(
    DISTINCT (
    CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine ))),
    0
    ) AS qty_not_act
    FROM
    injection_cleanings
    JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine )
    WHERE
    injection_cleaning_points.point_check_schedule = 'weekly'
    AND DATE( injection_cleanings.created_at ) = '" . $week_date[$i]->week_date . "'
    AND injection_cleanings.note = 'Tidak Ada Pengecekan'
    UNION ALL
    SELECT
        0 AS qty_check,
        0 AS qty_act,
    IF
        (
            DAYNAME( '" . $week_date[$i]->week_date . "' ) = 'Monday',
            COUNT(
                DISTINCT (
                CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine ))),
            0 
        ) AS qty_not_act 
    FROM
        injection_cleanings
        JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine ) 
    WHERE
        injection_cleaning_points.point_check_schedule = 'early_week' 
        AND DATE( injection_cleanings.created_at ) >= '" . $week_date[$i]->week_date . "' 
        AND DATE( injection_cleanings.created_at ) <= DATE_ADD('" . $week_date[$i]->week_date . "', INTERVAL 5 DAY)
    AND injection_cleanings.note = 'Tidak Ada Pengecekan' 
    UNION ALL
    SELECT
        0 AS qty_check,
        0 AS qty_act,
    IF
        (
            '" . $today . "' <= '".date('Y-m',strtotime($week_date[$i]->week_date))."-03' AND '" . $today . "' >= '".date('Y-m',strtotime($week_date[$i]->week_date))."-01',
            COUNT(
                DISTINCT (
                CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine ))),
            0 
        ) AS qty_not_act 
    FROM
        injection_cleanings
        JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine ) 
    WHERE
        injection_cleaning_points.point_check_schedule = 'monthly' 
        AND DATE_FORMAT( injection_cleanings.created_at,'%Y-%m' ) >= '" . date('Y-m',strtotime($today)) . "' 
    AND injection_cleanings.note = 'Tidak Ada Pengecekan' 
  )a");
                array_push($cleanings, $cleaning);
            }

            $now = date('Y-m-d');

            $response = array(
                'status' => true,
                'cleaning' => $cleanings,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
                'week_date' => $week_date,
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

    public function fetchCleaningMonitoringDetail(Request $request)
    {
        try {
            $date = $request->get('date');
            $dayname = date('D', strtotime($date));
            if ($request->get('name') == 'Sudah Dilakukan') {
                $union = "";
                $union2 = "";
                $union3 = "";
                if ($dayname == 'Mon' || $dayname == 'Wed' || $dayname == 'Fri') {
                    $union = "UNION ALL
                    SELECT DISTINCT
                    (
                    CONCAT( injection_cleanings.point_check_type, ' ', injection_cleanings.point_check_machine )) AS machine,
                    sum( CASE WHEN injection_cleanings.result_check = 'NS' THEN 1 ELSE 0 END ) AS ns,
                    sum( CASE WHEN injection_cleanings.result_check = 'OK' THEN 1 ELSE 0 END ) AS ok,
                    sum( CASE WHEN injection_cleanings.result_check = 'NG' THEN 1 ELSE 0 END ) AS ng,
                    employee_syncs.employee_id,
                    employee_syncs.`name`
                    FROM
                    injection_cleanings
                    JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine, injection_cleaning_points.point_check_index ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine, injection_cleanings.point_check_index )
                    JOIN employee_syncs ON employee_syncs.employee_id = injection_cleanings.pic_check
                    WHERE
                    DATE( injection_cleanings.created_at ) = '" . $date . "'
                    AND injection_cleaning_points.point_check_schedule = 'weekly'
                    GROUP BY
                    CONCAT( injection_cleanings.point_check_type, ' ', injection_cleanings.point_check_machine ),
                    employee_syncs.employee_id,
                    employee_syncs.`name`,
                    injection_cleanings.point_check_type,
                    injection_cleanings.point_check_machine";
                    if ($dayname == 'Mon') {
                        $union2 = "UNION ALL
                        SELECT DISTINCT
                        (
                        CONCAT( injection_cleanings.point_check_type, ' ', injection_cleanings.point_check_machine )) AS machine,
                        sum( CASE WHEN injection_cleanings.result_check = 'NS' THEN 1 ELSE 0 END ) AS ns,
                        sum( CASE WHEN injection_cleanings.result_check = 'OK' THEN 1 ELSE 0 END ) AS ok,
                        sum( CASE WHEN injection_cleanings.result_check = 'NG' THEN 1 ELSE 0 END ) AS ng,
                        employee_syncs.employee_id,
                        employee_syncs.`name`
                        FROM
                        injection_cleanings
                        JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine, injection_cleaning_points.point_check_index ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine, injection_cleanings.point_check_index )
                        JOIN employee_syncs ON employee_syncs.employee_id = injection_cleanings.pic_check
                        WHERE
                        DATE( injection_cleanings.created_at ) >= '".$date."' AND DATE( injection_cleanings.created_at ) <= '".date('Y-m-d',strtotime($date.' + 5 DAYS'))."'
                        AND injection_cleaning_points.point_check_schedule = 'early_week'
                        GROUP BY
                        CONCAT( injection_cleanings.point_check_type, ' ', injection_cleanings.point_check_machine ),
                        employee_syncs.employee_id,
                        employee_syncs.`name`,
                        injection_cleanings.point_check_type,
                        injection_cleanings.point_check_machine";
                    }

                    if ($date >= date('Y-m-01',strtotime($date)) && $date <= date('Y-m-04',strtotime($date))) {
                        $union3 = "UNION ALL
                        SELECT DISTINCT
                        (
                        CONCAT( injection_cleanings.point_check_type, ' ', injection_cleanings.point_check_machine )) AS machine,
                        sum( CASE WHEN injection_cleanings.result_check = 'NS' THEN 1 ELSE 0 END ) AS ns,
                        sum( CASE WHEN injection_cleanings.result_check = 'OK' THEN 1 ELSE 0 END ) AS ok,
                        sum( CASE WHEN injection_cleanings.result_check = 'NG' THEN 1 ELSE 0 END ) AS ng,
                        employee_syncs.employee_id,
                        employee_syncs.`name`
                        FROM
                        injection_cleanings
                        JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine, injection_cleaning_points.point_check_index ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine, injection_cleanings.point_check_index )
                        JOIN employee_syncs ON employee_syncs.employee_id = injection_cleanings.pic_check
                        WHERE
                        DATE_FORMAT( injection_cleanings.created_at,'%Y-%m' ) >= '".date('Y-m',strtotime($date))."''
                        AND injection_cleaning_points.point_check_schedule = 'monthly'
                        GROUP BY
                        CONCAT( injection_cleanings.point_check_type, ' ', injection_cleanings.point_check_machine ),
                        employee_syncs.employee_id,
                        employee_syncs.`name`,
                        injection_cleanings.point_check_type,
                        injection_cleanings.point_check_machine";
                    }
                }
                $detail = DB::SELECT("SELECT DISTINCT
                    (
                    CONCAT( injection_cleanings.point_check_type, ' ', injection_cleanings.point_check_machine )) AS machine,
                    sum( CASE WHEN injection_cleanings.result_check = 'NS' THEN 1 ELSE 0 END ) AS ns,
                    sum( CASE WHEN injection_cleanings.result_check = 'OK' THEN 1 ELSE 0 END ) AS ok,
                    sum( CASE WHEN injection_cleanings.result_check = 'NG' THEN 1 ELSE 0 END ) AS ng,
                    employee_syncs.employee_id,
                    employee_syncs.`name`
                    FROM
                    injection_cleanings
                    JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine, injection_cleaning_points.point_check_index ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine, injection_cleanings.point_check_index )
                    JOIN employee_syncs ON employee_syncs.employee_id = injection_cleanings.pic_check
                    WHERE
                    DATE( injection_cleanings.created_at ) = '" . $date . "'
                    AND injection_cleaning_points.point_check_schedule = 'daily'
                    GROUP BY
                    CONCAT( injection_cleanings.point_check_type, ' ', injection_cleanings.point_check_machine ),
                    employee_syncs.employee_id,
                    employee_syncs.`name`,
                    injection_cleanings.point_check_type,
                    injection_cleanings.point_check_machine
                    " . $union . "
                    ");
            } else {
                $union = "";
                $union2 = "";
                $union3 = "";
                 if ($dayname == 'Mon' || $dayname == 'Wed' || $dayname == 'Fri') {
                    // $union = "UNION ALL
                    // SELECT
                    // '' AS not_yet,
                    // GROUP_CONCAT( DISTINCT ( CONCAT( injection_cleanings.point_check_type, ' ', injection_cleanings.point_check_machine )) ) AS done
                    // FROM
                    // injection_cleanings
                    // JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine, injection_cleaning_points.point_check_index ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine, injection_cleanings.point_check_index )
                    // WHERE
                    // injection_cleaning_points.point_check_schedule = 'weekly'
                    // AND DATE( injection_cleanings.created_at ) = '" . $date . "'";
                    $union = "SELECT
                        CONCAT( point_check_type, ' ', point_check_machine ) AS not_yet,
                        '' AS done 
                    FROM
                        injection_cleaning_points 
                    WHERE
                        injection_cleaning_points.point_check_schedule = 'weekly' 
                        AND CONCAT( point_check_type, ' ', point_check_machine ) NOT IN ( SELECT DISTINCT ( CONCAT( point_check_type, ' ', point_check_machine ) ) AS done FROM injection_cleanings WHERE DATE( created_at ) = '".$date."' ) UNION ALL";
                    if ($dayname == 'Mon') {
                        $union2 = "SELECT
                            CONCAT( point_check_type, ' ', point_check_machine ) AS not_yet,
                            '' AS done 
                        FROM
                            injection_cleaning_points 
                        WHERE
                            injection_cleaning_points.point_check_schedule = 'early_week' 
                            AND CONCAT( point_check_type, ' ', point_check_machine ) NOT IN ( SELECT DISTINCT ( CONCAT( point_check_type, ' ', point_check_machine ) ) AS done FROM injection_cleanings WHERE DATE( created_at ) >= '".$date."' AND DATE( created_at ) <= '".date('Y-m-d',strtotime($date.' + 5 DAYS'))."' ) UNION ALL";
                    }
                    if ($date >= date('Y-m-01',strtotime($date)) && $date <= date('Y-m-04',strtotime($date))) {
                        $union3 = "SELECT
                            CONCAT( point_check_type, ' ', point_check_machine ) AS not_yet,
                            '' AS done 
                        FROM
                            injection_cleaning_points 
                        WHERE
                            injection_cleaning_points.point_check_schedule = 'monthly' 
                            AND CONCAT( point_check_type, ' ', point_check_machine ) NOT IN ( SELECT DISTINCT ( CONCAT( point_check_type, ' ', point_check_machine ) ) AS done FROM injection_cleanings WHERE DATE_FORMAT( created_at,'%Y-%m' ) >= '".date('Y-m',strtotime($date))."' ) UNION ALL";
                    }
                }
                $details = DB::SELECT("SELECT DISTINCT
                    ( a.not_yet ) AS not_yet 
                FROM
                    (
                    ".$union."
                    ".$union2."
                    ".$union3."
                    SELECT
                        CONCAT( point_check_type, ' ', point_check_machine ) AS not_yet,
                        '' AS done 
                    FROM
                        injection_cleaning_points 
                    WHERE
                        injection_cleaning_points.point_check_schedule = 'daily' 
                    AND CONCAT( point_check_type, ' ', point_check_machine ) NOT IN ( SELECT DISTINCT ( CONCAT( point_check_type, ' ', point_check_machine ) ) AS done FROM injection_cleanings WHERE DATE( created_at ) = '".$date."' ) 
                    ) a");

                $detail = [];
                // $not_yet = explode(',', $details[0]->not_yet);
                for ($i = 0; $i < count($details); $i++) {
                    array_push($detail, $details[$i]->not_yet);
                }
            }

            $dateTitle = date('d M Y', strtotime($request->get('date')));
            $response = array(
                'status' => true,
                'detail' => $detail,
                'dateTitle' => $dateTitle,
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

    public function indexStockMonitoringDaily()
    {
        $color = DB::SELECT('SELECT DISTINCT(color) FROM `injection_parts`');

        return view('injection.stock_monitoring3')
            ->with('mesin', $this->mesin)
            ->with('color', $color)
            ->with('part', $this->part)
            ->with('title', 'Injection Stock Monitoring')
            ->with('title_jp', '成形品在庫の監視');
    }

    public function fetchStockMonitoringDaily(Request $request)
    {
        try {
            $id_user = Auth::id();

            if ($request->get('color') == "" || $request->get('color') == "All") {
                $color = "";
            } else {
                $color = "AND TRIM(
      RIGHT(
      c.part,
      (LENGTH(c.part) - LOCATE('(',c.part))
    )) =  '" . $request->get('color') . ")'";
            }

            if ($request->get('part') == "" || $request->get('part') == "All") {
                $part = "";
            } else {
                $part = "and SPLIT_STRING(c.part, ' ',1) = '" . $request->get('part') . "'";
            }

            $j = 4;
            $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $nextdayplus1) {
                    if ($key->remark == 'H') {
                        $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                    }
                }
            }

            $j = 5;
            $nextdayplus2 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $nextdayplus2) {
                    if ($key->remark == 'H' || $nextdayplus2 == $nextdayplus1) {
                        $nextdayplus2 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                    }
                }
            }

            $j = 1;
            $nextday = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $nextday) {
                    if ($key->remark == 'H') {
                        $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                    }
                }
            }

            $first = date('Y-m-01');
            $now = date('Y-m-d');

            $yesterday = date('Y-m-d', strtotime(' -1 days'));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars` order by week_date desc");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $yesterday) {
                    if ($key->remark == 'H') {
                        $yesterday = date('Y-m-d', strtotime($yesterday . '-1 days'));
                    }
                }
            }

            if ($request->get('date') == '' || $request->get('date') == date('Y-m-d')) {
                $rc11 = "SELECT quantity FROM injection_inventories WHERE location = 'RC11' AND material_number = gmc AND injection_inventories.deleted_at IS NULL";
                $rc91 = "SELECT quantity FROM injection_inventories WHERE location = 'RC91' AND material_number = gmc AND injection_inventories.deleted_at IS NULL";
            } else {
                $rc11 = "SELECT quantity FROM injection_inventory_histories WHERE location = 'RC11' AND material_number = gmc AND injection_inventory_histories.deleted_at IS NULL and date = '" . $request->get('date') . "'";
                $rc91 = "SELECT quantity FROM injection_inventory_histories WHERE location = 'RC91' AND material_number = gmc AND injection_inventory_histories.deleted_at IS NULL and date = '" . $request->get('date') . "'";
            }

            $ymes = DB::connection('ymes_rio')->select("SELECT
    *
  FROM
    vd_mes0010
  WHERE
    ( location_code = 'RC11' AND unit_code = 'PC' )
    OR ( location_code = 'RC91' AND unit_code = 'PC' )");

            $ymes_new = [];

            for ($i = 0; $i < count($ymes); $i++) {
                $stock = round($ymes[$i]->stockqty, 1);
                if ($ymes[$i]->location_code == 'RC11') {

                    $temp = DB::connection('ympimis_2')->table('production_result_temps')->where('material_number', $ymes[$i]->item_code)->where('issue_location', $ymes[$i]->location_code)->get();
                    if (count($temp) > 0) {
                        for ($j = 0; $j < count($temp); $j++) {
                            $stock = $stock + $temp[$j]->quantity;
                        }
                    }

                    // $result = DB::connection('ympimis_2')->table('production_results')->where('material_number',$ymes[$i]->item_code)->where('issue_location',$ymes[$i]->location_code)->where('synced',null)->get();
                    // if (count($result) > 0) {
                    //   for ($j=0; $j < count($result); $j++) {
                    //     $stock = $stock + $result[$j]->quantity;
                    //   }
                    // }

                    $ymes_new[] = array(
                        'location' => $ymes[$i]->location_code,
                        'material_number' => $ymes[$i]->item_code,
                        'stock' => $stock,
                    );
                } else {
                    $ymes_new[] = array(
                        'location' => $ymes[$i]->location_code,
                        'material_number' => $ymes[$i]->item_code,
                        'stock' => $stock,
                    );
                }
            }

            $nextmonth = date('Y-m-t', strtotime('+ 1 MONTH'));
            $data_skeleton = DB::SELECT("SELECT
    c.part,
    c.material_number,
    TRIM(
    RIGHT(
    c.part,
    (LENGTH(c.part) - LOCATE('(',c.part))
    )
    ) as color,
    SPLIT_STRING(c.part, ' ',1) as parts,
    SUM( c.stock ) AS stock,
    SUM( c.stock_assy ) AS stock_assy,
    SUM( c.plan ) AS plan
    FROM
    (
    SELECT
    CONCAT( UPPER( injection_parts.part_code ), ' (', injection_parts.color, ')' ) AS part,
    gmc as material_number,
    COALESCE (( " . $rc11 . "), 0 ) AS stock,
    COALESCE (( " . $rc91 . " ), 0 ) AS stock_assy,
    0 AS plan
    FROM
    injection_parts where remark = 'injection'
    and part_type != 'mp'
    GROUP BY injection_parts.part_code,injection_parts.color,gmc,color  UNION ALL
    SELECT
    part,
    a.material_number,
    0 AS stock,
    0 AS stock_assy,
    sum( a.plan )- sum( a.stamp ) AS plan
    FROM
    (
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    injection_part_details.gmc AS material_number,
    SUM( quantity ) AS plan,
    0 AS stamp
    FROM
    production_schedules
    LEFT JOIN materials ON materials.material_number = production_schedules.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND production_schedules.due_date BETWEEN '" . $first . "'
    AND '" . $nextdayplus1 . "'
    GROUP BY
    part_code,color,gmc UNION ALL
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    injection_part_details.gmc AS material_number,
    0 AS plan,
    SUM( quantity ) AS stamp
    FROM
    flo_details
    LEFT JOIN materials ON materials.material_number = flo_details.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND DATE( flo_details.created_at ) BETWEEN '" . $first . "'
    AND '" . $now . "'
    GROUP BY
    part_code,color,gmc
    ) a
    GROUP BY
    a.part,
    a.material_number
    ) c
    where c.part not like '%YRF%' AND
    c.part not like '%IVORY%' AND
    c.part not like '%BEIGE%'
    and c.material_number != ''
    " . $color . "
    " . $part . "
    GROUP BY
    c.part,
    c.material_number
    ORDER BY color");

            // $skeleton = [];
            // for ($i=0; $i < count($data_skeleton); $i++) {

            // }

            $data_ivory = DB::SELECT("SELECT
    c.part,
    c.material_number,
    SPLIT_STRING(c.part, ' ',1) as parts,
    TRIM(
    RIGHT(
    c.part,
    (LENGTH(c.part) - LOCATE('(',c.part))
    )
    ) as color,
    SUM( c.stock ) AS stock,
    SUM( c.stock_assy ) AS stock_assy,
    SUM( c.plan ) AS plan
    FROM
    (
    SELECT
    CONCAT( UPPER( injection_parts.part_code ), ' (', injection_parts.color, ')' ) AS part,
    gmc as material_number,
    COALESCE (( " . $rc11 . "), 0 ) AS stock,
    COALESCE (( " . $rc91 . "), 0 ) AS stock_assy,
    0 AS plan
    FROM
    injection_parts where remark = 'injection'
    and part_type != 'mp'
    GROUP BY injection_parts.part_code,injection_parts.color,gmc,color  UNION ALL
    SELECT
    part,
    a.material_number,
    0 AS stock,
    0 AS stock_assy,
    sum( a.plan )- sum( a.stamp ) AS plan
    FROM
    (
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    injection_part_details.gmc AS material_number,
    SUM( quantity ) AS plan,
    0 AS stamp
    FROM
    production_schedules
    LEFT JOIN materials ON materials.material_number = production_schedules.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND production_schedules.due_date BETWEEN '" . $first . "'
    AND '" . $nextdayplus1 . "'
    GROUP BY
    part_code,color,gmc UNION ALL
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    injection_part_details.gmc AS material_number,
    0 AS plan,
    SUM( quantity ) AS stamp
    FROM
    flo_details
    LEFT JOIN materials ON materials.material_number = flo_details.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND DATE( flo_details.created_at ) BETWEEN '" . $first . "'
    AND '" . $now . "'
    GROUP BY
    part_code,color,gmc
    ) a
    GROUP BY
    a.part,
    a.material_number
    ) c
    where c.part not like '%PINK%' AND
    c.part not like '%RED%' AND
    c.part not like '%BLUE%' AND
    c.part not like '%BROWN%' AND
    c.part not like '%GREEN%'
    and c.material_number != ''
    " . $color . "
    " . $part . "
    GROUP BY
    c.part,c.material_number
    ORDER BY part");

            $datas_skeleton = [];
            $datas_ivory = [];

            foreach ($data_ivory as $key) {
                $stock_inj = 0;
                $stock_assy = 0;
                for ($j = 0; $j < count($ymes_new); $j++) {
                    if ($ymes_new[$j]['material_number'] == $key->material_number && $ymes_new[$j]['location'] == 'RC11') {
                        $stock_inj = $stock_inj + $ymes_new[$j]['stock'];
                    }
                    if ($ymes_new[$j]['material_number'] == $key->material_number && $ymes_new[$j]['location'] == 'RC91') {
                        $stock_assy = $stock_assy + $ymes_new[$j]['stock'];
                    }
                }
                $datas_ivory[] = array(
                    'part' => $key->part,
                    'color' => $key->color,
                    'stock' => $stock_inj,
                    'stock_assy' => $stock_assy,
                    'plan' => $key->plan);
            }

            foreach ($data_skeleton as $key) {
                $stock_inj = 0;
                $stock_assy = 0;
                for ($j = 0; $j < count($ymes_new); $j++) {
                    if ($ymes_new[$j]['material_number'] == $key->material_number && $ymes_new[$j]['location'] == 'RC11') {
                        $stock_inj = $stock_inj + $ymes_new[$j]['stock'];
                    }
                    if ($ymes_new[$j]['material_number'] == $key->material_number && $ymes_new[$j]['location'] == 'RC91') {
                        $stock_assy = $stock_assy + $ymes_new[$j]['stock'];
                    }
                }
                $datas_skeleton[] = array(
                    'part' => $key->part,
                    'color' => $key->color,
                    'stock' => $stock_inj,
                    'stock_assy' => $stock_assy,
                    'plan' => $key->plan);
            }

            $plan_day = DB::SELECT("SELECT
            *
    FROM
    (
    SELECT
    'RC91' AS location,
    c.part,
    SPLIT_STRING ( c.plan, '_', 1 ) AS qty,
    SPLIT_STRING ( c.plan, '_', 2 ) AS late_stock
    FROM
    (
    SELECT DISTINCT
    (
    CONCAT( part_code, ' (', color, ')' )) AS part,
    (
    SELECT
    CONCAT(
    COALESCE ( quantity, 0 ),
    '_',
    COALESCE ( DATEDIFF( due_date, '" . $now . "' ), 0 )) AS plan
    FROM
    production_schedules
    LEFT JOIN materials ON materials.material_number = production_schedules.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND production_schedules.due_date BETWEEN '" . $nextdayplus2 . "'
    AND '" . $nextmonth . "'
    AND CONCAT( part_code, ' (', color, ')' ) = CONCAT( p.part_code, ' (', p.color, ')' )
    LIMIT 1
    ) AS plan,
    0 AS stamp
    FROM
    production_schedules a
    LEFT JOIN materials m ON m.material_number = a.material_number
    LEFT JOIN injection_part_details p ON p.model = m.model
    WHERE
    m.category = 'FG'
    AND m.origin_group_code = '072'
    ) c
    GROUP BY
    c.part,
    c.plan
    ) d
    WHERE
    d.qty != 0");

            $jumlah_hari_kerja = DB::SELECT("SELECT
    count( week_date ) AS jumlah
    FROM
    weekly_calendars
    WHERE
    DATE_FORMAT( week_date, '%Y-%m' ) = DATE_FORMAT( NOW(), '%Y-%m' )
    AND remark != 'H'
    AND week_date BETWEEN DATE(
    NOW())
    AND LAST_DAY(
    NOW())");

            $all_day_month = DB::SELECT("SELECT
    count( week_date ) AS jumlah
    FROM
    weekly_calendars
    WHERE
    DATE_FORMAT( week_date, '%Y-%m' ) = DATE_FORMAT( NOW(), '%Y-%m' )
    AND remark != 'H'
    AND week_date BETWEEN CONCAT( DATE_FORMAT( NOW(), '%Y-%m' ), '-01' )
    AND LAST_DAY(
    NOW())");

            $response = array(
                'status' => true,
                'datas_skeleton' => $datas_skeleton,
                'datas_ivory' => $datas_ivory,
                'plan_day' => $plan_day,
                'day_month' => $jumlah_hari_kerja[0]->jumlah,
                'all_day_month' => $all_day_month[0]->jumlah,
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

    public function indexStockMonitoringMonthly()
    {
        $color = DB::SELECT('SELECT DISTINCT(color) FROM `injection_parts`');

        return view('injection.stock_monitoring4')
            ->with('mesin', $this->mesin)
            ->with('color', $color)
            ->with('part', $this->part)
            ->with('title', 'Injection Stock Monitoring')
            ->with('title_jp', '成形品在庫の監視');
    }

    public function fetchStockMonitoringMonthly(Request $request)
    {
        try {
            $id_user = Auth::id();

            if ($request->get('color') == "" || $request->get('color') == "All") {
                $color = "";
            } else {
                $color = "AND TRIM(
      RIGHT(
      c.part,
      (LENGTH(c.part) - LOCATE('(',c.part))
    )) =  '" . $request->get('color') . ")'";
            }

            if ($request->get('part') == "" || $request->get('part') == "All") {
                $part = "";
            } else {
                $part = "and SPLIT_STRING(c.part, ' ',1) = '" . $request->get('part') . "'";
            }

            $j = 2;
            $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $nextdayplus1) {
                    if ($key->remark == 'H') {
                        $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                    }
                }
            }

            $j = 3;
            $nextdayplus2 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $nextdayplus2) {
                    if ($key->remark == 'H' || $nextdayplus2 == $nextdayplus1) {
                        $nextdayplus2 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                    }
                }
            }

            $j = 1;
            $nextday = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $nextday) {
                    if ($key->remark == 'H') {
                        $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                    }
                }
            }

            $first = date('Y-m-01');
            $now = date('Y-m-d');

            $yesterday = date('Y-m-d', strtotime(' -1 days'));
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars` order by week_date desc");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $yesterday) {
                    if ($key->remark == 'H') {
                        $yesterday = date('Y-m-d', strtotime($yesterday . '-1 days'));
                    }
                }
            }

            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-01');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-01');
                    $last = date('Y-m-d', strtotime($date_to));
                }
            } else {
                if ($date_to == "") {
                    $first = date('Y-m-d', strtotime($date_from));
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-d', strtotime($date_from));
                    $last = date('Y-m-d', strtotime($date_to));
                }
            }

            // if ($request->get('date') == '' || $request->get('date') == date('Y-m-d')) {
            //   $rc11 = "SELECT quantity FROM injection_inventories WHERE location = 'RC11' AND material_number = gmc AND injection_inventories.deleted_at IS NULL";
            //   $rc91 = "SELECT quantity FROM injection_inventories WHERE location = 'RC91' AND material_number = gmc AND injection_inventories.deleted_at IS NULL";
            // }else{

            // }

            $date_all = DB::select("SELECT DISTINCT
  ( date )
  FROM
  `injection_inventory_histories`
  WHERE
  date >= '" . $first . "'
  AND date <= '" . $last . "' ");

            $data_ivorys = [];
            $data_skeletons = [];

            for ($i = 0; $i < count($date_all); $i++) {

                $rc11 = "SELECT quantity FROM injection_inventory_histories WHERE location = 'RC11' AND material_number = gmc AND injection_inventory_histories.deleted_at IS NULL and date = '" . $date_all[$i]->date . "'";
                $rc91 = "SELECT quantity FROM injection_inventory_histories WHERE location = 'RC91' AND material_number = gmc AND injection_inventory_histories.deleted_at IS NULL and date = '" . $date_all[$i]->date . "'";
                $data_skeleton = DB::SELECT("SELECT
    c.part,
    TRIM(
    RIGHT(
    c.part,
    (LENGTH(c.part) - LOCATE('(',c.part))
    )
    ) as color,
    SPLIT_STRING(c.part, ' ',1) as parts,
    SUM( c.stock ) AS stock,
    SUM( c.stock_assy ) AS stock_assy,
    SUM( c.plan ) AS plan
    FROM
    (
    SELECT
    CONCAT( UPPER( injection_parts.part_code ), ' (', injection_parts.color, ')' ) AS part,
    COALESCE (( " . $rc11 . "), 0 ) AS stock,
    COALESCE (( " . $rc91 . " ), 0 ) AS stock_assy,
    0 AS plan
    FROM
    injection_parts where remark = 'injection'
    GROUP BY injection_parts.part_code,injection_parts.color,gmc,color  UNION ALL
    SELECT
    part,
    0 AS stock,
    0 AS stock_assy,
    sum( a.plan )- sum( a.stamp ) AS plan
    FROM
    (
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    SUM( quantity ) AS plan,
    0 AS stamp
    FROM
    production_schedules
    LEFT JOIN materials ON materials.material_number = production_schedules.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND production_schedules.due_date BETWEEN '" . $first . "'
    AND '" . $nextdayplus1 . "'
    GROUP BY
    part_code,color UNION ALL
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    0 AS plan,
    SUM( quantity ) AS stamp
    FROM
    flo_details
    LEFT JOIN materials ON materials.material_number = flo_details.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND DATE( flo_details.created_at ) BETWEEN '" . $first . "'
    AND '" . $now . "'
    GROUP BY
    part_code,color
    ) a
    GROUP BY
    a.part
    ) c
    where c.part not like '%YRF%' AND
    c.part not like '%IVORY%' AND
    c.part not like '%BEIGE%'
    " . $color . "
    " . $part . "
    GROUP BY
    c.part ORDER BY color");

                $data_ivory = DB::SELECT("SELECT
    c.part,
    SPLIT_STRING(c.part, ' ',1) as parts,
    TRIM(
    RIGHT(
    c.part,
    (LENGTH(c.part) - LOCATE('(',c.part))
    )
    ) as color,
    SUM( c.stock ) AS stock,
    SUM( c.stock_assy ) AS stock_assy,
    SUM( c.plan ) AS plan
    FROM
    (
    SELECT
    CONCAT( UPPER( injection_parts.part_code ), ' (', injection_parts.color, ')' ) AS part,
    COALESCE (( " . $rc11 . "), 0 ) AS stock,
    COALESCE (( " . $rc91 . "), 0 ) AS stock_assy,
    0 AS plan
    FROM
    injection_parts where remark = 'injection'
    GROUP BY injection_parts.part_code,injection_parts.color,gmc,color  UNION ALL
    SELECT
    part,
    0 AS stock,
    0 AS stock_assy,
    sum( a.plan )- sum( a.stamp ) AS plan
    FROM
    (
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    SUM( quantity ) AS plan,
    0 AS stamp
    FROM
    production_schedules
    LEFT JOIN materials ON materials.material_number = production_schedules.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND production_schedules.due_date BETWEEN '" . $first . "'
    AND '" . $nextdayplus1 . "'
    GROUP BY
    part_code,color UNION ALL
    SELECT
    CONCAT( part_code, ' (', color, ')' ) AS part,
    0 AS plan,
    SUM( quantity ) AS stamp
    FROM
    flo_details
    LEFT JOIN materials ON materials.material_number = flo_details.material_number
    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
    WHERE
    materials.category = 'FG'
    AND materials.origin_group_code = '072'
    AND DATE( flo_details.created_at ) BETWEEN '" . $first . "'
    AND '" . $now . "'
    GROUP BY
    part_code,color
    ) a
    GROUP BY
    a.part
    ) c
    where c.part not like '%PINK%' AND
    c.part not like '%RED%' AND
    c.part not like '%BLUE%' AND
    c.part not like '%BROWN%' AND
    c.part not like '%GREEN%'
    " . $color . "
    " . $part . "
    GROUP BY
    c.part ORDER BY part");

                $datas_skeleton = [];
                $datas_ivory = [];

                foreach ($data_ivory as $key) {
                    $datas_ivory[] = array(
                        'part' => $key->part,
                        'color' => $key->color,
                        'stock' => $key->stock,
                        'stock_assy' => $key->stock_assy,
                        'plan' => $key->plan);
                }

                foreach ($data_skeleton as $key) {
                    $datas_skeleton[] = array(
                        'part' => $key->part,
                        'color' => $key->color,
                        'stock' => $key->stock,
                        'stock_assy' => $key->stock_assy,
                        'plan' => $key->plan);
                }

                array_push($data_ivorys, $datas_ivory);
                array_push($data_skeletons, $data_skeleton);
            }

            $nextmonth = date('Y-m-t', strtotime('+ 1 MONTH'));

            $plan_day = DB::SELECT("SELECT
            *
  FROM
  (
  SELECT
  'RC91' AS location,
  c.part,
  SPLIT_STRING ( c.plan, '_', 1 ) AS qty,
  SPLIT_STRING ( c.plan, '_', 2 ) AS late_stock
  FROM
  (
  SELECT DISTINCT
  (
  CONCAT( part_code, ' (', color, ')' )) AS part,
  (
  SELECT
  CONCAT(
  COALESCE ( quantity, 0 ),
  '_',
  COALESCE ( DATEDIFF( due_date, '" . $now . "' ), 0 )) AS plan
  FROM
  production_schedules
  LEFT JOIN materials ON materials.material_number = production_schedules.material_number
  LEFT JOIN injection_part_details ON injection_part_details.model = materials.model
  WHERE
  materials.category = 'FG'
  AND materials.origin_group_code = '072'
  AND production_schedules.due_date BETWEEN '" . $nextdayplus2 . "'
  AND '" . $nextmonth . "'
  AND CONCAT( part_code, ' (', color, ')' ) = CONCAT( p.part_code, ' (', p.color, ')' )
  LIMIT 1
  ) AS plan,
  0 AS stamp
  FROM
  production_schedules a
  LEFT JOIN materials m ON m.material_number = a.material_number
  LEFT JOIN injection_part_details p ON p.model = m.model
  WHERE
  m.category = 'FG'
  AND m.origin_group_code = '072'
  ) c
  GROUP BY
  c.part,
  c.plan
  ) d
  WHERE
  d.qty != 0");

            $jumlah_hari_kerja = DB::SELECT("SELECT
  count( week_date ) AS jumlah
  FROM
  weekly_calendars
  WHERE
  DATE_FORMAT( week_date, '%Y-%m' ) = DATE_FORMAT( NOW(), '%Y-%m' )
  AND remark != 'H'
  AND week_date BETWEEN DATE(
  NOW())
  AND LAST_DAY(
  NOW())");

            $all_day_month = DB::SELECT("SELECT
  count( week_date ) AS jumlah
  FROM
  weekly_calendars
  WHERE
  DATE_FORMAT( week_date, '%Y-%m' ) = DATE_FORMAT( NOW(), '%Y-%m' )
  AND remark != 'H'
  AND week_date BETWEEN CONCAT( DATE_FORMAT( NOW(), '%Y-%m' ), '-01' )
  AND LAST_DAY(
  NOW())");

            $response = array(
                'status' => true,
                'date_all' => $date_all,
                'datas_skeleton' => $datas_skeleton,
                'datas_ivory' => $datas_ivory,
                'data_skeletons' => $data_skeletons,
                'data_ivorys' => $data_ivorys,
                'plan_day' => $plan_day,
                'day_month' => $jumlah_hari_kerja[0]->jumlah,
                'all_day_month' => $all_day_month[0]->jumlah,
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

    public function indexReportMaintenanceMolding()
    {
        $title = 'Report Maintenance Molding';
        $title_jp = '';
        return view('injection.report_maintenance_molding', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Report Maintenance Molding')->with('jpn', '');
    }

    public function fetchReportMaintenanceMolding(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-01');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-01');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }
            $maintenance = DB::SELECT("SELECT
      *,
      DATE(created_at) as dates,
      round(TIMESTAMPDIFF(second,start_time,end_time)/60,2) AS duration
    FROM
      injection_maintenance_molding_logs
    WHERE DATE(created_at) >= '" . $first . "'
    AND DATE(created_at) <= '" . $last . "'");

            $dataworkall = [];

            foreach ($maintenance as $key) {
                $work = DB::SELECT("SELECT *,round(TIMESTAMPDIFF(second,start_time,end_time)/60,2) as duration FROM `injection_maintenance_molding_works` where maintenance_code = '" . $key->maintenance_code . "'");

                if (count($work) > 0) {
                    foreach ($work as $val) {
                        $datawork = array(
                            'maintenance_code' => $val->maintenance_code,
                            'status' => $val->status,
                            'start_time' => $val->start_time,
                            'end_time' => $val->end_time,
                            'duration' => $val->duration,
                            'reason' => $val->reason);
                        $dataworkall[] = join("+", $datawork);
                    }
                }

            }

            $response = array(
                'status' => true,
                'maintenance' => $maintenance,
                'dataworkall' => $dataworkall,
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
