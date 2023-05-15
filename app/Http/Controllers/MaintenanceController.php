<?php

namespace App\Http\Controllers;

use App\AreaCode;
use App\CodeGenerator;
use App\ElectricityConsumption;
use App\ElectricityTarget;
use App\EmployeeSync;
use App\Http\Controllers\Controller;
use App\Libraries\ActMLEasyIf;
use App\Mail\SendEmail;
use App\MaintenanceFinding;
use App\MaintenanceInventory;
use App\MaintenanceInventoryLog;
use App\MaintenanceJobOrder;
use App\MaintenanceJobOrderLog;
use App\MaintenanceJobPart;
use App\MaintenanceJobPending;
use App\MaintenanceJobProcess;
use App\MaintenanceJobReport;
use App\MaintenanceJobSparepart;
use App\MaintenanceMachineProblemLog;
use App\MaintenanceOperatorLocation;
use App\MaintenanceOperatorLocationLog;
use App\MaintenancePic;
use App\MaintenancePlan;
use App\MaintenancePlanCheck;
use App\MaintenancePlanItem;
use App\MaintenancePlanItemCheck;
use App\Plc;
use App\Process;
use App\User;
use App\Utility;
use App\UtilityCheck;
use App\UtilityOrder;
use App\UtilityUse;
use App\WeeklyCalendar;
use App\WwtDailyMaster;
use Carbon\Carbon;
use DataTables;
use Excel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\ImageManagerStatic as Image;
use Response;

class MaintenanceController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');

        $this->mt_employee = EmployeeSync::where("department", "like", "%Maintenance%")
        ->whereNull("end_date")
        ->whereNotNull("group")
        ->select("employee_id", db::raw("SUBSTRING_INDEX(`name`,' ',3) as name"))
        ->orderBy('hire_date', 'desc')
        ->get();

        $this->apar_type = [
            ['type' => 'powder', 'valid' => 3],
            ['type' => 'liquid', 'valid' => 5],
            ['type' => 'CO2', 'valid' => 5],
            ['type' => 'foam', 'valid' => 3],
        ];

        $this->lokasi = [
            'Produksi',
            'WWT',
            'Klinik',
        ];

        $this->uom = ['Pcs'];

        $this->inv_ctg = MaintenanceInventory::select("category")
        ->groupBy('category')
        ->orderBy('category', 'asc')
        ->get();

        $this->inv_rack = MaintenanceInventory::select("location")
        ->groupBy('location')
        ->orderBy('location', 'asc')
        ->get();

        $this->spk_category = [
            ['category' => 'Mesin Trouble'],
            ['category' => 'Kelistrikan dan Jaringan'],
            ['category' => 'Relayout'],
            ['category' => 'Spare part'],
            ['category' => 'General'],
            ['category' => 'Finding'],
        ];

        $this->location = [
            ['location' => '3D Room', 'alias' => 'tri_d', 'area' => ['3D Room']],
            ['location' => 'Analyzing Room', 'alias' => 'anz', 'area' => ['Analyzing Room']],
            ['location' => 'Assembly', 'alias' => 'assy', 'area' => ['Assembly', 'FL Assy']],
            ['location' => 'Bea Cukai', 'alias' => 'bea', 'area' => ['Bea Cukai']],
            ['location' => 'Barrel', 'alias' => 'tumb', 'area' => ['Buffing & Tumbling']],
            ['location' => 'Buffing', 'alias' => 'buff', 'area' => ['Buffing']],
            ['location' => 'Boiler Room', 'alias' => 'boi', 'area' => ['Boiler Room']],
            ['location' => 'B-Pro', 'alias' => 'bpro', 'area' => ['B-Pro', 'Buffing (B-Pro)', 'NC Kira - B-Pro']],
            ['location' => 'Case', 'alias' => 'case', 'area' => ['Case']],
            ['location' => 'Clarinet Body', 'alias' => 'cl', 'area' => ['Clarinet Body', 'NC Kira - CL-Body']],
            ['location' => 'Klinik', 'alias' => 'clc', 'area' => ['Klinik']],
            ['location' => 'Kantin', 'alias' => 'ctn', 'area' => ['Kantin']],
            ['location' => 'Engraving', 'alias' => 'eng', 'area' => ['Engraving']],
            ['location' => 'Magang', 'alias' => 'gtc', 'area' => ['GTC']],
            ['location' => 'Mouth Piece', 'alias' => 'mpc', 'area' => ['Mouth Piece']],
            ['location' => 'M-Pro', 'alias' => 'mpr', 'area' => ['M-Pro']],
            ['location' => 'Office', 'alias' => 'ofc', 'area' => ['Office']],
            ['location' => 'Plating', 'alias' => 'plt', 'area' => ['Plating']],
            ['location' => 'Pianica', 'alias' => 'pnc', 'area' => ['Pianica']],
            ['location' => 'Painting', 'alias' => 'lcq', 'area' => ['Lacquering', 'Painting']],
            ['location' => 'Quality Assurance', 'alias' => 'qa', 'area' => ['Quality Assurance']],
            ['location' => 'Recorder', 'alias' => 'rcd', 'area' => ['Recorder']],
            ['location' => 'Reed Plate', 'alias' => 'rpl', 'area' => ['Reed Plate']],
            ['location' => 'Soldering', 'alias' => 'wld', 'area' => ['Soldering']],
            ['location' => 'Tanpo', 'alias' => 'tnp', 'area' => ['Tanpo']],
            ['location' => 'Venova', 'alias' => 'vnv', 'area' => ['Venova']],
            ['location' => 'Warehouse', 'alias' => 'wrh', 'area' => ['Warehouse']],
            ['location' => 'Workshop', 'alias' => 'wrk', 'area' => ['Workshop']],
            ['location' => 'WWT', 'alias' => 'wwt', 'area' => ['WWT']],
            ['location' => 'Injection', 'alias' => 'inj', 'area' => ['Injection']],
            ['location' => 'MTC', 'alias' => 'mtc', 'area' => ['Maintenance']],
            ['location' => 'Gudang MTC', 'alias' => 'mtc2', 'area' => ['Maintenance Gudang']],
            ['location' => 'Press', 'alias' => 'prs', 'area' => ['Press']],
            ['location' => 'Transformer Room 1', 'alias' => 'trf1', 'area' => ['Transformer Room 1']],
            ['location' => 'Transformer Room 2', 'alias' => 'trf2', 'area' => ['Transformer Room 2']],
            ['location' => 'Transformer Room 3', 'alias' => 'trf3', 'area' => ['Transformer Room 3']],
            ['location' => 'Compressor Assy', 'alias' => 'com1', 'area' => ['Compressor Assy']],
            ['location' => 'Compressor Recorder', 'alias' => 'com2', 'area' => ['Compressor Recorder']],
            ['location' => 'Compressor T-Pro', 'alias' => 'com3', 'area' => ['Compressor T-Pro']],
            ['location' => 'Outdoor', 'alias' => 'oa', 'area' => ['Outdoor']],
            // Compressor Recorder
        ];

        $this->limbah = [
            'Sludge WWT',
            'Soft grid',
            'Dust collector/Buffing dust waste',
            'Used rags',
            'Sand filter waste',
            'Finger stall',
            'Filter painting',
            'Filter panjang',
            'Barik',
            'Lax saxophone',
            'Cassette ribbon',
            'Stamp ink',
            'Lubricant oil',
            'Painting liquid waste',
            'Spent activated carbon',
            'Used Battery',
            'Used lamp',
            'Contaminated pipe',
            'Liquid cleaning waste (OCW gell)',
            'Masker medis',
            'Infectious',
        ];

        $this->vendor = [
            'PPLI',
            'Satunya Lagi',
        ];

        $this->location_patrol = ['Assembly', 'Accounting', 'Body Process', 'Exim', 'Material Process', 'Surface Treatment', 'Educational Instrument', 'Standardization', 'QA Process', 'Chemical Process Control', 'Human Resources', 'General Affairs', 'Workshop and Maintenance Molding', 'Production Engineering', 'Maintenance', 'Procurement', 'Production Control', 'Warehouse Material', 'Warehouse Finished Goods', 'Welding Process', 'Case Tanpo CL Body 3D Room', 'Halte dan Trotoar', 'Area Parkir Motor', 'Area Ceklog LCQ – Plating', 'Area Ceklog Buffing ', 'Area Ceklog Assy – Soldering ', 'Area Ceklog Bpro – Pianica', 'Area Lobby dan Office', 'Area Ceklog Recorder', 'Area Ceklog Key Part Process', 'Klinik', 'Area Loker Produksi', 'Kantin', 'OMI', 'Oil Storage (Barat Pianica)', 'Oil Storage (Barat KPP)', 'Flammable Storage', 'Security'];

    }

    // -----------------------  START INDEX --------------------

    public function indexMachineryMonitoring(Request $request)
    {
        return view('plant_maintenance.machinery_monitoring', array(
            'title' => 'Machinery Monitoring',
            'title_jp' => '',
        )
    );
    }

    public function indexMachineryStop(Request $request)
    {
        return view('plant_maintenance.machinery_stop', array(
            'title' => 'Machine Trouble Status',
            'title_jp' => '機械故障・停止状況',
        )
    );
    }

    public function fetchMachineryMonitoring(Request $request)
    {
        $arr_api = [
            'http://10.109.52.7/zed/dashboard/getData',
            'http://10.109.52.7/zed/dashboard/getDataSystem',
        ];

        $machine_data = [
            ['1', 'MC 1st#6', 'Machining'],
            ['2', 'MC 1st#4', 'Machining'],
            ['3', 'MC 1st#3', 'Machining'],
            ['4', 'MC 1st#5', 'Machining'],
            ['5', 'MC 1st#1', 'Machining'],
            ['6', 'MC 1st#2', 'Machining'],
            ['7', 'MC 1st#7', 'Machining'],
            ['8', 'MC 1st#9', 'Machining'],
            ['9', 'MC 1st#8', 'Machining'],
            ['10', 'MC 1st#10', 'Machining'],
            ['11', 'MC 1st#11', 'Machining'],
            ['12', 'MC 1st#12', 'Machining'],
            ['13', 'MC 2nd#1', 'Machining'],
            ['14', 'MC 2nd#2', 'Machining'],
            ['15', 'MC 2nd#3', 'Machining'],
            ['16', 'MC 2nd#4', 'Machining'],
            ['17', 'MC 2nd#5', 'Machining'],
            ['18', 'MC 2nd#6', 'Machining'],
            ['19', 'MC 2nd#7', 'Machining'],
            ['20', 'MC 2nd#8', 'Machining'],
            ['21', 'MC 2nd#9', 'Machining'],
            ['22', 'MC 2nd#10', 'Machining'],
            ['100', 'K-Mkp', 'Press'],
            ['99', 'K-Nkp', 'Press'],
            ['63', 'K-Nuki', 'Press'],
            ['75', 'Kom#1', 'Press'],
            ['76', 'Kom#2', 'Press'],
            ['77', 'Kom#3', 'Press'],
            ['78', 'Kom#4', 'Press'],
            ['79', 'Kom#5', 'Press'],
            ['81', 'Amd-PC', 'Press'],
            ['80', 'Amd#1', 'Press'],
            ['69', 'Amd#2', 'Press'],
            ['70', 'Amd#3', 'Press'],
            ['71', 'Amd#4', 'Press'],
            ['72', 'Amd#5', 'Press'],
            ['73', 'Amd#6', 'Press'],
            ['74', 'Amd#7', 'Press'],
            ['47', 'Inj#1', 'Injection'],
            ['82', 'Inj#2', 'Injection'],
            ['57', 'Inj#3', 'Injection'],
            ['58', 'Inj#4', 'Injection'],
            ['59', 'Inj#5', 'Injection'],
            ['60', 'Inj#6', 'Injection'],
            ['61', 'Inj#7', 'Injection'],
            ['83', 'Inj#8', 'Injection'],
            ['62', 'Inj#9', 'Injection'],
            ['91', 'Inj#10', 'Injection'],
            ['64', 'Inj#11', 'Injection'],
            ['92', 'Inj#12', 'Injection'],
            ['67', 'Inj#13', 'Injection'],
            ['68', 'Inj#14', 'Injection'],
            ['65', 'Inj#15', 'Injection'],
            ['93', 'Inj#16', 'Injection'],
            ['96', 'Inj#17', 'Injection'],
            ['66', 'Inj#18', 'Injection'],
            ['97', 'Inj#19', 'Injection'],
            ['50', 'LT#1', 'Senban'],
            ['29', 'LT#2', 'Senban'],
            ['30', 'LT#3', 'Senban'],
            ['31', 'LT#4', 'Senban'],
            ['32', 'LT#5', 'Senban'],
            ['51', 'LT#6', 'Senban'],
            ['53', 'LT#7', 'Senban'],
            ['54', 'LT#8', 'Senban'],
            ['55', 'LT#9', 'Senban'],
            ['56', 'LT#10', 'Senban'],
            ['52', 'LT#11', 'Senban'],
            ['48', 'LT#12', 'Senban'],
            ['49', 'LT#13', 'Senban'],
            ['45', 'LT#14', 'Senban'],
            ['46', 'LT#15', 'Senban'],
            ['43', 'LT#16', 'Senban'],
            ['44', 'LT#17', 'Senban'],
            ['33', 'LT#18', 'Senban'],
            ['34', 'LT#19', 'Senban'],
            ['35', 'LT#20', 'Senban'],
            ['36', 'LT#21', 'Senban'],
            ['37', 'LT#22', 'Senban'],
            ['38', 'LT#23', 'Senban'],
            ['39', 'LT#24', 'Senban'],
            ['40', 'LT#25', 'Senban'],
            ['41', 'LT#26', 'Senban'],
            ['42', 'LT#27', 'Senban'],
            ['26', 'LT#28', 'Senban'],
            ['25', 'LT#29', 'Senban'],
            ['24', 'LT#30', 'Senban'],
            ['23', 'LT#31', 'Senban'],
            ['103', 'NC Milling', 'Workshop'],
            ['104', 'NC Bubut', 'Workshop'],
            ['105', 'Wirecut EDM', 'Workshop'],
            ['106', 'CNC Moriseiki', 'Workshop'],
            ['107', 'Wirecut Sodick', 'Workshop'],
            ['108', 'CNC Milling PS65', 'Workshop'],
            ['109', 'CNC Milling F3', 'Workshop'],
            ['85', 'WCut#1', 'ZPRO'],
            ['84', 'WCut#2', 'ZPRO'],
            ['98', 'WCut#3', 'ZPRO'],
            ['89', 'Shinogi', 'ZPRO'],
            ['94', 'MC1st#1', 'ZPRO'],
            ['86', 'MC1st#2', 'ZPRO'],
            ['95', 'MC2nd#1', 'ZPRO']
        ];

        foreach ($arr_api as $api) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $api);
            $result = curl_exec($ch);
            curl_close($ch);

            $mesin = explode('(ime)', $result);


            $mesin_split = [];
            for ($i = 0; $i < count($mesin); $i++) {
                array_push($mesin_split, explode('#', $mesin[$i]));
            }

            for ($i = 0; $i < count($machine_data); $i++) {
                for ($j = 0; $j < count($mesin_split); $j++) {
                    if ($machine_data[$i][0] == $mesin_split[$j][0]) {
                        $merah = explode(':', $mesin_split[$j][4]);

                        $nama_mesin = $machine_data[$i][1];
                        $lokasi = $machine_data[$i][2];

                        if ($mesin_split[$j][1] == 0) { //merah
                            $dt = date('Y-m-d H:i:s', strtotime($mesin_split[$j][3]));

                            $data = db::table('maintenance_machine_trouble_logs')->select('machine_code', 'machine_name', 'machine_location')
                            ->whereNull('finished_at')
                            ->where('machine_name', '=', $nama_mesin)
                            ->where('machine_location', '=', $lokasi)
                            ->first();

                            if (count($data) < 1) {
                                db::table('maintenance_machine_trouble_logs')->insert([
                                    'machine_name' => $nama_mesin,
                                    'machine_location' => $lokasi,
                                    'status_machine' => 'No Information Yet',
                                    'started_at' => date('Y-m-d H:i:s'),
                                    'created_by' => '1',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                            }
                        } else if ($mesin_split[$j][1] == 1 || $mesin_split[$j][1] == 2 || $mesin_split[$j][1] == 3 || $mesin_split[$j][1] == 4) { //hijau, setup, idle
                            $dt = date('Y-m-d H:i:s', strtotime($mesin_split[$j][3]));

                            $data = db::table('maintenance_machine_trouble_logs')->select('machine_code', 'machine_name', 'machine_location')
                            ->whereNull('finished_at')
                            ->where('machine_name', '=', $nama_mesin)
                            ->where('machine_location', '=', $lokasi)
                            ->first();

                            if (count($data) > 0) {
                                db::table('maintenance_machine_trouble_logs')
                                ->where('machine_name', '=', $nama_mesin)
                                ->where('machine_location', '=', $lokasi)
                                ->whereNull('finished_at')
                                ->update(['finished_at' => date('Y-m-d H:i:s')]);
                            }
                        }
                    }
                }
            }

        }
        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function fetchMachineryStop(Request $request)
    {
        $machines = db::table('maintenance_machine_trouble_logs')
        ->whereNull('finished_at')
        // ->whereRaw('TIMESTAMPDIFF(MINUTE,started_at,NOW()) > 30')
        ->get();

        $arr_api = [
            'http://10.109.52.7/zed/dashboard/getData',
            'http://10.109.52.7/zed/dashboard/getDataSystem',
        ];

        $machine_data = [
            ['1', 'MC 1st#6', 'Machining'],
            ['2', 'MC 1st#4', 'Machining'],
            ['3', 'MC 1st#3', 'Machining'],
            ['4', 'MC 1st#5', 'Machining'],
            ['5', 'MC 1st#1', 'Machining'],
            ['6', 'MC 1st#2', 'Machining'],
            ['7', 'MC 1st#7', 'Machining'],
            ['8', 'MC 1st#9', 'Machining'],
            ['9', 'MC 1st#8', 'Machining'],
            ['10', 'MC 1st#10', 'Machining'],
            ['11', 'MC 1st#11', 'Machining'],
            ['12', 'MC 1st#12', 'Machining'],
            ['13', 'MC 2nd#1', 'Machining'],
            ['14', 'MC 2nd#2', 'Machining'],
            ['15', 'MC 2nd#3', 'Machining'],
            ['16', 'MC 2nd#4', 'Machining'],
            ['17', 'MC 2nd#5', 'Machining'],
            ['18', 'MC 2nd#6', 'Machining'],
            ['19', 'MC 2nd#7', 'Machining'],
            ['20', 'MC 2nd#8', 'Machining'],
            ['21', 'MC 2nd#9', 'Machining'],
            ['22', 'MC 2nd#10', 'Machining'],
            ['100', 'K-Mkp', 'Press'],
            ['99', 'K-Nkp', 'Press'],
            ['63', 'K-Nuki', 'Press'],
            ['75', 'Kom#1', 'Press'],
            ['76', 'Kom#2', 'Press'],
            ['77', 'Kom#3', 'Press'],
            ['78', 'Kom#4', 'Press'],
            ['79', 'Kom#5', 'Press'],
            ['81', 'Amd-PC', 'Press'],
            ['80', 'Amd#1', 'Press'],
            ['69', 'Amd#2', 'Press'],
            ['70', 'Amd#3', 'Press'],
            ['71', 'Amd#4', 'Press'],
            ['72', 'Amd#5', 'Press'],
            ['73', 'Amd#6', 'Press'],
            ['74', 'Amd#7', 'Press'],
            ['47', 'Inj#1', 'Injection'],
            ['82', 'Inj#2', 'Injection'],
            ['57', 'Inj#3', 'Injection'],
            ['58', 'Inj#4', 'Injection'],
            ['59', 'Inj#5', 'Injection'],
            ['60', 'Inj#6', 'Injection'],
            ['61', 'Inj#7', 'Injection'],
            ['83', 'Inj#8', 'Injection'],
            ['62', 'Inj#9', 'Injection'],
            ['91', 'Inj#10', 'Injection'],
            ['64', 'Inj#11', 'Injection'],
            ['92', 'Inj#12', 'Injection'],
            ['67', 'Inj#13', 'Injection'],
            ['68', 'Inj#14', 'Injection'],
            ['65', 'Inj#15', 'Injection'],
            ['93', 'Inj#16', 'Injection'],
            ['96', 'Inj#17', 'Injection'],
            ['66', 'Inj#18', 'Injection'],
            ['97', 'Inj#19', 'Injection'],
            ['50', 'LT#1', 'Senban'],
            ['29', 'LT#2', 'Senban'],
            ['30', 'LT#3', 'Senban'],
            ['31', 'LT#4', 'Senban'],
            ['32', 'LT#5', 'Senban'],
            ['51', 'LT#6', 'Senban'],
            ['53', 'LT#7', 'Senban'],
            ['54', 'LT#8', 'Senban'],
            ['55', 'LT#9', 'Senban'],
            ['56', 'LT#10', 'Senban'],
            ['52', 'LT#11', 'Senban'],
            ['48', 'LT#12', 'Senban'],
            ['49', 'LT#13', 'Senban'],
            ['45', 'LT#14', 'Senban'],
            ['46', 'LT#15', 'Senban'],
            ['43', 'LT#16', 'Senban'],
            ['44', 'LT#17', 'Senban'],
            ['33', 'LT#18', 'Senban'],
            ['34', 'LT#19', 'Senban'],
            ['35', 'LT#20', 'Senban'],
            ['36', 'LT#21', 'Senban'],
            ['37', 'LT#22', 'Senban'],
            ['38', 'LT#23', 'Senban'],
            ['39', 'LT#24', 'Senban'],
            ['40', 'LT#25', 'Senban'],
            ['41', 'LT#26', 'Senban'],
            ['42', 'LT#27', 'Senban'],
            ['26', 'LT#28', 'Senban'],
            ['25', 'LT#29', 'Senban'],
            ['24', 'LT#30', 'Senban'],
            ['23', 'LT#31', 'Senban'],
            ['103', 'NC Milling', 'Workshop'],
            ['104', 'NC Bubut', 'Workshop'],
            ['105', 'Wirecut EDM', 'Workshop'],
            ['106', 'CNC Moriseiki', 'Workshop'],
            ['107', 'Wirecut Sodick', 'Workshop'],
            ['108', 'CNC Milling PS65', 'Workshop'],
            ['109', 'CNC Milling F3', 'Workshop'],
            ['85', 'WCut#1', 'ZPRO'],
            ['84', 'WCut#2', 'ZPRO'],
            ['98', 'WCut#3', 'ZPRO'],
            ['89', 'Shinogi', 'ZPRO'],
            ['94', 'MC1st#1', 'ZPRO'],
            ['86', 'MC1st#2', 'ZPRO'],
            ['95', 'MC2nd#1', 'ZPRO']
        ];

        $oee = [];

        foreach ($arr_api as $api) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $api);
            $result = curl_exec($ch);
            curl_close($ch);

            $mesin = explode('(ime)', $result);

            $mesin_split = [];
            for ($i = 0; $i < count($mesin); $i++) {
                array_push($mesin_split, explode('#', $mesin[$i]));
            }

            for ($i = 0; $i < count($machine_data); $i++) {
                for ($j = 0; $j < count($mesin_split); $j++) {
                    if ($machine_data[$i][0] == $mesin_split[$j][0]) {
                        $merah = explode(':', $mesin_split[$j][4]);

                        $nama_mesin = $machine_data[$i][1];
                        $lokasi = $machine_data[$i][2];
                        $mesin_sts = '';

                        if ($mesin_split[$j][1] == 0) { //merah
                            foreach ($machines as $mc) {
                                if ($mc->machine_name == $nama_mesin) {
                                    $mesin_sts = 'error';
                                } else {
                                    $mesin_sts = 'setup';
                                }
                            }

                            if ($mesin_sts == '') {
                                $mesin_sts = 'error';
                                db::table('maintenance_machine_trouble_logs')->insert([
                                    'machine_name' => $nama_mesin,
                                    'machine_location' => $lokasi,
                                    'status_machine' => 'No Information Yet',
                                    'started_at' => date('Y-m-d H:i:s'),
                                    'created_by' => '1',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                            }
                        } else if ($mesin_split[$j][1] == 1) {
                            $mesin_sts = 'inprogress';

                            db::table('maintenance_machine_trouble_logs')
                            ->where('machine_name', '=', $nama_mesin)
                            ->where('machine_location', '=', $lokasi)
                            ->whereNull('finished_at')
                            ->update(['finished_at' => date('Y-m-d H:i:s')]);
                        } else if ($mesin_split[$j][1] == 2) {
                            $mesin_sts = 'setup';

                            db::table('maintenance_machine_trouble_logs')
                            ->where('machine_name', '=', $nama_mesin)
                            ->where('machine_location', '=', $lokasi)
                            ->whereNull('finished_at')
                            ->update(['finished_at' => date('Y-m-d H:i:s')]);
                        } else if ($mesin_split[$j][1] == 3) {
                            $mesin_sts = 'iddle';

                            db::table('maintenance_machine_trouble_logs')
                            ->where('machine_name', '=', $nama_mesin)
                            ->where('machine_location', '=', $lokasi)
                            ->whereNull('finished_at')
                            ->update(['finished_at' => date('Y-m-d H:i:s')]);
                        } else if ($mesin_split[$j][1] == 4) {
                            $mesin_sts = 'iddle2';

                            db::table('maintenance_machine_trouble_logs')
                            ->where('machine_name', '=', $nama_mesin)
                            ->where('machine_location', '=', $lokasi)
                            ->whereNull('finished_at')
                            ->update(['finished_at' => date('Y-m-d H:i:s')]);
                        } else if ($mesin_split[$j][1] == 5) {
                            $mesin_sts = 'off';
                        }

                        array_push($oee, ['status_mesin' => $mesin_sts, 'mesin' => $nama_mesin]);
                    }
                }
            }
        }

        $machines2 = db::table('maintenance_machine_trouble_logs')
        ->whereNull('finished_at')
        ->whereRaw('TIMESTAMPDIFF(MINUTE,started_at,NOW()) > 30')
        ->get();

        $response = array(
            'status' => true,
            'machine_trouble' => $machines2,
            'oee' => $oee
        );

        return Response::json($response);
    }

    public function editMachineryStop(Request $request)
    {
        try {
            DB::table('maintenance_machine_trouble_logs')
            ->where('id', $request->get("id"))
            ->update(['status_machine' => $request->get("trouble_status")]);

            $response = array(
                'status' => true,
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

    public function indexSPKMonitoring()
    {
        $title = 'SPK Monitoring';
        $title_jp = '作業依頼書の管理';

        return view('maintenance.index_spk', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    );
    }

    public function indexMachineMonitoring()
    {
        $title = 'Machine Monitoring';
        $title_jp = 'マシン監視';

        return view('maintenance.index_machine', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    );
    }

    public function indexPlanMonitoring()
    {
        $title = 'Planned Maintenance';
        $title_jp = '予定保全';

        return view('maintenance.index_planned', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    );
    }

    public function indexMaintenanceForm()
    {
        $title = 'Maintenance Request List';
        $title_jp = '作業依頼書リスト';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section')->first();

        $job_order = MaintenanceJobOrder::where('created_by', '=', Auth::user()->username)
        ->select(db::raw('count(if(remark="0", 1, null)) as requested, count(if(remark="1", 1, null)) as verifying, count(if(remark="2", 1, null)) as received, count(if(remark="3", 1, null)) as listed, count(if(remark="4", 1, null)) as inProgress, count(if(remark="5", 1, null)) as pending, count(if(remark="6", 1, null)) as finished, count(if(remark="7", 1, null)) as canceled, count(if(remark="8", 1, null)) as rejected'))->first();

        return view('maintenance.maintenance_form', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'requested' => $job_order->requested,
            'verifying' => $job_order->verifying,
            'received' => $job_order->received,
            'listed' => $job_order->listed,
            'inProgress' => $job_order->inProgress,
            'pending' => $job_order->pending,
            'finished' => $job_order->finished,
            'canceled' => $job_order->canceled,
            'rejected' => $job_order->rejected,
        )
    )->with('page', 'Maintenance Form')->with('head2', 'SPK')->with('head', 'Maintenance');
    }

    public function indexMaintenanceList()
    {
        $title = 'Maintenance Request List';
        $title_jp = '作業依頼書リスト';

        $statuses = Process::where('processes.remark', '=', 'maintenance')
        ->orderBy('process_code', 'asc')
        ->get();

        $employees = EmployeeSync::whereNotNull('section')
        ->whereNotNull('group')
        ->select('employee_id', 'name', 'section', 'group')
        ->orderBy('employee_id', 'asc')
        ->get();

        $keys = [];
        foreach ($this->spk_category as $row) {
            foreach ($row as $key) {
                if (!in_array($row['category'], $keys)) {
                    array_push($keys, $row['category']);
                }
            }
        }

        return view('maintenance.spk_list', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'statuses' => $statuses,
            'employees' => $employees,
            'mt_employees' => $this->mt_employee,
            'category' => $keys,
        )
    )->with('page', 'Maintenance List')->with('head2', 'SPK')->with('head', 'Maintenance');
    }

    public function indexSPK()
    {
        $title = 'SPK Execution';
        $title_jp = '作業依頼書の実行';

        $employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)
        ->select('employee_id', 'name', 'section', 'group')
        ->first();

        $area = db::select('SELECT master_area.*, maintenance_operator_locations.employee_id from
         (SELECT machine_id as area_code, location FROM `maintenance_plan_items`
             union all
             SELECT area_code, area as location from area_codes
             ) master_area
         left join maintenance_operator_locations on maintenance_operator_locations.qr_code = master_area.area_code');

        $trouble_part = MaintenanceJobPart::select('machine_group', 'trouble_part', 'part_inspection')->get();

        return view('maintenance.spk', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_id' => Auth::user()->username,
            'name' => $employee->name,
            'area_list' => $area,
            'trouble_list' => $trouble_part,
        )
    )->with('page', 'SPK')->with('head2', 'SPK')->with('head', 'Maintenance');
    }

    public function indexDangerNote($order_no)
    {
        $title = 'Verifying SPK';
        $title_jp = '';

        $spk = MaintenanceJobOrder::where('order_no', '=', $order_no)
        ->select('type', 'category', 'machine_condition', 'danger', 'description', 'remark')
        ->first();

        // if ($spk->remark == "2") {
        //     $message = 'SPK dengan Order No. '.$order_no;
        //     $message2 ='Sudah diverifikasi';
        //     $stat = 0;
        // } else {
        $message = 'Untuk melakukan verifikasi SPK ini,';
        $message2 = 'Tambahkan catatan bahaya pada kolom dibawah ini :';
        $stat = 1;
        // }

        return view('maintenance.spk_danger_message', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'head' => $order_no,
            'data' => $spk,
            'message' => $message,
            'message2' => $message2,
            'status' => $stat,
        )
    )->with('page', 'verifying SPK');
    }

    public function indexElectricityKaizenMonitor()
    {
        $title = ' Electricity Kaizen Monitor';
        $title_jp = '電気に関する改善の監視';

        return view('maintenance.electricity.kaizen_monitor', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Electricity')->with('head', 'Maintenance');

    }

    public function indexElectricityConsumption()
    {
        $title = ' Electricity Kaizen Monitor';
        $title_jp = '電気に関する改善の監視';

        return view('maintenance.electricity.consumption', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Electricity')->with('head', 'Maintenance');

    }

    public function indexElectricity()
    {
        $title = 'Electricity';
        $title_jp = '電気';

        return view('maintenance.electricity.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Electricity')->with('head', 'Maintenance');
    }

    public function indexMaintenanceMonitoring()
    {
        $title = 'Maintenance SPK Monitoring';
        $title_jp = '';

        return view('maintenance.maintenance_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Maintenance Monitoring')->with('head2', 'SPK')->with('head', 'Maintenance');
    }

    public function indexOperatorMonitoring()
    {
        $title = 'Operator SPK Monitoring';
        $title_jp = '';

        return view('maintenance.operator_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'op' => $this->mt_employee,
        )
    )->with('page', 'Operator Monitoring')->with('head2', 'SPK')->with('head', 'Maintenance');
    }

    public function indexSPKGrafik()
    {
        $title = 'Maintenance SPK Monitoring';
        $title_jp = '作業依頼書の管理';

        return view('maintenance.spk_grafik', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'SPK Monitoring')->with('head2', 'SPK')->with('head', 'Maintenance');
    }

    public function indexApar()
    {
        $title = 'APAR Check Schedule';
        $title_jp = '消火器・消火栓の点検日程';

        $location = Utility::where('remark', '=', 'APAR')
        ->select('group', db::raw('REPLACE(`group`, " ", "_") as group2'))
        ->groupBy('group')
        ->orderBy('group')
        ->get();

        return view('maintenance.apar.aparMonitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $location,
        )
    )->with('page', 'APAR')->with('head2', 'Utility')->with('head', 'Maintenance');
    }

    public function indexAparCheck()
    {
        $title = 'Utility Check';
        $title_jp = 'ユーティリティーチェック';

        $employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)
        ->select('employee_id', 'name', 'section', 'group')
        ->first();

        $check = db::table("utility_check_lists")
        ->select('check_point', 'remark', 'synonim')
        ->get();
        // $check = "";

        return view('maintenance.apar.aparCheck', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_id' => Auth::user()->username,
            'name' => $employee->name,
            'check_list' => $check,
        )
    )->with('page', 'APAR Check')->with('head2', 'Utility')->with('head', 'Maintenance');
    }

    public function indexAparExpire()
    {
        $title = 'APAR Expired List';
        $subtitle = 'APAR That will be expire';
        $title_jp = '消火器・消火栓の使用期限一覧';

        $check = db::table("utility_check_lists")
        ->select('check_point', 'remark')
        ->get();

        return view('maintenance.apar.aparExpired', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'subtitle' => $subtitle,
            'check_list' => $check,
        )
    )->with('page', 'APAR expired')->with('head2', 'Utility')->with('head', 'Maintenance');
    }

    public function indexAparOrderList()
    {
        $title = 'APAR Order List';
        $title_jp = '消火器・消火栓の発注一覧';

        if (Auth::user()->email == "priyo.jatmiko@music.yamaha.com" || Auth::user()->role_code == "MIS" || Auth::user()->email == "bambang.supriyadi@music.yamaha.com") {
            return view('maintenance.apar.aparOrderList', array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'APAR order')->with('head2', 'Utility')->with('head', 'Maintenance');
        } else {
            return redirect()->route('login');
        }
    }

    public function indexAparTool()
    {
        $title = 'Fire Extinguiser List';
        $title_jp = 'ユーティリティー';

        $locations = Utility::distinct()->select('group', 'location')->get();

        $types = Utility::where('type', '<>', '-')->distinct()->select('type')->get();

        return view('maintenance.apar.aparTool', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'locations' => $locations,
            'types' => $types,
        )
    )->with('page', 'APAR')->with('head2', 'Utility')->with('head', 'Maintenance');
    }

    public function indexAparResume()
    {
        $title = 'Fire Extinguiser Resume';
        $title_jp = '??';

        return view('maintenance.apar.aparResume', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'APAR')->with('head2', 'Utility')->with('head', 'Maintenance');
    }

    public function indexAparUses()
    {
        $title = 'Fire Extinguiser Uses';
        $title_jp = '??';

        $employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)
        ->select('employee_id', 'name', 'section', 'group')
        ->first();

        return view('maintenance.apar.aparUses', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_id' => Auth::user()->username,
            'name' => $employee->name,
        )
    )->with('page', 'APAR Uses')->with('head2', 'Utility')->with('head', 'Maintenance');
    }

    public function indexAparNG()
    {
        $title = 'Not Good APAR Check';
        $title_jp = '??';

        $check = db::table("utility_check_lists")
        ->select('check_point', 'remark')
        ->get();

        return view('maintenance.apar.aparNGList', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'check_list' => $check,
        )
    )->with('page', 'APAR NG')->with('head2', 'Utility')->with('head', 'Maintenance');
    }

    public function indexAparMap()
    {
        $title = 'APAR MAP';
        $title_jp = '??';

        return view('maintenance.apar.aparMap', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'APAR MAP')->with('head2', 'Utility')->with('head', 'Maintenance');
    }

    public function indexInventory()
    {
        $title = 'Maintenance Spare Part Inventories';
        $title_jp = '??';

        if ($user = Auth::user()) {
            if (str_contains(Auth::user()->role_code, 'MIS') || strtoupper(Auth::user()->username) == "PI2102025" || strtoupper(Auth::user()->username) == "PI9906003" || strtoupper(Auth::user()->username) == "PI1404002" || strtoupper(Auth::user()->username) == "PI2206060") {
                $permission = 1;
            } else {
                $permission = 0;
            }
        } else {
            $permission = 0;
        }

        $op_mtc = EmployeeSync::leftJoin('employees', 'employees.employee_id', '=', 'employee_syncs.employee_id')
        ->whereRaw('(department = "Maintenance Department" OR department = "Management Information System Department")')
        ->whereNull('employee_syncs.end_date')
        ->select('tag', 'employee_syncs.name', 'employee_syncs.employee_id')
        ->get();

        $machine_list = MaintenancePlanItem::whereNull('remark')->select('machine_id', 'machine_name', 'description', 'location')->get();

        return view('maintenance.inventory', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'uom_list' => $this->uom,
            'category_list' => $this->inv_ctg,
            'rack_list' => $this->inv_rack,
            'machine_list' => $machine_list,
            'permission' => $permission,
            'op_mtc' => $op_mtc,
        )
    )->with('page', 'Spare Part')->with('head', 'Maintenance');
    }

    public function indexInventoryTransaction($stat)
    {
        $title = 'Maintenance Inventories Transaction';
        $title_jp = '??';

        if (Auth::user()->role_code == "MIS" || strtoupper(Auth::user()->username) == "PI2003013") {
            $permission = 1;
        } else {
            $permission = 0;
        }

        return view('maintenance.inventory_transaction', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'permission' => $permission,
        )
    )->with('page', 'Spare Part Transaction')->with('head', 'Maintenance');
    }

    public function indexPlannedMonitoring()
    {
        $title = 'Planned Maintenance Monitoring';
        $title_jp = '??';

        // $week = db::table("weekly_calendars")->select("week_name")
        // ->whereRaw("DATE_FORMAT(week_date, '%Y-%m') = '".$tgl."'")->groupBy("week_name")->get();

        return view('maintenance.planned.maintenance_plan_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Planned Maintenance Monitoring')->with('head', 'Maintenance');
    }

    public function indexPlannedSchedule()
    {
        $title = 'Planned Maintenance Schedule';
        $title_jp = '??';

        return view('maintenance.planned.maintenance_plan_schedule', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Planned Maintenance Schedule')->with('head', 'Maintenance');
    }

    public function indexPlanMaster()
    {
        $title = 'Planned Maintenance Data';
        $title_jp = '??';

        return view('maintenance.planned.maintenance_plan', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Planned Maintenance Data')->with('head', 'Maintenance');
    }

    public function indexPlannedForm()
    {
        $title = 'Planned Maintenance Check';
        $title_jp = '??';

        $item_check = MaintenancePlanItem::select('machine_id', 'maintenance_plan_items.machine_name', 'description', 'location', 'maintenance_plan_item_checks.remark')
        ->leftJoin('maintenance_plan_item_checks', 'maintenance_plan_item_checks.machine_name', '=', 'maintenance_plan_items.machine_name')
        ->groupBy('machine_id', 'maintenance_plan_items.machine_name', 'description', 'location', 'maintenance_plan_item_checks.remark')
        ->get();

        $op_mtc = EmployeeSync::where('department', '=', 'Maintenance Department')->whereNull('end_date')->select('employee_id', 'name')->orderBy('name', 'asc')->get();

        return view('maintenance.planned.maintenance_plan_form', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'item_check' => $item_check,
            'mtc_op' => $op_mtc,
        )
    )->with('page', 'Planned Maintenance Form')->with('head', 'Maintenance');
    }

    public function indexPIC($cat)
    {
        $title = 'Maintenance PICs';
        $title_jp = '??';

        return view('maintenance.maintenance_pic', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('head', 'Maintenance');
    }

    public function indexSPKUrgent()
    {
        $title = 'Urgent Maintenance SPK';
        $title_jp = '??';

        $data = db::select("SELECT mjo.order_no, department_shortname as department, priority, description, date(mjo.created_at) as req_date, start_actual, process_name from maintenance_job_orders as mjo
         left join departments on departments.department_name = SUBSTRING_INDEX(mjo.section,'_',1)
         left join (select process_code, process_name from processes where remark = 'Maintenance') as prs on prs.process_code = mjo.remark
         left join maintenance_job_processes as mjp on mjo.order_no = mjp.order_no
         where priority = 'Urgent' and mjo.remark <> 6");

        return view('maintenance.maintenance_urgent', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'datas' => $data,
        )
    )->with('head', 'Maintenance');
    }

    public function indexSPKUrgentReport()
    {
        $title = 'Urgent Maintenance SPK Monitoring';
        $title_jp = '??';

        return view('maintenance.maintenance_urgent_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Urgent Maintenance SPK Monitoring')->with('head', 'Maintenance');
    }

    public function indexSPKWeekly()
    {
        $title = 'Maintenance SPK Weekly Report';
        $title_jp = '??';

        return view('maintenance.maintenance_spk_weekly', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Maintenance SPK Weekly Report')->with('head', 'Maintenance');
    }

    public function indexMachineHistory()
    {
        $title = 'Maintenance Machine Log';
        $title_jp = '??';

        $location = MaintenancePlanItem::select('location')->distinct()->get();
        $machine = MaintenancePlanItem::select('location', 'machine_id', 'description')->get();

        return view('maintenance.machine_history_list', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $location,
            'machine' => $machine,
        )
    )->with('page', 'Machine Logs')->with('head', 'Maintenance');
    }

    public function indexOperatorPosition()
    {
        $title = 'Maintenance Operator Location';
        $title_jp = '保全班作業者の位置';

        $machine = MaintenancePlanItem::select('machine_id', 'description', 'area', 'location')->get();
        $area = AreaCode::select('area_code', 'area', 'remark')->get();

        $op = db::select("SELECT sunfish_shift_syncs.employee_id, `name`, shiftdaily_code, attend_code FROM `sunfish_shift_syncs`
         left join employee_syncs on sunfish_shift_syncs.employee_id = employee_syncs.employee_id
         where shift_date = '" . date('Y-m-d') . "'
         and `group` = 'Maintenance Group'
         and end_date is null");

        return view('maintenance.operator_position', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'machine' => $machine,
            'area' => $area,
            'op_shift' => $op,
            'loc_arr' => $this->location,
        )
    )->with('page', 'Operator Position')->with('head', 'Maintenance');
    }

    public function indexOperator()
    {
        $title = 'Sign Area - Maintenance Operator';
        $title_jp = '保全対象エリア';

        $machine = MaintenancePlanItem::select('machine_id', 'description', 'area', 'location')->get();
        $area = AreaCode::select('area_code', 'area', 'remark')->get();

        return view('maintenance.operator_area', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'machine' => $machine,
            'area' => $area,
            'loc_arr' => $this->location,
        )
    )->with('page', 'MP Position')->with('head', 'Maintenance');
    }

    public function indexMttbf()
    {
        $title = 'Monthly Machine Down Time Data';
        $title_jp = '??';

        $machine_group = MaintenancePlanItem::select('machine_group')->whereNotNull('machine_group')->groupBy('machine_group')->get();

        $machine_location = MaintenancePlanItem::select('location')->groupBy('location')->get();

        return view('maintenance.machine_status', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'machine_group' => $machine_group,
            'machine_location' => $machine_location,
        )
    )->with('page', 'MTBF')->with('head', 'Maintenance');
    }

    public function indexMachineGraph()
    {
        $title = 'Maintenance Breakdown Graph';
        $title_jp = '??';

        $machine_group = MaintenancePlanItem::select('machine_group')->whereNotNull('machine_group')->groupBy('machine_group')->get();

        return view('maintenance.report.mttbf_report', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'machine_group' => $machine_group,
        )
    )->with('page', 'Maintenance Graph Report')->with('head', 'Maintenance');
    }

    public function indexOperatorWorkload()
    {
        $title = 'Maintenance Operator Workload';
        $title_jp = '??';

        return view('maintenance.report.operator_workload', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Maintenance Workload')->with('head', 'Maintenance');
    }

    public function indexSPKOperator()
    {
        $title = 'SPK Workload Operator';
        $title_jp = '??';

        return view('maintenance.report.spk_operator_workload', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Maintenance SPK Workload')->with('head', 'Maintenance');
    }

    public function indextpm()
    {
        $title = 'Smart TPM (Total Productive Maintenance)';
        $title_jp = '';

        return view('maintenance.tpm.dashboard', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Maintenance SPK Workload')->with('head', 'Maintenance');

    }

    public function indexMachinePartGraph()
    {
        $title = 'Machine Part Graph';
        $title_jp = '??';

        return view('maintenance.report.machine_part_graph', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Part Machine Graph')->with('head', 'Maintenance');
    }

    public function machineTroubleReport()
    {
        $title = 'Trouble Machine Report';
        $title_jp = '??';

        return view('maintenance.report.machine_trouble_report', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Part Machine')->with('head', 'Maintenance');

    }

    public function indexMachinePartList()
    {
        $title = 'Machine Part List';
        $title_jp = '??';

        $machine_group = MaintenancePlanItem::select('machine_group')->groupBy('machine_group')->get();
        $department = EmployeeSync::select('department')->where('division', '=', 'Production Division')->groupBy('department')->get();

        return view('maintenance.machine_part_list', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'machine_groups' => $machine_group,
            'departments' => $department,
        )
    )->with('page', 'Part Machine List')->with('head', 'Maintenance');
    }

    public function indexPlannedTrendline()
    {
        $title = 'Planned Maintenance Trendline';
        $title_jp = '??';

        $machine_group = MaintenancePlanItem::select('machine_name', 'description', 'machine_group')->orderBy('machine_group', 'asc')->get();

        return view('maintenance.planned.maintenance_plan_trendline', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'machine_groups' => $machine_group,
        )
    )->with('page', 'Planned Maintenance Trendline')->with('head', 'Maintenance');
    }

    public function indexPlannedFinding()
    {
        $title = 'Maintenance Finding Monitoring';
        $title_jp = '??';

        $mtc_user = EmployeeSync::where('department', '=', 'Maintenance Department')
        ->whereNull('end_date')
        ->select('employee_id', 'name')
        ->orderBy('name', 'ASC')
        ->get();

        $mesin = db::select("SELECT maintenance_plan_item_checks.machine_name, maintenance_plan_items.description, maintenance_plan_items.machine_group, item_check FROM `maintenance_plan_item_checks`
         left join maintenance_plan_items on maintenance_plan_items.machine_name = maintenance_plan_item_checks.machine_name
         where maintenance_plan_items.description is not null
         group by maintenance_plan_item_checks.machine_name, maintenance_plan_items.description, item_check, maintenance_plan_items.machine_group");

        return view('maintenance.planned.maintenance_plan_finding', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'user' => $mtc_user,
            'mesin' => $mesin,
        )
    )->with('page', 'Maintenance Planned Monitoring');
    }

    public function indexPlannedTemp()
    {
        $title = 'Preventive Maintenance';
        $title_jp = '';

        return view('maintenance.planned.maintenance_plan_temp', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
    )->with('page', 'Maintenance Planned Monitoring');
    }

    public function indexWWTLimbah()
    {
        $title = 'WWT - Waste Control';
        $title_jp = '??';

        $op = EmployeeSync::where('department', '=', 'Maintenance Department')->whereNull('end_date')->where('position', '<>', 'Manager')
        ->select('employee_id', 'name')
        ->orderBy('name', 'asc')
        ->get();

        return view('maintenance.wwt_limbah_form', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'limbah' => $this->limbah,
            'pics' => $op,
        )
    )->with('page', 'WWT Kontrol Limbah')->with('head', 'Maintenance');
    }

    public function indexElectricityDailyRatio()
    {
        $title = ' Daily Electricity Consumption Ratio';
        $title_jp = '日次電気消費率';

        $year = WeeklyCalendar::select(db::raw('date_format(week_date, "%Y") AS year'))->distinct()->get();

        return view('maintenance.electricity.electricity_ratio', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'years' => $year,
        )
    )->with('page', 'Electricity')->with('head', 'Maintenance');
    }

    public function indexElectricitySavingMonitor()
    {
        $title = ' Electricity Saving Monitor';
        $title_jp = '電気代削減の進捗管理';

        $year = WeeklyCalendar::select(db::raw('date_format(week_date, "%Y") AS year'))->distinct()->get();

        return view('maintenance.electricity.electricity_vs_sales', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'years' => $year,
        )
    )->with('page', 'Electricity')->with('head', 'Maintenance');

    }

    //update

    public function indexWWTLimbahUpdate()
    {
        $user = User::where('username', Auth::user()->username)
        ->select('username', 'role_code')
        ->first();

        $title = 'WWT - Waste Control';
        $title_jp = '??';

        $op = EmployeeSync::where('group', '=', 'WWT Group')->whereNull('end_date')->where('position', '<>', 'Manager')
        ->select('employee_id', 'name')
        ->orderBy('name', 'asc')
        ->get();

        $jb1 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Jumbo Bag')->where('category', '=', 'IN')->select('remaining_stock')->get();
        $jb_count_in = $jb1->count();
        $jb2 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Jumbo Bag')->where('category', '=', 'OUT')->select('remaining_stock')->get();
        $jb_count_out = $jb2->count();
        $jb_sisa = $jb_count_in - $jb_count_out;

        $pail1 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Pail')->where('category', '=', 'IN')->select('remaining_stock')->get();
        $pail_count_in = $pail1->count();
        $pail2 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Pail')->where('category', '=', 'OUT')->select('remaining_stock')->get();
        $pail_count_out = $pail2->count();
        $pail_sisa = $pail_count_in - $pail_count_out;

        $drum1 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Drum')->where('category', '=', 'IN')->select('remaining_stock')->get();
        $drum_count_in = $drum1->count();
        $drum2 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Drum')->where('category', '=', 'OUT')->select('remaining_stock')->get();
        $drum_count_out = $drum2->count();
        $drum_sisa = $drum_count_in - $drum_count_out;

        $karton1 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Karton')->where('category', '=', 'IN')->select('remaining_stock')->get();
        $karton_count_in = $karton1->count();
        $karton2 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Karton')->where('category', '=', 'OUT')->select('remaining_stock')->get();
        $karton_count_out = $karton2->count();
        $karton_sisa = $karton_count_in - $karton_count_out;

        $master = db::connection('ympimis_2')->table('waste_masters')->select('waste_category', 'unit_weight', 'remark')->orderBy('waste_category', 'asc')->get();

        $location = db::select('SELECT DISTINCT location FROM storage_locations ORDER BY location ASC');

        $vendor = db::connection('ympimis_2')->select('select vendor, short_name from waste_vendors');

        $select_limbah = db::connection('ympimis_2')->table('waste_details')->distinct()->select('waste_category')->where('category', 'IN')->orderBy('waste_category', 'asc')->get();

        return view('maintenance.wwt_index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'limbah' => $master,
            'pics' => $op,
            'jb_in' => $jb_count_in,
            'jb_out' => $jb_count_out,
            'jb_sisa' => $jb_sisa,
            'pail_in' => $pail_count_in,
            'pail_out' => $pail_count_out,
            'pail_sisa' => $pail_sisa,
            'drum_in' => $drum_count_in,
            'drum_out' => $drum_count_out,
            'drum_sisa' => $drum_sisa,
            'karton_in' => $karton_count_in,
            'karton_out' => $karton_count_out,
            'karton_sisa' => $karton_sisa,
            'user' => $user,
            'location' => $this->lokasi,
            'vendor' => $vendor,
            'select_limbah' => $select_limbah
        )
    )->with('page', 'WWT Kontrol Limbah B3')->with('head', 'Maintenance');
    }

    // -----------------------  END INDEX --------------------

    public function fetchElectricityConsumption(Request $request)
    {

        $fy = $request->get('fiscal_year');
        if (strlen($fy) <= 0) {
            $now = date('Y-m-d');
            $date = db::table('weekly_calendars')
            ->where('fiscal_year', $fy)
            ->first();

            $fy = $date->fiscal_year;
        }

        $calendar = db::table('weekly_calendars')
        ->where('fiscal_year', $fy)
        ->select(
            db::raw('DATE_FORMAT(week_date, "%Y-%m") AS month_dt'),
            db::raw('DATE_FORMAT(week_date, "%b %Y") AS month_txt')
        )
        ->distinct()
        ->orderBy('week_date', 'ASC')
        ->get();

        $response = array(
            'status' => true,
            'calendar' => $calendar,
            'fy' => $fy,
        );
        return Response::json($response);

    }

    public function fetchElectricityKaizenMonitor(Request $request)
    {

        $date = date('Y-m-d');
        if (strlen($request->get('date')) > 0) {
            $date = $request->get('date');
        }
        $location = $request->get('location');
        $type = $request->get('type');

        // GET MONTH
        $week_date = db::table('weekly_calendars')
        ->where('week_date', $date)
        ->first();
        $fy = $week_date->fiscal_year;

        $min = db::table('weekly_calendars')
        ->where('fiscal_year', $fy)
        ->orderBy('week_date', 'ASC')
        ->first();

        $max = db::table('weekly_calendars')
        ->where('fiscal_year', $fy)
        ->orderBy('week_date', 'DESC')
        ->first();

        $calendar = db::table('weekly_calendars')
        ->where('fiscal_year', $fy)
        ->select(
            db::raw('DATE_FORMAT(week_date, "%Y-%m") AS month_dt'),
            db::raw('DATE_FORMAT(week_date, "%b %Y") AS month_txt')
        )
        ->distinct()
        ->orderBy('week_date', 'ASC')
        ->get();
        // END GET MONTH

        // dd($min->week_date);

        $before = db::table('electricity_past_usages')
        ->where('type', $type)
        ->where('location', $location)
        ->get();

        $after = db::table('electricity_usages')
        ->where('location', $location)
        ->where('datetime', '>=', $min->week_date)
        ->where('datetime', '<=', $max->week_date)
        ->get();

        $response = array(
            'status' => true,
            'before' => $before,
            'after' => $after,
            'date' => $date,
            'location' => $location,
            'calendar' => $calendar,
        );
        return Response::json($response);

    }

    public function fetchElectricitySavingMonitor(Request $request)
    {

        $month = $request->get('month');
        if (strlen($month) <= 0) {
            $month = date('Y-m');
        }

        $weekly_calendar = db::table('weekly_calendars')
        ->where('week_date', 'LIKE', '%' . $month . '%')
        ->first();

        $months = db::table('weekly_calendars')
        ->where('fiscal_year', $weekly_calendar->fiscal_year)
        ->select(
            db::raw('DATE_FORMAT(week_date, "%b %y") AS month_text'),
            db::raw('DATE_FORMAT(week_date, "%Y-%m") AS month')
        )
        ->distinct()
        ->orderBy('week_date')
        ->get();

        $exchanges = db::table('sales_exchange_rates')
        ->select(
            'period',
            db::raw('usd_to_jpy AS rate')
        )
        ->get();

        $base_data = db::table('electricity_saving_yearly_resumes')
        ->where('fiscal_year', 'FY194')
        ->first();

        $this_month_sales = db::table('sales_resumes')
        ->where('bl_date', 'LIKE', '%' . $month . '%')
        ->get();

        $this_month_electricity = db::table('electricity_consumptions')
        ->where('date', 'LIKE', '%' . $month . '%')
        ->select(
            db::raw('COALESCE(consumption_outgoing_i, 0) AS consumption_outgoing_i'),
            db::raw('COALESCE(consumption_outgoing_ii, 0) AS consumption_outgoing_ii'),
            db::raw('COALESCE(consumption_outgoing_iii, 0) AS consumption_outgoing_iii'),
            db::raw('COALESCE(consumption_outgoing_iv, 0) AS consumption_outgoing_iv')
        )
        ->get();

        $monthly_data = db::table('electricity_saving_monthly_resumes')
        ->where('fiscal_year', $weekly_calendar->fiscal_year)
        ->get();

        $yearly_data = db::table('electricity_saving_yearly_resumes')->get();

        $curr = db::select("SELECT base.`month`, elec.elec, sales.amount, ex.usd_to_jpy FROM
            (SELECT * FROM electricity_saving_monthly_resumes
                WHERE `month` = '" . date('Y-m') . "'
                ) AS base
                LEFT JOIN
                (SELECT  DATE_FORMAT(date,'%Y-%m') AS `month`, SUM(consumption_outgoing_i+consumption_outgoing_ii+consumption_outgoing_iii+consumption_outgoing_iv) AS elec FROM electricity_consumptions
                WHERE DATE_FORMAT(date,'%Y-%m') = '" . date('Y-m') . "'
                GROUP BY DATE_FORMAT(date,'%Y-%m')
                ) AS elec
                ON base.`month` = elec.`month`
                LEFT JOIN
                (SELECT DATE_FORMAT(bl_date,'%Y-%m') AS `month`, SUM(quantity * price) AS amount FROM sales_resumes
                WHERE DATE_FORMAT(bl_date,'%Y-%m') = '" . date('Y-m') . "'
                GROUP BY DATE_FORMAT(bl_date,'%Y-%m')
                ) AS sales
                ON base.`month` = sales.`month`
                LEFT JOIN
                (SELECT DATE_FORMAT(period,'%Y-%m') AS `month`, usd_to_jpy FROM sales_exchange_rates
                WHERE DATE_FORMAT(period,'%Y-%m') = '" . date('Y-m') . "'
                ) AS ex
                ON base.`month` = ex.`month`");

            $fy = db::table('weekly_calendars')
            ->where('week_date', '=', date('Y-m-d'))
            ->first();

            $response = array(
                'status' => true,
                'last_update' => date('Y-m-d H:i:s'),
                'month_tittle' => date('M Y', strtotime($month . '-01')),
                'month' => date('Y-m'),
                'fy' => $fy->fiscal_year,
                'curr' => $curr,
                'months' => $months,
                'exchanges' => $exchanges,
                'base_data' => $base_data,
                'monthly_data' => $monthly_data,
                'this_month_sales' => $this_month_sales,
                'this_month_electricity' => $this_month_electricity,
                'yearly_data' => $yearly_data,
            );
            return Response::json($response);

        }

        public function fetchElectricityPln(Request $request)
        {

            $month = $request->get('month');
            if (strlen($month) <= 0) {
                $month = date('Y-m');
            }

            $year = db::table('weekly_calendars')
            ->where('week_date', 'LIKE', '%' . $month . '%')
            ->first();

            $data = db::select("SELECT `month`.`month`, ratio_data.electricity_consumption, ratio_data.sales, ex.usd_to_jpy FROM
                (SELECT DISTINCT DATE_FORMAT(week_date,'%Y-%m') AS `month` FROM weekly_calendars
                    WHERE fiscal_year = '" . $year->fiscal_year . "'
                    ) AS `month`
                    LEFT JOIN
                    (SELECT * FROM `electricity_saving_monthly_resumes`
                    WHERE fiscal_year = '" . $year->fiscal_year . "'
                    ) AS ratio_data
                    ON `month`.`month` = ratio_data.`month`
                    LEFT JOIN
                    (SELECT DATE_FORMAT(period,'%Y-%m') AS `month`, usd_to_jpy FROM sales_exchange_rates
                    WHERE fiscal_year = '" . $year->fiscal_year . "'
                    ) AS ex
                    ON ex.`month` = `month`.`month`
                    ORDER BY `month`.`month` ASC");

                $response = array(
                    'status' => true,
                    'data' => $data,
                );
                return Response::json($response);

            }

            public function fetchElectricityTarget(Request $request)
            {

                $year = $request->get('year');
                if (strlen($year) <= 0) {
                    $year = date('Y');
                }

                $target = ElectricityTarget::where('year', $year)
                ->select(
                    'electricity_targets.*',
                    db::raw('DATE_FORMAT(electricity_targets.month, "%b %Y") AS month_name')
                )
                ->get();

                $response = array(
                    'status' => true,
                    'target' => $target,
                );
                return Response::json($response);

            }

            public function fetchElectricityDailyRatio(Request $request)
            {

                $month = $request->get('month');
                if (strlen($month) <= 0) {
                    $month = date('Y-m');
                }

                $target = ElectricityTarget::where('month', $month . '-01')->first();

                $calendar = db::select("SELECT fiscal_year, week_date, DATE_FORMAT(week_date,'%e %b') AS date_name, remark FROM weekly_calendars
                 WHERE DATE_FORMAT(week_date,'%Y-%m') = '" . $month . "'
                 ORDER BY week_date ASC");

                $consumption = ElectricityConsumption::where('date', 'LIKE', '%' . $month . '%')
                ->orderBy('date', 'ASC')
                ->get();

                $fy = db::select('SELECT
                 fiscal_year
                 FROM
                 weekly_calendars
                 ORDER BY
                 fiscal_year DESC
                 LIMIT 1');

                $weekly_calendar = db::select('SELECT DISTINCT
                 date_format( week_date, "%b %Y" ) as bulan
                 FROM
                 weekly_calendars
                 WHERE
                 fiscal_year = "' . $fy[0]->fiscal_year . '"
                 ORDER BY
                 id ASC');

                $month_consumption = db::select('SELECT
                 DATE_FORMAT( `date`, "%b %Y" ) AS date,
                 sum( consumption_outgoing_i ) AS consumption_outgoing_i,
                 sum( consumption_outgoing_ii ) AS consumption_outgoing_ii,
                 sum( consumption_outgoing_iii ) AS consumption_outgoing_iii,
                 sum( consumption_outgoing_iv ) AS consumption_outgoing_iv
                 FROM
                 electricity_consumptions
                 GROUP BY
                 DATE_FORMAT(
                     `date`,
                     "%b %Y")');

                $response = array(
                    'status' => true,
                    'last_update' => date('Y-m-d H:i:s'),
                    'month_name' => date('F Y', strtotime($month . '-01')),
                    'target' => $target,
                    'calendar' => $calendar,
                    'consumption' => $consumption,
                    'month_consumption' => $month_consumption,
                    'weekly_calendar' => $weekly_calendar,
                    'fy' => $fy,
                );
                return Response::json($response);
            }

            public function fetchElectricity(Request $request)
            {

                $month = $request->get('month');
                if (strlen($month) <= 0) {
                    $month = date('Y-m');
                }

                $electricity = WeeklyCalendar::leftJoin('electricity_consumptions', 'weekly_calendars.week_date', '=', 'electricity_consumptions.date')
                ->leftJoin('users', 'electricity_consumptions.created_by', '=', 'users.id')
                ->where('weekly_calendars.week_date', 'LIKE', '%' . $month . '%')
                ->select(
                    'electricity_consumptions.id',
                    'weekly_calendars.week_date',
                    'weekly_calendars.remark',

                    'electricity_consumptions.lbp1',
                    'electricity_consumptions.lbp2',
                    'electricity_consumptions.bp',
                    'electricity_consumptions.kvarh',

                    db::raw('ROUND(electricity_consumptions.lwbp1, 1) AS lwbp1'),
                    db::raw('ROUND(electricity_consumptions.lwbp2, 1) AS lwbp2'),
                    db::raw('ROUND(electricity_consumptions.wbp, 1) AS wbp'),
                    db::raw('ROUND(electricity_consumptions.consumption_kvarh, 1) AS consumption_kvarh'),

                    'electricity_consumptions.outgoing_i',
                    'electricity_consumptions.outgoing_ii',
                    'electricity_consumptions.outgoing_iii',
                    'electricity_consumptions.outgoing_iv',

                    db::raw('ROUND(electricity_consumptions.consumption_outgoing_i, 1) AS consumption_outgoing_i'),
                    db::raw('ROUND(electricity_consumptions.consumption_outgoing_ii, 1) AS consumption_outgoing_ii'),
                    db::raw('ROUND(electricity_consumptions.consumption_outgoing_iii, 1) AS consumption_outgoing_iii'),
                    db::raw('ROUND(electricity_consumptions.consumption_outgoing_iv, 1) AS consumption_outgoing_iv'),

                    'electricity_consumptions.created_at'
                )
                ->get();

                return DataTables::of($electricity)
                ->addColumn('edit', function ($electricity) {
                    $value = '<br><br>';
                // if ($electricity->remark == 'H') {
                //     $value = '<span>Holiday</span>';
                // }

                    if (!is_null($electricity->id)) {
                        $value = '<a href="javascript:void(0)" style="padding: 2px 10px 3px 10px;" class="btn btn-sm btn-danger" onClick="deleteData(id)" id="' . $electricity->id . '"><i class="fa fa-trash"></i>&nbsp;&nbsp;Delete</a>';
                    }

                // $value = '<br>-<br>';

                    return $value;
                })
                ->rawColumns([
                    'edit' => 'edit',
                ])
                ->make(true);

            }

            public function fetchMaintenance(Request $request)
            {
                $emp = Auth::user()->username;

                $datas = MaintenanceJobOrder::leftJoin(db::raw('(SELECT process_code ,process_name from processes where remark = "maintenance") as process'), 'process.process_code', '=', 'maintenance_job_orders.remark')
                ->select('id', 'order_no', db::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'), 'priority', 'type', 'target_date', 'description', 'process_name', 'remark')
                ->where('created_by', '=', $emp);

                if ($request->get('status') != 'all') {
                    $datas = $datas->where('remark', '=', $request->get('status'));
                }

                $datas = $datas->orderBy('created_at', 'desc')->get();

                $response = array(
                    'status' => false,
                    'datas' => $datas,
                );
                return Response::json($response);
            }

            public function fetchSPK()
            {
                DB::connection()->enableQueryLog();
                $spk = MaintenanceJobProcess::leftJoin("maintenance_job_orders", "maintenance_job_orders.order_no", "=", "maintenance_job_processes.order_no")
                ->leftJoin(db::raw('(select * from processes where remark = "maintenance") as prc'), 'prc.process_code', '=', 'maintenance_job_orders.remark')
                ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'maintenance_job_orders.created_by')
                ->leftJoin('maintenance_plan_items', 'maintenance_plan_items.machine_id', '=', 'maintenance_job_orders.machine_name')
                ->where("operator_id", "=", Auth::user()->username)
                ->whereNull('maintenance_job_processes.deleted_at')
                ->whereNull('maintenance_job_orders.deleted_at')
                ->whereRaw("maintenance_job_orders.remark in (3,4,5,9)")
                ->select("maintenance_job_orders.order_no", "maintenance_job_orders.section", "priority", "type", "maintenance_job_orders.category", "machine_condition", "danger", "maintenance_job_orders.description", "target_date", "safety_note", "start_plan", "finish_plan", "start_actual", "finish_actual", db::raw("DATE_FORMAT(maintenance_job_orders.created_at,'%d-%m-%Y') as request_date"), 'name', "maintenance_job_orders.remark", "process_name", db::raw("maintenance_job_processes.remark as stat"), db::raw('maintenance_plan_items.description as machine_desc'), 'att', 'machine_remark', 'maintenance_plan_items.machine_group')
                ->orderBy("maintenance_job_orders.remark", "asc")
                ->get();

                $op_list = MaintenanceJobOrder::join('maintenance_job_processes', 'maintenance_job_processes.order_no', '=', 'maintenance_job_orders.order_no')
                ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'maintenance_job_processes.operator_id')
                ->select('maintenance_job_orders.order_no', db::raw('GROUP_CONCAT(SUBSTRING_INDEX(`name`," ",2)) as op_name'))
                ->whereIn('maintenance_job_orders.remark', [3, 4, 5, 9])
                ->groupBy('maintenance_job_orders.order_no')
                ->get();

                $response = array(
                    'status' => false,
                    'datas' => $spk,
                    'op_list' => $op_list,
                    'query' => DB::getQueryLog(),
            // 'proses_log' => $proc_log
                );
                return Response::json($response);
            }

            public function createSPK(Request $request)
            {
                $date = date('Y-m-d');
                $prefix_now = 'SPK' . date("y") . date("m");
                $code_generator = CodeGenerator::where('note', '=', 'spk')->first();

                if ($prefix_now != $code_generator->prefix) {
                    $code_generator->prefix = $prefix_now;
                    $code_generator->index = '0';
                    $code_generator->save();
                }

                $tanggal = $request->get('tanggal');
                $bagian = $request->get('bagian');
                $prioritas = $request->get('prioritas');
        // $jenis_pekerjaan = $request->get('jenis_pekerjaan');
                $kategori = $request->get('kategori');
                $kondisi_mesin = $request->get('kondisi_mesin');
                $bahaya = implode(", ", $request->get('bahaya'));
                $detail = str_replace('\n', ' ', $request->get('detail'));
                $machine_name = $request->get('nama_mesin');
                $machine_detail = $request->get('nama_mesin_detail');
                $reason_urgent = $request->get('reason_urgent');

                if (count($request->file('lampiran')) > 0) {
                    $num = 1;
                    $file = $request->file('lampiran');

                    $nama = $file->getClientOriginalName();

                    $filename = pathinfo($nama, PATHINFO_FILENAME);
                    $extension = pathinfo($nama, PATHINFO_EXTENSION);

                    $att = $filename . '_' . date('YmdHis') . $num . '.' . $extension;

                    $file->move('maintenance/spk_att/', $att);

                } else {
                    $att = null;
                }

        // if ($prioritas == "Urgent") {
                $target_time = $request->get('jam_target');

                $hour = sprintf('%02d', explode(':', $target_time)[0]);
                $minute = explode(':', $target_time)[1];

                $target = $request->get('target') . " " . $hour . ":" . $minute . ":00";
        // } else {
        //     $target = date("Y-m-d H:i:s", strtotime('+ 7 days'));
        // }

                $safety = $request->get('safety');

                $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                $order_no = $code_generator->prefix . $number;
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();

                if ($prioritas == 'Urgent') {
                    $remark = 0;
                } else {
                    $remark = 2;
                }

                $spk = new MaintenanceJobOrder([
                    'order_no' => $order_no,
                    'section' => $bagian,
                    'priority' => $prioritas,
            // 'type' => $jenis_pekerjaan,
                    'category' => $kategori,
                    'machine_name' => $machine_name,
                    'machine_remark' => $machine_detail,
                    'machine_condition' => $kondisi_mesin,
                    'danger' => $bahaya,
                    'description' => $detail,
                    'target_date' => $target,
                    'safety_note' => $safety,
                    'remark' => $remark,
                    'note' => $reason_urgent,
                    'att' => $att,
                    'created_by' => Auth::user()->username,
                ]);

                $spk_log = new MaintenanceJobOrderLog([
                    'order_no' => $order_no,
                    'remark' => $remark,
                    'created_by' => Auth::user()->username,
                ]);

                try {

                    DB::transaction(function () use ($spk, $spk_log) {
                        $spk->save();
                        $spk_log->save();
                    });

                    $data = db::select("SELECT spk.order_no, spk.priority, spk.type, spk.category, spk.machine_name as machine_temp, maintenance_plan_items.description as machine_desc, spk.machine_remark, spk.description, spk.machine_condition, spk.danger, spk.safety_note, u.`name`, spk.section, spk.target_date from maintenance_job_orders spk
                        left join employee_syncs u on spk.created_by = u.employee_id
                        left join maintenance_plan_items on maintenance_plan_items.machine_id = spk.machine_name
                        where order_no = '" . $order_no . "'");

                    if ($prioritas == 'Urgent') {
                        $remark = 0;

                        $ids = ['PI0004007', 'PI0805001'];
                // $ids = ['PI2002021', 'PI1910003'];

                        $phones = EmployeeSync::select('phone')->whereIn('employee_id', $ids)->get();
                        $phone_log = [];

                        foreach ($phones as $phone) {
                            $new_phone = substr($phone->phone, 1, 15);
                            $new_phone = '62' . $new_phone;

                    // array_push($phone_log, $new_phone);
                            $query_string = "api.aspx?apiusername=API3Y9RTZ5R6Y&apipassword=API3Y9RTZ5R6Y3Y9RT";
                            $query_string .= "&senderid=" . rawurlencode("PT YMPI") . "&mobileno=" . rawurlencode($new_phone);
                            $query_string .= "&message=" . rawurlencode(stripslashes("Ada SPK Urgent Dari " . $data[0]->name . ", Mohon segera cek MIRAI > SPK List. Terimakasih")) . "&languagetype=1";
                            $url = "http://gateway.onewaysms.co.id:10002/" . $query_string;
                            $fd = @implode('', file($url));
                        }

                // ----------- EMAIL ----------
                // Mail::to('susilo.basri@music.yamaha.com')
                // ->bcc(['aditya.agassi@music.yamaha.com', 'nasiqul.ibat@music.yamaha.com'])
                // ->send(new SendEmail($data, 'urgent_spk'));

                // Mail::to('nasiqul.ibat@music.yamaha.com')
                // ->send(new SendEmail($data, 'urgent_spk'));

                //  ------------------------- NOTIF EMAIL -----------------
                        Mail::to(['susilo.basri@music.yamaha.com', 'bambang.supriyadi@music.yamaha.com', 'nadif@music.yamaha.com', 'duta.narendratama@music.yamaha.com'])
                        ->bcc(['nasiqul.ibat@music.yamaha.com'])
                        ->send(new SendEmail($data, 'urgent_spk'));
                    } else {
                        if (strpos($bahaya, 'Bahan Kimia Beracun') !== false) {
                            $remark = 2;

                    // Mail::to(['rizal.yohandhi@music.yamaha.com', 'whica.parama@music.yamaha.com'])
                    // ->bcc(['aditya.agassi@music.yamaha.com', 'nasiqul.ibat@music.yamaha.com'])
                    // ->send(new SendEmail($data, 'chemical_spk'));

                            Mail::to(['priyo.jatmiko@music.yamaha.com', 'whica.parama@music.yamaha.com'])
                            ->cc(['nasiqul.ibat@music.yamaha.com'])
                            ->send(new SendEmail($data, 'chemical_spk'));
                        }
                    }


                    if ($kategori == 'Mesin Trouble') {
                        $mesin = MaintenancePlanItem::where('machine_id', '=', $machine_name)
                        ->whereIn('class', ['S','A'])
                        ->select('machine_group', 'description', 'machine_name')->first();

                        if (count($mesin) < 1) {
                    // $mesin = ['machine_name' => null,'machine_group' => str_replace(" Section","",explode('_', $bagian)[1]), 'description' => $machine_detail];
                        } else {
                            $mesin = $mesin->toArray();
                            db::table('maintenance_machine_trouble_logs')->insert([
                                'machine_code' => $mesin['machine_name'],
                                'machine_name' => $mesin['description'],
                                'machine_location' => $mesin['location'],
                                'status_machine' => 'Machine Trouble',
                                'started_at' => date('Y-m-d H:i:s'),
                                'created_by' => Auth::user()->username,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                        }

                    }

                    $response = array(
                        'status' => true,
                        'message' => "Pembuatan SPK berhasil",
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

            public function editSPK(Request $request)
            {
                MaintenanceJobOrder::where('order_no', '=', $request->get("spk_edit"))
                ->update([
                    'machine_condition' => $request->get("kondisi_mesin_edit"),
                    'type' => $request->get("workType_edit"),
                    'category' => $request->get("kategori_edit"),
                    'danger' => implode(", ", $request->get('bahaya_edit')),
                    'machine_name' => $request->get('mesin_edit'),
                    'machine_remark' => $request->get('nama_mesin_detail'),
                    'description' => $request->get('uraian_edit'),
                    'safety_note' => $request->get('keamanan_edit'),
                    'note' => $request->get('reason_urgent_edit'),
                ]);

                $response = array(
                    'status' => true,
                );
                return Response::json($response);
            }

            public function updateSPK(Request $request)
            {
                MaintenanceJobOrder::where('order_no', '=', $request->get("order_no"))
                ->update([
                    'description' => $request->get('uraian'),
                ]);

                $response = array(
                    'status' => true,
                );
                return Response::json($response);
            }

            public function cancelSPK(Request $request)
            {
                MaintenanceJobOrder::where('order_no', '=', $request->get("order_no"))
                ->update([
                    'remark' => 7,
                ]);

                $response = array(
                    'status' => true,
                );
                return Response::json($response);
            }

            public function fetchSPKProgress(Request $request)
            {
                if ($request->get('from')) {
                    $from = $request->get('from');
                } else {
                    $from = date('Y-m-d', strtotime("-1 month"));
                    if ($from < '2021-01-01') {
                        $from = '2021-01-01';
                    }
                }

                if ($request->get('to')) {
                    $to = $request->get('to');
                } else if ($request->get('from')) {
                    $to = $request->get('from');
                } else {
                    $to = date('Y-m-d');
                }

                $get_data = db::select('
                 SELECT order_no, GROUP_CONCAT(priority) as priority, GROUP_CONCAT(bagian) bagian, GROUP_CONCAT(category) as category,GROUP_CONCAT(description)	description,GROUP_CONCAT(request_date)	request_date,GROUP_CONCAT(requester) requester,GROUP_CONCAT(inprogress) 	inprogress,GROUP_CONCAT(pic)	pic,GROUP_CONCAT(target_date)	target_date,GROUP_CONCAT(target)	target,GROUP_CONCAT(process_code)	process_code,GROUP_CONCAT(process_name)	process_name,GROUP_CONCAT(`status`)	`status`,GROUP_CONCAT(cause) cause,GROUP_CONCAT(handling) handling, GROUP_CONCAT(created_at ) created_at from
                 (select * from
                     (SELECT DISTINCT maintenance_job_orders.order_no, priority, category, department_shortname as bagian, maintenance_job_orders.description, DATE_FORMAT(maintenance_job_orders.created_at,"%Y-%m-%d") as request_date, `name` as requester, inprogress.created_at as inprogress, pic, date(target_date) as target_date, target_date as target, process_code, process_name, maintenance_job_pendings.status,null cause, null handling, maintenance_job_orders.created_at FROM `maintenance_job_orders`
                         left join employee_syncs on maintenance_job_orders.created_by = employee_syncs.employee_id
                         left join (
                             select order_no, GROUP_CONCAT(`name`) as pic from maintenance_job_processes
                             left join employee_syncs on employee_syncs.employee_id = maintenance_job_processes.operator_id
                             where deleted_at is null
                             group by order_no
                             ) as prcs on prcs.order_no = maintenance_job_orders.order_no
                         left join (select * from maintenance_job_processes where start_actual is not null and deleted_at is null) as inprogress on maintenance_job_orders.order_no = inprogress.order_no
                         left join (select process_code, process_name from processes where remark = "maintenance") l_pcr on l_pcr.process_code = maintenance_job_orders.remark
                         left join maintenance_job_pendings on maintenance_job_orders.order_no = maintenance_job_pendings.order_no
                         left join departments on SUBSTRING_INDEX(maintenance_job_orders.section,"_",1) = departments.department_name
                         where maintenance_job_orders.remark <> 8
                         and date(maintenance_job_orders.created_at) >= "' . $from . '" and date(maintenance_job_orders.created_at) <= "' . $to . '"
                         and maintenance_job_orders.deleted_at is null
                         order by target asc
                         ) as awal
                         union all

                         SELECT maintenance_job_reports.order_no, null priority,null category, null bagian, null	description,null	request_date,null	requester,null 	inprogress,null	pic,null	target_date,null	target,null	process_code,null	process_name,null	`status`, cause, handling, null as created_at FROM maintenance_job_reports left join maintenance_job_orders on maintenance_job_orders.order_no = maintenance_job_reports.order_no where maintenance_job_reports.id in (SELECT max(id) FROM maintenance_job_reports GROUP BY order_no) and date(maintenance_job_orders.created_at) >= "' . $from . '" and date(maintenance_job_orders.created_at) <= "' . $to . '") alls
                         group by order_no
                         order by target asc
                         ');

        // $data_progress = db::select('
        //     SELECT maintenance_job_orders.order_no, IF(TIMESTAMPDIFF(SECOND, maintenance_job_order_logs.created_at, target_date) < 0, 0, TIMESTAMPDIFF(SECOND, maintenance_job_order_logs.created_at, target_date)) as plan_time,  TIMESTAMPDIFF(SECOND, maintenance_job_order_logs.created_at, now()) as act_time from maintenance_job_orders
        //     left join maintenance_job_order_logs on maintenance_job_order_logs.order_no = maintenance_job_orders.order_no
        //     where maintenance_job_orders.remark = 4 and maintenance_job_order_logs.remark = 4 and maintenance_job_order_logs.deleted_at is null
        //     order by maintenance_job_orders.order_no
        //     ');

                     $data_bar = db::select('SELECT DATE_FORMAT(mstr.dt,"%d %b %Y") dt, mstr.process_code, mstr.process_name, IFNULL(datas.jml,0) jml from
                         (select * from
                             (select date(created_at) as dt from maintenance_job_orders where remark <> 8 group by date(created_at)) tgl
                             cross join (select process_code, process_name from processes where remark = "maintenance") as prs
                             ) as mstr
                         left join (select remark ,date(created_at) as dt, count(remark) as jml from maintenance_job_orders where remark <> 8 group by remark, date(created_at)) as datas on mstr.dt = datas.dt and mstr.process_code = datas.remark
                         where mstr.dt >= "' . $from . '" and mstr.dt <= "' . $to . '"
                         order by mstr.dt asc, mstr.process_code asc');

                     $response = array(
                        'status' => true,
                        'datas' => $get_data,
            // 'progress' => $data_progress,
                        'data_bar' => $data_bar,
                    );
                     return Response::json($response);
                 }

                 public function fetchSPKProgressDetail(Request $request)
                 {

                    $detail = 'SELECT maintenance_job_orders.order_no, department_shortname as bagian, priority, type, maintenance_job_orders.category, maintenance_job_orders.machine_name as machine_temp, maintenance_plan_items.description as machine_desc, machine_remark, maintenance_job_orders.description, DATE_FORMAT(maintenance_job_orders.created_at,"%d %b %Y") target_date, process_code, employee_syncs.name, date(maintenance_job_orders.created_at) as dt, maintenance_job_orders.created_at';

                    if ($request->get('process_name') != 'Listed' && $request->get('process_name') != 'InProgress') {
                        $detail .= ', cause, handling';
                    }

                    $detail .= ' from maintenance_job_orders
                    left join (select process_code, process_name from processes where remark = "maintenance") prs on prs.process_code = maintenance_job_orders.remark
                    left join maintenance_plan_items on maintenance_plan_items.machine_id = maintenance_job_orders.machine_name
                    left join employee_syncs on employee_syncs.employee_id = maintenance_job_orders.created_by
                    left join departments on departments.department_name = SUBSTRING_INDEX(maintenance_job_orders.section,"_",1)';

                    if ($request->get('process_name') != 'Listed' && $request->get('process_name') != 'InProgress') {
                        $detail .= ' left join (SELECT order_no, operator_id, cause, handling FROM maintenance_job_reports where id in (SELECT max(id) FROM maintenance_job_reports GROUP BY order_no)) as rpt on maintenance_job_orders.order_no = rpt.order_no';
                    }

                    $detail .= ' where DATE_FORMAT(maintenance_job_orders.created_at,"%d %b %Y") = "' . $request->get('date') . '" and process_name = "' . $request->get('process_name') . '" order by order_no asc';

                    $bar_detail = db::select($detail);

                    $finish_detail = db::select('select order_no, remark, created_at from  maintenance_job_order_logs where remark = 6 and DATE_FORMAT(maintenance_job_order_logs.created_at,"%d %b %Y") >= "' . $request->get('date') . '" order by remark asc');

                    $response = array(
                        'status' => true,
                        'datas' => $bar_detail,
                        'finish_detail' => $finish_detail,
                    );
                    return Response::json($response);
                }

                public function fetchSPKOperator(Request $request)
                {
                    $data_op = MaintenanceJobOrder::leftJoin('maintenance_job_processes', 'maintenance_job_orders.order_no', '=', 'maintenance_job_processes.order_no')
                    ->whereIn('maintenance_job_orders.remark', [3, 4])
                    ->whereNull('maintenance_job_processes.deleted_at')
                    ->select('maintenance_job_orders.order_no', 'maintenance_job_processes.operator_id', 'start_actual', 'start_plan')
                    ->get();

                    $response = array(
                        'status' => true,
                        'datas' => $data_op,
                    );
                    return Response::json($response);
                }

                public function fetchMaintenanceList(Request $request)
                {
                    DB::connection()->enableQueryLog();
                    $maintenance_job_orders = MaintenanceJobOrder::leftJoin(db::raw("(select process_code, process_name from processes where remark = 'maintenance') AS process"), "maintenance_job_orders.remark", "=", "process.process_code")
                    ->leftJoin(db::raw("(select order_no, operator_id, start_actual, finish_actual from maintenance_job_processes where deleted_at is null) as maintenance_job_processes"), "maintenance_job_processes.order_no", "=", "maintenance_job_orders.order_no")
                    ->leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "maintenance_job_orders.created_by")
                    ->leftJoin(db::raw("employee_syncs AS es"), "es.employee_id", "=", "maintenance_job_processes.operator_id")
                    ->leftJoin("maintenance_job_pendings", "maintenance_job_orders.order_no", "=", "maintenance_job_pendings.order_no")
                    ->leftJoin("maintenance_plan_items", "maintenance_job_orders.machine_name", "=", "maintenance_plan_items.machine_id")
                    ->select("maintenance_job_orders.order_no", "maintenance_job_orders.section", "priority", "type", "machine_condition", "danger", "target_date", db::raw("employee_syncs.`name` as requester"), db::raw("es.`name` as operator"), db::raw('DATE_FORMAT(maintenance_job_orders.created_at, "%Y-%m-%d") as date'), "process_name", "maintenance_job_orders.remark", "maintenance_job_orders.category", "start_actual", "finish_actual", "maintenance_job_pendings.status", "maintenance_job_orders.note", 'maintenance_job_orders.description', db::raw('maintenance_plan_items.description AS machine_desc'));

                    if (strlen($request->get('reqFrom')) > 0) {
                        $reqFrom = date('Y-m-d', strtotime($request->get('reqFrom')));
                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '>=', $reqFrom);
                    }
                    if (strlen($request->get('reqTo')) > 0) {
                        $reqTo = date('Y-m-d', strtotime($request->get('reqTo')));
                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '<=', $reqTo);
                    }
                    if (strlen($request->get('targetFrom')) > 0) {
                        $targetFrom = date('Y-m-d', strtotime($request->get('targetFrom')));
                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '>=', $targetFrom);
                    }
                    if (strlen($request->get('targetTo')) > 0) {
                        $targetTo = date('Y-m-d', strtotime($request->get('targetTo')));
                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '<=', $targetTo);
                    }
                    if (strlen($request->get('finFrom')) > 0) {
                        $finFrom = date('Y-m-d', strtotime($request->get('finFrom')));
                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '>=', $finFrom);
                    }
                    if (strlen($request->get('finTo')) > 0) {
                        $finTo = date('Y-m-d', strtotime($request->get('finTo')));
                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '<=', $finTo);
                    }
                    if (strlen($request->get('orderNo')) > 0) {
                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.order_no', '=', $request->get('orderNo'));
                    }
                    if (strlen($request->get('section')) > 0) {
                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.section', '=', $request->get('section'));
                    }
                    if (strlen($request->get('priority')) > 0) {
                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.priority', '=', $request->get('priority'));
                    }
                    if (strlen($request->get('workType')) > 0) {
                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.type', '=', $request->get('workType'));
                    }
                    if (strlen($request->get('remark')) > 0) {
                        if ($request->get('remark') != 'all') {
                            $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.remark', '=', $request->get('remark'));
                        }
                    } else {
                        $maintenance_job_orders = $maintenance_job_orders->whereIn('maintenance_job_orders.remark', [0, 2]);
                    }
                    if (strlen($request->get('status')) > 0) {
                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_pendings.status', '=', $request->get('status'));
                    }
                    if (strlen($request->get('username')) > 0) {
                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.created_by', '=', $request->get('username'));
                    }
                    if (strlen($request->get('category')) > 0) {
                        if ($request->get('category') != 'all') {
                            $keys = [];
                            foreach ($this->spk_category as $ctg) {
                                if ($ctg['category'] === $request->get('category')) {
                                    array_push($keys, $ctg['category']);
                                }
                            }

                            $maintenance_job_orders = $maintenance_job_orders->whereIn('maintenance_job_orders.category', $keys);
                        }
                    }

                    if (strlen($request->get('pic')) > 0) {
                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_processes.operator_id', '=', $request->get('pic'));
                    }

                    $maintenance_job_orders = $maintenance_job_orders->orderBy('maintenance_job_orders.created_at', 'desc')
                    ->get();

                    $response = array(
                        'status' => true,
                        'tableData' => $maintenance_job_orders,
                        'query' => DB::getQueryLog(),
                    );
                    return Response::json($response);
                }

                public function fetchMaintenanceDetail(Request $request)
                {
                    DB::connection()->enableQueryLog();
                    $detail = MaintenanceJobOrder::where("maintenance_job_orders.order_no", "=", $request->get('order_no'))
                    ->whereNull('maintenance_job_processes.deleted_at')
                    ->leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "maintenance_job_orders.created_by")
                    ->leftJoin("maintenance_job_processes", "maintenance_job_processes.order_no", "=", "maintenance_job_orders.order_no")
                    ->leftJoin('maintenance_job_reports', function ($join) {
                        $join->on('maintenance_job_reports.order_no', '=', 'maintenance_job_processes.order_no');
                        $join->on('maintenance_job_reports.operator_id', '=', 'maintenance_job_processes.operator_id');
                    })
                    ->leftJoin("maintenance_job_pendings", "maintenance_job_orders.order_no", "=", "maintenance_job_pendings.order_no")
                    ->leftJoin(db::raw("employee_syncs as  es"), "es.employee_id", "=", "maintenance_job_processes.operator_id")
                    ->leftJoin(db::raw("(select process_code, process_name from processes where remark = 'maintenance') AS process"), "maintenance_job_orders.remark", "=", "process.process_code")
                    ->leftJoin("maintenance_plan_items", "maintenance_plan_items.machine_id", "=", "maintenance_job_orders.machine_name")
                    ->select("maintenance_job_orders.order_no", "employee_syncs.name", db::raw('DATE_FORMAT(maintenance_job_orders.created_at, "%Y-%m-%d %H-%i") as date'), "priority", "maintenance_job_orders.section", "type", "maintenance_job_orders.category", "machine_condition", "danger", "maintenance_job_orders.description", "safety_note", "target_date", "process_name", db::raw("es.name as name_op"), db::raw("es.employee_id as id_op"), db::raw("DATE_FORMAT(maintenance_job_processes.start_actual, '%Y-%m-%d %H:%i') start_actual"), db::raw("DATE_FORMAT(maintenance_job_processes.finish_actual, '%Y-%m-%d %H:%i') finish_actual"), "maintenance_job_pendings.status", db::raw("maintenance_job_pendings.description as pending_desc"), "maintenance_job_orders.machine_name", "cause", "handling", "photo", "note", "machine_remark", db::raw("maintenance_plan_items.description as machine_desc"), "maintenance_plan_items.area", "att", db::raw("maintenance_job_pendings.remark as pending_remark"), 'prevention', 'cause_photo', 'handling_photo', 'prevention_photo', 'note', 'reject_reason')
                    ->get();

                    $parts = MaintenanceJobOrder::where('maintenance_job_orders.order_no', '=', $request->get('order_no'))
                    ->join("maintenance_job_spareparts", "maintenance_job_spareparts.order_no", "=", "maintenance_job_orders.order_no")
                    ->join("maintenance_inventories", "maintenance_inventories.part_number", "=", "maintenance_job_spareparts.part_number")
                    ->select("maintenance_job_orders.order_no", "maintenance_job_spareparts.part_number", "maintenance_inventories.part_name", "maintenance_inventories.specification", "maintenance_job_spareparts.quantity")
                    ->get();

                    $response = array(
                        'status' => true,
                        'detail' => $detail,
                        'part' => $parts,
                        'query' => DB::getQueryLog(),
                    );
                    return Response::json($response);
                }

                public function postMemberSPK(Request $request)
                {
                    $order_no = $request->get("order_no");
                    $member = $request->get("member");
                    $datas = [];

                    MaintenanceJobProcess::where('order_no', '=', $order_no)->forceDelete();

                    foreach ($member as $mbr) {
                        $jp = new MaintenanceJobProcess;
                        $jp->order_no = $order_no;
                        $jp->operator_id = $mbr['operator'];
                        $jp->start_plan = $mbr['start_date'] . " " . $mbr['start_time'];
                        $jp->finish_plan = $mbr['finish_date'] . " " . $mbr['finish_time'];
                        $jp->created_by = strtoupper(Auth::user()->username);
                        $jp->save();
                    }
                    ;

                    MaintenanceJobOrder::where('order_no', '=', $order_no)
                    ->update(['remark' => 3]);

                    $log = new MaintenanceJobOrderLog;
                    $log->order_no = $order_no;
                    $log->remark = 3;
                    $log->created_by = Auth::user()->username;
                    $log->save();

                    $response = array(
                        'status' => true,
                    );
                    return Response::json($response);
                }

                public function postNewMemberSPK(Request $request)
                {
                    $order_no = $request->get("order_no");
                    $member = $request->get("member");
                    $datas = [];

                    MaintenanceJobProcess::where('order_no', '=', $order_no)->delete();

                    foreach ($member as $mbr) {
                        $jp = new MaintenanceJobProcess;
                        $jp->order_no = $order_no;
                        $jp->operator_id = $mbr['operator'];
                        $jp->start_plan = $mbr['start_date'] . " " . $mbr['start_time'];
                        $jp->finish_plan = $mbr['finish_date'] . " " . $mbr['finish_time'];
                        $jp->created_by = strtoupper(Auth::user()->username);
                        $jp->save();
                    }

                    MaintenanceJobOrder::where('order_no', '=', $order_no)
                    ->update(['remark' => 3, 'description' => $request->get('uraian')]);

                    $log = new MaintenanceJobOrderLog;
                    $log->order_no = $order_no;
                    $log->remark = 3;
                    $log->created_by = Auth::user()->username;
                    $log->save();

                    $response = array(
                        'status' => true,
                    );
                    return Response::json($response);
                }

                public function verifySPK(Request $request)
                {
                    $stat = $request->get('stat');
                    $order_no = $request->get('order_no');

                    $get_spk = MaintenanceJobOrder::where('order_no', '=', $order_no)->select('remark', 'target_date', 'danger', db::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as tanggal'), 'priority')->first();

                    if ($get_spk->remark != "2") {
                        if ($stat == "1") {
                            $target_date = $get_spk->target_date;
                            $priority = "Urgent";
                            $message2 = 'Berhasil di approve sebagai SPK dengan prioritas urgent';
                        } else {
                // $target_date = date("Y-m-d", strtotime("+7 day", strtotime($get_spk->tanggal)));
                            $priority = "Normal";
                            $message2 = $order_no . ' berubah sebagai SPK dengan prioritas normal';
                        }

                        $remark = "2";

                        try {
                            $spk = MaintenanceJobOrder::where('order_no', '=', $order_no)->first();
                            $spk->remark = $remark;
                            $spk->priority = $priority;
                // $spk->target_date = $target_date;
                // $spk->note = "Maintenance_OK";

                // $manager = EmployeeSync::where('position', '=', 'Manager')
                // ->where('department', '=', 'Maintenance')
                // ->first();

                            $spk_log = new MaintenanceJobOrderLog([
                                'order_no' => $order_no,
                                'remark' => $remark,
                                'created_by' => Auth::user()->username,
                            ]);

                            $spk->save();
                            $spk_log->save();

                            $message = 'SPK dengan Order No. ' . $order_no . ' Berhasil Aprove';

                            $response = array(
                                'status' => true,
                                'message' => $message,
                            );
                            return Response::json($response);

                // return view('maintenance.spk_approval_message', array(
                //     'head' => $order_no,
                //     'message' => $message,
                //     'message2' => $message2,
                // ))->with('page', 'SPK Approval');
                        } catch (QueryException $e) {
                            $message = 'SPK dengan Order No. ' . $order_no . ' Berhasil Aprove';

                            $response = array(
                                'status' => true,
                                'message' => $message,
                            );
                            return Response::json($response);

                // return view('maintenance.spk_approval_message', array(
                //     'head' => $order_no,
                //     'message' => 'Update Error',
                //     'message2' => $e->getMessage(),
                // ))->with('page', 'SPK Approval');
                        }
                    } else {
                        $message = 'SPK dengan Order No. ' . $order_no;
                        $message2 = 'Sudah di approve/reject';
                        return view('maintenance.spk_approval_message', array(
                            'head' => $order_no,
                            'message' => $message,
                            'message2' => $message2,
                        )
                    )->with('page', 'SPK Approval');
                    }
                }

                public function addDangerNote(Request $request)
                {
        // $get_spk = MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))->select('remark')->first();
        // if ($get_spk->remark != "2") {
        //     $remark = "2";

                    $spk = MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))->first();
        // $spk->remark = $remark;
                    $spk->safety_note = $request->get('danger_note');
        // $spk->note = "Chemical_OK Manager_None";

                    $chemical = EmployeeSync::whereNull('group')
                    ->where('section', '=', 'Chemical Process Control Section')
                    ->first();

                    $spk_log = new MaintenanceJobOrderLog([
                        'order_no' => $request->get('order_no'),
            // 'remark' => $remark,
                        'created_by' => $chemical->employee_id,
                    ]);

                    try {
                        $spk->save();
                        $spk_log->save();

                        $response = array(
                            'status' => true,
                            'message' => "Berhasil diverifikasi",
                        );
                        return Response::json($response);
                    } catch (QueryException $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
        // } else {
        //     $response = array(
        //         'status' => false,
        //         'message' => "Sudah di approve/reject",
        //     );
        //     return Response::json($response);
        // }
                }

                public function startSPK(Request $request)
                {
                    $spk_log = MaintenanceJobOrderLog::firstOrNew(array('order_no' => $request->get('order_no'), 'remark' => 4));
                    $spk_log->order_no = $request->get('order_no');
                    $spk_log->remark = 4;
                    $spk_log->created_by = Auth::user()->username;

                    $spk_log->save();

                    MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
                    ->update(['remark' => 4]);

                    MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
                    ->where('operator_id', '=', strtoupper(Auth::user()->username))
                    ->update(['start_actual' => date('Y-m-d H:i:s')]);

        //location
                    $mtc_op = new MaintenanceOperatorLocation;
                    $mtc_op->employee_id = Auth::user()->username;
                    $mtc_op->employee_name = Auth::user()->name;
                    $mtc_op->qr_code = $request->get('code');
                    $mtc_op->machine_id = $request->get('code');
                    $mtc_op->description = $request->get('location');
                    $mtc_op->location = $request->get('location');
                    $mtc_op->remark = 'spk';
                    $mtc_op->created_by = Auth::user()->username;
                    $mtc_op->save();

                    $area = db::select('SELECT master_area.*, maintenance_operator_locations.employee_id from
                     (SELECT machine_id as area_code, location FROM `maintenance_plan_items`
                         union all
                         SELECT area_code, area as location from area_codes
                         ) master_area
                     left join maintenance_operator_locations on maintenance_operator_locations.qr_code = master_area.area_code');

                    $response = array(
                        'status' => true,
                        'area' => $area,
                    );
                    return Response::json($response);
                }

                public function restartSPK(Request $request)
                {
                    $spk_log = new MaintenanceJobOrderLog([
                        'order_no' => $request->get('order_no'),
                        'remark' => 4,
                        'created_by' => Auth::user()->username,
                    ]);
                    $spk_log->save();

                    MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
                    ->update(['remark' => 4]);

                    MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
                    ->where('operator_id', '=', strtoupper(Auth::user()->username))
                    ->update(['start_actual' => date('Y-m-d H:i:s'), 'finish_actual' => null]);

                    $response = array(
                        'status' => true,
                    );
                    return Response::json($response);
                }

                public function reportingSPK(Request $request)
                {
        // $data = $request->get('foto');
                    $foto_cause = $request->get('foto_penyebab');
                    $foto_handling = $request->get('foto_penanganan');
                    $foto_prev = $request->get('foto_pencegahan');

                    define('UPLOAD_DIR', 'images/');
                    $upload = [];
                    $upload_cause = [];
                    $upload_handling = [];
                    $upload_prev = [];

                    $operator_id = Auth::user()->username;

                    try {
            // -------- FOTO RESULT ----------
            // $no = 1;
            // foreach ($data as $key) {
            //     if ($key != "") {
            //         $image_parts = explode(";base64,", $key);
            //         $image_type_aux = explode("image/", $image_parts[0]);
            //         $image_type = $image_type_aux[1];
            //         $image_base64 = base64_decode($image_parts[1]);

            //         $file = public_path().'\maintenance\\spk_report\\'.$request->get('order_no').$operator_id.$no.'.png';
            //         $file2 = $request->get('order_no').$operator_id.$no.'.png';

            //         file_put_contents($file, $image_base64);

            //         array_push($upload, $file2);
            //         $no++;
            //     }
            // }

            // ---------- FOTO PENYEBAB --------------
                        $no = 1;
                        foreach ($foto_cause as $key2) {
                            if ($key2 != "") {
                                $image_parts = explode(";base64,", $key2);
                                $image_type_aux = explode("image/", $image_parts[0]);
                                $image_type = $image_type_aux[1];
                                $image_base64 = base64_decode($image_parts[1]);

                                $file = public_path() . '\maintenance\\spk_report\\cause_' . $request->get('order_no') . $operator_id . $no . '.png';
                                $file3 = 'cause_' . $request->get('order_no') . $operator_id . $no . '.png';

                                file_put_contents($file, $image_base64);

                                array_push($upload_cause, $file3);
                                $no++;
                            }
                        }

            // ---------- FOTO PENANGANAN ------------
                        $no = 1;
                        foreach ($foto_handling as $key3) {
                            if ($key3 != "") {
                                $image_parts = explode(";base64,", $key3);
                                $image_type_aux = explode("image/", $image_parts[0]);
                                $image_type = $image_type_aux[1];
                                $image_base64 = base64_decode($image_parts[1]);

                                $file = public_path() . '\maintenance\\spk_report\\handling_' . $request->get('order_no') . $operator_id . $no . '.png';
                                $file4 = 'handling_' . $request->get('order_no') . $operator_id . $no . '.png';

                                file_put_contents($file, $image_base64);

                                array_push($upload_handling, $file4);
                                $no++;
                            }
                        }

            // ---------- FOTO PENCEGAHAN ------------
                        $no = 1;
                        foreach ($foto_prev as $key3) {
                            if ($key3 != "") {
                                $image_parts = explode(";base64,", $key3);
                                $image_type_aux = explode("image/", $image_parts[0]);
                                $image_type = $image_type_aux[1];
                                $image_base64 = base64_decode($image_parts[1]);

                                $file = public_path() . '\maintenance\\spk_report\\prev_' . $request->get('order_no') . $operator_id . $no . '.png';
                                $file5 = 'prev_' . $request->get('order_no') . $operator_id . $no . '.png';

                                file_put_contents($file, $image_base64);

                                array_push($upload_prev, $file5);
                                $no++;
                            }
                        }

                        MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
                        ->where('operator_id', '=', strtoupper(Auth::user()->username))
                        ->update(['finish_actual' => date('Y-m-d H:i:s')]);

                        $proc = MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
                        ->where('operator_id', '=', strtoupper(Auth::user()->username))
                        ->first();

                        $rpt = new MaintenanceJobReport;
                        $rpt->order_no = $request->get('order_no');
                        $rpt->operator_id = $operator_id;
                        $rpt->cause = $request->get('penyebab');
                        $rpt->handling = $request->get('penanganan');
                        $rpt->prevention = $request->get('pencegahan');

            // $rpt->photo = implode(", ",$upload);
                        $rpt->cause_photo = implode(", ", $upload_cause);
                        $rpt->handling_photo = implode(", ", $upload_handling);
                        $rpt->prevention_photo = implode(", ", $upload_prev);
                        $rpt->remark = $request->get('other_part');
                        $rpt->started_at = $proc->start_actual;
                        $rpt->finished_at = date('Y-m-d H:i:s');
                        $rpt->created_by = $operator_id;

                        $rpt->save();

                        $spk_log = new MaintenanceJobOrderLog();
                        $spk_log->order_no = $request->get('order_no');
                        $spk_log->remark = 6;
                        $spk_log->created_by = Auth::user()->username;

                        $spk_log->save();

                        $mjo = MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
                        ->first();

                        $mjo->update(['remark' => 6]);

                        $parts = $request->get('spare_part');

                        if ($parts) {
                            foreach ($parts as $prts) {
                                $spk_part = MaintenanceJobSparepart::firstOrNew(array('order_no' => $request->get('order_no'), 'part_number' => $prts['part_number']));
                                $spk_part->quantity = $prts['qty'];
                                $spk_part->created_by = Auth::user()->username;

                                $spk_part->save();

                    //kurang minus sctock
                                $stok_itm = MaintenanceInventory::where('part_number', '=', $prts['part_number'])
                                ->first();

                                MaintenanceInventory::where('part_number', '=', $prts['part_number'])
                                ->update(['stock' => $stok_itm->stock - (int) $prts['qty']]);

                                $inv_log = new MaintenanceInventoryLog([
                                    'part_number' => $prts['part_number'],
                                    'status' => 'out',
                                    'quantity' => $prts['qty'],
                                    'remark1' => 'SPK',
                                    'remark2' => $request->get('order_no'),
                                    'machine_id' => $mjo->machine_name,
                                    'created_by' => Auth::user()->username,
                                ]);
                                $inv_log->save();

                            }
                        }

                        if ($mjo->machine_name != "Lain - lain" && $mjo->machine_name) {

                            if ($request->get('trouble_part')) {
                                $t_part = $request->get('trouble_part');
                            } else {
                                $t_part = $request->get('other_trouble_part');

                                $machine_part = new MaintenanceJobPart();
                                $machine_part->machine_group = $request->get('machine_group');
                                $machine_part->trouble_part = $t_part;
                                $machine_part->part_inspection = $request->get('other_trouble_inspection');
                                $machine_part->created_by = Auth::user()->username;

                                $machine_part->save();
                            }

                            if ($request->get('trouble_inspection')) {
                                $t_inspection = $request->get('trouble_inspection');
                            } else {
                                $t_inspection = $request->get('other_trouble_inspection');

                                $machine_part = new MaintenanceJobPart();
                                $machine_part->machine_group = $request->get('machine_group');
                                $machine_part->trouble_part = $t_part;
                                $machine_part->part_inspection = $request->get('other_trouble_inspection');
                                $machine_part->created_by = Auth::user()->username;

                                $machine_part->save();
                            }

                            $mch = MaintenancePlanItem::where('machine_id', '=', $mjo->machine_name)->select('description', 'area')->first();

                            $machine_log = new MaintenanceMachineProblemLog();
                            $machine_log->machine_id = $mjo->machine_name;
                            $machine_log->machine_name = $mch->description;
                            $machine_log->location = $mch->area;
                            $machine_log->trouble_part = $t_part;
                            $machine_log->part_inspection = $t_inspection;
                            $machine_log->defect = $request->get('penyebab');
                            $machine_log->handling = $request->get('penanganan');
                            $machine_log->prevention = $request->get('pencegahan');
                            $machine_log->part = '';
                            $machine_log->remark = $request->get('order_no');
                            $machine_log->started_time = $proc->start_actual;
                            $machine_log->finished_time = date('Y-m-d H:i:s');
                            $machine_log->created_by = Auth::user()->username;

                            $machine_log->save();
                        }

                        $op_qty = MaintenanceOperatorLocation::where('employee_id', '=', Auth::user()->username)->first();

                        if (count($op_qty) > 0) {
                            $mtc_op_log = new MaintenanceOperatorLocationLog;
                            $mtc_op_log->employee_id = $op_qty->employee_id;
                            $mtc_op_log->employee_name = $op_qty->employee_name;
                            $mtc_op_log->qr_code = $op_qty->qr_code;
                            $mtc_op_log->machine_id = $op_qty->machine_id;
                            $mtc_op_log->description = $op_qty->description;
                            $mtc_op_log->location = $op_qty->location;
                            $mtc_op_log->remark = $op_qty->remark;
                            $mtc_op_log->logged_in_at = $op_qty->created_at;
                            $mtc_op_log->logged_out_at = date('Y-m-d H:i:s');
                            $mtc_op_log->created_by = Auth::user()->username;
                            $mtc_op_log->save();

                            $op_qty->forceDelete();
                        }

                        if ($mjo->category == 'Mesin Trouble') {
                           $mesin_trouble = db::table('maintenance_machine_trouble_logs')
                           ->leftJoin('maintenance_plan_items', 'maintenance_machine_trouble_logs.machine_code', '=', 'maintenance_plan_items.machine_name')
                           ->whereNull('finished_at')
                           ->where('maintenance_plan_items.machine_id', '=', $mjo->machine_name)
                           ->update([
                            'finished_at' => date('Y-m-d H:i:s')
                        ]);
                       }

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

            public function reportingSPKPending(Request $request)
            {
                try {
                    $no = 1;
                    $operator_id = Auth::user()->username;

                    MaintenanceJobProcess::where("order_no", "=", $request->get('order_no'))
                    ->where("operator_id", "=", strtoupper(Auth::user()->username))
                    ->update(['finish_actual' => date("Y-m-d H:i:s"), 'remark' => $request->get('status')]);

                    $proc = MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
                    ->where('operator_id', '=', strtoupper(Auth::user()->username))
                    ->first();

                    $rpt = new MaintenanceJobReport;
                    $rpt->order_no = $request->get('order_no');
                    $rpt->operator_id = $operator_id;
                    $rpt->cause = $request->get('penyebab');
                    $rpt->handling = $request->get('penanganan');
                    $rpt->started_at = $proc->start_actual;
                    $rpt->finished_at = date('Y-m-d H:i:s');

            // $rpt->photo = implode(", ",$upload);
                    $rpt->created_by = $operator_id;

                    $rpt->save();

                    $arr_part = [];
                    $part = '';
                    if (count($request->get('spare_part')) > 0) {
                        foreach ($request->get('spare_part') as $prt) {
                            array_push($arr_part, $prt['part_number'] . " : " . $prt['qty']);
                        }

                        $part = implode("; ", $arr_part);
                    }

                    $other_part = "";
                    if ($request->get('other_part')) {
                        $other_part = $request->get('other_part');
                    }

                    $spk_pending = MaintenanceJobPending::firstOrNew(array('order_no' => $request->get('order_no')));
                    $spk_pending->order_no = $request->get('order_no');
                    $spk_pending->remark = $other_part;
                    $spk_pending->description = $part;
                    $spk_pending->status = $request->get('status');

                    $spk_pending->created_by = Auth::user()->username;
                    $spk_pending->save();

                    MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
                    ->update(['remark' => 5]);

                    $spk_log = MaintenanceJobOrderLog::firstOrNew(
                        array(
                            'order_no' => $request->get('order_no'),
                            'remark' => 5,
                        )
                    );
                    $spk_log->created_by = Auth::user()->username;
                    $spk_log->save();

                    $op_qty = MaintenanceOperatorLocation::where('employee_id', '=', Auth::user()->username)->first();

                    if (count($op_qty) > 0) {
                        $mtc_op_log = new MaintenanceOperatorLocationLog;
                        $mtc_op_log->employee_id = $op_qty->employee_id;
                        $mtc_op_log->employee_name = $op_qty->employee_name;
                        $mtc_op_log->qr_code = $op_qty->qr_code;
                        $mtc_op_log->machine_id = $op_qty->machine_id;
                        $mtc_op_log->description = $op_qty->description;
                        $mtc_op_log->location = $op_qty->location;
                        $mtc_op_log->remark = $op_qty->remark;
                        $mtc_op_log->logged_in_at = $op_qty->created_at;
                        $mtc_op_log->logged_out_at = date('Y-m-d H:i:s');
                        $mtc_op_log->created_by = Auth::user()->username;
                        $mtc_op_log->save();

                        $op_qty->forceDelete();
                    }

                    $response = array(
                        'status' => true,
                        'message' => 'OK',
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

            public function reportingSPKPause(Request $request)
            {
                try {
                    $no = 1;
                    $operator_id = Auth::user()->username;

            // $data = $request->get('foto');
            // define('UPLOAD_DIR', 'images/');
            // $upload = [];

            // foreach ($data as $key) {
            //     if ($key != "") {
            //         $image_parts = explode(";base64,", $key);
            //         $image_type_aux = explode("image/", $image_parts[0]);
            //         $image_type = $image_type_aux[1];
            //         $image_base64 = base64_decode($image_parts[1]);

            //         $file = public_path().'\maintenance\\spk_report\\'.$request->get('order_no').$operator_id.$no.'.png';
            //         $file2 = $request->get('order_no').$operator_id.$no.'.png';

            //         file_put_contents($file, $image_base64);

            //         array_push($upload, $file2);
            //         $no++;
            //     }
            // }

                    MaintenanceJobProcess::where("order_no", "=", $request->get('order_no'))
                    ->where("operator_id", "=", strtoupper(Auth::user()->username))
                    ->update(['finish_actual' => date("Y-m-d H:i:s"), 'remark' => $request->get('status')]);

                    $proc = MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
                    ->where('operator_id', '=', strtoupper(Auth::user()->username))
                    ->first();

                    $rpt = new MaintenanceJobReport;
                    $rpt->order_no = $request->get('order_no');
                    $rpt->operator_id = $operator_id;
                    $rpt->cause = $request->get('penyebab');
                    $rpt->handling = $request->get('penanganan');
                    $rpt->started_at = $proc->start_actual;
                    $rpt->finished_at = date('Y-m-d H:i:s');

            // $rpt->photo = implode(", ",$upload);
                    $rpt->created_by = $operator_id;

                    $rpt->save();

                    $arr_part = [];
                    $part = '';
                    if (count($request->get('spare_part')) > 0) {
                        foreach ($request->get('spare_part') as $prt) {
                            array_push($arr_part, $prt['part_number'] . " : " . $prt['qty']);
                        }

                        $part = implode("; ", $arr_part);
                    }

                    MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
                    ->update(['remark' => 9]);

                    $spk_log = MaintenanceJobOrderLog::firstOrNew(
                        array(
                            'order_no' => $request->get('order_no'),
                            'remark' => 9,
                        )
                    );
                    $spk_log->created_by = Auth::user()->username;
                    $spk_log->save();

                    $op_qty = MaintenanceOperatorLocation::where('employee_id', '=', Auth::user()->username)->first();

                    $mtc_op_log = new MaintenanceOperatorLocationLog;
                    $mtc_op_log->employee_id = $op_qty->employee_id;
                    $mtc_op_log->employee_name = $op_qty->employee_name;
                    $mtc_op_log->qr_code = $op_qty->qr_code;
                    $mtc_op_log->machine_id = $op_qty->machine_id;
                    $mtc_op_log->description = $op_qty->description;
                    $mtc_op_log->location = $op_qty->location;
                    $mtc_op_log->remark = $op_qty->remark;
                    $mtc_op_log->logged_in_at = $op_qty->created_at;
                    $mtc_op_log->logged_out_at = date('Y-m-d H:i:s');
                    $mtc_op_log->created_by = Auth::user()->username;
                    $mtc_op_log->save();

                    $op_qty->forceDelete();

                    $response = array(
                        'status' => true,
                        'message' => 'OK',
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

            public function fetchSPKStatus()
            {
                $mtc_statuses = db::select("SELECT process_name, count(remark) as tot from (select process_code, process_name from processes where remark = 'maintenance') proc
                 left join `maintenance_job_orders` on proc.process_code = maintenance_job_orders.remark
                 GROUP BY process_name");
            }

    // --------------------------  APAR ----------------------

            public function fetchAparList(Request $request)
            {
                DB::connection()->enableQueryLog();

                $apars = Utility::select('id', 'utility_code', 'utility_name', 'type', 'group', 'capacity', 'location', db::raw('DATE_FORMAT(exp_date, "%d %M %Y") as exp_date2'), 'exp_date', db::raw("TIMESTAMPDIFF(MONTH, exp_date, now()) as age_left"), 'remark', 'order');

                if ($request->get('type')) {
                    $apars = $apars->where('remark', '=', $request->get('type'));
                }

                if ($request->get('area')) {
                    $apars = $apars->where('location', '=', $request->get('area'));
                }

                if ($request->get('location')) {
                    $apars = $apars->where('group', '=', $request->get('location'));
                }

                if ($request->get('expMon')) {
                    $apars = $apars->where(db::raw('DATE_FORMAT(exp_date,"%m-%Y")'), '=', $request->get('expMon'));
                }

                if ($request->get('order')) {
                    $apars = $apars->orderBy($request->get('order'), $request->get('order2'));
                }

                $apars = $apars->get();

                $response = array(
                    'status' => true,
                    'apar' => $apars,
                    'query' => DB::getQueryLog(),
                );
                return Response::json($response);
            }

            public function fetchAparCheck2(Request $request)
            {
                if ($request->get('mon_check') == "") {
                    $mon_check = Date('m-Y');
                } else {
                    $mon_check = $request->get('mon_check');
                }

                $apars = Utility::whereRaw('DATE_FORMAT(last_check, "%m-%Y") ="' . $mon_check . '"')
                ->select('id', 'utility_code', 'utility_name', 'type', 'group', 'capacity', 'location', db::raw('DATE_FORMAT(exp_date, "%d %M %Y") as exp_date'), db::raw("(MONTH(exp_date) - MONTH(now())) as age_left"), 'remark', 'last_check')->get();

                $response = array(
                    'status' => true,
                    'apar' => $apars,
                );
                return Response::json($response);
            }

            public function fetchAparCheck(Request $request)
            {
                $checks = Utility::leftJoin('utility_checks', 'utilities.id', '=', 'utility_checks.utility_id')
                ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'utility_checks.created_by')
                ->where('utilities.id', '=', $request->get('utility_id'))
                ->whereNull('utility_checks.deleted_at')
                ->select('utility_id', 'check', db::raw('DATE_FORMAT(check_date,"%d %M %Y") check_date2'), 'utility_checks.remark', 'utility_checks.created_by', 'employee_syncs.name', db::raw('utilities.remark as remark2'), db::raw('utility_checks.id as id_check'), db::raw('IF(DATEDIFF(DATE_FORMAT(check_date,"%Y-%m-%d"), now()) = 0,1, 0) as action'))
                ->orderBy('check_date', 'desc')
                ->limit(5)
                ->get();

                $response = array(
                    'status' => true,
                    'check' => $checks,
                );
                return Response::json($response);
            }

            public function postCheck(Request $request)
            {
                try {
                    $utl_check = new UtilityCheck;

                    Utility::where('id', $request->get('utility_id'))
                    ->update(['last_check' => date('Y-m-d H:i:s')]);

                    $utl_check->utility_id = $request->get('utility_id');

                    $check = "";
                    $arrCheck = $request->get('check');

                    foreach ($arrCheck as $cek) {
                        $check .= $cek . ",";
                    }
                    $check = rtrim($check, ",");

                    if (strpos($check, '0') !== false) {
                        Utility::where('id', $request->get('utility_id'))
                        ->update(['status' => 'NG']);
                    } else {
                        $utl_check->remark = 'OK';

                        $utl_check2 = new UtilityCheck;
                        $utl_check2::where('utility_id', $request->get('utility_id'))->whereNull('remark')->update(array('remark' => 'OK'));

                        Utility::where('id', $request->get('utility_id'))
                        ->update(['status' => null]);
                    }

                    $utl_check->check = $check;
                    $utl_check->check_date = date('Y-m-d H:i:s');
                    $utl_check->created_by = Auth::user()->username;

                    $utl_check->save();

            // GET DATA LAST CHECK
                    $last_check_tool = Utility::where('utilities.id', $request->get('utility_id'))
                    ->leftJoin('utility_checks', 'utility_checks.utility_id', '=', 'utilities.id')
                    ->select('utilities.utility_code', 'utility_name', 'exp_date', 'utilities.status', db::raw('DATE_FORMAT(utility_checks.check_date, "%d-%m-%Y") as check_date'), 'utilities.remark')
                    ->whereNull('utility_checks.deleted_at')
                    ->orderBy('utility_checks.id', 'desc')
                    ->limit(2)
                    ->get();

            // $this->printApar($last_check_tool);

                    $response = array(
                        'status' => true,
                        'cek' => $check,
                        'checked_apar' => $last_check_tool,
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

            public function fetchAparExpire(Request $request)
            {
                $exp = Utility::where('remark', '=', 'APAR')
                ->leftJoin('utility_orders', 'utilities.id', '=', 'utility_orders.utility_id')
                ->whereRaw('TIMESTAMPDIFF(MONTH, now(), exp_date) <= ' . $request->get('mon'))
                ->select('utilities.id', 'utility_code', 'utility_name', 'exp_date', 'group', 'location', 'last_check', db::raw('TIMESTAMPDIFF(MONTH, now(), exp_date) as exp'), 'capacity', 'type', 'pr_date', 'no_pr')
                ->orderBy('exp_date')
                ->get();

                $response = array(
                    'status' => true,
                    'expired_list' => $exp,
                );
                return Response::json($response);
            }

            public function fetchAparNG(Request $request)
            {
                $mon = date('Y-m');
                if ($request->get('mon') != "") {
                    $mon = $request->get('mon');
                }

                $check_by_operator = db::select("SELECT utilities.id, utility_code, utility_name, `group`, location, utilities.remark, last_check, `check`, capacity, type from utilities
                 left join
                 (SELECT id, utility_id, `check`
                     FROM utility_checks
                     WHERE id IN (
                         SELECT MAX(id)
                         FROM utility_checks
                         WHERE DATE_FORMAT(created_at, '%Y-%m') = '" . $mon . "' and deleted_at is null and utility_checks.remark is null
                         GROUP BY utility_id
                         )) as utility_checks
                         on utility_checks.utility_id = utilities.id
                         where `status` = 'NG'
                         order by last_check");

                     $response = array(
                        'status' => true,
                        'operator_check' => $check_by_operator,
                    );
                     return Response::json($response);
                 }

                 public function createTool(Request $request)
                 {
                    $type = $request->get('extinguisher_type');
                    $exp = $request->get('extinguisher_exp');

                    if ($request->get('extinguisher_type') == "") {
                        $type = "-";
                    }

                    if ($request->get('extinguisher_exp') == "") {
                        $exp = null;
                    }

                    try {
                        $utl = new Utility;
                        $utl->utility_code = $request->get('extinguisher_id');
                        $utl->utility_name = $request->get('extinguisher_name');
                        $utl->type = $type;
                        $utl->group = $request->get('extinguisher_location2');
                        $utl->capacity = $request->get('extinguisher_capacity');
                        $utl->location = $request->get('extinguisher_location1');
                        $utl->remark = $request->get('extinguisher_category');
                        $utl->exp_date = $exp;
                        $utl->last_check = date('Y-m-d H:i:s');
                        $utl->created_by = Auth::user()->username;

                        $utl->save();

                        $response = array(
                            'status' => true,
                            'message' => 'OK',
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

                public function updateElectricityPln(Request $request)
                {

                    $data = $request->get('update');
                    for ($i = 0; $i < count($data); $i++) {

            // UPDATE PLN
                        $check = db::table('electricity_saving_monthly_resumes')
                        ->where('month', $data[$i]['month'])
                        ->first();

                        if ($check) {
                            try {
                                $electricity_consumption = 0;
                                if (strlen($data[$i]['electricity_consumption']) > 0) {
                                    $electricity_consumption = $data[$i]['electricity_consumption'];
                                }

                                $sales = 0;
                                if (strlen($data[$i]['sales']) > 0) {
                                    $sales = $data[$i]['sales'];
                                }

                                $check = db::table('electricity_saving_monthly_resumes')
                                ->where('month', $data[$i]['month'])
                                ->update([
                                    'electricity_consumption' => $electricity_consumption,
                                    'sales' => $sales,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);

                            } catch (QueryException $e) {
                                DB::rollback();
                                $response = array(
                                    'status' => false,
                                    'message' => 'Data update failed',
                                );
                                return Response::json($response);
                            }

                        } else {
                            try {
                                $year = db::table('weekly_calendars')
                                ->where('week_date', 'LIKE', '%' . $data[$i]['month'] . '%')
                                ->first();

                                $insert = DB::table('electricity_saving_monthly_resumes')
                                ->insert([
                                    'fiscal_year' => $year->fiscal_year,
                                    'month' => $data[$i]['month'],
                                    'electricity_consumption' => $data[$i]['electricity_consumption'],
                                    'sales' => $data[$i]['sales'],
                                    'created_by' => Auth::id(),
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);

                            } catch (QueryException $e) {
                                DB::rollback();
                                $response = array(
                                    'status' => false,
                                    'message' => 'Data update failed',
                                );
                                return Response::json($response);
                            }

                        }

            // UPDATE RATE JYP / USD
                        $check = db::table('sales_exchange_rates')
                        ->where('period', $data[$i]['month'] . '-01')
                        ->first();

                        if ($check) {
                            try {
                                $usd_to_jpy = 0;
                                if (strlen($data[$i]['usd_to_jpy']) > 0) {
                                    $usd_to_jpy = $data[$i]['usd_to_jpy'];
                                }

                                $check = db::table('sales_exchange_rates')
                                ->where('period', $data[$i]['month'] . '-01')
                                ->update([
                                    'usd_to_jpy' => $usd_to_jpy,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);

                            } catch (QueryException $e) {
                                DB::rollback();
                                $response = array(
                                    'status' => false,
                                    'message' => 'Data update failed',
                                );
                                return Response::json($response);
                            }

                        } else {
                            try {
                                $year = db::table('weekly_calendars')
                                ->where('week_date', 'LIKE', '%' . $data[$i]['month'] . '%')
                                ->first();

                                $insert = DB::table('sales_exchange_rates')
                                ->insert([
                                    'fiscal_year' => $year->fiscal_year,
                                    'period' => $data[$i]['month'] . '-01',
                                    'usd_to_jpy' => $data[$i]['usd_to_jpy'],
                                    'created_by' => Auth::id(),
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);

                            } catch (QueryException $e) {
                                DB::rollback();
                                $response = array(
                                    'status' => false,
                                    'message' => 'Data update failed',
                                );
                                return Response::json($response);
                            }

                        }

                    }

                    DB::commit();
                    $response = array(
                        'status' => true,
                        'message' => 'Data update successful',
                    );
                    return Response::json($response);

                }

                public function updateTool(Request $request)
                {
                    try {
                        Utility::where('utility_code', '=', $request->get('edit_code'))
                        ->where('remark', '=', 'APAR')
                        ->update([
                            'utility_name' => $request->get('edit_name'),
                            'type' => $request->get('edit_type'),
                            'location' => $request->get('edit_location1'),
                            'group' => $request->get('edit_location2'),
                            'capacity' => $request->get('edit_capacity'),
                            'exp_date' => $request->get('edit_exp'),
                        ]);

                        $response = array(
                            'status' => true,
                            'message' => 'OK',
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

                public function replaceTool(Request $request)
                {
                    $cat = db::table('utility_check_lists')->where('remark', '=', 'APAR')->select('check_point')->get();

                    $utl = Utility::where('remark', '=', 'APAR')
                    ->where('utility_code', '=', $request->get('code'))
                    ->first();

                    foreach ($this->apar_type as $type) {
                        if ($utl->type == $type['type']) {
                            $exp_date = date("Y-m-d", strtotime('+' . $type['valid'] . ' years', strtotime($request->get('entry_date'))));
                        }
                    }

                    Utility::where('remark', '=', 'APAR')
                    ->where('utility_code', '=', $request->get('code'))
                    ->update([
                        'capacity' => $request->get('capacity'),
                        'exp_date' => $exp_date,
                        'entry_date' => $request->get('entry_date'),
                        'status' => null,
                    ]);

                    $cek = "";
                    foreach ($cat as $c) {
                        $cek .= "1,";
                    }
                    $cek = rtrim($cek, ",");

                    $utl_check2 = new UtilityCheck;
                    $utl_check2::where('utility_id', $utl->id)->whereNull('remark')->update(array('remark' => 'OK', 'check' => $cek));

                    $hasil_check = UtilityCheck::select(db::raw('DATE_FORMAT(check_date,"%d-%m-%Y") as cek_date'))->where('utility_id', $utl->id)->orderBy('check_date', 'desc')->limit(2)->get();

                    $uo = UtilityOrder::where('Utility_id', '=', $utl->id)->get()->count();

                    if ($uo > 0) {
                        UtilityOrder::where('Utility_id', '=', $utl->id)->delete();
                    }

                    $response = array(
                        'status' => true,
                        'check' => $hasil_check,
                        'new_exp' => $exp_date,
                    );
                    return Response::json($response);
                }

    // public function fetchAparbyCode(Request $request)
    // {
    //     $utl = Utility::where('remark', '=', 'APAR')
    //     ->where('utility_code', '=', $request->get('utility_code'))
    //     ->first();

    //     $response = array(
    //         'status' => true,
    //         'data' => $utl
    //     );
    //     return Response::json($response);
    // }

    // public function printApar($apar){
    //     $printer_name = 'TESTPRINTER';
    //     $connector = new WindowsPrintConnector($printer_name);
    //     $printer = new Printer($connector);

    //     $utility_code = $apar->utility_code;
    //     $utility_name = $apar->utility_name;
    //     $expired_date = $apar->exp_date;

    //     $qr = $utility_code."/".$utility_name;

    //     if (is_null($apar->status)) {
    //         $status = "BAIK";
    //     } else {
    //         $status = "KURANG";
    //     }

    //     $last_check = $apar->last_check;

    //     $printer->setJustification(Printer::JUSTIFY_CENTER);
    //     $printer->setEmphasis(true);
    //     $printer->setReverseColors(true);
    //     $printer->setTextSize(2, 1);
    //     $printer->text("  APAR  "."\n");
    //     $printer->initialize();
    //     $printer->setTextSize(2, 1);
    //     $printer->setJustification(Printer::JUSTIFY_CENTER);
    //     $printer->text($utility_code."\n");
    //     $printer->text($utility_name."\n");
    //     $printer->qrCode($qr, Printer::QR_ECLEVEL_L, 5, Printer::QR_MODEL_2);
    //     $printer->initialize();
    //     $printer->setEmphasis(true);
    //     $printer->setTextSize(1, 1);
    //     $printer->setJustification(Printer::JUSTIFY_CENTER);
    //     $printer->text("Exp.  ".$expired_date."\n");
    //     $printer->text("Last Check : ".$last_check." (".$status.") \n");
    //     $printer->feed(1);
    //     $printer->cut();
    //     $printer->close();
    // }

                public function print_apar2($apar_id, $apar_name, $exp_date, $last_check, $last_check2, $hasil_check, $remark)
                {
                    if ($exp_date == "null") {
                        $exp = "-";
                    } else {
                        $exp = $exp_date;
                    }

                    $data = [
                        'apar_code' => $apar_id,
                        'apar_name' => $apar_name,
                        'exp_date' => $exp,
                        'last_check' => $last_check,
                        'last_check2' => $last_check2,
                        'status' => $hasil_check,
                        'remark' => $remark,
                    ];

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->getDomPDF()->set_option("enable_php", true);
        // $pdf->setPaper([0, 0, 141.732, 184.252], 'landscape');
                    $pdf->setPaper([0, 0, 150.236, 184.252], 'landscape');
                    $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

                    $pdf->loadView('maintenance.apar.aparPrint', array(
                        'data' => $data,
                    )
                );

        // return $pdf->download("APAR_QR.pdf");

        // $pdf->save(public_path() . "/APAR_QR.pdf");
        // file_put_contents(public_path() . "/APAR_QR.pdf", $pdf->output());

                    return $pdf->stream("APAR_QR.pdf");
                }

                public function fetch_apar_monitoring(Request $request)
                {

                    if ($request->get('mon') % 2 === 0) {
                        $loc = "Factory II";
                    } else if ($request->get('mon') % 2 === 1) {
                        $loc = "Factory I";
                    }

                    DB::connection()->enableQueryLog();

                    $check = Utility::where("location", "=", $loc)
                    ->where("remark", "=", "APAR")
                    ->select('id', 'utility_code', 'utility_name', 'type', 'group', 'capacity', 'location', 'remark', db::raw('DATE_FORMAT(last_check, "%d %M %Y") last_check'), db::raw('DATE_FORMAT(entry_date, "%Y-%m-%d") entry'), db::raw('DATE_FORMAT(DATE_ADD(last_check, INTERVAL 2 MONTH), "%d %M %Y") as cek_before'), db::raw("FLOOR((DayOfMonth(DATE_ADD(DATE(last_check), INTERVAL 2 MONTH)-1)/7)+1) as wek"))
                    ->whereRaw('DATE_FORMAT(last_check,"%Y-%m") <> DATE_FORMAT("' . $request->get('dt') . '", "%Y-%m")')
                    ->orderBy("wek", "ASC")
                    ->orderBy("last_check", "ASC")
                    ->get();

                    $hasil_check = db::select("SELECT DATE_FORMAT(check_date,'%Y-%m-%d') as dt_cek, utility_code, utility_name, location, DATE_FORMAT(last_check,'%d %M %Y') as last_check FROM utility_checks
                     LEFT JOIN utilities on utilities.id = utility_checks.utility_id
                     WHERE utility_checks.id IN (
                         SELECT MAX(id)
                         FROM utility_checks
                         where DATE_FORMAT(check_date,'%Y-%m') = DATE_FORMAT('" . $request->get('dt') . "', '%Y-%m') and deleted_at is null
                             GROUP BY utility_id
                         ) AND utilities.remark = 'APAR'");

                         $response = array(
                            'status' => true,
                            'check_list' => $check,
                            'hasil_check' => $hasil_check,
                            'query' => DB::getQueryLog(),
                        );
                         return Response::json($response);
                     }

                     public function fetch_hydrant_monitoring(Request $request)
                     {
                        DB::connection()->enableQueryLog();

                        if ($request->get('mon') % 2 === 0) {
                            $loc = "Factory II";
                        } else if ($request->get('mon') % 2 === 1) {
                            $loc = "Factory I";
                        }

                        $check = Utility::where("location", "=", $loc)
                        ->where("remark", "=", "HYDRANT")
                        ->select('id', 'utility_code', 'utility_name', 'type', 'group', 'capacity', 'location', 'remark', db::raw('DATE_FORMAT(last_check, "%d %M %Y") last_check'), db::raw('DATE_FORMAT(entry_date, "%Y-%m-%d") entry'), db::raw('DATE_FORMAT(DATE_ADD(last_check, INTERVAL 2 MONTH), "%d %M %Y") as cek_before'), db::raw("FLOOR((DayOfMonth(DATE_ADD(DATE(last_check), INTERVAL 2 MONTH)-1)/7)+1) as wek"))
                        ->whereRaw('DATE_FORMAT(last_check,"%Y-%m") <> DATE_FORMAT("' . $request->get('dt') . '", "%Y-%m")')
                        ->orderBy("wek", "ASC")
                        ->orderBy("id", "ASC")
                        ->get();

                        $hasil_check = db::select("SELECT DATE_FORMAT(check_date,'%Y-%m-%d') as dt_cek, utility_code, utility_name, location, DATE_FORMAT(last_check,'%d %M %Y') as last_check FROM utility_checks
                         LEFT JOIN utilities on utilities.id = utility_checks.utility_id
                         WHERE utility_checks.id IN (
                             SELECT MAX(id)
                             FROM utility_checks
                             where DATE_FORMAT(check_date,'%Y-%m') = DATE_FORMAT('" . $request->get('dt') . "', '%Y-%m') and deleted_at is null
                                 GROUP BY utility_id
                             ) AND utilities.remark = 'HYDRANT'");

                             $response = array(
                                'status' => true,
                                'check_list' => $check,
                                'hasil_check' => $hasil_check,
                                'query' => DB::getQueryLog(),
                            );
                             return Response::json($response);
                         }

                         public function fetch_apar_resume(Request $request)
                         {
                            $getCheckedData = DB::select('SELECT mon, jml_tot, IFNULL(jml,0) as jml FROM
                             (SELECT COUNT(entry) as jml_tot, mst.mo, mon from
                                 (SELECT id, IF(location = "FACTORY II", 0, 1) as mo, DATE_FORMAT(entry_date,"%Y-%m-%d") as entry from utilities) utl
                                 left join
                                 (select DATE_FORMAT(week_date,"%Y-%m") as mon, MOD(MONTH(week_date),2) as mo from weekly_calendars where week_date >= "2020-01-01" group by DATE_FORMAT(week_date,"%Y-%m"), mo) mst on mst.mo = utl.mo
                                 where DATE_FORMAT(entry, "%Y-%m") <= mon
                                 group by mon, mst.mo) base
                             left join (
                                 SELECT count(utility_id) as jml, cek_date from
                                 (SELECT utility_checks.utility_id, DATE_FORMAT(check_date, "%Y-%m") as cek_date from utility_checks
                                     left join utilities on utility_checks.utility_id = utilities.id
                                     where utilities.remark = "APAR" and utility_checks.deleted_at is null
                                     group by utility_id, DATE_FORMAT(check_date, "%Y-%m")
                                     ) checked_data
                                 group by cek_date
                                 ) as cek on base.mon = cek.cek_date
                             ');

                            $getAparNew = DB::select('SELECT mstr.mon, IFNULL(new.jml,0) as new, IFNULL(exp.jml,0) as exp FROM
                             (select DATE_FORMAT(week_date,"%Y-%m") as mon from weekly_calendars where week_date >= "2020-01-01" group by DATE_FORMAT(week_date,"%Y-%m")) mstr
                             left join
                             (select count(id) as jml, DATE_FORMAT(entry_date,"%Y-%m") as mon from utilities
                                 where DATE_FORMAT(entry_date,"%Y-%m") >= "2020-01" and remark = "APAR"
                                 group by DATE_FORMAT(entry_date,"%Y-%m")) as new on mstr.mon = new.mon
                             left join
                             (select count(id) as jml, DATE_FORMAT(exp_date,"%Y-%m") as mon from utilities where remark = "APAR"
                                 group by DATE_FORMAT(exp_date,"%Y-%m")) as exp on mstr.mon = exp.mon
                             ');

                            $response = array(
                                'status' => true,
                                'check_list' => $getCheckedData,
                                'replace_list' => $getAparNew,
                            );
                            return Response::json($response);
                        }

                        public function fetch_apar_resume_week(Request $request)
                        {
                            $ym = Date('Y-m');

                            $mon = Date('m');

                            if ($request->get('mon')) {
                                $ym = $request->get('mon');

                                $mon = explode("-", $request->get('mon'));
                                $mon = $mon[1];
                            }

                            $mon = intval($mon);

                            if ($mon % 2 === 0) {
                                $loc = "Factory II";
                            } else if ($mon % 2 === 1) {
                                $loc = "Factory I";
                            }

                            $cek_week = db::select('select "' . $ym . '" as mon, wek, sum(jml_cek) as uncek, sum(cek) as cek from
                             (SELECT wek, COUNT(weeks) as jml_cek, 0 as cek from
                             (SELECT IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) as weeks from utilities where remark = "APAR" and location = "' . $loc . '") un
                             right join
                             (select FLOOR((DayOfMonth(week_date)-1)/7)+1 as wek from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = "' . $ym . '" GROUP BY wek) mstr on mstr.wek+1 = un.weeks
                             group by wek

                             union all
                             select mstr.wek, 0 as jml_cek, count(cek.wek) as cek from
                             (select utility_id, IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) - 1 as wek from utility_checks
                             left join utilities on utilities.id = utility_checks.utility_id
                             where location = "' . $loc . '" and utilities.remark = "APAR" and DATE_FORMAT(check_date,"%Y-%m") = "' . $ym . '" and utility_checks.deleted_at is null
                             group by utility_id, wek) cek
                             right join
                             (select FLOOR((DayOfMonth(week_date)-1)/7)+1 as wek from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = "' . $ym . '" GROUP BY wek) mstr on mstr.wek = cek.wek
                             group by mstr.wek) semua
                             group by wek');

                            $replace_week = db::select('SELECT mstr.wek, IFNULL(entry_apar.entry,0) as entry, IFNULL(expired_apar.exp,0) as expire from
                             (select FLOOR((DayOfMonth(week_date)-1)/7)+1 as wek from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = "' . $ym . '" GROUP BY wek) as mstr
                                 left join
                                 (select wek, count(utility_code) as entry from
                                 (select utility_code, utility_name, location, DATE(entry_date) as entry, IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) - 1 as wek from utilities where remark = "APAR" and DATE_FORMAT(entry_date,"%Y-%m") = "' . $ym . '") as entry
                                 group by wek ) as entry_apar on mstr.wek = entry_apar.wek
                                 left join
                                 (select wek, count(utility_code) as exp from
                                 (select utility_code, utility_name, location, DATE(exp_date) as exp, IF(FLOOR((DayOfMonth(exp_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(exp_date)-1)/7)+1) - 1 as wek from utilities where remark = "APAR" and DATE_FORMAT(exp_date,"%Y-%m") = "' . $ym . '") as expired
                                 group by wek) as expired_apar on mstr.wek = expired_apar.wek');

                             $apar_progres = db::select('SELECT cal.wek, IFNULL(datas.jml,0) as jml from
                                 (select FLOOR((DayOfMonth(week_date)-1)/7)+1 as wek from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = "' . $ym . '" GROUP BY wek) as cal
                                     left join
                                     (select wek, COUNT(utilities.id) as jml from utilities
                                     join (
                                     SELECT FLOOR((DayOfMonth(utility_checks.check_date)-1)/7)+1 as wek, utility_id
                                     FROM utility_checks
                                     WHERE id IN (
                                     SELECT min(id)
                                     FROM utility_checks
                                     where DATE_FORMAT(utility_checks.check_date, "%Y-%m") = "' . $ym . '" and utility_checks.deleted_at is null
                                     GROUP BY utility_id
                                     )
                                     ) utility_checks on utilities.id = utility_checks.utility_id
                                     where location = "' . $loc . '" and utilities.remark = "APAR"
                                     group by wek) as datas on cal.wek = datas.wek');

                                 $apar_total = db::select('select count(id) as total from utilities where location = "' . $loc . '" and remark = "APAR"');

                                 $response = array(
                                    'status' => true,
            // 'cek_week' => $cek_week,
            // 'replace_week' => $replace_week,
                                    'apar_progres' => $apar_progres,
                                    'apar_total' => $apar_total,
                                );
                                 return Response::json($response);
                             }

    // ------------------------------
                             public function fetch_apar_resume_detail(Request $request)
                             {
                                $detailCheck = DB::select('SELECT utilities.utility_code, utilities.utility_name, utilities.location, utilities.`group`, 1 as cek from utility_checks
                                 left join utilities on utility_checks.utility_id = utilities.id
                                 where utilities.remark = "APAR" and DATE_FORMAT(check_date, "%M %Y") = "' . $request->get('mon') . '" and utility_checks.deleted_at is null
                                 group by utilities.utility_code, utilities.utility_name, utilities.location, utilities.`group`, DATE_FORMAT(check_date, "%Y-%m")
                                 union all
                                 SELECT utility_code, utility_name, location, `group`, 0 as cek from utilities
                                 LEFT join utility_checks on utilities.id = utility_checks.utility_id
                                 where utilities.remark = "APAR" AND location = "FACTORY I" AND DATE_FORMAT(entry_date, "%Y-%m") <= "' . $request->get('mon2') . '" AND (DATE_FORMAT(check_date, "%Y-%m") <> "' . $request->get('mon2') . '" OR check_date is null and utility_checks.deleted_at is null)
                                 GROUP BY utility_code, utility_name, location, `group`
                                 ORDER BY cek asc
                                 ');

                                $detailNew = DB::select('select utility_code, utility_name, location, `group`, exp_date as dt, "Expired" as stat from utilities where remark = "APAR" and DATE_FORMAT(exp_date,"%M %Y") = "' . $request->get('mon') . '"
                                 union all
                                 select utility_code, utility_name, location, `group`, DATE_FORMAT(entry_date,"%Y-%m-%d") as dt, "Replace/New" as stat from utilities where remark = "APAR" and DATE_FORMAT(entry_date,"%M %Y") = "' . $request->get('mon') . '"
                                 order by dt asc');

                                $response = array(
                                    'status' => true,
                                    'check_detail_list' => $detailCheck,
                                    'replace_list' => $detailNew,
                                );
                                return Response::json($response);
                            }
    // ---------------------------------

                            public function fetch_apar_resume_detail_week(Request $request)
                            {
                                DB::connection()->enableQueryLog();
                                $ym = Date('Y-m');

                                $mon = Date('m');

                                if ($request->get('mon')) {
                                    $ym = $request->get('mon');

                                    $mon = explode("-", $request->get('mon'));
                                    $mon = $mon[1];
                                }

                                $mon = intval($mon);

                                if ($mon % 2 === 0) {
                                    $loc = "Factory II";
                                } else if ($mon % 2 === 1) {
                                    $loc = "Factory I";
                                }

                                $detail_cek = db::select('SELECT semua.utility_code, semua.utility_name, semua.location, semua.`group`, IFNULL(cek.cek, 0) as cek from
                                 (SELECT id, wek, utility_code, utility_name, location, `group`, 0 as cek from
                                     (SELECT IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) as weeks, utility_code, utility_name, location, `group`, id from utilities where remark = "APAR" and location = "' . $loc . '") un
                                         right join
                                         (select FLOOR((DayOfMonth(week_date)-1)/7)+1 as wek from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = "' . $ym . '" GROUP BY wek) mstr on mstr.wek+1 = un.weeks
                                         where wek = ' . $request->get('week') . ' ) as semua
                                     left join
                                     (SELECT utility_id as id, IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) - 1 as wek, utility_code, utility_name, location, `group`, 1 as cek from utility_checks
                                         left join utilities on utilities.id = utility_checks.utility_id
                                         where location = "' . $loc . '" and utilities.remark = "APAR" and DATE_FORMAT(check_date,"%Y-%m") = "' . $ym . '" and utility_checks.deleted_at is null
                                         group by utility_id, wek, utility_code, utility_name, location, `group`) as cek on semua.id = cek.id
                                         order by cek.cek asc');

                                     $detail_expired = db::select('select wek, utility_code, utility_name, location, dt, exp from
                                         (select utility_code, utility_name, location, DATE_FORMAT(exp_date, "%Y-%m-%d") as dt, IF(FLOOR((DayOfMonth(exp_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(exp_date)-1)/7)+1) - 1 as wek, 1 as exp from utilities where remark = "APAR" and DATE_FORMAT(exp_date,"%Y-%m") = "' . $ym . '") as expired
                                         where wek = ' . $request->get('week') . '
                                         union all
                                         select wek, utility_code, utility_name, location, dt, exp from
                                         (select utility_code, utility_name, location, DATE_FORMAT(entry_date, "%Y-%m-%d") as dt, IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) - 1 as wek, 0 as exp from utilities where remark = "APAR" and DATE_FORMAT(entry_date,"%Y-%m") = "' . $ym . '") as entry
                                         where wek =' . $request->get('week'));

        // return dd($detail_expired);

                                     $response = array(
                                        'status' => true,
                                        'check_detail_list' => $detail_cek,
                                        'replace_list' => $detail_expired,
            // 'query' => DB::getQueryLog()
                                    );
                                     return Response::json($response);
                                 }

                                 public function check_apar_use(request $request)
                                 {

                                    $use = new UtilityUse;

                                    $use->utility_id = $request->get('utility_id');
                                    $use->created_by = Auth::user()->username;

                                    $use->save();

                                    $response = array(
                                        'status' => true,
                                        'message' => 'Berhasil',
                                    );
                                    return Response::json($response);

                                }

                                public function fetch_apar_use(Request $request)
                                {
                                    $apar_use = UtilityUse::leftJoin('utilities', 'utilities.id', '=', 'utility_uses.utility_id')->select('utility_code', 'utility_name', 'location', 'group', 'remark', 'utility_uses.created_at')->orderBy('created_at', "DESC")->get();

                                    $response = array(
                                        'status' => true,
                                        'use_list' => $apar_use,
                                    );
                                    return Response::json($response);
                                }

                                public function deleteElectricityConsumption(Request $request)
                                {

                                    $data = ElectricityConsumption::where('id', $request->get('id'))->first();

                                    $last = ElectricityConsumption::where('date', '<', $data->date)
                                    ->orderBy('date', 'DESC')
                                    ->first();

                                    try {
                                        $delete = ElectricityConsumption::where('date', '>=', $data->date)->delete();

                                        $update = ElectricityConsumption::where('date', '=', $last->date)
                                        ->update([
                                            'wbp' => null,
                                            'lwbp1' => null,
                                            'lwbp2' => null,
                                            'consumption_kvarh' => null,
                                            'consumption_outgoing_i' => null,
                                            'consumption_outgoing_ii' => null,
                                            'consumption_outgoing_iii' => null,
                                            'consumption_outgoing_iv' => null,
                                        ]);

                                        $response = array(
                                            'status' => true,
                                            'message' => 'Berhasil',
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

                                public function delete_history(Request $request)
                                {
                                    $ck = UtilityCheck::find($request->get('id_check'));

                                    $ck->delete();

                                    $cek_apar_ck = UtilityCheck::where('utility_id', '=', $ck->utility_id)->orderBy('id', 'desc')->first();

                                    if ($cek_apar_ck) {
                                        if (strpos($cek_apar_ck->check, '0') !== false) {
                                            $stat = 'NG';
                                        } else {
                                            $stat = null;
                                        }

                                        Utility::where('id', $ck->utility_id)
                                        ->update(['status' => $stat, 'last_check' => $cek_apar_ck->created_at]);

                                    } else {
                                        Utility::where('id', $ck->utility_id)
                                        ->update(['status' => null]);
                                    }

                                    $response = array(
                                        'status' => true,
                                        'datas' => $cek_apar_ck,
                                    );
                                    return Response::json($response);
                                }

                                public function apar_order(Request $request)
                                {
                                    try {
                                        if ($request->get('param') == 'order') {
                                            $utl_id = $request->get('utility_id');

                                            for ($i = 0; $i < count($utl_id); $i++) {
                                                $order = new UtilityOrder;
                                                $order->utility_id = $utl_id[$i];
                                                $order->no_pr = $request->get('pr_num');
                                                $order->pr_date = $request->get('pr_date');
                                                $order->created_by = Auth::user()->username;
                                                $order->save();
                                            }

                                        } else {
                                            UtilityOrder::where('utility_id', $request->get('utility_id'))
                                            ->where('order_date', $request->get('order_date'))
                                            ->update(['ready_date' => date('Y-m-d'), 'order_status' => 'Ready']);
                                        }

                                        $response = array(
                                            'status' => true,
                                            'message' => 'Berhasil',
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

                                public function fetchInventory(Request $request)
                                {
                                    $inv = MaintenanceInventory::select('part_number', 'item_number', 'part_name', 'category', 'location', 'specification', 'maker', 'user', 'stock', 'uom', 'min_stock', 'max_stock', 'updated_at', 'cost')->get();

                                    $response = array(
                                        'status' => true,
                                        'inventory' => $inv,
                                    );
                                    return Response::json($response);
                                }

                                public function fetchPartbyCode(Request $request)
                                {
                                    try {
                                        $inv_code = MaintenanceInventory::where('part_number', '=', $request->get('code'))->select('part_number', 'part_name', 'specification', 'stock', 'uom')->first();

                                        $response = array(
                                            'status' => true,
                                            'datas' => $inv_code,
                                        );
                                        return Response::json($response);
                                    } catch (QueryException $e) {
                                        $response = array(
                                            'status' => false,
                                            'message' => $e->gertMessage(),
                                        );
                                        return Response::json($response);
                                    }
                                }

                                public function postInventory(Request $request)
                                {
                                    try {
                                        $prt = $request->get('part');

                                        if ($request->get('stat') == 'in') {
                                            for ($i = 0; $i < count($prt); $i++) {
                                                $inventory = MaintenanceInventory::where('part_number', $prt[$i][0])->first();

                                                MaintenanceInventory::where('part_number', $prt[$i][0])
                                                ->update(['stock' => $inventory->stock + $prt[$i][1]]);

                                                $inv_log = new MaintenanceInventoryLog;
                                                $inv_log->part_number = $prt[$i][0];
                                                $inv_log->status = "in";
                                                $inv_log->quantity = $prt[$i][1];
                                                $inv_log->created_by = Auth::user()->username;
                                                $inv_log->save();
                                            }
                                        } else {
                                            for ($i = 0; $i < count($prt); $i++) {
                                                $inventory = MaintenanceInventory::where('part_number', $prt[$i][0])->first();

                                                MaintenanceInventory::where('part_number', $prt[$i][0])
                                                ->update(['stock' => $inventory->stock - $prt[$i][1]]);

                                                $inv_log = new MaintenanceInventoryLog;
                                                $inv_log->part_number = $prt[$i][0];
                                                $inv_log->status = "out";
                                                $inv_log->quantity = $prt[$i][1];
                                                $inv_log->remark1 = $request->get('ket');
                                                $inv_log->remark2 = $request->get('ket2');
                                                $inv_log->created_by = Auth::user()->username;
                                                $inv_log->save();
                                            }
                                        }

                                        $response = array(
                                            'status' => true,
                                            'message' => 'Berhasil',
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

                                public function inventory_save(Request $request)
                                {
                                    $inv = new MaintenanceInventory;
                                    $inv->part_number = $request->get('part_number');
                                    $inv->item_number = $request->get('item_number');
                                    $inv->part_name = $request->get('part_name');
                                    $inv->category = $request->get('category');
                                    $inv->specification = $request->get('specification');
                                    $inv->maker = $request->get('maker');
                                    $inv->location = $request->get('location');
                                    $inv->stock = $request->get('stock');
                                    $inv->min_stock = $request->get('min');
                                    $inv->max_stock = $request->get('max');
                                    $inv->uom = $request->get('uom');
                                    $inv->user = $request->get('user');
                                    $inv->created_by = Auth::user()->username;

                                    $inv->save();

                                    $response = array(
                                        'status' => true,
                                        'message' => 'Berhasil',
                                    );
                                    return Response::json($response);
                                }

                                public function fetchInventoryPart(Request $request)
                                {
                                    $inv_code = MaintenanceInventory::where('part_number', '=', $request->get('part_number'))->first();

                                    $response = array(
                                        'status' => true,
                                        'message' => 'Berhasil',
                                        'datas' => $inv_code,
                                    );
                                    return Response::json($response);
                                }

                                public function inventory_edit(Request $request)
                                {
                                    try {
                                        MaintenanceInventory::where('part_number', $request->get('part_number'))
                                        ->update([
                                            'item_number' => $request->get('item_number'),
                                            'part_name' => $request->get('part_name'),
                                            'category' => $request->get('category'),
                                            'specification' => $request->get('specification'),
                                            'maker' => $request->get('maker'),
                                            'location' => $request->get('location'),
                                            'min_stock' => $request->get('min'),
                                            'max_stock' => $request->get('max'),
                                            'uom' => $request->get('uom'),
                                            'user' => $request->get('user'),
                                            'cost' => $request->get('cost'),
                                        ]);

                                        $response = array(
                                            'status' => true,
                                            'message' => 'Berhasil',
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

                                public function fetchPM(Request $request)
                                {
                                    $dt = date("F");
                                    $dt = strtolower($dt);

                                    if ($request->get('mon')) {
                                        $select = db::raw($request->get('mon') . " as mon");
                                    } else {
                                        $select = db::raw('april, may, june, july, august, september, october, november, december, january, february, march');
                                    }

                                    $pms = MaintenancePlan::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'maintenance_plans.pic')
                                    ->select("maintenance_plans.id", "item_check", "quantity", "category", "maintenance_plans.status", "schedule", "name", "fiscal", $select);

                                    if ($request->get('ctg')) {
                                        $pms = $pms->where('category', '=', $request->get('ctg'));
                                    }

                                    if ($request->get('fy')) {
                                        $pms = $pms->where('fiscal', '=', $request->get('fy'));
                                    }

                                    $pms = $pms->get();

                                    $daily = db::table("maintenance_plan_logs")->get();

                                    $response = array(
                                        'status' => true,
                                        'datas' => $pms,
                                        'daily' => $daily,
                                    );
                                    return Response::json($response);
                                }

                                public function fetchMachine(Request $request)
                                {
                                    $datas = MaintenancePlanItem::select('description', 'machine_name', 'machine_id', 'area');

                                    if ($request->get('kategori') != 'all') {
                                        $datas = $datas->where('category', '=', $request->get('kategori'));
                                    }

                                    $datas = $datas->whereNull('remark')->get();

                                    $response = array(
                                        'status' => true,
                                        'datas' => $datas,
                                    );
                                    return Response::json($response);
                                }

                                public function importPM(Request $request)
                                {
                                    if ($request->hasFile('excel_file')) {
                                        try {
                                            $file = $request->file('excel_file');
                                            $file_name = 'import_pm.' . $file->getClientOriginalExtension();
                                            $file->move(public_path('maintenance/'), $file_name);

                                            $excel = public_path('maintenance/') . $file_name;
                                            $rows = Excel::load($excel, function ($reader) {
                                                $reader->noHeading();
                    //Skip Header
                                                $reader->skipRows(1);
                                            })->get();
                                            $rows = $rows->toArray();

                                            for ($i = 0; $i < count($rows); $i++) {
                                                $plan = MaintenancePlan::firstOrNew(array('item_check' => $rows[$i][1], 'fiscal' => $rows[$i][4]));
                                                $plan->category = $rows[$i][2];
                                                $plan->status = $rows[$i][3];
                                                $plan->schedule = $rows[$i][6];
                                                $plan->pic = $rows[$i][7];
                                                $plan->quantity = $rows[$i][5];
                                                $plan->april = $rows[$i][8];
                                                $plan->mei = $rows[$i][9];
                                                $plan->juni = $rows[$i][10];
                                                $plan->juli = $rows[$i][11];
                                                $plan->agustus = $rows[$i][12];
                                                $plan->september = $rows[$i][13];
                                                $plan->oktober = $rows[$i][14];
                                                $plan->november = $rows[$i][15];
                                                $plan->desember = $rows[$i][16];
                                                $plan->januari = $rows[$i][17];
                                                $plan->februari = $rows[$i][18];
                                                $plan->maret = $rows[$i][19];
                                                $plan->created_by = Auth::user()->username;

                                                $plan->save();

                                            }

                                            return redirect('/index/maintenance/planned/master')->with('status', 'Upload Schedule success')->with('page', 'Planned Maintenance Data')->with('head', 'Maintenance');
                                        } catch (QueryException $e) {
                                            return redirect('/index/maintenance/planned/master')->with('error', $e->getMessage())->with('page', 'Planned Maintenance Data')->with('head', 'Maintenance');
                                        }
                                    } else {
                                        return redirect('/index/maintenance/planned/master')->with('error', 'File not Found')->with('page', 'Planned Maintenance Data')->with('head', 'Maintenance');
                                    }
                                }

                                public function openSPKPending(Request $request)
                                {
                                    try {
                                        MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
                                        ->update(['remark' => 2, 'note' => $request->get('reason')]);

                                        MaintenanceJobOrderLog::where("order_no", "=", $request->get("order_no"))->delete();

                                        MaintenanceJobProcess::where("order_no", "=", $request->get("order_no"))->delete();

                                        $spk_log = new MaintenanceJobOrderLog;
                                        $spk_log->order_no = $request->get('order_no');
                                        $spk_log->remark = 2;
                                        $spk_log->created_by = Auth::user()->username;

                                        $response = array(
                                            'status' => true,
                                            'message' => 'success',
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

                                public function postPlannedCheck(Request $request)
                                {
                                    if ($request->get('ng')) {
                                        $arr_ng = $request->get('ng');
                                    } else {
                                        $arr_ng = [];
                                    }

                                    $ido = array_diff($request->get('ids'), $arr_ng);
                                    $cek_val = $request->get('val');

                                    foreach ($ido as $itm) {
                                        $val = null;
                                        if (count($cek_val) > 0) {
                                            for ($i = 0; $i < count($cek_val); $i++) {
                                                if ($cek_val[$i]['id'] == $itm) {
                                                    $val = $cek_val[$i]['value'];
                                                }
                                            }
                                        }

                                        $mtc_itm = MaintenancePlanItemCheck::where('id', '=', $itm)->select('machine_name', 'item_check', 'substance', 'remark')->first();

                                        $mtc_check = MaintenancePlanCheck::firstOrNew(array('item_code' => $mtc_itm->machine_name, 'item_check' => $mtc_itm->item_check, 'substance' => $mtc_itm->substance, 'check_date' => date('Y-m-d')));
                                        $mtc_check->period = $mtc_itm->remark;
                                        $mtc_check->check = 'OK';
                                        $mtc_check->check_value = $val;
                                        $mtc_check->description = null;
                                        $mtc_check->photo_before = null;
                                        $mtc_check->photo_after = null;
                                        $mtc_check->remark = null;
                                        $mtc_check->created_by = Auth::user()->username;

                                        $mtc_check->save();
                                    }

                                    foreach ($arr_ng as $an) {
                                        $ng_val = db::table('maintenance_plan_check_temps')->where('id_check', '=', $an)->select('description', 'before_photo', 'after_photo')->first();

                                        $mtc_itm = MaintenancePlanItemCheck::where('id', '=', $an)->select('machine_name', 'item_check', 'substance', 'remark')->first();

                                        $cek_val = $request->get('val');

                                        $val = null;
                                        if (count($cek_val) > 0) {
                                            for ($i = 0; $i < count($cek_val); $i++) {
                                                if ($cek_val[$i]['id'] == $an) {
                                                    $val = $cek_val[$i]['value'];
                                                }
                                            }
                                        }

                                        $mtc_check = MaintenancePlanCheck::firstOrNew(array('item_code' => $mtc_itm->machine_name, 'item_check' => $mtc_itm->item_check, 'substance' => $mtc_itm->substance));
                                        $mtc_check->period = $mtc_itm->remark;
                                        $mtc_check->check = 'NG';
                                        $mtc_check->check_value = $val;
                                        $mtc_check->description = $ng_val->description;
                                        $mtc_check->photo_before = $ng_val->before_photo;
                                        $mtc_check->photo_after = $ng_val->after_photo;
                                        $mtc_check->remark = null;
                                        $mtc_check->created_by = Auth::user()->username;

                                        $mtc_check->save();

                                        db::table('maintenance_plan_check_temps')->where('id_check', '=', $an)->delete();
                                    }

                                    $response = array(
                                        'status' => true,
                                        'message' => 'OK',
                                    );
                                    return Response::json($response);

                                }

    // public function getHistoryPlanned(Request $request)
    // {
    //     $history = MaintenancePlanCheck::select('item_code', db::raw('GROUP_CONCAT(DISTINCT `check`) as ck'), db::raw("DATE_FORMAT(created_at,'%Y-%m-%d %H:%i') as dt"), db::raw('GROUP_CONCAT(DISTINCT created_by) as pic'))
    //     ->where('item_code', '=', $request->get('item_code'))
    //     ->where('remark', '=', $request->get('period'))
    //     ->groupBy('item_code', db::raw("DATE_FORMAT(created_at,'%Y-%m-%d %H:%i')"))
    //     ->orderBy(db::raw("DATE_FORMAT(created_at,'%Y-%m-%d %H:%i')"), 'desc')
    //     ->get();

    //     $response = array(
    //         'status' => true,
    //         'datas' => $history
    //     );
    //     return Response::json($response);
    // }

                                public function fetchItemCheckList(Request $request)
                                {
                                    $datas = MaintenancePlanItemCheck::leftjoin('maintenance_plan_items', 'maintenance_plan_item_checks.machine_name', '=', 'maintenance_plan_items.machine_name')
                                    ->where("maintenance_plan_items.machine_id", "=", $request->get('item_no'))
                                    ->where('maintenance_plan_item_checks.remark', '=', $request->get('periode'))
                                    ->select('maintenance_plan_item_checks.id', 'maintenance_plan_items.machine_id', 'maintenance_plan_item_checks.item_check', 'maintenance_plan_item_checks.substance', 'maintenance_plan_item_checks.essay_category', 'maintenance_plan_item_checks.remark', 'maintenance_plan_item_checks.lower_limit', 'maintenance_plan_item_checks.upper_limit')
                                    ->get();

                                    $response = array(
                                        'status' => true,
                                        'datas' => $datas,
                                    );
                                    return Response::json($response);
                                }

                                public function postInventoryStock(Request $request)
                                {
                                    try {

                                        $inventory = MaintenanceInventory::where('part_number', '=', $request->get('material_number'))->first();

                                        if ($request->get('status') == 'out') {
                                            MaintenanceInventory::where('part_number', $request->get('material_number'))
                                            ->update(['stock' => $inventory->stock - 1]);

                                            $inv_log = new MaintenanceInventoryLog;
                                            $inv_log->part_number = $request->get('material_number');
                                            $inv_log->status = $request->get('status');
                                            $inv_log->remark1 = $request->get('category');
                                            $inv_log->machine_id = $request->get('machine');
                                            $inv_log->quantity = 1;
                                            $inv_log->created_by = $request->get('employee_id');

                                            $inv_log->save();
                                        } else {

                                        }

                                        $response = array(
                                            'status' => true,
                                            'message' => 'success',
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

                                public function fetchMaintenanePic(Request $request)
                                {
                                    $pics = MaintenancePic::select('pic_id', 'pic_name', db::raw('GROUP_CONCAT(item_name) as item'), db::raw('GROUP_CONCAT(skill) as skill'))->groupBy('pic_id', 'pic_name')->get();

                                    $response = array(
                                        'status' => true,
                                        'datas' => $pics,
                                    );
                                    return Response::json($response);
                                }

                                public function fetchPlanedMonitoring(Request $request)
                                {
                                    $satu_hari = "SELECT masters.machine_name, description from
                                    (SELECT machine_name FROM `maintenance_plan_item_checks` where remark = '1-HARI' group by machine_name) as masters
                                    left join maintenance_plan_items on maintenance_plan_items.machine_name = masters.machine_name";

                                    $response = array(
                                        'status' => true,
                                        'datas' => $satu_hari,
                                    );
                                    return Response::json($response);
                                }

                                public function postPlannedNotGood(Request $request)
                                {
                                    $poin_cek = MaintenancePlanItemCheck::where('id', '=', $request->get('id'))->first();

                                    $pm = MaintenancePlanCheck::firstOrNew(array('item_code' => $poin_cek->machine_name, 'item_check' => $poin_cek->item_check, 'substance' => $poin_cek->substance));
                                    $pm->period = $poin_cek->remark;
                                    $pm->check = 'NG';
                                    $pm->check_value = $request->get('cek_val');
                                    $pm->description = $request->get('desc');
                                    $pm->photo_before = $request->get('before');
                                    $pm->photo_after = $request->get('after');
                                    $pm->remark = $request->get('keterangan');
                                    $pm->created_by = Auth::user()->username;
                                    $pm->save();

                                    $response = array(
                                        'status' => true,
                                        'datas' => $pm,
                                    );
                                    return Response::json($response);
                                }

                                public function getPlannedNotGood(Request $request)
                                {
                                    DB::connection()->enableQueryLog();
                                    $poin_cek = MaintenancePlanItemCheck::where('maintenance_plan_item_checks.id', '=', $request->get('id'))
                                    ->where(db::raw('DATE_FORMAT(maintenance_plan_checks.updated_at, "%Y-%m-%d")'), '=', date('Y-m-d'))
            // ->leftJoin('maintenance_plan_checks', 'maintenance_plan_item_checks.machine_name')
                                    ->leftJoin('maintenance_plan_checks', function ($join) {
                                        $join->on('maintenance_plan_item_checks.machine_name', '=', 'maintenance_plan_checks.item_code');
                                        $join->on('maintenance_plan_item_checks.item_check', '=', 'maintenance_plan_checks.item_check');
                                        $join->on('maintenance_plan_item_checks.substance', '=', 'maintenance_plan_checks.substance');
                                        $join->on('maintenance_plan_item_checks.remark', '=', 'maintenance_plan_checks.period');
                                    })
                                    ->select('maintenance_plan_checks.description', 'maintenance_plan_checks.remark', 'photo_before', 'photo_after')
                                    ->first();

        // $poin_cek = db::select('SELECT `maintenance_plan_checks`.`description`, `maintenance_plan_checks`.`remark`, `photo_before`, `photo_after` from `maintenance_plan_item_checks` left join `maintenance_plan_checks` on `maintenance_plan_item_checks`.`machine_name` = `maintenance_plan_checks`.`item_code` and `maintenance_plan_item_checks`.`item_check` = `maintenance_plan_checks`.`item_check` and `maintenance_plan_item_checks`.`substance` = `maintenance_plan_checks`.`substance` and `maintenance_plan_item_checks`.`remark` = `maintenance_plan_checks`.`period` where `maintenance_plan_item_checks`.`id` = '.$request->get('id').' and DATE_FORMAT(maintenance_plan_checks.updated_at, "%Y-%m-%d") = "'.date('Y-m-d').'" and `maintenance_plan_item_checks`.`deleted_at` is null limit 1');

                                    $response = array(
                                        'status' => true,
                                        'datas' => $poin_cek,
                                        'query' => DB::getQueryLog(),
                                    );
                                    return Response::json($response);
                                }

                                public function getPlannedSchedule(Request $request)
                                {

                                    $mon = date("Y-m");

                                    if ($request->get('mon')) {
                                        $mon = $request->get('mon');
                                    }
        // $daily = MaintenancePlanItemCheck::leftJoin('maintenance_plan_items', 'maintenance_plan_items.machine_name', '=', 'maintenance_plan_item_checks.machine_name')
        // ->where('maintenance_plan_item_checks.remark', 'like', '%HARI%')
        // ->select('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
        // ->groupBy('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
        // ->orderBy('maintenance_plan_item_checks.machine_name', 'asc')
        // ->get();

                                    $master_machine_daily = db::select("SELECT * from
                                     (SELECT count(machine_name) as jml_mesin, machine_detail.remark, machine_detail.category, machine_detail.machine_group from
                                         (SELECT maintenance_plan_item_checks.machine_name, maintenance_plan_item_checks.remark, maintenance_plan_items.category, maintenance_plan_items.machine_group from
                                             maintenance_plan_item_checks left join maintenance_plan_items on maintenance_plan_items.machine_name = maintenance_plan_item_checks.machine_name
                                             where maintenance_plan_item_checks.remark = '1-HARI'
                                             group by maintenance_plan_item_checks.machine_name, maintenance_plan_item_checks.remark, maintenance_plan_items.category, maintenance_plan_items.machine_group) as machine_detail
                                         group by machine_detail.remark, machine_detail.category, machine_detail.machine_group) as machine");

                                    $daily_result = db::select("SELECT item_code, machine_group, DATE_FORMAT(maintenance_plan_checks.created_at,'%Y-%m-%d') as dt from maintenance_plan_checks
                                     left join maintenance_plan_items on maintenance_plan_items.machine_name = maintenance_plan_checks.item_code
                                     where period = '1-HARI' and DATE_FORMAT(maintenance_plan_checks.created_at,'%Y-%m') = '" . $mon . "'
                                     group by item_code, machine_group, DATE_FORMAT(maintenance_plan_checks.created_at,'%Y-%m-%d')");

                                    $daily_summary = db::select("SELECT machine_group, check_date, count(item_code) as jml_cek from
                                     (SELECT item_code, machine_group, check_date from maintenance_plan_checks
                                         left join maintenance_plan_items on maintenance_plan_items.machine_name = maintenance_plan_checks.item_code
                                         where period = '1-HARI' and DATE_FORMAT(maintenance_plan_checks.created_at,'%Y-%m') = '" . $mon . "'
                                         group by item_code, machine_group, check_date) as semua
                                         group by machine_group, check_date");

        // $weekly = MaintenancePlanItemCheck::leftJoin('maintenance_plan_items', 'maintenance_plan_items.machine_name', '=', 'maintenance_plan_item_checks.machine_name')
        // ->where('maintenance_plan_item_checks.remark', 'like', '%MINGGU%')
        // ->select('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
        // ->groupBy('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
        // ->orderBy('maintenance_plan_item_checks.machine_name', 'asc')
        // ->get();

        // $monthly = MaintenancePlanItemCheck::leftJoin('maintenance_plan_items', 'maintenance_plan_items.machine_name', '=', 'maintenance_plan_item_checks.machine_name')
        // ->where('maintenance_plan_item_checks.remark', 'like', '%BULAN%')
        // ->select('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
        // ->groupBy('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
        // ->orderBy('maintenance_plan_item_checks.machine_name', 'asc')
        // ->get();

        // $yearly = MaintenancePlanItemCheck::leftJoin('maintenance_plan_items', 'maintenance_plan_items.machine_name', '=', 'maintenance_plan_item_checks.machine_name')
        // ->where('maintenance_plan_item_checks.remark', 'like', '%TAHUN%')
        // ->select('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
        // ->groupBy('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
        // ->orderBy('maintenance_plan_item_checks.machine_name', 'asc')
        // ->get();

                                     $weeks = db::select('SELECT week_name FROM weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = DATE_FORMAT(now(),"%Y-%m") group by week_name');

                                     $date = $mon . '-01';

                                     $mons = date('M Y', strtotime($date));

                                     $response = array(
                                        'status' => true,
                                        'mch_daily' => $master_machine_daily,
                                        'daily_data' => $daily_result,
                                        'daily_summary' => $daily_summary,
                                        'now' => $date,
                                        'week' => $weeks,
                                        'mon' => $mons,
                                    );
                                     return Response::json($response);
                                 }

                                 public function getPlannedScheduleDetail(Request $request)
                                 {
                                    $data_details = MaintenancePlanCheck::leftJoin('maintenance_plan_items', 'maintenance_plan_checks.item_code', '=', 'maintenance_plan_items.machine_name')
                                    ->where('check_date', '=', $request->get('date'))
                                    ->where('machine_group', '=', $request->get('group_machine'))
                                    ->select('maintenance_plan_checks.item_code', db::raw('maintenance_plan_items.description as item_name'), 'maintenance_plan_items.machine_group', 'maintenance_plan_items.location', 'item_check', 'substance', 'period', 'check', 'check_value', 'maintenance_plan_checks.description', 'photo_before')
                                    ->get();

                                    $response = array(
                                        'status' => true,
                                        'data_details' => $data_details,
                                    );
                                    return Response::json($response);
                                }

                                public function closePendingVendor(Request $request)
                                {
        // DB::transaction(function () use ($request) {
                                    MaintenanceJobPending::where('order_no', '=', $request->get('spk_number'))
                                    ->update([
                                        'description' => $request->get('vendor_po') . " ~ " . $request->get('vendor_name'),
                                        'time' => $request->get('vendor_start') . " ~ " . $request->get('vendor_finish'),
                                    ]);

                                    MaintenanceJobOrder::where('order_no', $request->get('spk_number'))
                                    ->update(['remark' => 7]);

                                    $spk_log = new MaintenanceJobOrderLog;
                                    $spk_log->order_no = $request->get('order_no');
                                    $spk_log->remark = 7;
                                    $spk_log->created_by = Auth::user()->username;
                                    $spk_log->save();

        // });

                                    $response = array(
                                        'status' => true,
                                    );
                                    return Response::json($response);
                                }

                                public function fetchSPKUrgentReport(Request $request)
                                {
                                    $master = MaintenanceJobOrder::leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "maintenance_job_orders.created_by")
                                    ->leftJoin(db::raw('(SELECT process_code ,process_name from processes where remark = "maintenance") as process'), 'process.process_code', '=', 'maintenance_job_orders.remark')
                                    ->where("priority", "=", "Urgent")
                                    ->select("order_no", db::raw("name AS requester"), "priority", "category", "description", "process_name", "note")
                                    ->get();

                                    $details = db::select("select maintenance_job_orders.order_no, maintenance_job_reports.operator_id, cause, handling, photo, maintenance_job_processes.operator_id as operator_process, start_actual, finish_actual from maintenance_job_orders
                                     left join maintenance_job_reports on maintenance_job_reports.order_no = maintenance_job_orders.order_no
                                     left join maintenance_job_processes on maintenance_job_processes.order_no = maintenance_job_orders.order_no
                                     where priority = 'Urgent' and maintenance_job_orders.remark < 6");

                                    $response = array(
                                        'status' => true,
                                        'datas' => $master,
                                    );
                                    return Response::json($response);
                                }

                                public function receiptSPK(Request $request)
                                {
                                    MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
                                    ->update(['remark' => 7]);

                                    $spk_log = MaintenanceJobOrderLog::firstOrNew(
                                        array(
                                            'order_no' => $request->get('order_no'),
                                            'remark' => 7,
                                        )
                                    );
                                    $spk_log->created_by = Auth::user()->username;
                                    $spk_log->save();

                                    MaintenanceJobReport::where('order_no', '=', $request->get('order_no'))
                                    ->where('operator_id', '=', Auth::user()->username)
                                    ->update(['receipt_id' => $request->get('employee_id')]);

                                    $response = array(
                                        'status' => true,
                                    );
                                    return Response::json($response);
                                }

                                public function rejectSPK(Request $request)
                                {
                                    MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
                                    ->update([
                                        'remark' => 8,
                                        'rejected_by' => Auth::user()->username . '/' . Auth::user()->name,
                                        'reject_reason' => $request->get('reason'),
                                    ]);

                                    $spk_log = MaintenanceJobOrderLog::firstOrNew(
                                        array(
                                            'order_no' => $request->get('order_no'),
                                            'remark' => 8,
                                        )
                                    );
                                    $spk_log->created_by = Auth::user()->username;
                                    $spk_log->save();

                                    $response = array(
                                        'status' => true,
                                    );
                                    return Response::json($response);
                                }

                                public function exportSPKList(Request $request)
                                {
                                    DB::connection()->enableQueryLog();
                                    $maintenance_job_orders = MaintenanceJobOrder::whereNull('maintenance_job_processes.deleted_at')
                                    ->leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "maintenance_job_orders.created_by")
                                    ->leftJoin("maintenance_job_processes", "maintenance_job_processes.order_no", "=", "maintenance_job_orders.order_no")
                                    ->leftJoin('maintenance_job_reports', function ($join) {
                                        $join->on('maintenance_job_reports.order_no', '=', 'maintenance_job_processes.order_no');
                                        $join->on('maintenance_job_reports.operator_id', '=', 'maintenance_job_processes.operator_id');
                                    })
                                    ->leftJoin("maintenance_job_pendings", "maintenance_job_orders.order_no", "=", "maintenance_job_pendings.order_no")
                                    ->leftJoin(db::raw("employee_syncs as  es"), "es.employee_id", "=", "maintenance_job_processes.operator_id")
                                    ->leftJoin(db::raw("(select process_code, process_name from processes where remark = 'maintenance') AS process"), "maintenance_job_orders.remark", "=", "process.process_code")
                                    ->leftJoin("maintenance_plan_items", "maintenance_plan_items.machine_id", "=", "maintenance_job_orders.machine_name")
                                    ->select("maintenance_job_orders.order_no", "employee_syncs.name", db::raw('DATE_FORMAT(maintenance_job_orders.created_at, "%Y-%m-%d %H:%i") as date'), "priority", "maintenance_job_orders.section", "type", "maintenance_job_orders.category", "machine_condition", "danger", "maintenance_job_orders.description", "safety_note", "target_date", "process_name", db::raw("es.name as name_op"), db::raw("es.employee_id as id_op"), db::raw("DATE_FORMAT(maintenance_job_processes.start_actual, '%Y-%m-%d %H:%i') start_actual"), db::raw("DATE_FORMAT(maintenance_job_processes.finish_actual, '%Y-%m-%d %H:%i') finish_actual"), db::raw("ROUND(TIMESTAMPDIFF(second, maintenance_job_processes.start_actual, maintenance_job_processes.finish_actual) / 60,2) as time_actual"), "maintenance_job_pendings.status", db::raw("maintenance_job_pendings.description as pending_desc"), "maintenance_job_orders.machine_name", "cause", "handling", db::raw('handling_photo as photo'), "note", "machine_remark", db::raw("maintenance_plan_items.description as machine_desc"), "maintenance_plan_items.location", 'maintenance_job_reports.prevention', 'maintenance_job_orders.remark');

                                    if (strlen($request->get('reqFrom')) > 0) {
                                        $reqFrom = date('Y-m-d', strtotime($request->get('reqFrom')));
                                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '>=', $reqFrom);
                                    }
                                    if (strlen($request->get('reqTo')) > 0) {
                                        $reqTo = date('Y-m-d', strtotime($request->get('reqTo')));
                                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '<=', $reqTo);
                                    }
                                    if (strlen($request->get('targetFrom')) > 0) {
                                        $targetFrom = date('Y-m-d', strtotime($request->get('targetFrom')));
                                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '>=', $targetFrom);
                                    }
                                    if (strlen($request->get('targetTo')) > 0) {
                                        $targetTo = date('Y-m-d', strtotime($request->get('targetTo')));
                                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '<=', $targetTo);
                                    }
                                    if (strlen($request->get('finFrom')) > 0) {
                                        $finFrom = date('Y-m-d', strtotime($request->get('finFrom')));
                                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '>=', $finFrom);
                                    }
                                    if (strlen($request->get('finTo')) > 0) {
                                        $finTo = date('Y-m-d', strtotime($request->get('finTo')));
                                        $maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '<=', $finTo);
                                    }
                                    if (strlen($request->get('orderNo')) > 0) {
                                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.order_no', '=', $request->get('orderNo'));
                                    }
                                    if (strlen($request->get('section')) > 0) {
                                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.section', '=', $request->get('section'));
                                    }
                                    if (strlen($request->get('priority')) > 0) {
                                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.priority', '=', $request->get('priority'));
                                    }
                                    if (strlen($request->get('workType')) > 0) {
                                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.type', '=', $request->get('workType'));
                                    }
                                    if (strlen($request->get('remark')) > 0) {
                                        if ($request->get('remark') != 'all') {
                                            $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.remark', '=', $request->get('remark'));
                                        }
                                    } else {
                                        $maintenance_job_orders = $maintenance_job_orders->whereIn('maintenance_job_orders.remark', [5, 6]);
                                    }
                                    if (strlen($request->get('status')) > 0) {
                                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_pendings.status', '=', $request->get('status'));
                                    }
                                    if (strlen($request->get('username')) > 0) {
                                        $maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.created_by', '=', $request->get('username'));
                                    }

                                    $maintenance_job_orders = $maintenance_job_orders->orderBy('maintenance_job_orders.created_at', 'desc')
                                    ->get();

                                    $data = array(
                                        'maintenance_job_orders' => $maintenance_job_orders,
                                        'query' => DB::getQueryLog(),
                                    );

                                    ob_clean();
                                    Excel::create('List SPK', function ($excel) use ($data) {
                                        $excel->sheet('SPK', function ($sheet) use ($data) {
                                            return $sheet->loadView('maintenance.spk_excel', $data);
                                        });
                                    })->export('xlsx');
                                }

                                public function fetchMachineHistory(Request $request)
                                {
                                    $machine_logs = MaintenanceMachineProblemLog::select('machine_id', 'machine_name', 'location', 'started_time', 'finished_time', 'defect', 'handling', 'prevention', 'trouble_part');
                                    if (strlen($request->get('reqFrom')) > 0) {
                                        $machine_logs = $machine_logs->where('maintenance_machine_problem_logs.started_time', '>=', $request->get('reqFrom'));
                                    }

                                    if (strlen($request->get('reqTo')) > 0) {
                                        $machine_logs = $machine_logs->where('maintenance_machine_problem_logs.started_time', '<=', $request->get('reqTo'));
                                    }

                                    if (strlen($request->get('machineName')) > 0) {
                                        $machine_logs = $machine_logs->where('maintenance_machine_problem_logs.machine_id', '=', $request->get('machineName'));
                                    }

                                    if (strlen($request->get('location_filter')) > 0) {
                                        $machine_logs = $machine_logs->where('maintenance_machine_problem_logs.location', '=', $request->get('location_filter'));
                                    }

                                    $machine_logs = $machine_logs->orderBy('started_time', 'asc')->get();

                                    $response = array(
                                        'status' => true,
                                        'logs' => $machine_logs,
                                    );
                                    return Response::json($response);
                                }

                                public function postMachineHistory(Request $request)
                                {
                                    $mct_log = new MaintenanceMachineProblemLog;
                                    $mct_log->machine_id = $request->get('id_mesin');
                                    $mct_log->machine_name = $request->get('nama_mesin');
                                    $mct_log->location = $request->get('lokasi');
                                    $mct_log->defect = $request->get('kerusakan');
                                    $mct_log->handling = $request->get('penanganan');
                                    $mct_log->prevention = $request->get('pencegahan');
                                    $mct_log->part = $request->get('part');
                                    $mct_log->started_time = $request->get('mulai');
                                    $mct_log->finished_time = $request->get('selesai');
                                    $mct_log->created_by = Auth::user()->username;
                                    $mct_log->save();

                                    $response = array(
                                        'status' => true,
                                    );
                                    return Response::json($response);
                                }

                                public function fetchSPKOperatorWorkload()
                                {
                                    DB::enableQueryLog();
                                    $datas = MaintenanceJobProcess::whereNull('maintenance_job_processes.deleted_at')
                                    ->whereNull('maintenance_job_orders.deleted_at')
                                    ->whereIn('maintenance_job_orders.remark', [3, 4, 5])
                                    ->leftJoin('maintenance_job_orders', 'maintenance_job_processes.order_no', '=', 'maintenance_job_orders.order_no')
                                    ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'maintenance_job_processes.operator_id')
                                    ->select('maintenance_job_processes.order_no', 'operator_id', 'name', 'maintenance_job_processes.start_actual', 'maintenance_job_processes.finish_actual', 'maintenance_job_orders.remark', db::raw('maintenance_job_processes.remark AS status'))
                                    ->orderBy('operator_id', 'asc')
                                    ->orderBy('remark', 'desc')
                                    ->get();

                                    $op = EmployeeSync::whereNull('end_date')
                                    ->where('group', '=', 'Maintenance Group')
                                    ->select('employee_id', 'name')
                                    ->orderBy('employee_id', 'asc')
                                    ->get();

                                    $response = array(
                                        'status' => true,
                                        'datas' => $datas,
                                        'operator' => $op,
                                        'query' => DB::getQueryLog(),
                                    );
                                    return Response::json($response);
                                }

                                public function fetchOperatorPosition(Request $request)
                                {
                                    $dt = date('Y-m-d');
        // $emp_loc = MaintenanceOperatorLocation::select('employee_id', 'employee_name', 'location', 'remark', 'created_at', db::raw('RIGHT(acronym(employee_name), 2) AS short_name'))->get();

                                    $emp_loc = db::select("SELECT employee_syncs.employee_id, `name`, RIGHT(acronym(`name`), 2) as short_name, shiftdaily_code, location, maintenance_operator_locations.remark as job, attend_code, maintenance_pics.remark from employee_syncs
                                     left join sunfish_shift_syncs on sunfish_shift_syncs.employee_id = employee_syncs.employee_id
                                     left join maintenance_operator_locations on maintenance_operator_locations.employee_id = employee_syncs.employee_id
                                     left join maintenance_pics on employee_syncs.employee_id = maintenance_pics.pic_id
                                     where shift_date = '" . $dt . "' and  `group` = 'Maintenance Group' and end_date is null
                                     order by shiftdaily_code asc");

                                    $loc_data_temp = MaintenanceOperatorLocation::select(
                                        'employee_id',
                                        'employee_name',
                                        'location',
                                        'remark',
                                        'created_at',
                                        'qr_code'
                                    )->get();

                                    $response = array(
                                        'status' => true,
                                        'emp_loc' => $emp_loc,
                                        'loc_temp' => $loc_data_temp,
                                    );
                                    return Response::json($response);
                                }

                                public function postOperatorPosition(Request $request)
                                {
                                    $emp = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();

                                    $op_qty = MaintenanceOperatorLocation::where('employee_id', '=', Auth::user()->username)->first();

                                    if (count($op_qty) > 0) {
                                        if ($op_qty->qr_code == $request->get('code')) {
                                            $mtc_op = $op_qty;

                                            $mtc_op_log = new MaintenanceOperatorLocationLog;
                                            $mtc_op_log->employee_id = $op_qty->employee_id;
                                            $mtc_op_log->employee_name = $op_qty->employee_name;
                                            $mtc_op_log->qr_code = $op_qty->qr_code;
                                            $mtc_op_log->machine_id = $op_qty->machine_id;
                                            $mtc_op_log->description = $op_qty->description;
                                            $mtc_op_log->location = $op_qty->location;
                                            $mtc_op_log->remark = $op_qty->remark;
                                            $mtc_op_log->logged_in_at = $op_qty->created_at;
                                            $mtc_op_log->logged_out_at = date('Y-m-d H:i:s');
                                            $mtc_op_log->created_by = Auth::user()->username;
                                            $mtc_op_log->save();

                                            $op_qty->forceDelete();

                                            $response = array(
                                                'status' => true,
                                                'op_time' => $mtc_op,
                                                'remark' => 'logged_out',
                                            );
                                            return Response::json($response);
                                        } else {
                                            $response = array(
                                                'status' => false,
                                            );
                                            return Response::json($response);
                                        }
                                    } else {
                                        $mtc_op = new MaintenanceOperatorLocation;
                                        $mtc_op->employee_id = Auth::user()->username;
                                        $mtc_op->employee_name = $emp->name;
                                        $mtc_op->qr_code = $request->get('code');
                                        $mtc_op->machine_id = $request->get('code');
                                        $mtc_op->description = $request->get('desc');
                                        $mtc_op->location = $request->get('location');
                                        $mtc_op->remark = $request->get('remark');
                                        $mtc_op->created_by = Auth::user()->username;
                                        $mtc_op->save();

                                        $response = array(
                                            'status' => true,
                                            'remark' => 'logged_in',
                                            'op_time' => $mtc_op,
                                        );
                                        return Response::json($response);
                                    }
                                }

                                public function fetchOperatorWorkload(Request $request)
                                {
                                    $workload = db::select("SELECT total.name, SUM(waktu_job) waktu_job, SUM(waktu_plan) waktu_plan, SUM(waktu_spk) waktu_spk FROM
                                     (SELECT emp.name, IFNULL(waktu_job,0) waktu_job, IFNULL(waktu_plan,0) waktu_plan, IFNULL(waktu_spk,0) waktu_spk from
                                         (SELECT `name` from employee_syncs where `Group` = 'Maintenance Group' and end_date is null) as emp
                                         left join
                                         (select employee_name,
                                             ROUND(IF(remark = 'job',TIMESTAMPDIFF(SECOND, created_at, now()),0) / 60) as waktu_job,
                                             ROUND(IF(remark = 'planned',TIMESTAMPDIFF(SECOND,created_at, now()),0) / 60) as waktu_plan,
                                             ROUND(IF(remark = 'spk',TIMESTAMPDIFF(SECOND, created_at, now()),0) / 60) as waktu_spk
                                             from maintenance_operator_locations where qr_code is not null
                                             and DATE_FORMAT(created_at, '%Y-%m-%d') = '" . $request->get('tanggal') . "' ) as work_time on emp.name = work_time.employee_name

                                             UNION ALL

                                             SELECT emp.name, IFNULL(ROUND(waktu_job),0) waktu_job, IFNULL(ROUND(waktu_plan), 0) waktu_plan, IFNULL(ROUND(waktu_spk), 0) waktu_spk from
                                             (SELECT `name` from employee_syncs where `Group` = 'Maintenance Group' and end_date is null) as emp
                                             left join
                                             (SELECT employee_name,
                                             SUM(IF(remark = 'job',TIMESTAMPDIFF(SECOND, logged_in_at, logged_out_at),0) / 60) as waktu_job,
                                             SUM(IF(remark = 'planned',TIMESTAMPDIFF(SECOND,logged_in_at, logged_out_at),0) / 60) as waktu_plan,
                                             SUM(IF(remark = 'spk',TIMESTAMPDIFF(SECOND, logged_in_at, logged_out_at),0) / 60) as waktu_spk
                                             FROM `maintenance_operator_location_logs`
                                             WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '" . $request->get('tanggal') . "'
                                             group by employee_name) as work_time on emp.name = work_time.employee_name
                                             ) as total
                                             group by `name`
                                             order by `name` asc");

                                         $response = array(
                                            'status' => true,
                                            'data_op' => $workload,
                                        );
                                         return Response::json($response);
                                     }

                                     public function fetchMttbf(Request $request)
                                     {
                                        DB::connection()->enableQueryLog();
                                        $l_hours = db::table('maintenance_machine_load_hours')
                                        ->leftJoin('maintenance_plan_items', 'maintenance_machine_load_hours.machine_id', '=', 'maintenance_plan_items.machine_id');

                                        if (strlen($request->get('period')) > 0) {
                                            $l_hours = $l_hours->where('maintenance_machine_load_hours.mon', '=', $request->get('period') . '-01');
                                        }

                                        if (strlen($request->get('location')) > 0) {
                                            $l_hours = $l_hours->where('location', '=', $request->get('location'));
                                        }

                                        if (strlen($request->get('machine_group')) > 0) {
                                            $l_hours = $l_hours->where('machine_group', '=', $request->get('machine_group'));
                                        }

                                        if (strlen($request->get('machine_code')) > 0) {
                                            $l_hours = $l_hours->where('maintenance_machine_load_hours.machine_id', '=', $request->get('machine_code'));
                                        }

                                        $l_hours = $l_hours->select('maintenance_machine_load_hours.machine_id', 'machine_group', 'description', 'area', 'load_hour', 'shift_number', 'trouble', 'working_day', db::raw('DATE_FORMAT(mon,"%b %Y") as mon2'))
                                        ->get();

                                        $period = date('Y-m');

                                        if (strlen($request->get('period')) > 0) {
                                            $period = $request->get('period');
                                        }

                                        $datas = db::select("SELECT mjo.machine_name, SUM(TIMESTAMPDIFF(MINUTE,created_at,fin)) as down_time_min, SUM(dt) as repair_time, COUNT(mjo.order_no) as down_time_count from
                                         (select order_no, machine_name, created_at from maintenance_job_orders
                                             where deleted_at is null and remark in (5,6)
                                             and machine_name is not null and machine_name <> 'Lain - lain' and type = 'Perbaikan'
                                             and DATE_FORMAT(created_at, '%Y-%m') = '" . $period . "') mjo
                                             left join
                                             (
                                             SELECT order_no, max(down_time) as dt from
                                             (SELECT order_no, operator_id, SUM(TIMESTAMPDIFF(MINUTE,started_at,finished_at)) as down_time from maintenance_job_reports
                                             group by order_no, operator_id) as rpt
                                             group by order_no
                                             ) rpts on mjo.order_no = rpts.order_no
                                             left join
                                             (
                                             select order_no, max(finished_at) as fin from maintenance_job_reports
                                             group by order_no
                                             ) as rptrep on mjo.order_no = rptrep.order_no
                                             group by machine_name");

                                         $response = array(
                                            'status' => true,
                                            'l_hours' => $l_hours,
                                            'datas' => $datas,
                                            'query' => DB::getQueryLog(),
                                        );
                                         return Response::json($response);
                                     }

                                     public function fetchMachineBreakdownGraph(Request $request)
                                     {
                                        if ($request->get('fiscal')) {
                                            $dates = db::select("SELECT week_date FROM `weekly_calendars` where fiscal_year = '" . $request->get('fiscal') . "' and week_date <= NOW() ORDER BY week_date asc");
                                        } else {
                                            $dates = db::select("SELECT week_date FROM `weekly_calendars` where fiscal_year = (SELECT fiscal_year from weekly_calendars where week_date = DATE(NOW())) and week_date <= NOW()
                                                ORDER BY week_date asc");
                                        }

                                        $load_hour = db::select("SELECT maintenance_machine_load_hours.machine_id, machine_group, description, area, load_hour, shift_number, trouble, working_day, DATE_FORMAT(mon,'%Y-%m') as mon2 from maintenance_machine_load_hours
                                         left join maintenance_plan_items on maintenance_machine_load_hours.machine_id = maintenance_plan_items.machine_id
                                         where mon >= '" . $dates[0]->week_date . "' AND mon <= '" . $dates[count($dates) - 1]->week_date . "'");

                                        $mon_min = explode('-', $dates[0]->week_date);
                                        $mon_max = explode('-', $dates[count($dates) - 1]->week_date);

                                        $where_group = "";
                                        if ($request->get('machine_group')) {
                                            $where_group = "where machine_group = '" . $request->get('machine_group') . "'";
                                        }

                                        $chart_data = db::select("SELECT masters.*, maintenance_plan_items.machine_group from
                                         (SELECT DATE_FORMAT(created_at, '%Y-%m') as mon, mjo.machine_name, SUM(TIMESTAMPDIFF(MINUTE,created_at,fin)) as down_time_min, SUM(dt) as repair_time, COUNT(mjo.order_no) as down_time_count from
                                             (select order_no, machine_name, created_at from maintenance_job_orders
                                                 where deleted_at is null and remark in (5,6)
                                                 and machine_name is not null and machine_name <> 'Lain - lain' and type = 'Perbaikan'
                                                 and DATE_FORMAT(created_at, '%Y-%m') >= '" . $mon_min[0] . "-" . $mon_min[1] . "'
                                                 and DATE_FORMAT(created_at, '%Y-%m') <= '" . $mon_max[0] . "-" . $mon_max[1] . "') mjo
                                                 left join
                                                 (
                                                 SELECT order_no, max(down_time) as dt from
                                                 (SELECT order_no, operator_id, SUM(TIMESTAMPDIFF(MINUTE,started_at,finished_at)) as down_time from maintenance_job_reports
                                                 group by order_no, operator_id) as rpt
                                                 group by order_no
                                                 ) rpts on mjo.order_no = rpts.order_no
                                                 left join
                                                 (
                                                 select order_no, max(finished_at) as fin from maintenance_job_reports
                                                 group by order_no
                                                 ) as rptrep on mjo.order_no = rptrep.order_no
                                                 group by DATE_FORMAT(created_at, '%Y-%m'), machine_name) as masters
                                                 left join maintenance_plan_items on maintenance_plan_items.machine_id = masters.machine_name " . $where_group);

                                             $response = array(
                                                'status' => true,
                                                'load_hour' => $load_hour,
                                                'chart_data' => $chart_data,
                                            );
                                             return Response::json($response);
                                         }

                                         public function fetchTroubleReport(Request $request)
                                         {
                                            $date_from = date('Y-m-01');
                                            $date_to = date('Y-m-t');

                                            if (strlen($request->get('tanggal_from')) > 0) {
                                                $date_from = $request->get('tanggal_from') . '-01';
                                            }

                                            if (strlen($request->get('tanggal_to')) > 0) {
                                                $date_to = date('Y-m-t', strtotime($request->get('tanggal_to') . '-01'));
                                            }

                                            $machine_groups = MaintenanceMachineProblemLog::leftJoin('maintenance_plan_items', 'maintenance_machine_problem_logs.machine_id', '=', 'maintenance_plan_items.machine_id')
                                            ->where('maintenance_machine_problem_logs.started_time', '>=', $date_from)
                                            ->where('maintenance_machine_problem_logs.started_time', '<=', $date_to)
                                            ->whereNotNull('maintenance_plan_items.machine_group')
                                            ->whereNotNull('maintenance_machine_problem_logs.part_inspection')
                                            ->select('maintenance_plan_items.machine_group', db::raw('count(maintenance_machine_problem_logs.id) as jml_rusak'))
                                            ->groupBy('maintenance_plan_items.machine_group')
                                            ->orderBy(db::raw('count(maintenance_machine_problem_logs.id)'), 'desc')
                                            ->limit(20)
                                            ->get();

                                            $trouble_list = MaintenanceMachineProblemLog::leftJoin('maintenance_plan_items', 'maintenance_machine_problem_logs.machine_id', '=', 'maintenance_plan_items.machine_id')
                                            ->where('maintenance_machine_problem_logs.started_time', '>=', $date_from)
                                            ->where('maintenance_machine_problem_logs.started_time', '<=', $date_to)
                                            ->whereNotNull('maintenance_machine_problem_logs.part_inspection')
                                            ->select('maintenance_plan_items.machine_group', 'maintenance_machine_problem_logs.part_inspection', db::raw('count(maintenance_machine_problem_logs.id) as jml_trouble'))
                                            ->groupBy('maintenance_machine_problem_logs.part_inspection', 'maintenance_plan_items.machine_group')
                                            ->get();

        // $error_log = MaintenanceMachineProblemLog::where(db::raw('DATE_FORMAT(started_time,"%Y-%m")'), '=', '2021-06')
        // ->select('trouble_part', db::raw('count(trouble_part) as jml_ng'))
        // ->groupBy('trouble_part')
        // ->orderBy(db::raw('count(trouble_part)'), 'desc')
        // ->get();

        // $machine_log = MaintenanceMachineProblemLog::where(db::raw('DATE_FORMAT(started_time,"%Y-%m")'), '=', '2021-06')
        // ->select('machine_id', 'machine_name', 'trouble_part', db::raw('count(trouble_part) as jml_ng'))
        // ->groupBy('machine_id', 'machine_name', 'trouble_part')
        // ->orderBy('machine_id', 'asc')
        // ->get();

        // $trouble_list = db::select('SELECT * from
        //     (select machine_name from maintenance_machine_problem_logs
        //     group by machine_name) mch
        //     cross join
        //     (select trouble_part from maintenance_machine_problem_logs
        //     group by trouble_part) trb
        //     order by machine_name, trouble_part');

                                            $response = array(
                                                'status' => true,
            // 'by_trouble' => $error_log,
                                                'machine_groups' => $machine_groups,
                                                'trouble_list' => $trouble_list,
                                                'mon_from' => date('Y M', strtotime($date_from)),
                                                'mon_to' => date('Y M', strtotime($date_to)),
                                            );
                                            return Response::json($response);
                                        }

                                        public function fetchSPKWeekly(Request $request)
                                        {
                                            $datas = db::select("SELECT mstr.week_name,
                                             SUM(IF(mstr.process_code = '3' OR mstr.process_code = '4' OR mstr.process_code = '5',IFNULL(spk.tot_spk,0),0)) as open_spk,
                                             SUM(IF(mstr.process_code = '6',IFNULL(spk.tot_spk,0),0)) as close_spk
                                             from
                                             (select * from
                                                 (select week_name from weekly_calendars
                                                     where week_date >= '2021-01-01'
                                                     group by week_name) wk_name
                                                 cross join
                                                 (select process_code from processes
                                                     where remark = 'maintenance') mtc
                                                 ) as mstr
                                             left join
                                             (
                                                 select weekly_calendars.week_name, spk.remark, SUM(spk.tot) as tot_spk from
                                                 (select DATE_FORMAT(created_at, '%Y-%m-%d') as dt, remark, count(order_no) as tot from maintenance_job_orders
                                                     where remark <> 7 and deleted_at is null
                                                     group by DATE_FORMAT(created_at, '%Y-%m-%d'), remark) as spk
                                                 left join weekly_calendars on spk.dt = weekly_calendars.week_date
                                                 group by week_name, remark
                                                 ) as spk on mstr.week_name = spk.week_name AND mstr.process_code = spk.remark
                                             group by mstr.week_name
                                             ");

                                            $response = array(
                                                'status' => true,
                                                'datas' => $datas,
                                            );
                                            return Response::json($response);
                                        }

                                        public function fetchSparePartHistory(Request $request)
                                        {
                                            $history_part = MaintenanceInventoryLog::leftJoin('maintenance_job_orders', 'maintenance_inventory_logs.remark2', '=', 'maintenance_job_orders.order_no')
                                            ->leftJoin('maintenance_inventories', 'maintenance_inventory_logs.part_number', '=', 'maintenance_inventories.part_number')
                                            ->leftJoin('employee_syncs', 'maintenance_inventory_logs.created_by', '=', 'employee_syncs.employee_id')
                                            ->where('maintenance_inventory_logs.part_number', '=', $request->get('part_number'))
                                            ->select('maintenance_inventory_logs.part_number', 'maintenance_inventories.part_name', 'maintenance_inventory_logs.status', 'maintenance_inventory_logs.quantity', 'remark1', 'remark2', 'maintenance_inventory_logs.created_by', 'employee_syncs.name', 'machine_name', 'machine_remark', 'maintenance_inventory_logs.created_at', 'maintenance_inventories.specification', 'maintenance_inventories.stock')
                                            ->get();

                                            $response = array(
                                                'status' => true,
                                                'datas' => $history_part,
                                            );
                                            return Response::json($response);
                                        }

                                        public function setSessionPlanned(Request $request)
                                        {
        // $request->get('desc');

                                            $path = public_path() . '\maintenance/planned_temp/';

                                            if ($request->get('before') != '#') {
                                                Image::make(file_get_contents($request->get('before')))->save($path . '\before_' . $request->get('id') . '.png');
            // Image::make(file_get_contents($request->get('after')))->save($path.'\after_'.$request->get('id').'.png');
                                            } else {
                                                $response = array(
                                                    'status' => false,
                                                );
                                                return Response::json($response);
                                            }

                                            db::table('maintenance_plan_check_temps')->insert([
                                                'id_check' => $request->get('id'),
                                                'description' => $request->get('desc'),
                                                'before_photo' => 'before_' . $request->get('id') . '.png',
                                                'after_Photo' => 'after_' . $request->get('id') . '.png',
                                                'remark' => 'NG',
                                                'created_by' => Auth::user()->username,
                                                'created_at' => date('Y-m-d H:i:s'),
                                                'updated_at' => date('Y-m-d H:i:s'),
                                            ]);

                                            $response = array(
                                                'status' => true,
                                            );
                                            return Response::json($response);
                                        }

                                        public function getSessionPlanned(Request $request)
                                        {
                                            $data = db::table('maintenance_plan_check_temps')->where('id_check', $request->get('id'))->first();
        // $data = db::select('select id_check, description, before_photo from maintenance_plan_check_temps where id_check = '.$request->get('id'));
                                            $response = array(
                                                'status' => true,
                                                'data' => $data,
                                            );
                                            return Response::json($response);
                                        }

                                        public function fetchMachinePartList(Request $request)
                                        {
                                            $datas = DB::table('maintenance_machine_parts');
                                            if ($request->get('id')) {
                                                $datas = $datas->where('id', '=', $request->get('id'));
                                            }

                                            $datas = $datas->get();

                                            $response = array(
                                                'status' => true,
                                                'datas' => $datas,
                                            );
                                            return Response::json($response);
                                        }

                                        public function postMachinePartList(Request $request)
                                        {

                                            if (count($request->file('part_picture')) > 0) {
                                                $num = 1;
                                                $file = $request->file('part_picture');

                                                $nama = $file->getClientOriginalName();

                                                $extension = pathinfo($nama, PATHINFO_EXTENSION);

                                                $att = date('YmdHis') . '.' . $extension;

                                                $file->move('maintenance/machine_part/', $att);

                                            } else {
                                                $att = null;
                                            }

                                            if ($request->get('id')) {
                                                DB::table('maintenance_machine_parts')
                                                ->where('id', $request->get('id'))
                                                ->update([
                                                    'machine_group' => $request->get('machine'),
                                                    'department' => $request->get('dept'),
                                                    'process' => $request->get('proc'),
                                                    'product' => $request->get('product'),
                                                    'part_name' => $request->get('part_name'),
                                                    'part_picture' => $att,
                                                    'qty' => $request->get('qty'),
                                                    'uom' => $request->get('uom'),
                                                    'price' => $request->get('price'),
                                                    'total_price' => $request->get('tot_price'),
                                                    'note' => $request->get('note'),
                                                    'created_by' => Auth::user()->username,
                                                    'created_at' => date('Y-m-d H:i:s'),
                                                    'updated_at' => date('Y-m-d H:i:s'),
                                                ]);
                                            } else {
                                                DB::table('maintenance_machine_parts')->insert([
                                                    'machine_group' => $request->get('machine'),
                                                    'department' => $request->get('dept'),
                                                    'process' => $request->get('proc'),
                                                    'product' => $request->get('product'),
                                                    'part_name' => $request->get('part_name'),
                                                    'part_picture' => $att,
                                                    'qty' => $request->get('qty'),
                                                    'uom' => $request->get('uom'),
                                                    'price' => $request->get('price'),
                                                    'total_price' => $request->get('tot_price'),
                                                    'note' => $request->get('note'),
                                                    'created_by' => Auth::user()->username,
                                                    'created_at' => date('Y-m-d H:i:s'),
                                                    'updated_at' => date('Y-m-d H:i:s'),
                                                ]);
                                            }

                                            $response = array(
                                                'status' => true,
                                            );
                                            return Response::json($response);
                                        }

                                        public function fetchPlannedTrendline(Request $request)
                                        {

                                            $datas = db::select("SELECT item_code, item_check, substance, CONCAT(item_check,' - ', substance) as cek, check_value, DATE_FORMAT(maintenance_plan_checks.created_at,'%d %b %Y') as dt from maintenance_plan_checks
                                             where check_value is not null and DATE_FORMAT(maintenance_plan_checks.created_at,'%Y-%m') = '" . $request->get('mon') . "' and item_code = '" . $request->get('machine_group') . "'
                                             order by created_at asc");

                                            $data_cek = MaintenancePlanItemCheck::select('machine_name', 'item_check', 'substance', 'lower_limit', 'upper_limit')->where('remark', '=', '1-HARI')->where('essay_category', '=', '1')->get();

                                            $response = array(
                                                'status' => true,
                                                'datas' => $datas,
                                                'cek_datas' => $data_cek,
                                            );
                                            return Response::json($response);
                                        }

                                        public function fetchMachinePartGraph()
                                        {
                                            $machine_part = DB::table('maintenance_machine_parts')
                                            ->select('department', db::raw('sum(total_price) as tot_price'))
                                            ->groupBy('department')
                                            ->get();

                                            $response = array(
                                                'status' => true,
                                                'machine_part' => $machine_part,
                                            );
                                            return Response::json($response);
                                        }

                                        public function PressureControl()
                                        {
                                            $title = 'Monitoring Utility Machine & Pressure';
                                            $title_jp = '';

                                            $location = Plc::select('location')->where('remark', '=', 'Temperature')->where('location', '=', 'Clean Room')->orderBy('location', 'asc')->get();

                                            return view('maintenance.tpm.pressure_machine', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                                'location' => $location,
                                            )
                                        )->with('page', 'Pressure Machine');
                                        }

                                        public function fetchPressureControl(Request $request)
                                        {

                                            $suhus = Plc::where('location', '=', 'Clean Room')->orderBy('location', 'asc')->get();
                                            $list_suhu = array();

                                            foreach ($suhus as $suhu) {
                                                $cpu = new ActMLEasyIf($suhu->station);
                                                $datas = $cpu->read_data($suhu->address, 10);
                                                $data = $datas[$suhu->arr];

                                                if ($suhu->remark == 'temperature') {
                // $data -= 2;
                                                }

                                                if ($suhu->remark == 'humidity') {
                                                    $data -= 2;
                                                }

                                                array_push($list_suhu, [
                                                    'location' => $suhu->location,
                                                    'remark' => $suhu->remark,
                                                    'value' => $data,
                                                    'upper_limit' => $suhu->upper_limit,
                                                    'lower_limit' => $suhu->lower_limit,
                                                ]);
                                            }

                                            $plcs = Plc::orderBy('location', 'asc')->where('remark', '=', 'Pressure')->get();
                                            $lists = array();

                                            foreach ($plcs as $plc) {
                                                $cpu = new ActMLEasyIf($plc->station);
                                                $datas = $cpu->read_data($plc->address, 10);
                                                $data = $datas[$plc->arr];

                                                array_push($lists, [
                                                    'location' => $plc->location,
                                                    'remark' => $plc->remark,
                                                    'value' => $data,
                                                    'upper_limit' => $plc->upper_limit,
                                                    'lower_limit' => $plc->lower_limit,
                                                ]);
                                            }

                                            $panel = db::select('SELECT id, data_time, remark, unit
                                                FROM sensor_datas
                                                WHERE id IN (
                                                    SELECT MAX(id)
                                                    FROM sensor_datas
                                                    WHERE category = "Panel"
                                                    GROUP BY unit
                                                    )
                                                ORDER BY data_time DESC');

                                            if (date('Y-m-d', strtotime($panel[0]->data_time)) == date('Y-m-d')) {
                                                $status = 1;
                                            } else {
                                                $status = 0;
                                            }

                                            $pump = db::select('SELECT DATE_FORMAT(data_time,"%H:%i:%s") as data_time, ROUND(sensor_value,1) as value_sensor from sensor_datas where category = "Temperature" and DATE_FORMAT(data_time,"%Y-%m-%d") = "' . date('Y-m-d') . '" and sensor_value < 100 order by data_time desc
                                                limit 1');

                                            $response = array(
                                                'status' => true,
                                                'lists' => $lists,
                                                'list_suhu' => $list_suhu,
                                                'machine_status' => $panel,
                                                'last_status' => $status,
                                                'pump_data' => $pump,
                                            );
                                            return Response::json($response);
                                        }

                                        public function fetchPlannedFinding(Request $request)
                                        {
                                            $start = '';
                                            $end = '';
                                            $where = '';

                                            if ($request->get('tgl_start') != '' && $request->get('tgl_end') != '') {
                                                $start = $request->get('tgl_start');
                                                $end = $request->get('tgl_end');

                                                $where = "WHERE DATE_FORMAT(finding_date,'%Y-%m-%d') >= '" . $start . "' AND DATE_FORMAT(finding_date,'%Y-%m-%d') <= '" . $end . "'";
                                            } else {
                                                $start = date('Y-m-d', strtotime('-6 months'));
                                                $end = date('Y-m-d');
                                            }

                                            $data_chart = db::select("SELECT DATE_FORMAT(finding_date,'%Y-%m-%d') as find_date, SUM(IF(`status` = 'Open',1,0)) as sum_open, SUM(IF(`status` = 'Close',1,0)) as sum_close, COUNT(`status`) as count_temuan FROM `maintenance_findings` " . $where . " group by DATE_FORMAT(finding_date,'%Y-%m-%d')");

                                            $data_details = MaintenanceFinding::where('status', '=', 'Open')
                                            ->where(db::raw('DATE_FORMAT(finding_date, "%Y-%m-%d")'), '>=', $start)
                                            ->where(db::raw('DATE_FORMAT(finding_date, "%Y-%m-%d")'), '<=', $end)
                                            ->select('id', 'machine_id', 'machine_description', 'machine_group', 'part_machine', 'finding_date', 'finding_description', 'finding_photo', 'handling_description', 'handling_photo', 'pic', 'status', 'remark')
                                            ->get();

                                            $response = array(
                                                'status' => true,
                                                'datas' => $data_chart,
                                                'details' => $data_details,
                                            );
                                            return Response::json($response);
                                        }

                                        public function fetchPlannedFindingbyId(Request $request)
                                        {
                                            $detail_finding = MaintenanceFinding::where('id', '=', $request->get('id'))
                                            ->first();

                                            $response = array(
                                                'status' => true,
                                                'detail' => $detail_finding,
                                            );
                                            return Response::json($response);
                                        }

                                        public function fetchPlannedFindingbyChart(Request $request)
                                        {
                                            $detail_finding = MaintenanceFinding::where(db::raw('DATE_FORMAT(finding_date, "%Y-%m-%d")'), '=', $request->get('dt'))
                                            ->where('status', '=', $request->get('status'))
                                            ->get();

                                            $response = array(
                                                'status' => true,
                                                'details' => $detail_finding,
                                            );
                                            return Response::json($response);
                                        }

                                        public function uploadPlannedFinding(Request $request)
                                        {
                                            try {
                                                if (count($request->file('foto_temuan')) > 0) {
                                                    $file = $request->file('foto_temuan');

                                                    $nama = $file->getClientOriginalName();

                                                    $filename = md5(date('Y-m-d H:i:s'));
                                                    $extension = pathinfo($nama, PATHINFO_EXTENSION);

                                                    $att = $filename . '.' . $extension;

                                                    $file->move('maintenance/finding/finding/', $att);

                                                } else {
                                                    $att = null;
                                                }

                                                $find = new MaintenanceFinding([
                                                    'machine_id' => $request->get('mesin'),
                                                    'machine_description' => $request->get('nama_mesin'),
                                                    'machine_group' => $request->get('mesin_group'),
                                                    'part_machine' => $request->get('part_mesin'),
                                                    'finding_date' => $request->get('tanggal_temuan'),
                                                    'finding_description' => $request->get('deskripsi_temuan'),
                                                    'finding_photo' => $att,
                                                    'pic' => $request->get('pic_temuan'),
                                                    'status' => 'Open',
                                                    'created_by' => Auth::user()->username,
                                                ]);
                                                $find->save();

                                                $response = array(
                                                    'status' => true,
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

                                        public function postPlannedFinding(Request $request)
                                        {
                                            try {

                                                if (count($request->file('bukti_penanganan')) > 0) {
                                                    $file = $request->file('bukti_penanganan');

                                                    $nama = $file->getClientOriginalName();

                                                    $filename = md5(date('Y-m-d H:i:s'));
                                                    $extension = pathinfo($nama, PATHINFO_EXTENSION);

                                                    $att = $filename . '.' . $extension;

                                                    $file->move('maintenance/finding/handling/', $att);

                                                } else {
                                                    $att = null;
                                                }

                                                MaintenanceFinding::where('id', '=', $request->get("id"))
                                                ->update([
                                                    'handling_description' => $request->get('penanganan'),
                                                    'handling_photo' => $att,
                                                    'handling_date' => date('Y-m-d H:i:s'),
                                                    'handling_by' => strtoupper(Auth::user()->username) . '/' . Auth::user()->name,
                                                    'status' => 'Close',
                                                ]);

                                                $response = array(
                                                    'status' => true,
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

                                        public function sendTestMail()
                                        {
                                            $data_spk = MaintenanceJobOrder::leftjoin('maintenance_job_reports', 'maintenance_job_reports.order_no', '=', 'maintenance_job_orders.order_no')
                                            ->leftJoin('maintenance_job_pendings', 'maintenance_job_pendings.order_no', '=', 'maintenance_job_orders.order_no')

                                            ->where('order_no', '=', 'SPK21030428')
                                            ->where('maintenance_job_pendings.status', '=', 'Part Tidak Ada')
                                            ->first();

                                            $response = array(
                                                'status' => true,
                                                'detail' => $detail_finding,
                                            );
                                            return Response::json($response);
                                        }

                                        public function fetchLimbah(Request $request)
                                        {
                                            $category = $request->get('category');
                                            $kemasan = $request->get('kemasan');
                                            $bulan_sekarang = date('Y-m');

                                            $select_limbah = '';
                                            if ($request->get('select_limbah') == null) {
                                                $select_limbah = '';
                                            } else {
                                                $select_limbah = "and waste_details.waste_category = '" . $request->get('select_limbah') . "'";
                                            }

                                            $stock = db::connection('ympimis_2')->table('waste_details')->where('waste_category', '=', $request->get('limbah'))->select('remaining_stock')
                                            ->orderBy('created_at', 'desc')->get();

                                            $data_limbah = db::connection('ympimis_2')->table('waste_details')->where('waste_details.waste_category', '=', $request->get('limbah'))->where('category', '=', $request->get('plh'))->select('waste_details.id', 'waste_details.waste_category', 'quantity', 'remaining_stock', 'kemasan', 'category', 'pic', 'date_in', 'unit_weight')
                                            ->leftJoin('waste_masters', 'waste_masters.waste_category', '=', 'waste_details.waste_category')
                                            ->orderBy('waste_details.created_at', 'desc')->get();

                                            $resume_detail = '';

                                            if ($category == 'Disposal') {
                                                $resume_detail = db::connection('ympimis_2')->select('select slip, waste_details.id, waste_details.waste_category, quantity, remaining_stock, kemasan, category, pic, date_in, unit_weight from waste_details left join waste_masters on waste_masters.waste_category = waste_details.waste_category where waste_details.kemasan = "' . $kemasan . '" and waste_details.category = "LOG DISPOSAL" ' . $select_limbah . ' and DATE_FORMAT(waste_details.date_disposal, "%Y-%m") = "' . $bulan_sekarang . '" order by date_in asc');
                                            } else {
                                                $resume_detail = db::connection('ympimis_2')->select('select slip, waste_details.id, waste_details.waste_category, quantity, remaining_stock, kemasan, category, pic, date_in, unit_weight from waste_details left join waste_masters on waste_masters.waste_category = waste_details.waste_category where waste_details.kemasan = "' . $kemasan . '" and waste_details.category = "' . $category . '" ' . $select_limbah . ' order by date_in asc');
                                            }

                                            $disposal = db::connection('ympimis_2')->table('waste_details')->where('waste_details.category', '=', 'Pengajuan Disposal')->select('waste_details.id')->get();
                                            $count_disposal = $disposal->count();

                                            $detail_disposal = db::connection('ympimis_2')->table('waste_details')->where('category', '=', 'Pengajuan Disposal')->select('slip', 'waste_details.id', 'waste_details.waste_category', 'quantity', 'remaining_stock', 'kemasan', 'category', 'pic', 'date_in', 'unit_weight', 'waste_vendors.short_name')
                                            ->leftJoin('waste_masters', 'waste_masters.waste_category', '=', 'waste_details.waste_category')
                                            ->leftJoin('waste_vendors', 'waste_vendors.vendor', '=', 'waste_details.vendor')
            // ->leftJoin('waste_vendors', 'waste_vendors.vendor', '=', 'waste_details.vendor')
                                            ->orderBy('waste_details.created_at', 'desc')->get();

                                            $jb1 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Jumbo Bag')->where('category', '=', 'IN')->select('remaining_stock')->get();
                                            $jb_count_in = $jb1->count();
                                            $jb2 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Jumbo Bag')->where('category', '=', 'LOG DISPOSAL')->where(db::raw('DATE_FORMAT(date_disposal, "%Y-%m")'), '=', $bulan_sekarang)->select('remaining_stock')->get();
                                            $jb_count_out = $jb2->count();
        // $jb_sisa = $jb_count_in - $jb_count_out;

                                            $pail1 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Pail')->where('category', '=', 'IN')->select('remaining_stock')->get();
                                            $pail_count_in = $pail1->count();
                                            $pail2 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Pail')->where('category', '=', 'LOG DISPOSAL')->where(db::raw('DATE_FORMAT(date_disposal, "%Y-%m")'), '=', $bulan_sekarang)->select('remaining_stock')->get();
                                            $pail_count_out = $pail2->count();
        // $pail_sisa = $pail_count_in - $pail_count_out;

                                            $drum1 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Drum')->where('category', '=', 'IN')->select('remaining_stock')->get();
                                            $drum_count_in = $drum1->count();
                                            $drum2 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Drum')->where('category', '=', 'LOG DISPOSAL')->where(db::raw('DATE_FORMAT(date_disposal, "%Y-%m")'), '=', $bulan_sekarang)->select('remaining_stock')->get();
                                            $drum_count_out = $drum2->count();
        // $drum_sisa = $drum_count_in - $drum_count_out;

                                            $karton1 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Karton')->where('category', '=', 'IN')->select('remaining_stock')->get();
                                            $karton_count_in = $karton1->count();
                                            $karton2 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Karton')->where('category', '=', 'LOG DISPOSAL')->where(db::raw('DATE_FORMAT(date_disposal, "%Y-%m")'), '=', $bulan_sekarang)->select('remaining_stock')->get();
                                            $karton_count_out = $karton2->count();
        // $karton_sisa = $karton_count_in - $karton_count_out;

                                            $plastik1 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Kantong Plastik')->where('category', '=', 'IN')->select('remaining_stock')->get();
                                            $plastik_count_in = $plastik1->count();
                                            $plastik2 = db::connection('ympimis_2')->table('waste_details')->where('kemasan', '=', 'Kantong Plastik')->where('category', '=', 'LOG DISPOSAL')->where(db::raw('DATE_FORMAT(date_disposal, "%Y-%m")'), '=', $bulan_sekarang)->select('remaining_stock')->get();
                                            $plastik_count_out = $plastik2->count();
        // $plastik_sisa = $plastik_count_in - $plastik_count_out;

                                            $konfirmasi_disposal = db::connection('ympimis_2')->select('SELECT
                                             slip_disposal
                                             FROM
                                             waste_details
                                             WHERE
                                             category = "Pengajuan Disposal"
                                             AND slip_disposal IS NOT NULL
                                             GROUP BY
                                             slip_disposal');

                                            $response = array(
                                                'status' => true,
                                                'history_data' => $data_limbah,
                                                'stock' => $stock,
                                                'resume_detail' => $resume_detail,
                                                'disposal' => $count_disposal,
                                                'detail_disposal' => $detail_disposal,
                                                'jb_in' => $jb_count_in,
                                                'jb_out' => $jb_count_out,
            // 'jb_sisa' => $jb_sisa,
                                                'pail_in' => $pail_count_in,
                                                'pail_out' => $pail_count_out,
            // 'pail_sisa' => $pail_sisa,
                                                'drum_in' => $drum_count_in,
                                                'drum_out' => $drum_count_out,
            // 'drum_sisa' => $drum_sisa,
                                                'karton_in' => $karton_count_in,
                                                'karton_out' => $karton_count_out,
            // 'karton_sisa' => $karton_sisa,
                                                'plastik_in' => $plastik_count_in,
                                                'plastik_out' => $plastik_count_out,
            // 'plastik_sisa' => $plastik_sisa,
                                                'konfirmasi_disposal' => $konfirmasi_disposal,
                                            );
                                            return Response::json($response);
                                        }

                                        public function postLimbah(Request $request)
                                        {
                                            try {
            // if ($request->get('pic') == null || $request->get('tanggal') == null || $request->get('jumlah') == null) {
                                                if ($request->get('pic') == null || $request->get('tanggal') == null) {
                                                    $response = array(
                                                        'status' => false,
                                                        'message' => 'Isikan Data Dengan Lengkap.',
                                                    );
                                                    return Response::json($response);
                                                } else {
                                                    $prefix_now = 'WWT';
                                                    $code_generator = CodeGenerator::where('note', '=', 'limbah wwt')->first();
                                                    if ($prefix_now != $code_generator->prefix) {
                                                        $code_generator->prefix = $prefix_now;
                                                        $code_generator->index = '0';
                                                        $code_generator->save();
                                                    }

                                                    $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                                                    $slip = $code_generator->prefix . $numbers;
                                                    $code_generator->index = $code_generator->index + 1;
                                                    $code_generator->save();

                                                    $input = db::connection('ympimis_2')->table('waste_details')->insert([
                                                        'slip' => $slip,
                                                        'waste_category' => $request->get('jenis_limbah'),
                                                        'quantity' => null,
                                                        'category' => 'IN',
                                                        'pic' => $request->get('pic'),
                                                        'date_in' => $request->get('tanggal'),
                    // 'tanggal_logbook' => $request->get('tanggal'),
                                                        'kemasan' => $request->get('kemasan'),
                                                        'dari_lokasi' => $request->get('dari_lokasi'),
                                                        'created_by' => Auth::user()->username,
                                                        'created_at' => date('Y-m-d H:i:s'),
                                                    ]);

                                                    $input_logbook = db::connection('ympimis_2')->table('waste_logbooks')->insert([
                                                        'slip' => $slip,
                                                        'waste_category' => $request->get('jenis_limbah'),
                                                        'quantity' => null,
                                                        'category' => 'IN',
                                                        'pic' => $request->get('pic'),
                                                        'date_in' => $request->get('tanggal'),
                                                        'tanggal_logbook' => $request->get('tanggal'),
                                                        'kemasan' => $request->get('kemasan'),
                                                        'dari_lokasi' => $request->get('dari_lokasi'),
                                                        'created_by' => Auth::user()->username,
                                                        'created_at' => date('Y-m-d H:i:s'),
                                                    ]);

                                                    $datas = db::connection('ympimis_2')->select('SELECT
                                                       slip,
                                                       waste_details.waste_category,
                                                       kode_limbah,
                                                       quantity,
                                                       kemasan,
                                                       category,
                                                       date_in
                                                       FROM
                                                       waste_details
                                                       LEFT JOIN waste_masters ON waste_masters.waste_category = waste_details.waste_category
                                                       WHERE
                                                       slip = "' . $slip . '"
                                                       AND category = "IN"
                                                       GROUP BY
                                                       slip,
                                                       waste_category,
                                                       kode_limbah,
                                                       quantity,
                                                       kemasan,
                                                       category,
                                                       date_in');

                                                    $sifat_limbah = '';

                                                    if ($datas[0]->waste_category == 'Lubricant Oil' || $datas[0]->waste_category == 'Painting Liquid Laste' || $datas[0]->waste_category == 'Liquid Cleaning Waste') {
                                                        $sifat_limbah = 'Larutan Mudah Terbakar';
                                                    } else {
                                                        $sifat_limbah = 'Padatan';
                                                    }

                // return view('maintenance.print_label_kecil_wwt', array(
                //     'data' => $datas,
                //     'sifat_limbah' => $sifat_limbah
                // ))->with('page', 'WWT');

                                                    $pdf = \App::make('dompdf.wrapper');
                                                    $pdf->getDomPDF()->set_option("enable_php", true);
                                                    $pdf->setPaper('A6', 'potrait');

                                                    $pdf->loadView('maintenance.print_label_kecil_wwt', array(
                                                        'data' => $datas,
                                                        'sifat_limbah' => $sifat_limbah,
                                                    )
                                                );

                                                    $pdf->save(public_path() . "/data_file/wwt/slip_kecil/" . $slip . ".pdf");

                                                    $response = array(
                                                        'status' => true,
                                                        'message' => 'Data Berhasil Disimpan.',
                                                        'slip' => $slip,
                                                    );
                                                    return Response::json($response);
                                                }
                                            } catch (Exception $e) {
                                                $response = array(
                                                    'status' => true,
                                                    'message' => $e->getMessage(),
                                                );
                                                return Response::json($response);
                                            }
                                        }

                                        public function UpdateLogBook(Request $request)
                                        {
                                            try {
                                                $slip = $request->get('slip');

                                                $wwt_detail = db::connection('ympimis_2')->select('select waste_category, kemasan, category, date_in from waste_details where slip = "' . $slip . '"');
                                                $tanggal = $request->get('tanggal');
                                                $pic = $request->get('pic');
                                                $dari_lokasi = $request->get('dari_lokasi');

                                                $update_logbook = db::connection('ympimis_2')->table('waste_logbooks')->insert([
                                                    'slip' => $slip,
                                                    'dari_lokasi' => $dari_lokasi,
                                                    'waste_category' => $wwt_detail[0]->waste_category,
                                                    'kemasan' => $wwt_detail[0]->kemasan,
                                                    'category' => $wwt_detail[0]->category,
                                                    'pic' => $pic,
                                                    'tanggal_logbook' => $tanggal,
                                                    'created_by' => strtoupper(Auth::user()->username),
                                                    'created_at' => date('Y-m-d H:i:s'),
                                                ]);

                                                $response = array(
                                                    'status' => true,
                                                    'message' => 'Data Berhasil Disimpan.',
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

                                        public function reviewWwt($slip)
                                        {
                                            return redirect('/data_file/wwt/slip_kecil/' . $slip . '.pdf')->with('page', 'WWT');
                                        }

                                        public function reviewWwtSlipDisposal($slip)
                                        {
                                            return redirect('/data_file/wwt/slip_besar/' . $slip . '.pdf')->with('page', 'WWT');
                                        }

                                        public function reviewConfirm($slip_disposal)
                                        {
                                            return redirect('/data_file/wwt/disposal/' . $slip_disposal . '.pdf')->with('page', 'WWT');
                                        }

                                        public function testreviewWwt(Request $request)
                                        {
                                            $title = 'WWT - Waste Control';
                                            $title_jp = '??';

        // $resumes = db::connection('ympimis_2')->select('select GROUP_CONCAT(slip) as slip, GROUP_CONCAT(waste_category) as jenis, GROUP_CONCAT(quantity) as quantity,  sum(quantity) as jumlah, count(slip) as banyak from waste_details where category = "Disposal" GROUP BY waste_category');

        // $data = [
        //     'resumes' => $resumes,
        //     'slip_disposal' => 'DPWWT00074',
        // ];

                                            $data_in = db::connection('ympimis_2')->table('waste_details')->where('category', '=', 'IN')->get();
                                            $data_disposal = db::connection('ympimis_2')->table('waste_details')->where('category', '=', 'Disposal')->get();

                                            return view('maintenance.wwt_checklist', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                                'data_in' => $data_in,
                                                'data_disposal' => $data_disposal,
                                            )
                                        )->with('page', 'WWT - Waste Control');
                                        }

                                        public function UpdateDisposal(Request $request)
                                        {
                                            try {
                                                $sl = $request->get('slip');
                                                $slip = explode(",", $sl);
                                                $vendor_arr = [];

                                                $cek_proses = db::connection('ympimis_2')->select('select distinct slip_disposal from waste_details where category = "Pengajuan Disposal"');

                                                $slip_dsp = '';

                                                if (count($cek_proses) == 0) {
                                                    $prefix_now = 'DPWWT';
                                                    $code_generator = CodeGenerator::where('note', '=', 'disposal wwt')->first();
                                                    if ($prefix_now != $code_generator->prefix) {
                                                        $code_generator->prefix = $prefix_now;
                                                        $code_generator->index = '0';
                                                        $code_generator->save();
                                                    }

                                                    $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                                                    $slip_dsp = $code_generator->prefix . $numbers;
                                                    $code_generator->index = $code_generator->index + 1;
                                                    $code_generator->save();

                                                    $newdate = date("m-d-Y");

                                                    $resumes = db::connection('ympimis_2')->select('select GROUP_CONCAT(slip) as slip, GROUP_CONCAT(kode_limbah) as kode_limbah, GROUP_CONCAT(waste_details.waste_category) as jenis, GROUP_CONCAT(quantity) as quantity,  sum(quantity) as jumlah, count(slip) as banyak from waste_details left join waste_masters on waste_details.waste_category = waste_masters.waste_category where category = "Pengajuan Disposal" and slip_disposal = "' . $slip_dsp . '" GROUP BY waste_details.waste_category');

                                                    $select = db::connection('ympimis_2')->select('select slip, waste_category, quantity, pic, date_in, kemasan from waste_details where category = "Pengajuan Disposal"');

                                                    $staff = db::connection('ympimis_2')->table('waste_approvals')->insert([
                                                        'slip_disposal' => $slip_dsp,
                                                        'approver_id' => 'PI1210001',
                                                        'approver_name' => 'Whica Parama Sastra',
                                                        'approver_email' => 'whica.parama@music.yamaha.com',
                                                        'remark' => 'Staff CHM',
                                                        'created_at' => date('Y-m-d H:i:s'),
                                                        'updated_at' => date('Y-m-d H:i:s'),
                                                    ]);

                                                    $chief = db::connection('ympimis_2')->table('waste_approvals')->insert([
                                                        'slip_disposal' => $slip_dsp,
                                                        'approver_id' => 'PI1404002',
                                                        'approver_name' => 'Priyo Jatmiko',
                                                        'approver_email' => 'priyo.jatmiko@music.yamaha.com',
                                                        'remark' => 'Chief CHM & WWT',
                                                        'created_at' => date('Y-m-d H:i:s'),
                                                        'updated_at' => date('Y-m-d H:i:s'),
                                                    ]);

                                                    $user = strtoupper(Auth::user()->username);

                                                    $approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_dsp . '" and status is null order by id asc limit 1');
                                                    $isi_approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_dsp . '"');
                                                } else {
                                                    $slip_dsp = $cek_proses[0]->slip_disposal;
                                                }

                                                foreach ($slip as $sl) {
                                                    $vendor = explode("-", $sl);
                                                    $arr = ['slip' => $vendor[0], 'vendor' => $vendor[1]];
                                                    array_push($vendor_arr, $arr);
                                                }

                                                for ($i = 0; $i < count($slip); $i++) {
                                                    $update_disposal = db::connection('ympimis_2')->table('waste_details')->where('waste_details.slip', '=', $vendor_arr[$i]['slip'])->update([
                                                        'category' => 'Pengajuan Disposal',
                                                        'slip_disposal' => $slip_dsp,
                                                        'updated_at' => date('Y-m-d H:i:s')
                                                    ]);
                                                }


                                                $response = array(
                                                    'status' => true
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

                                        public function UpdateQty(Request $request)
                                        {
                                            try {
                                                $update_qty = db::connection('ympimis_2')->table('waste_details')->where('waste_details.slip', '=', $request->get('slip'))->update([
                                                    'quantity' => $request->get('jumlah')
                                                ]);

                                                $datas = db::connection('ympimis_2')->select('SELECT
                                                    slip,
                                                    waste_details.waste_category,
                                                    kode_limbah,
                                                    quantity,
                                                    kemasan,
                                                    category,
                                                    date_in
                                                    FROM
                                                    waste_details
                                                    LEFT JOIN waste_masters ON waste_masters.waste_category = waste_details.waste_category
                                                    WHERE
                                                    slip = "' . $request->get('slip') . '"
                                                    AND category = "IN"
                                                    GROUP BY
                                                    waste_category,
                                                    slip,
                                                    waste_category,
                                                    kode_limbah,
                                                    quantity,
                                                    kemasan,
                                                    category,
                                                    date_in');

                                                $sifat_limbah = '';

                                                if ($datas[0]->waste_category == 'Lubricant Oil' || $datas[0]->waste_category == 'Painting Liquid Laste' || $datas[0]->waste_category == 'Liquid Cleaning Waste') {
                                                    $sifat_limbah = 'Larutan Mudah Terbakar';
                                                } else {
                                                    $sifat_limbah = 'Padatan';
                                                }

                                                $pdf = \App::make('dompdf.wrapper');
                                                $pdf->getDomPDF()->set_option("enable_php", true);
                                                $pdf->setPaper('A4', 'potrait');

                                                $pdf->loadView('maintenance.print_label_wwt', array(
                                                    'data' => $datas,
                                                    'sifat_limbah' => $sifat_limbah,
                                                )
                                            );

                                                $pdf->save(public_path() . "/data_file/wwt/slip_besar/" . $request->get('slip') . ".pdf");

                                                $response = array(
                                                    'status' => true,
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

                                        public function KirimEmailDisposal(Request $request)
                                        {
                                            try {
                                                $prefix_now = 'DPWWT';
                                                $code_generator = CodeGenerator::where('note', '=', 'disposal wwt')->first();
                                                if ($prefix_now != $code_generator->prefix) {
                                                    $code_generator->prefix = $prefix_now;
                                                    $code_generator->index = '0';
                                                    $code_generator->save();
                                                }

                                                $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                                                $slip = $code_generator->prefix . $numbers;
                                                $code_generator->index = $code_generator->index + 1;
                                                $code_generator->save();

                                                $newdate = date("m-d-Y");

                                                $update_pengajuan = db::connection('ympimis_2')->table('waste_details')->where('waste_details.date_in', '!=', null)->where('waste_details.category', '=', 'Pengajuan Disposal')->update([
                // 'category' => 'Pengajuan Disposal',
                                                    'slip_disposal' => $slip
                                                ]);

                                                $resumes = db::connection('ympimis_2')->select('select GROUP_CONCAT(slip) as slip, GROUP_CONCAT(kode_limbah) as kode_limbah, GROUP_CONCAT(waste_details.waste_category) as jenis, GROUP_CONCAT(quantity) as quantity,  sum(quantity) as jumlah, count(slip) as banyak from waste_details left join waste_masters on waste_details.waste_category = waste_masters.waste_category where category = "Pengajuan Disposal" and slip_disposal = "' . $slip . '" GROUP BY waste_details.waste_category');

                                                $select = db::connection('ympimis_2')->select('select slip, waste_category, quantity, pic, date_in, kemasan from waste_details where category = "Pengajuan Disposal"');

                                                $staff = db::connection('ympimis_2')->table('waste_approvals')->insert([
                                                    'slip_disposal' => $slip,
                                                    'approver_id' => 'PI1210001',
                                                    'approver_name' => 'Whica Parama Sastra',
                                                    'approver_email' => 'whica.parama@music.yamaha.com',
                // 'approver_email' => 'lukmannul.arif@music.yamaha.com',
                                                    'remark' => 'Staff CHM',
                                                    'created_at' => date('Y-m-d H:i:s'),
                                                    'updated_at' => date('Y-m-d H:i:s'),
                                                ]);

                                                $chief = db::connection('ympimis_2')->table('waste_approvals')->insert([
                                                    'slip_disposal' => $slip,
                                                    'approver_id' => 'PI1404002',
                                                    'approver_name' => 'Priyo Jatmiko',
                                                    'approver_email' => 'priyo.jatmiko@music.yamaha.com',
                                                    'remark' => 'Chief CHM & WWT',
                                                    'created_at' => date('Y-m-d H:i:s'),
                                                    'updated_at' => date('Y-m-d H:i:s'),
                                                ]);

                                                $user = strtoupper(Auth::user()->username);

                                                $approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip . '" and status is null order by id asc limit 1');
                                                $isi_approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip . '"');

            // $data = [
            //     'resumes' => $resumes,
            //     'select' => $select,
            //     'approval' => $approval,
            //     'slip_disposal' => $slip,
            //     'isi_approval' => $isi_approval,
            // ];

            // $mail_to = [];
            // array_push($mail_to, $approval[0]->approver_email);

            // ob_clean();
            // Excel::create($slip, function($excel) use ($data){
            //     $excel->sheet('Disposal', function($sheet) use ($data) {
            //         return $sheet->loadView('maintenance.attachment_loading_wwt', $data);
            //     });
            // })->store('xls', public_path("disposal_wwt"));

            // Mail::to($mail_to)
            // ->bcc(['lukmannul.arif@music.yamaha.com'])
            // ->send(new SendEmail($data, 'resume_loading_wwt'));

                                                return redirect('/index/maintenance/wwt/waste_control/update')->with('status', 'Resume Disposal Berhasil Dikirim');
            // return view('maintenance.mails_loading_wwt', array(
            //     'data' => $data
            // ))->with('page', 'WWT - Waste Control');
                                            } catch (Exception $e) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => $e->getMessage(),
                                                );
                                                return Response::json($response);
                                            }
                                        }

                                        public function ConfirmationLoading($category, $slip_disposal, $approver_id)
                                        {
                                            try {
                                                $username = strtoupper(Auth::user()->username);
                                                if ($category == 'approve') {
                                                    $approver_id = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_email from waste_approvals where slip_disposal = "' . $slip_disposal . '" and status is null order by id asc limit 1');
                                                    if ($approver_id[0]->approver_id == $username) {
                                                        $approve = db::connection('ympimis_2')->table('waste_approvals')->where('slip_disposal', '=', $slip_disposal)->where('approver_id', '=', $approver_id[0]->approver_id)->update([
                                                            'status' => 'Approve',
                                                            'approved_at' => date('Y-m-d H:i:s')
                                                        ]);

                                                        $jumlah = db::connection('ympimis_2')->select('select id from waste_approvals where slip_disposal = "' . $slip_disposal . '" and status is null order by id asc limit 1');

                    // $delete = db::connection('ympimis_2')->delete('delete from waste_details where category = "Pengajuan Disposal"');

                                                        if (count($jumlah) != 0) {
                                                            $resumes = db::connection('ympimis_2')->select('select GROUP_CONCAT(slip) as slip, GROUP_CONCAT(kode_limbah) as kode_limbah, GROUP_CONCAT(waste_details.waste_category) as jenis, GROUP_CONCAT(quantity) as quantity,  sum(quantity) as jumlah, count(slip) as banyak from waste_details left join waste_masters on waste_details.waste_category = waste_masters.waste_category where category = "Pengajuan Disposal" and slip_disposal = "' . $slip_disposal . '" GROUP BY waste_details.waste_category');
                                                            $select = db::connection('ympimis_2')->select('select slip, waste_category, quantity, pic, date_in, kemasan from waste_details where slip_disposal = "' . $slip_disposal . '"');
                                                            $approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_disposal . '" and status is null order by id asc limit 1');
                                                            $isi_approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_disposal . '"');

                                                            $data = [
                                                                'resumes' => $resumes,
                                                                'select' => $select,
                                                                'approval' => $approval,
                                                                'slip_disposal' => $slip_disposal,
                                                                'isi_approval' => $isi_approval,
                                                            ];

                                                            $mail_next = db::connection('ympimis_2')->select('select id, approver_email from waste_approvals where slip_disposal = "' . $slip_disposal . '" and status is null order by id asc limit 1');

                                                            $mail_to = [];
                                                            array_push($mail_to, $mail_next[0]->approver_email);

                        // Mail::to($mail_to)
                        // ->bcc(['lukmannul.arif@music.yamaha.com'])
                        // ->send(new SendEmail($data, 'resume_loading_wwt'));

                                                            return view('auto_approve.notif_click', array(
                                                                'title' => 'Confirmation Loading WWT',
                                                                'title_jp' => '???',
                                                                'username' => $username,
                                                                'category' => $category,
                                                                'approver_id' => $approver_id,
                                                            )
                                                        )->with('page', 'Confirmation Loading WWT');
                                                        } else {
                        // $resumes = db::connection('ympimis_2')->select('select GROUP_CONCAT(slip) as slip, GROUP_CONCAT(kode_limbah) as kode_limbah, GROUP_CONCAT(waste_details.waste_category) as jenis, GROUP_CONCAT(quantity) as quantity,  sum(quantity) as jumlah, count(slip) as banyak, nama, alamat, provinsi from waste_details 
                        //     left join waste_masters on waste_details.waste_category = waste_masters.waste_category 
                        //     left join waste_vendors on waste_details.vendor = waste_vendors.vendor 
                        //     where category = "LOG DISPOSAL" and slip_disposal = "'.$slip_disposal.'" GROUP BY waste_details.waste_category');
                        // $select = db::connection('ympimis_2')->select('select slip, waste_category, quantity, pic, date_in, kemasan from waste_details where slip_disposal = "' . $slip_disposal . '"');
                        // $approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_disposal . '" and status is null order by id asc limit 1');
                        // $isi_approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_disposal . '"');

                        // $data = [
                        //     'resumes' => $resumes,
                        //     'select' => $select,
                        //     'approval' => $approval,
                        //     'slip_disposal' => $slip_disposal,
                        //     'isi_approval' => $isi_approval,
                        // ];

                        // dd('masuk sini ya');

                                                            $update_details = db::connection('ympimis_2')->table('waste_details')->where('slip_disposal', '=', $slip_disposal)->update([
                                                                'category' => 'LOG DISPOSAL',
                                                                'updated_at' => date('Y-m-d H:i:s')
                                                            ]);

                                                            $select = db::connection('ympimis_2')->select('select slip, waste_category, quantity, pic, date_in, date_disposal, kemasan, dari_lokasi, vendor from waste_details where slip_disposal = "' . $slip_disposal . '"');

                                                            for ($i = 0; $i < count($select); $i++) {
                                                                $logs = db::connection('ympimis_2')->table('waste_logs')->insert([
                                                                    'slip_disposal' => $slip_disposal,
                                                                    'slip' => $select[$i]->slip,
                                                                    'waste_category' => $select[$i]->waste_category,
                                                                    'quantity' => $select[$i]->quantity,
                                                                    'category' => 'DISPOSAL',
                                                                    'pic' => $select[$i]->pic,
                                                                    'date_in' => $select[$i]->date_in,
                                // 'date_disposal' => date('Y-m-d H:i:s'),
                                                                    'kemasan' => $select[$i]->kemasan,
                                                                    'created_by' => $username,
                                                                    'created_at' => date('Y-m-d H:i:s'),
                                                                    'updated_at' => date('Y-m-d H:i:s'),
                                                                    'dari_lokasi' => $select[$i]->dari_lokasi,
                                                                    'vendor' => $select[$i]->vendor,
                                                                    'date_disposal' => $select[$i]->date_disposal,
                                                                ]);
                                                            }

                        // Mail::to(['whica.parama@music.yamaha.com'])
                        // ->bcc(['lukmannul.arif@music.yamaha.com'])
                        // ->send(new SendEmail($data, 'done_resume_loading_wwt'));

                        //Create PDF

                                                            $resumes = db::connection('ympimis_2')->select('SELECT
                                                              GROUP_CONCAT( slip ) AS slip,
                                                              GROUP_CONCAT( kode_limbah ) AS kode_limbah,
                                                              GROUP_CONCAT( waste_logs.waste_category ) AS jenis,
                                                              GROUP_CONCAT( quantity ) AS quantity,
                                                              sum( quantity ) AS jumlah,
                                                              count( slip ) AS banyak,
                                                              waste_logs.vendor,
                                                              nama,
                                                              alamat,
                                                              fax,
                                                              provinsi,
                                                              DATE_FORMAT( date_disposal, "%d-%m-%Y" ) AS dd,
                                                              DATE_FORMAT( waste_logs.created_at, "%d-%m-%Y" ) AS tanggal_dibuat,
                                                              kendaraan,
                                                              kapasitas
                                                              FROM
                                                              waste_logs
                                                              LEFT JOIN waste_masters ON waste_masters.waste_category  = waste_logs.waste_category
                                                              LEFT JOIN waste_vendors ON waste_vendors.vendor = waste_logs.vendor
                                                              WHERE
                                                              slip_disposal = "' . $slip_disposal . '"
                                                              GROUP BY
                                                              waste_logs.waste_category, vendor, nama, alamat, fax, provinsi, date_disposal, kendaraan, kapasitas, waste_logs.created_at');

                                                            $isi_approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_disposal . '"');

                                                            $hari = db::connection('ympimis_2')->select('SELECT
                                                              DATE_FORMAT(date_disposal,"%a") AS date_disposal
                                                              FROM
                                                              waste_logs
                                                              WHERE
                                                              slip_disposal = "' . $slip_disposal . '"
                                                              LIMIT 1');

                                                            $hari_indo = '';

                                                            if ($hari[0]->date_disposal == 'Mon') {
                                                                $hari_indo = "Senin";
                                                            } else if ($hari[0]->date_disposal == 'Tue') {
                                                                $hari_indo = "Selasa";
                                                            } else if ($hari[0]->date_disposal == 'Wed') {
                                                                $hari_indo = "Rabu";
                                                            } else if ($hari[0]->date_disposal == 'Thu') {
                                                                $hari_indo = "Kamis";
                                                            } else if ($hari[0]->date_disposal == 'Fri') {
                                                                $hari_indo = "Jum'at";
                                                            } else if ($hari[0]->date_disposal == 'Sat') {
                                                                $hari_indo = "Sabtu";
                                                            } else {
                                                                $hari_indo = "Minggu";
                                                            }

                                                            $pdf = \App::make('dompdf.wrapper');
                                                            $pdf->getDomPDF()->set_option("enable_php", true);
                                                            $pdf->setPaper('A4', 'potrait');

                                                            $pdf->loadView('maintenance.download_pdf_wwt', array(
                                                                'resumes' => $resumes,
                                                                'slip_disposal' => $slip_disposal,
                                                                'isi_approval' => $isi_approval,
                                                                'hari_indo' => $hari_indo,
                                                            )
                                                        );

                                                            $pdf->save(public_path() . "/data_file/wwt/disposal/" . $slip_disposal . ".pdf");

                                                            return view('auto_approve.notif_click', array(
                                                                'title' => 'Confirmation Loading WWT',
                                                                'title_jp' => '???',
                                                                'username' => $username,
                                                                'category' => 'Full Approve',
                                                            )
                                                        )->with('page', 'Confirmation Loading WWT');
                                                        }
                                                    } else {
                                                        return view('auto_approve.notif_click', array(
                                                            'title' => 'Confirmation Loading WWT',
                                                            'title_jp' => '???',
                                                            'username' => $username,
                                                            'category' => $category,
                                                            'approver_id' => $approver_id,
                                                        )
                                                    )->with('page', 'Confirmation Loading WWT');
                                                    }
                                                } else {
                                                    $approver_id = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id from waste_approvals where slip_disposal = "' . $slip_disposal . '" and status is null order by id asc limit 1');
                                                    if ($approver_id[0]->approver_id == $username) {
                                                        $update_pengajuan = db::connection('ympimis_2')->table('waste_details')->where('waste_details.category', '=', 'Pengajuan Disposal')->where('slip_disposal', $slip_disposal)->update([
                                                            'category' => 'IN',
                                                            'slip_disposal' => null
                                                        ]);

                                                        $delete = db::connection('ympimis_2')->delete('delete from waste_approvals where slip_disposal = "' . $slip_disposal . '"');

                    //kirim wa ya

                                                        $message = 'Pengajuan%20Disposal%20Limbah%0A%0ASlip%20Disposal%20:%20*' . $slip_disposal . '*%0ATelah%20direject.%20%0A%0APeriksa%20Kembali%20Mirai%20Anda%0A%0A-YMPI%20MIS%20Dept.-';
                                                        $curl = curl_init();

                                                        curl_setopt_array($curl, array(
                                                            CURLOPT_URL => 'https://app.whatspie.com/api/messages',
                                                            CURLOPT_SSL_VERIFYHOST => FALSE,
                                                            CURLOPT_SSL_VERIFYPEER => FALSE,
                                                            CURLOPT_RETURNTRANSFER => true,
                                                            CURLOPT_ENCODING => '',
                                                            CURLOPT_MAXREDIRS => 10,
                                                            CURLOPT_TIMEOUT => 0,
                                                            CURLOPT_FOLLOWLOCATION => true,
                                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                            CURLOPT_CUSTOMREQUEST => 'POST',
                                                            CURLOPT_POSTFIELDS => 'receiver=6281231393911&device=6281130561777&message=' . $message . '&type=chat',
                                                            CURLOPT_HTTPHEADER => array(
                                                                'Accept: application/json',
                                                                'Content-Type: application/x-www-form-urlencoded',
                                                                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
                                                            )
                                                        )
                                                    );

                                                        curl_exec($curl);


                                                        return view('auto_approve.notif_click', array(
                                                            'title' => 'Confirmation Loading WWT',
                                                            'title_jp' => '???',
                                                            'username' => $username,
                                                            'category' => $category,
                                                            'approver_id' => $approver_id,
                                                        )
                                                    )->with('page', 'Confirmation Loading WWT');
                                                    } else {
                                                        return view('auto_approve.notif_click', array(
                                                            'title' => 'Confirmation Loading WWT',
                                                            'title_jp' => '???',
                                                            'username' => $username,
                                                            'category' => $category,
                                                            'approver_id' => $approver_id,
                                                        )
                                                    )->with('page', 'Confirmation Loading WWT');
                                                    }
                                                }
                                            } catch (\Exception $e) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => $e->getMessage() . ' on Line ' . $e->getLine(),
                                                );
                                                return Response::json($response);
                                            }
                                        }

                                        public function ConfirmLimbahKeluar($slip_disposal)
                                        {
                                            $date_now = date('Y-m-d');
                                            $title = 'WWT - Waste Control';
                                            $title_jp = '??';
                                            $resumes = db::connection('ympimis_2')->select('select GROUP_CONCAT(slip) as slip, GROUP_CONCAT(kode_limbah) as kode_limbah, GROUP_CONCAT(waste_details.waste_category) as jenis, GROUP_CONCAT(quantity) as quantity,  sum(quantity) as jumlah, count(slip) as banyak from waste_details left join waste_masters on waste_details.waste_category = waste_masters.waste_category where category = "Pengajuan Disposal" GROUP BY waste_details.waste_category');

                                            $data = [
                                                'resumes' => $resumes,
                                            ];

                                            $vendor = db::connection('ympimis_2')->select('select vendor from waste_vendors');

                                            return view('maintenance.confirm_limbah_keluar', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                                'slip_disposal' => $slip_disposal,
                                                'data' => $data,
                                                'vendor' => $vendor,
                                                'date_now' => $date_now,
                                            )
                                        )->with('page', 'WWT - Waste Control');
                                        }

                                        public function SaveConfirm(Request $request)
                                        {
                                            try {
                                                $username = strtoupper(Auth::user()->username);
                                                $slip_disposal = $request->get('slip_disposal');
                                                $vendor = $request->get('vendor');
                                                $date = $request->get('date');

                                                $wwt_log = db::connection('ympimis_2')->select('select slip_disposal, slip from waste_logs where slip_disposal = "' . $slip_disposal . '"');

                                                if (count($wwt_log) == 0) {
                                                    if (($vendor && $date) != null) {
                                                        $select = db::connection('ympimis_2')->select('select slip, waste_category, quantity, pic, date_in, kemasan from waste_details where slip_disposal = "' . $slip_disposal . '"');

                                                        for ($i = 0; $i < count($select); $i++) {
                                                            $logs = db::connection('ympimis_2')->table('waste_logs')->insert([
                                                                'slip_disposal' => $slip_disposal,
                                                                'slip' => $select[$i]->slip,
                                                                'waste_category' => $select[$i]->waste_category,
                                                                'quantity' => $select[$i]->quantity,
                                                                'category' => 'DISPOSAL',
                                                                'pic' => $select[$i]->pic,
                                                                'date_in' => $select[$i]->date_in,
                                                                'date_disposal' => $date,
                                                                'kemasan' => $select[$i]->kemasan,
                                                                'created_by' => $username,
                                                                'created_at' => date('Y-m-d H:i:s'),
                                                                'vendor' => $vendor,
                                                            ]);
                                                        }

                                                        $resumes = db::connection('ympimis_2')->select('SELECT
                                                          GROUP_CONCAT( slip ) AS slip,
                                                          GROUP_CONCAT( kode_limbah ) AS kode_limbah,
                                                          GROUP_CONCAT( waste_logs.waste_category ) AS jenis,
                                                          GROUP_CONCAT( quantity ) AS quantity,
                                                          sum( quantity ) AS jumlah,
                                                          count( slip ) AS banyak,
                                                          waste_logs.vendor,
                                                          nama,
                                                          alamat,
                                                          fax,
                                                          provinsi,
                                                          DATE_FORMAT( date_disposal, "%d-%m-%Y" ) AS dd,
                                                          kendaraan,
                                                          kapasitas
                                                          FROM
                                                          waste_logs
                                                          LEFT JOIN waste_masters ON waste_masters.waste_category  = waste_logs.waste_category
                                                          LEFT JOIN waste_vendors ON waste_vendors.vendor = waste_logs.vendor
                                                          WHERE
                                                          slip_disposal = "' . $slip_disposal . '"
                                                          GROUP BY
                                                          waste_logs.waste_category, vendor, nama, alamat, fax, provinsi, date_disposal, kendaraan, kapasitas');

                                                        $isi_approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_disposal . '"');

                                                        $hari = db::connection('ympimis_2')->select('SELECT
                                                          DATE_FORMAT(date_disposal,"%a") AS date_disposal
                                                          FROM
                                                          waste_logs
                                                          WHERE
                                                          slip_disposal = "' . $slip_disposal . '"
                                                          LIMIT 1');

                                                        $hari_indo = '';

                                                        if ($hari[0]->date_disposal == 'Mon') {
                                                            $hari_indo = "Senin";
                                                        } else if ($hari[0]->date_disposal == 'Tue') {
                                                            $hari_indo = "Selasa";
                                                        } else if ($hari[0]->date_disposal == 'Wed') {
                                                            $hari_indo = "Rabu";
                                                        } else if ($hari[0]->date_disposal == 'Thu') {
                                                            $hari_indo = "Kamis";
                                                        } else if ($hari[0]->date_disposal == 'Fri') {
                                                            $hari_indo = "Jum'at";
                                                        } else if ($hari[0]->date_disposal == 'Sat') {
                                                            $hari_indo = "Sabtu";
                                                        } else {
                                                            $hari_indo = "Minggu";
                                                        }

                                                        $pdf = \App::make('dompdf.wrapper');
                                                        $pdf->getDomPDF()->set_option("enable_php", true);
                                                        $pdf->setPaper('A4', 'potrait');

                                                        $pdf->loadView('maintenance.download_pdf_wwt', array(
                                                            'resumes' => $resumes,
                                                            'slip_disposal' => $slip_disposal,
                                                            'isi_approval' => $isi_approval,
                                                            'hari_indo' => $hari_indo,
                                                        )
                                                    );

                                                        $pdf->save(public_path() . "/data_file/wwt/disposal/" . $slip_disposal . ".pdf");

                                                        $delete = db::connection('ympimis_2')->delete('delete from waste_details where slip_disposal = "' . $slip_disposal . '"');

                                                        $response = array(
                                                            'status' => true,
                                                        );
                                                    } else {
                                                        $response = array(
                                                            'status' => false,
                                                            'message' => 'Isikan Data Dengan Lengkap',
                                                        );
                                                    }
                                                } else {
                                                    $response = array(
                                                        'status' => false,
                                                        'message' => 'Data Sudah Di Disposal',
                                                    );
                                                }
                                                return Response::json($response);

                                            } catch (\Exception $e) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => $e->getMessage(),
                                                );
                                                return Response::json($response);
                                            }
                                        }

                                        public function DisplayEmail($slip_disposal)
                                        {
                                            $user = strtoupper(Auth::user()->username);

                                            $resumes = db::connection('ympimis_2')->select('select GROUP_CONCAT(slip) as slip, GROUP_CONCAT(waste_category) as jenis, GROUP_CONCAT(quantity) as quantity,  sum(quantity) as jumlah, count(slip) as banyak from waste_details where category = "Pengajuan Disposal" GROUP BY waste_category');

                                            $jumlah = db::connection('ympimis_2')->select('select id from waste_approvals where slip_disposal = "' . $slip_disposal . '" and status is null order by id asc limit 1');

                                            if (count($jumlah) == 0) {
                                                return view('auto_approve.notif_click', array(
                                                    'title' => 'Confirmation Loading WWT',
                                                    'title_jp' => '???',
                                                    'username' => $user,
                                                    'category' => 'Full Approve',
                                                )
                                            )->with('page', 'Confirmation Loading WWT');

                                            } else {
                                                $approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_disposal . '" and status is null order by id asc limit 1');
                                                $isi_approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_disposal . '"');
                                                $data = [
                                                    'resumes' => $resumes,
                                                    'approval' => $approval,
                                                    'isi_approval' => $isi_approval,
                                                ];

                                                return view('maintenance.mails_loading_wwt', array(
                                                    'title' => '',
                                                    'title_jp' => '',
                                                    'data' => $data,
                                                    'user' => $user,
                                                )
                                            )->with('page', 'Human Resource');
                                            }
                                        }

                                        public function InventoryWWT(Request $request)
                                        {
                                            $title = 'WWT - Waste Control';
                                            $title_jp = '??';

                                            return view('maintenance.wwt_inventory', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                            )
                                        )->with('page', 'WWT - Waste Control');
                                        }

                                        public function FetchInventoryWWT(Request $request)
                                        {
                                            $jenis = $request->get('id');
                                            $dari_tanggal = $request->get('dari_tanggal');
                                            $sampai_tanggal = $request->get('sampai_tanggal');

                                            if (($dari_tanggal && $sampai_tanggal) == null) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => 'Masukkan Tanggal Terlebih Dahulu',
                                                );
                                                return Response::json($response);
                                            } else {
                                                if ($jenis == 'wwt') {
                                                    $resumes = db::connection('ympimis_2')->select('select waste_details.waste_category, quantity, unit_weight, kemasan, category, pic, date_in, waste_details.created_by from waste_details left join waste_masters on waste_details.waste_category = waste_masters.waste_category where date_in >= "' . $dari_tanggal . '" and date_in <= "' . $sampai_tanggal . '"');
                                                } else {
                                                    $resumes = db::connection('ympimis_2')->select('select slip, waste_logs.waste_category, quantity, unit_weight, kemasan, category, pic, date_in, date_disposal, waste_logs.created_by from waste_logs left join waste_masters on waste_logs.waste_category = waste_masters.waste_category where date_in >= "' . $dari_tanggal . '" and date_in <= "' . $sampai_tanggal . '"');
                                                }
                                            }

                                            $response = array(
                                                'status' => true,
                                                'resumes' => $resumes,
                                            );
                                            return Response::json($response);
                                        }

                                        public function MonitoringWWT(Request $request)
                                        {
                                            $title = 'Monitoring WWT - Waste Control';
                                            $title_jp = '??';
                                            $dari = date('Y-m-01', strtotime(carbon::now()));
                                            $sampai = date('Y-m-d', strtotime(carbon::now()));

                                            return view('maintenance.wwt_monitoring', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                                'dari' => $dari,
                                                'sampai' => $sampai,
                                            )
                                        )->with('page', 'WWT - Waste Control');
                                        }

                                        public function FetchMonitoringWWT(Request $request)
                                        {
                                            $dari_tanggal = $request->get('dari_tanggal');
                                            $sampai_tanggal = $request->get('sampai_tanggal');
                                            $category = $request->get('category');
                                            $jenis = $request->get('jenis');
        // $fy = $request->get('fy'); // sampe sini ya
                                            $date_now = date('Y-m-d', strtotime(carbon::now()));
                                            $month_now = date('m', strtotime(carbon::now()));

                                            $bulan_sekarang = date('Y-m');

                                            $grafik = db::connection('ympimis_2')->select('select waste_masters.waste_category, COALESCE(wwt.jumlah, "") as jml_wwt, COALESCE(disposal.jumlah, "") as jml_disposal from waste_masters
                                             left join
                                             (
                                             select waste_category, count(category) as jumlah from waste_details where date_in >= "' . $dari_tanggal . '" and date_in <= "' . $sampai_tanggal . '" group by waste_category
                                             ) as wwt on waste_masters.waste_category = wwt.waste_category
                                             left join
                                             (
                                             select waste_category, count(category) as jumlah from waste_logs where date_disposal >= "' . $dari_tanggal . '" and date_disposal <= "' . $sampai_tanggal . '" group by waste_category
                                         ) as disposal on waste_masters.waste_category = disposal.waste_category');

                                            $weekly_calendar = db::select('SELECT DISTINCT
                                             date_format( week_date, "%Y-%m" ) as bulan
                                             FROM
                                             weekly_calendars
                                             WHERE
                                             fiscal_year = "FY199"
                                             ORDER BY
                                             id ASC');

                                            $series_disposal = db::select('SELECT DISTINCT
                                             monthname( week_date ) AS bulan,
                                             YEAR ( week_date ) AS tahun
                                             FROM
                                             weekly_calendars
                                             WHERE
			fiscal_year = "FY199" -- inputan FY
			ORDER BY id ASC');

                                            $data_disposal = db::connection('ympimis_2')->select('SELECT
                                             count(category) as jumlah,
                                             monthname( date_disposal ) AS bulan2,
                                             YEAR ( date_disposal ) AS tahun
                                             FROM
                                             waste_logs
                                             GROUP BY
                                             bulan2,
                                             tahun
                                             ORDER BY
                                             tahun,
                                             MONTH ( date_disposal ) ASC');

                                            $masa_simpan = db::connection('ympimis_2')->select('select DATEDIFF("' . $date_now . '", date_in) as hari from waste_details where category = "IN" ORDER BY DATEDIFF("' . $date_now . '", date_in) DESC LIMIT 1');

                                            $wwt = db::connection('ympimis_2')->select('select count(category) as wwt from waste_details where kemasan = "Jumbo Bag" and category = "IN" and date_in >= "' . $dari_tanggal . '" and date_in <= "' . $sampai_tanggal . '"');
                                            $wwt_pail = db::connection('ympimis_2')->select('select count(category) as wwt from waste_details where kemasan = "Pail" and category = "IN" and date_in >= "' . $dari_tanggal . '" and date_in <= "' . $sampai_tanggal . '"');
                                            $wwt_drum = db::connection('ympimis_2')->select('select count(category) as wwt from waste_details where kemasan = "Drum" and category = "IN" and date_in >= "' . $dari_tanggal . '" and date_in <= "' . $sampai_tanggal . '"');

                                            $disposal = db::connection('ympimis_2')->select('SELECT
                                             count( category ) AS disposal
                                             FROM
                                             waste_logs
                                             WHERE
                                             kemasan = "Jumbo Bag"
                                             AND date_format(date_disposal, "%Y-%m") >= "' . $weekly_calendar[0]->bulan . '"
                                             AND date_format(date_disposal, "%Y-%m") <= "' . $weekly_calendar[11]->bulan . '"');

                                            $disposal_pail = db::connection('ympimis_2')->select('SELECT
                                             count( category ) AS disposal
                                             FROM
                                             waste_logs
                                             WHERE
                                             kemasan = "Pail"
                                             AND date_format(date_disposal, "%Y-%m") >= "' . $weekly_calendar[0]->bulan . '"
                                             AND date_format(date_disposal, "%Y-%m") <= "' . $weekly_calendar[11]->bulan . '"');

                                            $disposal_drum = db::connection('ympimis_2')->select('SELECT
                                             count( category ) AS disposal
                                             FROM
                                             waste_logs
                                             WHERE
                                             kemasan = "Drum"
                                             AND date_format(date_disposal, "%Y-%m") >= "' . $weekly_calendar[0]->bulan . '"
                                             AND date_format(date_disposal, "%Y-%m") <= "' . $weekly_calendar[11]->bulan . '"');

                                            $hasil = '';

                                            if ($masa_simpan == null) {
                                                $hasil = '0';
                                            } else {
                                                $hasil = $masa_simpan[0]->hari;

            //     if ($hasil >= '60') {
            //         $isi_mails = db::connection('ympimis_2')->select('select slip, GROUP_CONCAT(waste_category) as jenis, GROUP_CONCAT(quantity) as quantity, date_in, DATEDIFF("'.$date_now.'", date_in) as jml from waste_details where DATEDIFF("'.$date_now.'", date_in) = "'.$hasil.'"');

            //         $data = [
            //             'isi_mails' => $isi_mails
            //         ];

            //         Mail::to(['whica.parama@music.yamaha.com', 'priyo.jatmiko@music.yamaha.com'])
            //         ->bcc(['lukmannul.arif@music.yamaha.com'])
            //         ->send(new SendEmail($data, 'resume_notif_wwt'));
            //     }
                                            }

                                            $response = array(
                                                'status' => true,
                                                'grafik' => $grafik,
                                                'wwt' => $wwt,
                                                'wwt_pail' => $wwt_pail,
                                                'wwt_drum' => $wwt_drum,
                                                'disposal' => $disposal,
                                                'disposal_pail' => $disposal_pail,
                                                'disposal_drum' => $disposal_drum,
                                                'masa_simpan' => $masa_simpan,
                                                'hasil' => $hasil,
                                                'series_disposal' => $series_disposal,
                                                'data_disposal' => $data_disposal,
                                            );
                                            return Response::json($response);
                                        }

                                        public function NotifikasiEmail(Request $request)
                                        {
                                            $hasil = $request->get('hasil');
                                            $date_now = date('Y-m-d', strtotime(carbon::now()));
                                            $waktu = date('H:i');

                                            if ($waktu == '07:00' || $waktu == '10:36' || $waktu == '15:00') {
                                                $isi_mails = db::connection('ympimis_2')->select('select slip, GROUP_CONCAT(waste_category) as jenis, GROUP_CONCAT(quantity) as quantity, date_in, DATEDIFF("' . $date_now . '", date_in) as jml from waste_details where DATEDIFF("' . $date_now . '", date_in) = "' . $hasil . '"');

                                                $data = [
                                                    'isi_mails' => $isi_mails,
                                                ];

            // Mail::to(['whica.parama@music.yamaha.com', 'priyo.jatmiko@music.yamaha.com'])
                                                Mail::to(['lukmannul.arif@music.yamaha.com'])
                // ->bcc(['lukmannul.arif@music.yamaha.com'])
                                                ->send(new SendEmail($data, 'resume_notif_wwt'));
                                            }
                                        }

                                        public function FetchDetailMonitoring(Request $request)
                                        {
                                            $dari_tanggal = $request->get('dari_tanggal');
                                            $sampai_tanggal = $request->get('sampai_tanggal');
                                            $category = $request->get('category');
                                            $jenis = $request->get('jenis');
                                            $date_now = date('Y-m-d', strtotime(carbon::now()));
                                            $month_now = date('m', strtotime(carbon::now()));

                                            if (($dari_tanggal && $sampai_tanggal) == null) {
                                                if ($jenis == 'WWT') {
                                                    $detail_modal = db::connection('ympimis_2')->select('select slip, quantity, date_in, waste_category, DATEDIFF("' . $date_now . '", date_in) as jml from waste_details where waste_category = "' . $category . '" and DATE_FORMAT(date_in,"%m") = "' . $month_now . '"');
                                                } else {
                                                    $detail_modal = db::connection('ympimis_2')->select('select slip, quantity, date_in, date_disposal, waste_category, DATEDIFF(date_disposal, date_in) as jml from waste_logs where waste_category = "' . $category . '" and DATE_FORMAT(date_disposal,"%m") = "' . $month_now . '"');
                                                }
                                            } else {
                                                if ($jenis == 'WWT') {
                                                    $detail_modal = db::connection('ympimis_2')->select('select slip, quantity, date_in, waste_category, DATEDIFF("' . $date_now . '", date_in) as jml from waste_details where date_in >= "' . $dari_tanggal . '" and date_in <= "' . $sampai_tanggal . '" and waste_category = "' . $category . '"');
                                                } else {
                                                    $detail_modal = db::connection('ympimis_2')->select('select slip, quantity, date_in, date_disposal, waste_category, DATEDIFF(date_disposal, date_in) as jml from waste_logs where date_disposal >= "' . $dari_tanggal . '" and date_disposal <= "' . $sampai_tanggal . '" and waste_category = "' . $category . '"');
                                                }
                                            }

                                            $resume_limbah = db::connection('ympimis_2')->select('select * from waste_details where category = "IN"');

                                            $response = array(
                                                'status' => true,
                                                'detail_modal' => $detail_modal,
                                                'resume_limbah' => $resume_limbah,
                                            );
                                            return Response::json($response);
                                        }

                                        public function FetchAllMonitoring(Request $request)
                                        {
                                            $category = $request->get('category');
                                            $date_now = date('Y-m-d', strtotime(carbon::now()));
                                            $terbanyak = $request->get('terbanyak');
                                            $dari_tanggal = $request->get('dari_tanggal');
                                            $sampai_tanggal = $request->get('sampai_tanggal');

                                            $weekly_calendar = db::select('SELECT DISTINCT
                                             date_format( week_date, "%Y-%m" ) as bulan
                                             FROM
                                             weekly_calendars
                                             WHERE
                                             fiscal_year = "FY199"
                                             ORDER BY
                                             id ASC');

                                            $detail_modal = '';

                                            if ($category == 'modal_wwt') {
                                                $detail_modal = db::connection('ympimis_2')->select('select slip, quantity, date_in, waste_category, DATEDIFF("' . $date_now . '", date_in) as jml from waste_details where kemasan = "Jumbo Bag" and date_in >= "' . $dari_tanggal . '" and date_in <= "' . $sampai_tanggal . '"');
                                            } else if ($category == 'modal_wwt_pail') {
                                                $detail_modal = db::connection('ympimis_2')->select('select slip, quantity, date_in, waste_category, DATEDIFF("' . $date_now . '", date_in) as jml from waste_details where kemasan = "Pail" and date_in >= "' . $dari_tanggal . '" and date_in <= "' . $sampai_tanggal . '"');
                                            } else if ($category == 'modal_wwt_drum') {
                                                $detail_modal = db::connection('ympimis_2')->select('select slip, quantity, date_in, waste_category, DATEDIFF("' . $date_now . '", date_in) as jml from waste_details where kemasan = "Drum" and date_in >= "' . $dari_tanggal . '" and date_in <= "' . $sampai_tanggal . '"');
                                            } else if ($category == 'modal_disposal') {
                                                $detail_modal = db::connection('ympimis_2')->select('select slip, quantity, date_in, date_disposal, waste_category, DATEDIFF(date_disposal, date_in) as jml from waste_logs where kemasan = "Jumbo Bag" and date_format(date_disposal, "%Y-%m") >= "' . $weekly_calendar[0]->bulan . '" and date_format(date_disposal, "%Y-%m") <= "' . $weekly_calendar[11]->bulan . '"');
                                            } else if ($category == 'modal_disposal_pail') {
                                                $detail_modal = db::connection('ympimis_2')->select('select slip, quantity, date_in, date_disposal, waste_category, DATEDIFF(date_disposal, date_in) as jml from waste_logs where kemasan = "Pail" and date_format(date_disposal, "%Y-%m") >= "' . $weekly_calendar[0]->bulan . '" and date_format(date_disposal, "%Y-%m") <= "' . $weekly_calendar[11]->bulan . '"');
                                            } else if ($category == 'modal_disposal_drum') {
                                                $detail_modal = db::connection('ympimis_2')->select('select slip, quantity, date_in, date_disposal, waste_category, DATEDIFF(date_disposal, date_in) as jml from waste_logs where kemasan = "Drum" and date_format(date_disposal, "%Y-%m") >= "' . $weekly_calendar[0]->bulan . '" and date_format(date_disposal, "%Y-%m") <= "' . $weekly_calendar[11]->bulan . '"');
                                            }

                                            $masa_simpan = db::connection('ympimis_2')->select('select slip, quantity, date_in, waste_category, DATEDIFF("' . $date_now . '", date_in) as jml from waste_details where DATEDIFF("' . $date_now . '", date_in) = "' . $terbanyak . '"');

                                            $response = array(
                                                'status' => true,
                                                'detail_modal' => $detail_modal,
                                                'masa_simpan' => $masa_simpan,
                                            );
                                            return Response::json($response);
                                        }

                                        public function inputTempHumWH(Request $request)
                                        {
                                            try {

                                                $location = "";

                                                if ($request->get('device') == 'wh_atas') {
                                                    $location = "warehouse lt2";
                                                } else if ($request->get('device') == 'wh_bawah') {
                                                    $location = "warehouse lt1";
                                                } else if ($request->get('device') == 'seasoning') {
                                                    $location = "seasoning cl";
                                                }

                                                if ($request->get('temp') != "" || $request->get('temp') != null) {
                                                    DB::table('temperature_room_logs')->insert([
                                                        'location' => $location,
                                                        'remark' => 'temperature',
                                                        'value' => (int) $request->get('temp'),
                                                        'created_by' => '1',
                                                        'created_at' => date('Y-m-d H:i:s'),
                                                        'updated_at' => date('Y-m-d H:i:s'),
                                                    ]);
                                                }
                                                if ($request->get('hum') != "" || $request->get('hum') != null) {
                                                    if ($location == "seasoning cl") {
                                                        DB::table('temperature_room_logs')->insert([
                                                            'location' => $location,
                                                            'remark' => 'humidity',
                                                            'value' => (int) $request->get('hum') - 3,
                                                            'created_by' => '1',
                                                            'created_at' => date('Y-m-d H:i:s'),
                                                            'updated_at' => date('Y-m-d H:i:s'),
                                                        ]);
                                                    } else {
                                                        DB::table('temperature_room_logs')->insert([
                                                            'location' => $location,
                                                            'remark' => 'humidity',
                                                            'value' => (int) $request->get('hum'),
                                                            'created_by' => '1',
                                                            'created_at' => date('Y-m-d H:i:s'),
                                                            'updated_at' => date('Y-m-d H:i:s'),
                                                        ]);
                                                    }
                                                }

                                                $response = array(
                                                    'status' => true,
                                                    'message' => 'Input Berhasil',
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

                                        public function inputMachinePower(Request $request)
                                        {
                                            DB::table('sensor_datas')->insert([
                                                'category' => 'Panel',
                                                'data_time' => date('Y-m-d H:i:s'),
                                                'remark' => $request->get('status'),
                                                'unit' => $request->get('mesin'),
                                                'created_by' => '1',
                                                'created_at' => date('Y-m-d H:i:s'),
                                                'updated_at' => date('Y-m-d H:i:s'),
                                            ]);

                                            if ($request->get('status') == 'OFF') {
            // $message = urlencode(str_replace('_', ' ', $request->get('mesin')) . " OFF. Please Check!");

            // $curl = curl_init();
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://app.whatspie.com/api/messages',
            // CURLOPT_SSL_VERIFYHOST => FALSE,
            //     CURLOPT_SSL_VERIFYPEER => FALSE,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => 'receiver=6281132210008&device=6281130561777&message=' . $message . '&type=chat',
            //     CURLOPT_HTTPHEADER => array(
            //         'Accept: application/json',
            //         'Content-Type: application/x-www-form-urlencoded',
            //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
            //     ),
            // ));
            // curl_exec($curl);

            // $curl = curl_init();
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://app.whatspie.com/api/messages',
            // CURLOPT_SSL_VERIFYHOST => FALSE,
            //     CURLOPT_SSL_VERIFYPEER => FALSE,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => 'receiver=6285336304044&device=6281130561777&message=' . $message . '&type=chat',
            //     CURLOPT_HTTPHEADER => array(
            //         'Accept: application/json',
            //         'Content-Type: application/x-www-form-urlencoded',
            //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
            //     ),
            // ));
            // curl_exec($curl);

            // $curl = curl_init();
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://app.whatspie.com/api/messages',
            // CURLOPT_SSL_VERIFYHOST => FALSE,
            //     CURLOPT_SSL_VERIFYPEER => FALSE,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => 'receiver=62811375232&device=6281130561777&message=' . $message . '&type=chat',
            //     CURLOPT_HTTPHEADER => array(
            //         'Accept: application/json',
            //         'Content-Type: application/x-www-form-urlencoded',
            //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
            //     ),
            // ));
            // curl_exec($curl);

                                                if ($request->get('mesin') == 'Domestic_Pump') {
                                                    DB::table('sensor_data_logs')
                                                    ->where('item_name', '=', 'Domestic_Pump')
                                                    ->where('category', '=', 'Volume')
                                                    ->whereNull('finish_date')
                                                    ->update(['finish_date' => date('Y-m-d H:i:s'), 'status' => 'OFF']);
                                                }
                                            }

                                            if ($request->get('status') == 'ON') {
            // $message = urlencode(str_replace('_', ' ', $request->get('mesin')) . " ON.");

            // $curl = curl_init();
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://app.whatspie.com/api/messages',
            // CURLOPT_SSL_VERIFYHOST => FALSE,
            //     CURLOPT_SSL_VERIFYPEER => FALSE,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => 'receiver=6281132210008&device=6281130561777&message=' . $message . '&type=chat',
            //     CURLOPT_HTTPHEADER => array(
            //         'Accept: application/json',
            //         'Content-Type: application/x-www-form-urlencoded',
            //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
            //     ),
            // ));
            // curl_exec($curl);

            // $curl = curl_init();
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://app.whatspie.com/api/messages',
            // CURLOPT_SSL_VERIFYHOST => FALSE,
            //     CURLOPT_SSL_VERIFYPEER => FALSE,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => 'receiver=6285336304044&device=6281130561777&message=' . $message . '&type=chat',
            //     CURLOPT_HTTPHEADER => array(
            //         'Accept: application/json',
            //         'Content-Type: application/x-www-form-urlencoded',
            //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
            //     ),
            // ));
            // curl_exec($curl);

            // $curl = curl_init();
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://app.whatspie.com/api/messages',
            // CURLOPT_SSL_VERIFYHOST => FALSE,
            //     CURLOPT_SSL_VERIFYPEER => FALSE,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => 'receiver=62811375232&device=6281130561777&message=' . $message . '&type=chat',
            //     CURLOPT_HTTPHEADER => array(
            //         'Accept: application/json',
            //         'Content-Type: application/x-www-form-urlencoded',
            //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
            //     ),
            // ));
            // curl_exec($curl);

                                                if ($request->get('mesin') == 'Domestic_Pump') {
                                                    DB::table('sensor_data_logs')->insert([
                                                        'item_name' => 'Domestic_Pump',
                                                        'start_date' => date('Y-m-d H:i:s'),
                                                        'category' => 'Volume',
                                                        'status' => 'ON',
                                                        'created_by' => '1',
                                                        'created_at' => date('Y-m-d H:i:s'),
                                                        'updated_at' => date('Y-m-d H:i:s'),
                                                    ]);
                                                }
                                            }
                                        }

                                        public function indexPlannedResume()
                                        {
                                            $title = 'Planned Maintenance';
                                            $title_jp = '予定保全';

                                            return view('maintenance.planned.maintenance_plan_resume', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                                'page' => 'Planned Maintenance Monitoring',
                                            )
                                        );
                                        }

                                        public function inputElectricityTarget(Request $request)
                                        {

                                            $data = $request->get('update');

                                            try {

                                                for ($i = 0; $i < count($data); $i++) {
                                                    $update = ElectricityTarget::updateOrCreate(
                                                        [
                                                            'month' => $data[$i]['month'],
                                                        ],
                                                        [
                                                            'daily_target' => $data[$i]['daily'],
                                                            'monthly_target' => $data[$i]['monthly'],
                                                            'yearly_target' => $data[$i]['yearly'],
                                                            'created_by' => Auth::id(),
                                                            'updated_at' => Carbon::now(),
                                                        ]
                                                    );
                                                }

                                                $response = array(
                                                    'status' => true,
                                                );
                                                return Response::json($response);

                                            } catch (Exception $e) {
                                                $response = array(
                                                    'status' => true,
                                                    'message' => $e->getMessage(),
                                                );
                                                return Response::json($response);
                                            }
                                        }

                                        public function inputElectricityConsumption(Request $request)
                                        {

                                            $date = $request->get('date');

                                            $lbp1 = $request->get('lbp1');
                                            $lbp2 = $request->get('lbp2');
                                            $bp = $request->get('bp');
                                            $kvarh = $request->get('kvarh');

                                            $outgoing_i = $request->get('outgoing_i');
                                            $outgoing_ii = $request->get('outgoing_ii');
                                            $outgoing_iii = $request->get('outgoing_iii');
                                            $outgoing_iv = $request->get('outgoing_iv');

                                            $before = ElectricityConsumption::where('date', '<', $date)->orderBy('date', 'DESC')->first();
                                            if ($lbp1 <= $before->lbp1) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => 'nilai lbP lebih kecil dari pada nilai lbP sebelumnya',
                                                );
                                                return Response::json($response);
                                            }

        // if($lbp2 <= $before->lbp2){
        //     $response = array(
        //         'status' => false,
        //         'message' => 'nilai lbP2 lebih kecil dari pada nilai lbP2 sebelumnya'
        //     );
        //     return Response::json($response);
        // }

                                            if ($bp <= $before->bp) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => 'nilai bP lebih kecil dari pada nilai bP sebelumnya',
                                                );
                                                return Response::json($response);
                                            }

                                            if ($kvarh <= $before->kvarh) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => 'nilai KVARH lebih kecil dari pada nilai KVARH sebelumnya',
                                                );
                                                return Response::json($response);
                                            }

                                            if ($outgoing_i <= $before->outgoing_i) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => 'nilai Outgoing I lebih kecil dari pada nilai Outgoing I sebelumnya',
                                                );
                                                return Response::json($response);
                                            }

                                            if ($outgoing_ii <= $before->outgoing_ii) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => 'nilai Outgoing II lebih kecil dari pada nilai Outgoing II sebelumnya',
                                                );
                                                return Response::json($response);
                                            }

                                            if ($outgoing_iii <= $before->outgoing_iii) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => 'nilai Outgoing III lebih kecil dari pada nilai Outgoing III sebelumnya',
                                                );
                                                return Response::json($response);
                                            }

                                            if ($outgoing_iv <= $before->outgoing_iv) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => 'nilai Outgoing IV lebih kecil dari pada nilai Outgoing IV sebelumnya',
                                                );
                                                return Response::json($response);
                                            }

                                            DB::beginTransaction();
                                            try {
                                                $insert = new ElectricityConsumption([
                                                    'date' => $date,
                                                    'lbp1' => $lbp1,
                                                    'lbp2' => $lbp2,
                                                    'bp' => $bp,
                                                    'kvarh' => $kvarh,
                                                    'outgoing_i' => $outgoing_i,
                                                    'outgoing_ii' => $outgoing_ii,
                                                    'outgoing_iii' => $outgoing_iii,
                                                    'outgoing_iv' => $outgoing_iv,
                                                    'created_by' => Auth::id(),
                                                ]);
                                                $insert->save();

                                                $now = ElectricityConsumption::where('date', $date)->first();

                                                $before->lwbp1 = ($now->lbp1 - $before->lbp1) * 4000;
            // $before->lwbp2 = ($now->lbp2 - $before->lbp2) * 4000;
                                                $before->wbp = ($now->bp - $before->bp) * 4000;
                                                $before->consumption_kvarh = ($now->kvarh - $before->kvarh) * 4000;

                                                $before->consumption_outgoing_i = ($now->outgoing_i - $before->outgoing_i) * 1818;
                                                $before->consumption_outgoing_ii = ($now->outgoing_ii - $before->outgoing_ii) * 1818;
                                                $before->consumption_outgoing_iii = ($now->outgoing_iii - $before->outgoing_iii) * 1091;
                                                $before->consumption_outgoing_iv = ($now->outgoing_iv - $before->outgoing_iv);

                                                $before->save();

                                                DB::commit();
                                                $response = array(
                                                    'status' => true,
                                                    'message' => 'Konsumsi listrik berhasil disimpan',
                                                );
                                                return Response::json($response);

                                            } catch (Exception $e) {
                                                DB::rollback();
                                                $response = array(
                                                    'status' => false,
                                                    'message' => $e->getMessage(),
                                                );
                                                return Response::json($response);
                                            }
                                        }

    // ---------------  WWT DAILY REPORT ----------------------

                                        public function indexWWTDailyReport()
                                        {
                                            $title = 'WWT Daily Report';
                                            $title_jp = '??';

                                            $chemical_injection = [
                                                ['code' => 'T-701', 'tank' => 'IK-110', 'concentration' => 'neat', 'capacity' => '200'],
                                                ['code' => 'T-741', 'tank' => 'K-800', 'concentration' => 'neat', 'capacity' => '500'],
                                                ['code' => 'T-761', 'tank' => 'Polymer', 'concentration' => '0.1%wt', 'capacity' => '1000'],
                                                ['code' => 'T-731', 'tank' => 'NaOH', 'concentration' => '48%vol.', 'capacity' => '2000'],
                                                ['code' => 'T-851', 'tank' => 'N-195', 'concentration' => 'neat', 'capacity' => '200'],
                                                ['code' => 'T-831', 'tank' => 'FeCl3', 'concentration' => '15%vol.', 'capacity' => '2000'],
                                                ['code' => 'T-821', 'tank' => 'H2SO4', 'concentration' => '30%vol.', 'capacity' => '8000'],
                                                ['code' => 'T-811', 'tank' => 'Acid', 'concentration' => 'N/A', 'capacity' => '3000'],
                                                ['code' => 'T-721', 'tank' => 'Ca(OH)2', 'concentration' => '10%wt.', 'capacity' => '3000'],
                                                ['code' => 'T-711', 'tank' => 'NaClO', 'concentration' => '12%vol.', 'capacity' => '2000'],
                                                ['code' => 'T-801', 'tank' => 'NaHSO3', 'concentration' => '10%wt.', 'capacity' => '5000'],
                                                ['code' => 'T-841', 'tank' => 'S-115', 'concentration' => 'neat', 'capacity' => '200'],
                                                ['code' => 'T-751', 'tank' => 'Bentonite', 'concentration' => '10%wt.', 'capacity' => '500'],
                                            ];

                                            $quantity_analysis = [
                                                ['parameter' => 'pH', 'unit' => '-', 'fill_in' => 'Crw,CNW', 'std' => '6-9'],
                                                ['parameter' => 'TDS', 'unit' => 'ppm', 'fill_in' => 'Crw,CNW', 'std' => '<= 2000'],
                                                ['parameter' => 'CNT', 'unit' => 'ppm', 'fill_in' => 'CNW', 'std' => '<= 1'],
                                                ['parameter' => 'Cr6+', 'unit' => 'ppm', 'fill_in' => 'Crw', 'std' => '<= 2'],
                                                ['parameter' => 'CrT', 'unit' => 'ppm', 'fill_in' => '', 'std' => '<= 2'],
                                                ['parameter' => 'Cu', 'unit' => 'ppm', 'fill_in' => 'Crw,CNW', 'std' => '<= 5'],
                                                ['parameter' => 'Zn', 'unit' => 'ppm', 'fill_in' => 'Crw,CNW', 'std' => '<= 5'],
                                                ['parameter' => 'Ni', 'unit' => 'ppm', 'fill_in' => 'Crw,CNW', 'std' => '< 2'],
                                            ];

                                            return view('maintenance.wwt_daily_index', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                                'chemical' => $chemical_injection,
                                                'analysis' => $quantity_analysis,
                                            )
                                        )->with('page', 'Daily WWT')->with('head2', 'WWT')->with('head', 'Maintenance');
                                        }

                                        public function fetchWWTDailyForm(Request $request)
                                        {
                                            $form = WwtDailyMaster::where('form_category', $request->get('category'))
                                            ->get();

                                            $response = array(
                                                'status' => true,
                                                'form' => $form,
                                            );
                                            return Response::json($response);
                                        }

                                        public function inputSuhuSensor(Request $request)
                                        {
                                            $temp = (float) $request->get('wtemp');
                                            if ($temp < 21) {
                                                DB::table('sensor_datas')->insert([
                                                    'category' => 'Temperature',
                                                    'data_time' => date('Y-m-d H:i:s'),
                                                    'sensor_value' => $temp,
                                                    'unit' => 'Temperature Tandon Chiller',
                                                    'created_by' => '1',
                                                    'created_at' => date('Y-m-d H:i:s'),
                                                    'updated_at' => date('Y-m-d H:i:s'),
                                                ]);
                                            }

                                            $response = array(
                                                'status' => true,
                                                'data' => $request->get('wtemp'),
                                            );
                                            return Response::json($response);
                                        }

                                        public function indexSuhuChiller()
                                        {
                                            return view('Maintenance.iot.temperature');
                                        }

                                        public function indexPump()
                                        {
                                            return view('Maintenance.iot.pump');
                                        }

                                        public function fetchSuhuChiller(Request $request)
                                        {
                                            $datas = db::select('SELECT DATE_FORMAT(data_time,"%H:%i:%s") as data_time, ROUND(sensor_value,1) as value_sensor from sensor_datas where category = "Temperature" and DATE_FORMAT(data_time,"%Y-%m-%d") = "' . $request->get('date') . '" and sensor_value < 100 order by data_time asc');

                                            $mon = date('Y-m', strtotime($request->get('date')));

                                            $datas_new = db::select('SELECT DATE_FORMAT(data_time,"%Y-%m-%d") as hari, min(sensor_value) as minus, max(sensor_value) as maksimal, round(avg(sensor_value),2) as average from sensor_datas where category = "Temperature" and DATE_FORMAT(data_time,"%Y-%m") = "' . $mon . '" and sensor_value < 100 GROUP BY DATE_FORMAT(data_time,"%Y-%m-%d")');

                                            $response = array(
                                                'status' => true,
                                                'datas' => $datas,
                                                'data_new' => $datas_new,
                                            );
                                            return Response::json($response);
                                        }

                                        public function IndexLogsWWT(Request $request)
                                        {
                                            $title = 'WWT - Waste Control';
                                            $title_jp = '??';
                                            $jenis = db::connection('ympimis_2')->table('waste_masters')->select('waste_category', 'unit_weight', 'remark')->orderBy('waste_category', 'asc')->get();
                                            $vendor = db::connection('ympimis_2')->table('waste_vendors')->select('vendor')->orderBy('vendor', 'asc')->get();

                                            return view('maintenance.wwt_logs_view', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                                'jenis' => $jenis,
                                                'vendor' => $vendor,
                                            )
                                        )->with('page', 'WWT - Waste Control');
                                        }

                                        public function FetchLogsWWT(Request $request)
                                        {
                                            $resumes = db::connection('ympimis_2')->select('select DISTINCT slip_disposal, date_disposal, dokumen_teknis, dokumen_manifest from waste_logs');

                                            $response = array(
                                                'status' => true,
                                                'resumes' => $resumes,
                                            );
                                            return Response::json($response);
                                        }

                                        public function UploadDokumenTeknis(Request $request)
                                        {
                                            try {
                                                $slip = $request->get('slip_id');
                                                $category = $request->get('category_upload');

                                                if ($category == 'Dokumen Teknis') {
                                                    $file = $request->file('dokumen_teknis');

                                                    $isi = $file->getClientOriginalName();
                                                    $extension = pathinfo($isi, PATHINFO_EXTENSION);

                                                    $nama_file = $slip . '.' . $extension;
                                                    $file->move('data_file/wwt/teknis', $nama_file);

                                                    $upload = db::connection('ympimis_2')->table('waste_logs')->where('waste_logs.slip_disposal', '=', $slip)->update([
                                                        'dokumen_teknis' => $nama_file
                                                    ]);
                                                } else {
                                                    $file = $request->file('dokumen_teknis');

                                                    $isi = $file->getClientOriginalName();
                                                    $extension = pathinfo($isi, PATHINFO_EXTENSION);

                                                    $nama_file = $slip . '.' . $extension;
                                                    $file->move('data_file/wwt/manifest', $nama_file);

                                                    $upload = db::connection('ympimis_2')->table('waste_logs')->where('waste_logs.slip_disposal', '=', $slip)->update([
                                                        'dokumen_manifest' => $nama_file
                                                    ]);
                                                }

                                                return redirect('/index/logs/wwt')->with('status', 'Berhasil Di Upload')->with('page', 'Success');

                                            } catch (Exception $e) {
                                                $response = array(
                                                    'status' => false,
                                                    'message' => $e->getMessage(),
                                                );
                                                return Response::json($response);
                                            }
                                        }

                                        public function SelectSlip(Request $request)
                                        {
                                            $slip = $request->get('slip');

                                            $data_slip = db::connection('ympimis_2')->select('select slip from waste_details where slip = "' . $slip . '" and quantity is null');

                                            if (count($data_slip) == 0) {
                                                $response = array(
                                                    'status' => false,
                                                );
                                                return Response::json($response);
                                            } else {
                                                $response = array(
                                                    'status' => true,
                                                );
                                                return Response::json($response);
                                            }
                                        }

                                        public function LogBookIndex(Request $request, $slip)
                                        {
                                            $title = 'WWT - Waste Control';
                                            $title_jp = '??';

                                            return view('maintenance.wwt_logbook', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                                'slip' => $slip,
                                            )
                                        )->with('page', 'WWT - Waste Control');
                                        }

                                        public function FetchLogBook(Request $request)
                                        {
                                            $slip = $request->get('slip');
                                            $resumes = db::connection('ympimis_2')->select('
                                             SELECT
                                             slip,
                                             dari_lokasi,
                                             waste_category,
                                             tanggal_logbook,
                                             pic
                                             FROM
                                             waste_logbooks
                                             WHERE
                                             slip = "' . $slip . '"');

                                            $response = array(
                                                'status' => true,
                                                'resumes' => $resumes,
                                            );
                                            return Response::json($response);
                                        }

    // ---------- PATROL BUILDING --------

                                        public function indexPatrol()
                                        {
                                            $title = 'Patrol Kondisi Bangunan';
                                            $title_jp = '';

                                            $emp = EmployeeSync::where('employee_id', Auth::user()->username)
                                            ->select('employee_id', 'name', 'position', 'department')->first();

                                            $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
                                             where end_date is null and employee_id in ('PI9906003', 'PI2102025', 'PI0302001')");

                                            return view('maintenance.tpm.index_patrol', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                                'employee' => $emp,
                                                'auditee' => $auditee,
                                                'location' => $this->location_patrol,
                                            )
                                        )->with('page', 'Building Patrol');
                                        }

                                        public function indexPatrolBuilding()
                                        {

                                        }

                                        public function indexTbm($category)
                                        {
                                            $emp_id = strtoupper(Auth::user()->username);
                                            $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);
                                            $title = 'Time Based Maintenance';
                                            $title_jp = '時間ベースの保全';

                                            $fy_all = DB::SELECT("SELECT DISTINCT
                                                ( fiscal_year )
                                                FROM
                                                weekly_calendars");

                                            return view('maintenance.tbm.index', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                                'fy_all' => $fy_all,
                                                'mp_ut' => $category,
                                                'role' => Auth::user()->role_code,
                                            )
                                        )->with('page', 'TBM');
                                        }

                                        public function fetchTbm(Request $request)
                                        {
                                            try {
                                                if ($request->get('fy') != '') {
                                                    $fy = $request->get('fy');
                                                } else {
                                                    $fys = DB::SELECT("SELECT DISTINCT
                                                        ( fiscal_year )
                                                        FROM
                                                        weekly_calendars
                                                        WHERE
                                                        week_date = DATE(
                                                            NOW())");

                                                    $fy = $fys[0]->fiscal_year;
                                                }

                                                $firstlast = DB::SELECT("( SELECT DISTINCT
                                                    (
                                                    DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
                                                    DATE_FORMAT( week_date, '%b %Y' ) AS month_name
                                                    FROM
                                                    weekly_calendars
                                                    WHERE
                                                    fiscal_year = '" . $fy . "'
                                                    ORDER BY
                                                    week_date
                                                    LIMIT 1
                                                    ) UNION ALL
                                                    (
                                                    SELECT DISTINCT
                                                    (
                                                    DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
                                                    DATE_FORMAT( week_date, '%b %Y' ) AS month_name
                                                    FROM
                                                    weekly_calendars
                                                    WHERE
                                                    fiscal_year = '" . $fy . "'
                                                    ORDER BY
                                                    week_date DESC
                                                    LIMIT 1)");

                                                $point_check = DB::connection('ympimis_2')->table('daily_audit_points');
                                                $schedule = DB::connection('ympimis_2')->table('daily_audit_schedules')->select('*', DB::RAW('DATE_FORMAT(schedule_date,"%Y-%m") as schedule_month'));

                                                $where = '';

            // if ($request->get('point_id') != '' && $request->get('point_id') != 'All') {
            //     $point_check = $point_check->where('daily_audit_points.id', $request->get('point_id'));
            //     $schedule = $schedule->where('point_id', $request->get('point_id'));
            //     $where = "AND point_id = '" . $request->get('point_id') . "'";
            // }

                                                $point_check_all = DB::connection('ympimis_2')->table('daily_audit_points');

                                                $wheremonth = "";
                                                $whereresume = "";
                                                $months = date('Y-m');
                                                if ($request->get('category') == 'all') {
                                                    if ($request->get('month') != '') {
                                                        $months = $request->get('month');
                                                    }
                                                    $schedule = $schedule->where('schedule_date', 'like', '%' . $months . '%');
                                                    $wheremonth = "AND week_date like '%" . $months . "%'";
                                                    $whereresume = "WHERE schedule_date like '%" . $months . "%'";
                                                } else {
                                                    $point_check = $point_check->where('category', $request->get('category'));
                                                    $schedule = $schedule->where('category', $request->get('category'));
                                                    $whereresume = "
                                                    WHERE DATE_FORMAT( schedule_date, '%Y-%m' ) >= '" . $firstlast[0]->month . "'
                                                    AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '" . $firstlast[1]->month . "'
                                                    AND `category` = '" . $request->get('category') . "'";
                                                    $point_check_all = $point_check_all->where('category', $request->get('category'));
                                                }

                                                $resume = DB::connection('ympimis_2')->select("SELECT
                                                    point_id,
                                                    location,
                                                    point_check,
                                                    scan_index,
                                                    specification,
                                                    sum( CASE WHEN schedule_status = 'Sudah Dikerjakan' THEN 1 ELSE 0 END ) AS sudah,
                                                    sum( CASE WHEN schedule_status = 'Belum Dikerjakan' THEN 1 ELSE 0 END ) AS belum
                                                    FROM
                                                    `daily_audit_schedules`
                                                    " . $whereresume . "
                                                    " . $where . "
                                                    AND remark = '" . $request->get('mp_ut') . "'
                                                    GROUP BY
                                                    point_id,
                                                    location,
                                                    point_check,
                                                    scan_index,
                                                    specification
                                                    ORDER BY
                                                    priority ASC");

                                                $schedule = $schedule->where('remark', $request->get('mp_ut'))->orderBy('daily_audit_schedules.created_at', 'desc')->get();
                                                $point_check = $point_check->where('remark', $request->get('mp_ut'))->orderby('daily_audit_points.priority', 'asc')->get();
                                                $point_check_all = $point_check_all->where('remark', $request->get('mp_ut'))->get();

                                                $month = DB::SELECT("SELECT
                                                    DISTINCT(DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
                                                    DATE_FORMAT( week_date, '%b %Y' ) AS `month_name`
                                                    FROM
                                                    weekly_calendars
                                                    WHERE
                                                    fiscal_year = '" . $fy . "'
                                                    " . $wheremonth . "
                                                    GROUP BY
                                                    DATE_FORMAT( week_date, '%Y-%m' ),
                                                    week_date
                                                    ORDER BY
                                                    `month`");

                                                $monthTitle = date("M-Y", strtotime($months));

                                                $months = date('Y-m');
                                                if ($request->get('month') != '') {
                                                    $months = $request->get('month');
                                                }

                                                $calendar = WeeklyCalendar::select('weekly_calendars.*',DB::RAW("DATE_FORMAT(week_date,'%d') as dates"))->where(DB::RAW("DATE_FORMAT(week_date,'%Y-%m')"),$months)->get();

                                                $first = $calendar[0]->week_date;
                                                $last = $calendar[0]->week_date;
                                                for ($i=0; $i < count($calendar); $i++) { 
                                                    $last = $calendar[$i]->week_date;
                                                }

                                                $whereresume_daily = "
                                                WHERE schedule_date >= '" . $first . "'
                                                AND schedule_date <= '" . $last . "'
                                                AND `category` = '" . $request->get('category') . "'";

                                                $resume_daily = DB::connection('ympimis_2')->select("SELECT
                                                    point_id,
                                                    location,
                                                    point_check,
                                                    scan_index,
                                                    specification,
                                                    sum( CASE WHEN schedule_status = 'Sudah Dikerjakan' THEN 1 ELSE 0 END ) AS sudah,
                                                    sum( CASE WHEN schedule_status = 'Belum Dikerjakan' THEN 1 ELSE 0 END ) AS belum
                                                    FROM
                                                    `daily_audit_schedules`
                                                    " . $whereresume_daily . "
                                                    AND remark = '" . $request->get('mp_ut') . "'
                                                    GROUP BY
                                                    point_id,
                                                    location,
                                                    point_check,
                                                    scan_index,
                                                    specification
                                                    ORDER BY
                                                    priority ASC");

                                                $schedule_daily = DB::connection('ympimis_2')->table('daily_audit_schedules')->select('*')->where('category', $request->get('category'))->where('schedule_date','>=',$first)->where('schedule_date','<=',$last)->where('remark', $request->get('mp_ut'))->orderBy('daily_audit_schedules.created_at', 'desc')->get();

                                                $response = array(
                                                    'status' => true,
                                                    'schedule' => $schedule,
                                                    'point_check' => $point_check,
                                                    'point_check_all' => $point_check_all,
                                                    'month' => $month,
                                                    'calendar' => $calendar,
                                                    'resume_daily' => $resume_daily,
                                                    'schedule_daily' => $schedule_daily,
                                                    'monthTitle' => $monthTitle,
                                                    'fy' => $fy,
                                                    'resume' => $resume,
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

                                        public function indexTbmPointCheck($category, $mp_ut)
                                        {
                                            $title = 'TBM Point Check ~ ' . strtoupper($category);
                                            $title_jp = '時間ベースの保全点検項目';

                                            return view('maintenance.tbm.point_check', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                                'category' => $category,
                                                'mp_ut' => $mp_ut,
                                            )
                                        )->with('page', 'TBM Point Check');
                                        }

                                        public function fetchTbmPointCheck($category)
                                        {
                                            try {
                                                $point_check = DB::connection('ympimis_2')->table('daily_audit_points')->where('category', $category)->get();
                                                $response = array(
                                                    'status' => true,
                                                    'point_check' => $point_check,
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

                                        public function inputTbmPointCheck($category, Request $request)
                                        {
                                            try {
                                                $location = $request->get('location');
                                                $point_check = $request->get('point_check');
                                                $scan_index = $request->get('scan_index');
                                                $specification = $request->get('specification');
                                                $image_reference = $request->get('image_reference');
                                                $priority = $request->get('priority');
                                                $mp_ut = $request->get('mp_ut');

                                                $schedule_category = null;
                                                if (str_contains($category,'daily')) {
                                                    $schedule_category = 'daily';
                                                }

                                                $input = DB::connection('ympimis_2')->table('daily_audit_points')->insert([
                                                    'category' => $category,
                                                    'location' => $location,
                                                    'point_check' => $point_check,
                                                    'scan_index' => $scan_index,
                                                    'specification' => $specification,
                                                    'image_reference' => $image_reference,
                                                    'priority' => $priority,
                                                    'schedule_category' => $schedule_category,
                                                    'remark' => $mp_ut,
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

                                        public function updateTbmPointCheck($category, Request $request)
                                        {
                                            try {
                                                $id = $request->get('id');
                                                $location = $request->get('location');
                                                $point_check = $request->get('point_check');
                                                $scan_index = $request->get('scan_index');
                                                $specification = $request->get('specification');
                                                $image_reference = $request->get('image_reference');
                                                $priority = $request->get('priority');

                                                $input = DB::connection('ympimis_2')->table('daily_audit_points')->where('id', $id)->update([
                                                    'category' => $category,
                                                    'location' => $location,
                                                    'point_check' => $point_check,
                                                    'scan_index' => $scan_index,
                                                    'specification' => $specification,
                                                    'image_reference' => $image_reference,
                                                    'priority' => $priority,
                                                    'updated_at' => date('Y-m-d H:i:s'),
                                                ]);

                                                $schedule = DB::connection('ympimis_2')->table('daily_audit_schedules')->where('point_id', $id)->get();
                                                if (count($schedule) > 0) {
                                                    $schedule = DB::connection('ympimis_2')->table('daily_audit_schedules')->where('point_id', $id)->update([
                                                        'priority' => $priority,
                                                        'image_reference' => $image_reference,
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

                                        public function deleteTbmPointCheck($category, Request $request)
                                        {
                                            try {
                                                $id = $request->get('id');

                                                $input = DB::connection('ympimis_2')->table('daily_audit_points')->where('id', $id)->delete();

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

                                        public function indexPumpMonitoring()
                                        {
                                            $title = 'Monitoring Domestic Pump Consumption';
                                            $title_jp = '';

                                            return view('maintenance.domestic_pump', array(
                                                'title' => $title,
                                                'title_jp' => $title_jp,
                                            )
                                        )->with('page', 'Domestic Pump');
                                        }

                                        public function fetchPumpMonitoring(Request $request)
                                        {
                                            $mon = '';
                                            if ($request->get('mon') == '') {
                                                $mon = date('Y-m');
                                            } else {
                                                $mon = $request->get('mon');
                                            }

                                            $datas = DB::select("SELECT hari, SUM(diff) as time from
                                                (SELECT hari, ROUND(TIME_TO_SEC(TIMEDIFF(fin_date, start_date)) / 60 / 60, 3) as diff  from
                                                    (SELECT DATE_FORMAT(start_date, '%Y-%m-%d') hari, start_date, finish_date,
                                                        IFNULL(IF(DATE_FORMAT(start_date, '%d') <> DATE_FORMAT(finish_date, '%d'), DATE_FORMAT(start_date, '%Y-%m-%d 23:59:59'), finish_date), now()) as fin_date
                                                        FROM `sensor_data_logs` where DATE_FORMAT(start_date, '%Y-%m') = '" . $mon . "') as masters) all_datas
                                                        group by hari");

                                                    $response = array(
                                                        'status' => true,
                                                        'datas' => $datas,
                                                    );
                                                    return Response::json($response);
                                                }

                                                public function inputTbmSchedule(Request $request)
                                                {
                                                    try {
                                                        $location = $request->get('location');
                                                        $point_check = $request->get('point_check');
                                                        $scan_index = $request->get('scan_index');
                                                        $id = $request->get('id');
                                                        $schedule_date = $request->get('schedule_date');
                                                        $category = $request->get('category');

                                                        $point = DB::connection('ympimis_2')->table('daily_audit_points')->where('id', $id)->first();

                                                        $input = DB::connection('ympimis_2')->table('daily_audit_schedules')->insert([
                                                            'point_id' => $id,
                                                            'category' => $category,
                                                            'location' => $point->location,
                                                            'point_check' => $point->point_check,
                                                            'scan_index' => $point->scan_index,
                                                            'image_reference' => $point->image_reference,
                                                            'specification' => $point->specification,
                                                            'priority' => $point->priority,
                                                            'remark' => $point->remark,
                                                            'schedule_date' => $schedule_date . '-01',
                                                            'schedule_status' => 'Belum Dikerjakan',
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

                                                public function inputTbmDoing(Request $request)
                                                {
                                                    try {
                                                        $filename = "";
                                                        $file_destination = 'data_file/maintenance/tbm';
                                                        $file_destination2 = 'data_file/maintenance/tbm/report';

                                                        if (count($request->file('file')) > 0) {
                                                            $file = $request->file('file');
                                                            $filename = 'evidence_' . date('YmdHisa') . '.' . $request->input('extension');
                                                            $file->move($file_destination, $filename);

                                                            $filename2 = "";
                                                            if (count($request->file('file2')) > 0) {
                                                                $file2 = $request->file('file2');
                                                                $filename2 = 'report_' . date('YmdHisa') . '.' . $request->input('extension');
                                                                $file2->move($file_destination2, $filename2);
                                                            }

                                                            $id = $request->get('id');
                                                            $note = $request->get('note');
                                                            $values = $request->get('values');
                                                            $values2 = $request->get('values2');

                                                            $schedule = DB::connection('ympimis_2')->table('daily_audit_schedules')->where('id', $id)->first();

                                                            $update = DB::connection('ympimis_2')->table('daily_audit_schedules')->where('id', $id)->update([
                                                                'note' => $note,
                                                                'values' => $values,
                                                                'values2' => $values2,
                                                                'evidence' => $filename,
                                                                'report' => $filename2,
                                                                'auditor_id' => strtoupper(Auth::user()->username),
                                                                'auditor_name' => Auth::user()->name,
                                                                'audited_at' => date('Y-m-d H:i:s'),
                                                                'schedule_status' => 'Sudah Dikerjakan',
                                                            ]);

                                                            $point = DB::connection('ympimis_2')->table('daily_audit_points')->where('id', $schedule->point_id)->first();

                                                            if ($point->image_reference != null) {
                                                                if ($point->schedule_category == null) {
                                                                    $new_date = date('Y-m-01', strtotime($schedule->schedule_date . ' +' . $point->image_reference . ' months'));
                                                                }else{
                                                                    $new_date = date('Y-m-d', strtotime($schedule->schedule_date . ' +' . $point->image_reference . ' days'));
                                                                    $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
                                                                    foreach ($weekly_calendars as $key) {
                                                                        if ($key->week_date == $new_date) {
                                                                            if (str_contains($key->remark,'H')) {
                                                                                $new_date = date('Y-m-d', strtotime($new_date . ' + 1 days'));
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                $new_schedule = DB::connection('ympimis_2')->table('daily_audit_schedules')->insert([
                                                                    'point_id' => $schedule->point_id,
                                                                    'category' => $point->category,
                                                                    'location' => $point->location,
                                                                    'point_check' => $point->point_check,
                                                                    'scan_index' => $point->scan_index,
                                                                    'image_reference' => $point->image_reference,
                                                                    'specification' => $point->specification,
                                                                    'remark' => $point->remark,
                                                                    'schedule_date' => $new_date,
                                                                    'priority' => $point->priority,
                                                                    'schedule_status' => 'Belum Dikerjakan',
                                                                    'created_by' => Auth::user()->id,
                                                                    'created_at' => date('Y-m-d H:i:s'),
                                                                    'updated_at' => date('Y-m-d H:i:s'),
                                                                ]);
                                                            }

                                                            $response = array(
                                                                'status' => true,
                                                                'message' => 'Upload Evidence Success',
                                                            );
                                                            return Response::json($response);
                                                        } else {
                                                            $response = array(
                                                                'status' => false,
                                                                'message' => 'Upload File Evidence',
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

                                                public function deleteTbmSchedule(Request $request)
                                                {
                                                    try {
                                                        $id = $request->get('id');
                                                        $delete = DB::connection('ympimis_2')->table('daily_audit_schedules')->where('id', $id)->delete();
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

                                                public function fetchTbmSchedule(Request $request)
                                                {
                                                    try {
                                                        $id = $request->get('id');
                                                        $schedule = DB::connection('ympimis_2')->table('daily_audit_schedules')->where('id', $id)->first();
                                                        $point_check = DB::connection('ympimis_2')->table('daily_audit_points')->where('id', $schedule->point_id)->first();
                                                        $response = array(
                                                            'status' => true,
                                                            'schedule' => $schedule,
                                                            'point_check' => $point_check,
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

                                                public function GetDataChecklist(Request $request)
                                                {
                                                    $periode = $request->get('periode');
                                                    $jenis = $request->get('jenis');
                                                    $vendor = $request->get('vendor');
                                                    $nomor = $request->get('nomor');

                                                    $month_before = date('Y-m', strtotime('-1 month', strtotime($periode)));
                                                    $name_month = date('F', strtotime($month_before));

                                                    $data_before = db::connection('ympimis_2')->table('waste_logs')
                                                    ->where(db::raw("DATE_FORMAT(date_in, '%Y-%m')"), $month_before)
                                                    ->where('waste_category', $jenis)
                                                    ->sum('quantity');

                                                    $sum_before = number_format($data_before, 0, '.', '');

                                                    $vd = '';
                                                    if ($vendor == 'PT. SBI') {
                                                        $vd = 'PT. Solusi Bangun Indonesia Tbk';
                                                    }

                                                    $data_in = db::connection('ympimis_2')->table('waste_logs')
                                                    ->where(db::raw("DATE_FORMAT(date_in, '%Y-%m')"), $periode)
                                                    ->where('waste_category', $jenis)
                                                    ->where('vendor', $vd)
                                                    ->limit('15')->get();

        // $data_disposal = db::connection('ympimis_2')->table('waste_details')->where('category', '=', 'Disposal')->where('waste_category', $jenis)->limit('15')->get();

                                                    $date = date('d/m/y');
                                                    $periode = date('F y');
                                                    $tanggal_ttd = date('d F Y');

                                                    $data = array(
                                                        'data_in' => $data_in,
            // 'data_disposal' => $data_disposal,
                                                        'jenis' => $jenis,
                                                        'date' => $date,
                                                        'periode' => $periode,
                                                        'tanggal_ttd' => $tanggal_ttd,
                                                        'name_month' => $name_month,
                                                        'sum_before' => $sum_before,
                                                        'tujuan' => $vendor,
                                                        'nomor' => $nomor,
                                                    );

                                                    $pdf = \App::make('dompdf.wrapper');
                                                    $pdf->getDomPDF()->set_option("enable_php", true);
                                                    $pdf->setPaper('A4', 'landscape');
                                                    $pdf->loadView('maintenance.wwt_checklist', $data);

                                                    $pdf->save(public_path() . "/files/data_wwt/" . $jenis . " Checklist.pdf");

                                                    return view('maintenance.wwt_checklist', $data);
                                                }

                                                public function HapusSlipLimbah(Request $request)
                                                {
                                                    try {
                                                        $id = $request->get('id');
                                                        $delete = DB::connection('ympimis_2')->table('waste_details')->where('id', $id)->delete();
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

                                                public function ChemicalWWTMonitoring(Request $request)
                                                {
                                                    $title = 'Wastewater Treatment Monitoring';
                                                    $title_jp = '';
                                                    $fy = db::select('SELECT DISTINCT fiscal_year FROM weekly_calendars ORDER BY id DESC');

                                                    return view('maintenance.chemical_wwt_monitoring', array(
                                                        'title' => $title,
                                                        'title_jp' => $title_jp,
                                                        'fy' => $fy
                                                    )
                                                )->with('page', 'Wastewater Treatment Monitoring');
                                                }

                                                public function FetchChemicalWWTMonitoring(Request $request)
                                                {
                                                    try {
                                                        $date_now = date('Y-m-d');
                                                        $fy = db::select('SELECT fiscal_year FROM weekly_calendars where week_date = "' . $date_now . '" ORDER BY id desc limit 1');

                                                        $p = '';
                                                        if ($request->get('fy') == null) {
                                                            $p = $fy[0]->fiscal_year;
                                                        } else {
                                                            $p = $request->get('fy');
                                                        }

                                                        $wc = db::select('SELECT DISTINCT date_format( week_date, "%Y-%m" ) as bulan FROM weekly_calendars WHERE fiscal_year = "' . $p . '" ORDER BY id ASC');
                                                        $fy = db::select('SELECT DISTINCT fiscal_year FROM weekly_calendars ORDER BY id ASC');

                                                        $series = '1,2,3,4,0,7,8,-5,6,8';

                                                        $response = array(
                                                            'status' => true,
                                                            'wc' => $wc,
                                                            'data' => $series,
                                                            'fy' => $fy
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

                                                public function IndexConfirmationLimbah(Request $request)
                                                {
                                                    $title = 'Wastewater Treatment';
                                                    $title_jp = '';

                                                    $resumes = db::connection('ympimis_2')->select('select GROUP_CONCAT(slip) as slip, GROUP_CONCAT(kode_limbah) as kode_limbah, GROUP_CONCAT(waste_details.waste_category) as jenis, GROUP_CONCAT(quantity) as quantity,  sum(quantity) as jumlah, count(slip) as banyak from waste_details left join waste_masters on waste_details.waste_category = waste_masters.waste_category where category = "Pengajuan Disposal" and slip_disposal = "DPWWT00049" GROUP BY waste_details.waste_category');
                                                    $select = db::connection('ympimis_2')->select('select slip, waste_category, quantity, pic, date_in, kemasan from waste_details where category = "Pengajuan Disposal"');
                                                    $approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "DPWWT00049" and status is null order by id asc limit 1');
                                                    $isi_approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "DPWWT00049"');

                                                    $vendor = db::connection('ympimis_2')->select('select short_name from waste_vendors orderby');

                                                    $data = [
                                                        'resumes' => $resumes,
                                                        'select' => $select,
                                                        'approval' => $approval,
                                                        'slip_disposal' => 'DPWWT00049',
                                                        'isi_approval' => $isi_approval,
                                                    ];

                                                    return view('maintenance.index_confirmation', array(
                                                        'title' => $title,
                                                        'title_jp' => $title_jp,
                                                        'data' => $data,
                                                        'vendor' => $vendor

                                                    )
                                                )->with('page', $title);
                                                }

                                                public function FetchRequestDisposal(Request $request)
                                                {
                                                    try {
                                                        $data = db::connection('ympimis_2')->select('SELECT
                * 
                                                            FROM
                                                            (
                                                                SELECT
                                                                slip_disposal,
                                                                vendor,
                                                                count( id ) AS jumlah,
                                                                date_format(updated_at, "%Y-%m-%d") as updated_at
                                                                FROM
                                                                waste_details 
                                                                WHERE
                                                                slip_disposal IS NOT NULL 
                                                                AND category = "Pengajuan Disposal" 
                                                                GROUP BY
                                                                slip_disposal,
                                                                vendor,
                                                                date_format(updated_at, "%Y-%m-%d")
                                                            ) AS p');

                                                        $data_log = db::connection('ympimis_2')->select('SELECT
                * 
                                                            FROM
                                                            (
                                                                SELECT
                                                                slip_disposal,
                                                                vendor,
                                                                count( id ) AS jumlah,
                                                                date_disposal 
                                                                FROM
                                                                waste_details 
                                                                WHERE
                                                                slip_disposal IS NOT NULL 
                                                                AND category = "LOG DISPOSAL" 
                                                                GROUP BY
                                                                slip_disposal,
                                                                vendor,
                                                                date_disposal 
                                                            ) AS p');

                                                        $response = array(
                                                            'status' => true,
                                                            'data' => $data,
                                                            'data_log' => $data_log
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

                                                public function FetchDetailRequestDisposal(Request $request)
                                                {
                                                    try {
                                                        $slip_disposal = $request->get('slip_disposal');

                                                        $resumes = db::connection('ympimis_2')->select('select GROUP_CONCAT(slip) as slip, GROUP_CONCAT(kode_limbah) as kode_limbah, GROUP_CONCAT(waste_details.waste_category) as jenis, GROUP_CONCAT(quantity) as quantity,  sum(quantity) as jumlah, count(slip) as banyak, date_disposal, vendor from waste_details left join waste_masters on waste_details.waste_category = waste_masters.waste_category where slip_disposal = "' . $slip_disposal . '" GROUP BY waste_details.waste_category, date_disposal, vendor');
                                                        $select = db::connection('ympimis_2')->select('select slip, waste_category, quantity, pic, date_in, kemasan from waste_details where category = "Pengajuan Disposal"');
                                                        $approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_disposal . '" and status is null order by id asc limit 1');
                                                        $isi_approval = db::connection('ympimis_2')->select('select id, slip_disposal, approver_id, approver_name, approver_email, status, approved_at, remark from waste_approvals where slip_disposal = "' . $slip_disposal . '"');

                                                        $vendor = db::connection('ympimis_2')->select('select short_name from waste_vendors');


                                                        $resumes_all = db::connection('ympimis_2')->select('SELECT
                                                            slip,
                                                            pic,
                                                            waste_details.waste_category as jenis,
                                                            kode_limbah,
                                                            quantity as berat,
                                                            vendor,
                                                            date_in
                                                            FROM
                                                            waste_details
                                                            LEFT JOIN waste_masters ON waste_details.waste_category = waste_masters.waste_category 
                                                            WHERE
                                                            slip_disposal = "' . $slip_disposal . '"');

                                                        $data = [
                                                            'resumes' => $resumes,
                                                            'select' => $select,
                                                            'approval' => $approval,
                                                            'slip_disposal' => $slip_disposal,
                                                            'isi_approval' => $isi_approval,
                                                            'resumes_all' => $resumes_all
                                                        ];

                                                        $response = array(
                                                            'status' => true,
                                                            'data' => $data,
                                                            'vendor' => $vendor
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

                                                public function InsertDateDisposalWWT(Request $request)
                                                {
                                                    try {
                                                        $slip_disposal = $request->get('slip_disposal');
                                                        $tanggal = $request->get('tanggal');
                                                        $vendor = $request->get('vendor');

                                                        $select_vendor = db::connection('ympimis_2')->select('select vendor from waste_vendors where short_name = "' . $vendor . '"');

                                                        db::connection('ympimis_2')->table('waste_details')->where('slip_disposal', '=', $slip_disposal)->update([
                                                            'date_disposal' => $tanggal,
                                                            'vendor' => $select_vendor[0]->vendor
                                                        ]);

                                                        $resumes = db::connection('ympimis_2')->select('SELECT date_disposal, vendor FROM waste_details WHERE slip_disposal = "'.$slip_disposal.'"');

                                                        $response = array(
                                                            'status' => true,
                                                            'resumes' => $resumes
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