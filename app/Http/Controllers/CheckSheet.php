<?php

namespace App\Http\Controllers;

use App\AreaInspection;
use App\CodeGenerator;
use App\Destination;
use App\DetailChecksheet;
use App\Inspection;
use App\Mail\SendEmail;
use App\MasterChecksheet;
use App\SendingApplication;
use App\SendingApplicationLog;
use App\ShipmentCondition;
use App\ShipmentReservation;
use App\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Response;

class CheckSheet extends Controller
{

    private $category;
    private $hpl;
    public function __construct()
    {
        $this->middleware('auth');
        $this->warehouse = array(
            'dwi.misnanto@music.yamaha.com',
            'nurul.hidayat@music.yamaha.com',
        );
        $this->logistic_team = array(
            'fatchur.rozi@music.yamaha.com',
            'dwi.misnanto@music.yamaha.com',
            'karina.elnusawati@music.yamaha.com',
        );
        $this->ir_team = array(
            'prawoto@music.yamaha.com',
            'adhi.satya.indradhi@music.yamaha.com',
            'mahendra.putra@music.yamaha.com',
        );
        $this->exim = array(
            'karina.elnusawati@music.yamaha.com',
        );
        $this->shipment_condition = array(
            'C1' => 'SEA',
            'C2' => 'AIR',
            'C3' => 'COURIER',
            'C4' => 'TRUCK',
            'C5' => 'LCL',
        );
        $this->hpl = [
            'ASBELL&BOW',
            'ASBODY',
            'ASFG',
            'ASKEY',
            'ASNECK',
            'ASPAD',
            'ASPART',
            'CASE',
            'CLBARREL',
            'CLBELL',
            'CLFG',
            'CLKEY',
            'CLLOWER',
            'CLPART',
            'CLUPPER',
            'FLBODY',
            'FLFG',
            'FLFOOT',
            'FLHEAD',
            'FLKEY',
            'FLPAD',
            'FLPART',
            'MOUTHPIECE',
            'PN',
            'PN PARTS',
            'RC',
            'TSBELL&BOW',
            'TSBODY',
            'TSFG',
            'TSKEY',
            'TSNECK',
            'TSPART',
            'VENOVA',
        ];
        $this->category = [
            'FG',
            'KD',
            'WIP',
        ];
    }

    public function index()
    {

        $time = MasterChecksheet::orderBy('created_at', 'desc')->get();
        $carier = ShipmentCondition::orderBy('shipment_condition_code', 'asc')->get();
        $destination = Destination::where('destination_code', '<>', 'ITM')
            ->WhereNull('deleted_at')
            ->orderBy('destination_code', 'asc')->get();

        return view('Check_Sheet.index', array(
            'time' => $time,
            'carier' => $carier,
            'carier1' => $carier,
            'destination' => $destination,
        ))->with('page', 'Check Sheet');
    }

    public function indexSecurityChecklist()
    {
        return view('containers.checklist.index_security');
    }

    public function indexSecurityCheckReport($checklist_id)
    {
        $checklist_point = db::connection('ympimis_2')
            ->table('container_security_checklist_points')
            ->where('checklist_id', $checklist_id)
            ->get();

        $checklist_result = db::connection('ympimis_2')
            ->table('container_security_checklist_results')
            ->where('checklist_id', $checklist_id)
            ->first();

        $security = db::table('employee_syncs')
            ->where('group', 'Security Group')
            ->get();

        $check_in_name = '';
        for ($i = 0; $i < count($security); $i++) {
            if ($checklist_result->check_in_by == $security[$i]->employee_id) {
                $check_in_name = $security[$i]->name;
                break;
            }
        }

        $check_out_name = '';
        for ($i = 0; $i < count($security); $i++) {
            if ($checklist_result->check_out_by == $security[$i]->employee_id) {
                $check_out_name = $security[$i]->name;
                break;
            }
        }

        return view('containers.checklist.report_security', array(
            'checklist_point' => $checklist_point,
            'checklist_result' => $checklist_result,
            'security' => $security,
            'check_in_name' => $check_in_name,
            'check_out_name' => $check_out_name,
        ))->with('page', 'Checklist Container');

    }
    public function indexSecurityCheck($status)
    {

        $checklist_id = strtoupper($status);
        if (strtoupper($status) != 'CHECK-IN') {
            $checklist = db::connection('ympimis_2')
                ->table('container_security_checklist_results')
                ->where('checklist_id', $status)
                ->first();

            if ($checklist->category == 'EXPORT') {

                $master_checksheet = MasterChecksheet::where('Stuffing_date', substr($checklist->check_in_at, 0, 10))
                    ->where('countainer_number', $checklist->container_number)
                    ->first();

                if ($master_checksheet) {
                    if ($master_checksheet->checklist_checked != 1) {
                        return redirect('index/checklist_container_security')->with('error', 'Pengecekan kontainer di Warehouse belum dilakukan')->with('page', 'Check Sheet');
                    }
                } else {
                    return redirect('index/checklist_container_security')->with('error', 'Nomor kontainer tidak terhubung dengan checksheet yang ada di Warehouse')->with('page', 'Check Sheet');
                }
            }
        } else {
            $prefix_now = date('Ym');
            $code_generator = CodeGenerator::where('note', '=', 'checklist_container_security')->first();
            if ($prefix_now != $code_generator->prefix) {
                $code_generator->prefix = $prefix_now;
                $code_generator->index = '0';
                $code_generator->save();
            }
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $checklist_id = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

        }

        $checklist = db::connection('ympimis_2')
            ->table('container_security_checklist_masters')
            ->get();

        $security = db::table('employee_syncs')
            ->where('group', 'Security Group')
            ->whereNull('end_date')
            ->get();

        $data = db::connection('ympimis_2')
            ->table('container_security_checklist_results')
            ->where('checklist_id', $status)
            ->first();

        $security = db::table('employee_syncs')
            ->where('group', 'Security Group')
            ->whereNull('end_date')
            ->get();

        return view('containers.checklist.checklist_security', array(
            'checklist' => $checklist,
            'security' => $security,
            'data' => $data,
            'status' => $status,
            'checklist_id' => $checklist_id,
        ))->with('page', 'Checklist Container');

    }

    public function show($id)
    {

        $time = MasterChecksheet::find($id);

        $photo = '';
        if (strlen($time->driver_photo) > 0) {
            $photo = asset("/files/checksheet/driver/" . $time->driver_photo);
        }

        $seal_photo = '';
        if (strlen($time->seal_photo) > 0) {
            $seal_photo = asset("/files/checksheet/seal/" . $time->seal_photo);
        }

        $container_photo = '';
        if (strlen($time->container_photo) > 0) {
            $container_photo = asset("/files/checksheet/container/" . $time->container_photo);
        }

        $detail = DetailChecksheet::where('id_checkSheet', '=', $time->id_checkSheet)->get();
        $container = AreaInspection::orderBy('id', 'ASC')->get();
        $Inspection = Inspection::where('id_checkSheet', '=', $time->id_checkSheet)->get();

        $checklist = db::connection('ympimis_2')
            ->table('container_checklist_results')
            ->where('checksheet_id', strtoupper($time->id_checkSheet))
            ->get();

        $checklist_photo = db::connection('ympimis_2')
            ->table('container_checklist_photos')
            ->where('checksheet_id', strtoupper($time->id_checkSheet))
            ->get();

        $employees = db::select("SELECT * FROM `employee_syncs`");

        return view('Check_Sheet.show', array(
            'time' => $time,
            'detail' => $detail,
            'container' => $container,
            'inspection' => $Inspection,
            'photo' => $photo,
            'seal_photo' => $seal_photo,
            'container_photo' => $container_photo,
            'checklist' => $checklist,
            'checklist_photo' => $checklist_photo,
            'employees' => $employees,
        ))->with('page', 'Check Sheet');
    }

    public function fetchSecurityChecklist(Request $request)
    {

        $checklist = db::connection('ympimis_2')->table('container_security_checklist_results');

        $is_filtered = false;
        if (strlen($request->get('check_in_from')) > 0) {
            $check_in_from = date('Y-m-d', strtotime($request->get('check_in_from')));
            $checklist = $checklist->where(db::raw('date_format(container_security_checklist_results.check_in_at, "%Y-%m-%d")'), '>=', $check_in_from);
            $is_filtered = true;
        }
        if (strlen($request->get('check_in_to')) > 0) {
            $check_in_to = date('Y-m-d', strtotime($request->get('check_in_to')));
            $checklist = $checklist->where(db::raw('date_format(container_security_checklist_results.check_in_at, "%Y-%m-%d")'), '<=', $check_in_to);
            $is_filtered = true;
        }
        if (count($request->get('category')) > 0) {
            $checklist = $checklist->whereIn('container_security_checklist_results.category', $request->get('category'));
        }

        $checklist = $checklist->orderBy('container_security_checklist_results.check_in_at', 'DESC');

        if (!$is_filtered) {
            $checklist = $checklist->limit(100);
        }
        $checklist = $checklist->get();

        $security = db::table('employee_syncs')
            ->where('group', 'Security Group')
            ->get();

        $response = array(
            'status' => true,
            'checklist' => $checklist,
            'security' => $security,
        );
        return Response::json($response);

    }

    public function fetch_checksheet($id)
    {

        $time = MasterChecksheet::where('id_checkSheet', $id)->first();

        $response = array(
            'status' => true,
            'time' => $time,
        );
        return Response::json($response);
    }

    public function check($id)
    {

        $time = MasterChecksheet::find($id);

        $photo = '';
        if (strlen($time->driver_photo) > 0) {
            $photo = asset("/files/checksheet/driver/" . $time->driver_photo);
        }

        $seal_photo = '';
        if (strlen($time->seal_photo) > 0) {
            $seal_photo = asset("/files/checksheet/seal/" . $time->seal_photo);
        }

        $container_photo = '';
        if (strlen($time->container_photo) > 0) {
            $container_photo = asset("/files/checksheet/container/" . $time->container_photo);
        }

        $detail = db::select("select cek.*, IFNULL(inv.quantity,0) as stock from (
               SELECT * from detail_checksheets WHERE id_checkSheet='" . $time->id_checkSheet . "'
               and deleted_at is null) cek
               LEFT JOIN (
               SELECT material_number, quantity  from inventories WHERE storage_location='FSTK'
          ) as inv on cek.gmc = inv.material_number ORDER BY cek.id asc");

        $container = AreaInspection::orderBy('id', 'ASC')->get();
        $Inspection = Inspection::where('id_checkSheet', '=', $time->id_checkSheet)->get();

        $checklist = db::connection('ympimis_2')
            ->table('container_checklist_results')
            ->where('checksheet_id', strtoupper($time->id_checkSheet))
            ->get();

        $checklist_photo = db::connection('ympimis_2')
            ->table('container_checklist_photos')
            ->where('checksheet_id', strtoupper($time->id_checkSheet))
            ->get();

        $employees = db::select("SELECT * FROM `employee_syncs`
                WHERE end_date IS NULL
                AND department IN (
                'Logistic Department',
                'Management Information System Department')");

        return view('Check_Sheet.check', array(
            'time' => $time,
            'detail' => $detail,
            'checklist' => $checklist,
            'checklist_photo' => $checklist_photo,
            'asset_route' => asset("/files/checksheet/checklist_evidence/"),
            'photo' => $photo,
            'seal_photo' => $seal_photo,
            'container_photo' => $container_photo,
            'employees' => $employees,
        ))->with('page', 'Check Sheet');
    }

    public function print_check($id)
    {

        $time = MasterChecksheet::find($id);
        $detail = DetailChecksheet::where('id_checkSheet', '=', $time->id_checkSheet)
            ->get();
        $container = AreaInspection::orderBy('id', 'ASC')
            ->get();
        $Inspection = Inspection::where('id_checkSheet', '=', $time->id_checkSheet)
            ->get();
        return view('Check_Sheet.print', array(
            'time' => $time,
            'detail' => $detail,
            'container' => $container,
            'inspection' => $Inspection,
        ))->with('page', 'Check Sheet');
    }

    public function print_check_surat($id)
    {
        $checksheet = MasterChecksheet::where('master_checksheets.id', '=', $id)
            ->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'master_checksheets.carier')
            ->select(
                'master_checksheets.do_number',
                'master_checksheets.Stuffing_date',
                'master_checksheets.invoice_date',
                'master_checksheets.toward',
                'master_checksheets.id_checkSheet',
                'master_checksheets.no_pol',
                'master_checksheets.countainer_number',
                'master_checksheets.seal_number',
                'shipment_conditions.shipment_condition_name',
                'master_checksheets.ct_size'
            )
            ->first();

        $checksheet_details = db::select("SELECT
               invoice AS no_invoice,
               gmc AS material_number,
               goods AS material_description,
               IF
               ( package_qty IS NULL OR package_qty = '', '-', package_qty ) AS no_package,
               IF
               ( package_set IS NULL OR package_set = '', '-', package_set ) AS package,
               qty_qty AS quantity,
               qty_set AS uom
               FROM
               detail_checksheets
               WHERE
               id_checkSheet = '" . $checksheet->id_checkSheet . "'
               AND deleted_at IS NULL");

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('Check_Sheet.printsurat', array(
            'checksheet' => $checksheet,
            'checksheet_details' => $checksheet_details,
        ));

        return $pdf->stream("Surat Jalan.pdf");
    }

    public function checkmarking($id)
    {
        $time = MasterChecksheet::find($id);

        $container = AreaInspection::orderBy('id', 'ASC')->get();
        $Inspection = Inspection::where('id_checkSheet', '=', $time->id_checkSheet)->get();
        $detail = db::select("select cek.*, IFNULL(inv.quantity,0) as stock from (
               SELECT * from detail_checksheets WHERE id_checkSheet='" . $time->id_checkSheet . "'
               and deleted_at is null) cek
               LEFT JOIN (
               SELECT material_number, quantity  from inventories WHERE storage_location='FSTK'
          ) as inv on cek.gmc = inv.material_number");

        return view('Check_Sheet.checkmarking', array(
            'time' => $time,
            'detail' => $detail,
            'container' => $container,
            'inspection' => $Inspection,
        ))->with('page', 'Check Sheet');
    }

    public function import(Request $request)
    {

        if ($request->hasFile('check_sheet_import')) {
            $id = Auth::id();

            $towards = $request->get('toward');
            $toward_length = count($towards);
            $toward = "";

            for ($x = 0; $x < $toward_length; $x++) {
                $toward = $toward . "" . $towards[$x] . "";
                if ($x != $toward_length - 1) {
                    $toward = $toward . "-";
                }
            }

            DB::beginTransaction();
            $destination_standarts = Destination::whereNotNull('priority')->get();

            $file = $request->file('check_sheet_import');
            $data = file_get_contents($file);
            $code_generator = CodeGenerator::where('note', '=', 'check')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index);
            $a = 0;
            $rows = explode("\r\n", $data);
            $code = $number;
            $number1 = sprintf("%'.0" . $code_generator->length . "d", $code);
            $master = new MasterChecksheet([
                'id_checkSheet' => $code_generator->prefix . $number1,
                'do_number' => strtoupper($request->get('do_number')),
                'destination_code' => strtoupper($request->get('destination_code')),
                'destination' => strtoupper($request->get('destination')),
                'invoice' => $request->get('invoice'),
                'countainer_number' => $request->get('countainer_number'),
                'seal_number' => $request->get('seal_number'),
                'shipped_from' => $request->get('shipped_from'),
                'shipped_to' => $request->get('shipped_to'),
                'carier' => $request->get('carier'),
                'payment' => $request->get('payment'),
                'etd_sub' => $request->get('etd_sub'),
                'no_pol' => $request->get('nopol'),
                'Stuffing_date' => $request->get('Stuffing_date'),
                'invoice_date' => $request->get('invoice_date'),
                'toward' => $toward,
                'ct_size' => $request->get('ct_size'),
                'period' => $request->get('period'),
                'ycj_ref_number' => $request->get('ycj_ref_number'),
                'created_by' => $id,
            ]);

            $code_generator->index = $code_generator->index + 1;

            $master_checklist = DB::connection('ympimis_2')
                ->table('container_checklist_masters')
                ->get();

            $master_photo_checklist = DB::connection('ympimis_2')
                ->table('container_checklist_masters')
                ->whereNotNull('photo_requirment')
                ->get();

            try {
                $master->save();
                $code_generator->save();

                if ($request->get('carier') == 'C1') {
                    for ($i = 0; $i < count($master_checklist); $i++) {
                        $insert = DB::connection('ympimis_2')
                            ->table('container_checklist_results')
                            ->insert([
                                'checksheet_id' => $code_generator->prefix . $number1,
                                'checklist_id' => $master_checklist[$i]->id,
                                'area' => $master_checklist[$i]->area,
                                'photo_requirment' => $master_checklist[$i]->photo_requirment,
                                'point_check' => $master_checklist[$i]->point_check,
                                'guidelines' => $master_checklist[$i]->guidelines,
                                'area_setting' => $master_checklist[$i]->area_setting,
                                'guidelines_setting' => $master_checklist[$i]->guidelines_setting,
                                'created_by' => Auth::id(),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }

                    for ($i = 0; $i < count($master_photo_checklist); $i++) {
                        for ($j = 1; $j <= $master_photo_checklist[$i]->photo_requirment; $j++) {
                            $insert = DB::connection('ympimis_2')
                                ->table('container_checklist_photos')
                                ->insert([
                                    'checksheet_id' => $code_generator->prefix . $number1,
                                    'area' => $master_photo_checklist[$i]->area,
                                    'area_photo_id' => $j,
                                    'created_by' => Auth::id(),
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }
                    }
                }

            } catch (Exception $e) {
                DB::rollback();
                return redirect('/index/CheckSheet')->with('error', $e->getMessage())->with('page', 'Check Sheet');

            }

            $count = 0;
            foreach ($rows as $row) {
                if (strlen($row) > 0) {
                    $count++;
                    $row = explode("\t", $row);
                    if ($row[1] != 'DESTINATION' && $row[1] != '') {
                        if ($row[6] == '') {
                            $row[6] = '-';
                        }
                        if ($row[7] == '') {
                            $row[7] = '-';
                        }

                        $check_destination = false;
                        for ($i = 0; $i < count($destination_standarts); $i++) {
                            if ($row[1] == $destination_standarts[$i]->destination_code) {
                                $check_destination = true;
                                true;
                            }
                        }

                        if (!$check_destination) {
                            DB::rollback();
                            return redirect('/index/CheckSheet')->with('error', 'Destination code ' . $row[1] . ' is unregistered (Row : ' . $count . ')')->with('page', 'Check Sheet');
                        }

                        $cek = db::table('sales_prices')
                            ->where('material_number', $row[3])
                            ->first();

                        try {
                            $detail = new DetailChecksheet([
                                'id_checkSheet' => $code_generator->prefix . $number1,
                                'order_type' => $row[0],
                                'destination' => $row[1],
                                'invoice' => $row[2],
                                'gmc' => $row[3],
                                'goods' => $row[4],
                                'marking' => $row[5],
                                'package_qty' => $row[6],
                                'package_set' => $row[7],
                                'qty_qty' => $row[8],
                                'qty_set' => $row[9],
                                'box' => $row[10],
                                'created_by' => $id,
                            ]);
                            $detail->save();

                        } catch (Exception $e) {
                            DB::rollback();
                            return redirect('/index/CheckSheet')->with('error', $e->getMessage())->with('page', 'Check Sheet');

                        }

                    }

                }
            }

            $data = [
                'checksheet' => $master,
                'carier' => $this->shipment_condition,
            ];

            $cc = [];
            $cc = array_merge($cc, $this->exim);

            $user = User::where('id', $id)->first();
            if ($user) {
                if (str_contains($user->email, 'yamaha')) {
                    array_push($cc, $user->email);
                }
            }

            Mail::to($this->warehouse)
                ->cc($cc)
                ->bcc([
                    'muhammad.ikhlas@music.yamaha.com',
                ])
                ->send(new SendEmail($data, 'new_checksheet'));

            DB::commit();
            return redirect('/index/CheckSheet')->with('status', 'New Check Sheet has been imported.')->with('page', 'Check Sheet');

        } else {
            return redirect('/index/CheckSheet')->with('error', 'Please select a file.')->with('page', 'Check Sheet');
        }
    }

    public function importDetail(Request $request)
    {
        $id = Auth::id();

        // $Inspection = new Inspection([
        //     'id_checksheet' => $request->get('idcs2'),
        //     'created_by' => $id,
        // ]);
        // $Inspection->save();

        if ($request->hasFile('check_sheet_import2')) {

            $destination_standarts = Destination::whereNotNull('priority')->get();
            $file = $request->file('check_sheet_import2');
            $data = file_get_contents($file);
            $code_master = $request->get('idcs2');
            $rows = explode("\r\n", $data);
            $count = 0;
            foreach ($rows as $row) {
                if (strlen($row) > 0) {
                    $count++;
                    $row1 = explode("\t", $row);
                    if ($row1[1] != 'DESTINATION' && $row1[1] != '') {
                        if ($row1[6] == '') {
                            $row1[6] = '-';
                        }
                        if ($row1[7] == '') {
                            $row1[7] = '-';
                        }

                        $check_destination = false;
                        for ($i = 0; $i < count($destination_standarts); $i++) {
                            if ($row1[1] == $destination_standarts[$i]->destination_code) {
                                $check_destination = true;
                                true;
                            }
                        }

                        if (!$check_destination) {
                            DB::rollback();
                            return redirect('/index/CheckSheet')->with('error', 'Destination code ' . $row1[1] . ' is unregistered (Row : ' . $count . ')')->with('page', 'Check Sheet');
                        }

                        try {
                            $detail = new DetailChecksheet([
                                'id_checkSheet' => $code_master,
                                'order_type' => $row1[0],
                                'destination' => $row1[1],
                                'invoice' => $row1[2],
                                'gmc' => $row1[3],
                                'goods' => $row1[4],
                                'marking' => $row1[5],
                                'package_qty' => $row1[6],
                                'package_set' => $row1[7],
                                'qty_qty' => $row1[8],
                                'qty_set' => $row1[9],
                                'box' => $row1[10],
                                'created_by' => $id,
                            ]);
                            $detail->save();

                        } catch (Exception $e) {
                            DB::rollback();
                            return redirect('/index/CheckSheet')->with('error', $e->getMessage())->with('page', 'Check Sheet');

                        }

                    }

                }
            }

            $master = MasterChecksheet::where('id_checkSheet', $code_master)->first();
            $master->rev = $master->rev + 1;
            $master->approved_at = null;
            $master->approved_by = null;
            $master->updated_at = date('Y-m-d H:i:s');
            $master->save();

            $data = [
                'checksheet' => $master,
                'carier' => $this->shipment_condition,
            ];

            $cc = [];
            $cc = array_merge($cc, $this->exim);

            $user = User::where('id', $id)->first();
            if ($user) {
                if (str_contains($user->email, 'yamaha')) {
                    array_push($cc, $user->email);
                }
            }

            Mail::to($this->warehouse)
                ->cc($cc)
                ->bcc([
                    'muhammad.ikhlas@music.yamaha.com',
                ])
                ->send(new SendEmail($data, 'revised_checksheet'));

            return redirect('/index/CheckSheet')->with('status', 'Re - Import Success')->with('page', 'Check Sheet');

        } else {
            return redirect('/index/CheckSheet')->with('error', 'Please select a file.')->with('page', 'Check Sheet');
        }
    }

    public function update(Request $request)
    {
        $DetailChecksheet = DetailChecksheet::find($request->get('id_detail'));
        $DetailChecksheet->confirm = $request->get('confirm');
        $DetailChecksheet->diff = $request->get('diff');
        $DetailChecksheet->save();

        $start = MasterChecksheet::where('id_checkSheet', '=', $DetailChecksheet->id_checkSheet)
            ->select('id', 'start_stuffing', 'period', 'ycj_ref_number')
            ->first();

        if ($start->start_stuffing == null) {
            $start2 = MasterChecksheet::find($start->id);
            $start2->start_stuffing = date('Y-m-d H:i:s');
            $start2->save();
        }

        $start2 = MasterChecksheet::find($start->id);
        $start2->finish_stuffing = date('Y-m-d H:i:s');
        $start2->save();

        if (($start->period != null) && ($start->ycj_ref_number != null)) {
            $booking = ShipmentReservation::where('period', $start->period)
                ->where('ycj_ref_number', $start->ycj_ref_number)
                ->where('status', 'BOOKING CONFIRMED')
                ->update([
                    'actual_stuffing' => date('Y-m-d'),
                ]);
        }

        $response = array(
            'status' => true,
            'message' => 'Update Success',
            'start' => $start->start_stuffing,
        );
        return Response::json($response);

    }

    public function add(Request $request)
    {
        $id_user = Auth::id();
        $Inspection = Inspection::where('id_checksheet', '=', $request->get('id'))
            ->select('id_checksheet')
            ->first();
        if ($Inspection == '') {
            $Inspection1 = new Inspection([
                'id_checksheet' => $request->get('id'),
                'created_by' => $id_user,
            ]);
            $Inspection1->save();
        }

    }

    public function addDetail(Request $request)
    {
        $id_user = Auth::id();
        $Inspection = Inspection::where('id_checksheet', '=', $request->get('id'))
            ->first();
        $a = $request->get('inspection');
        $Inspection->$a = $request->get('confirm');
        $Inspection->created_by = $id_user;
        $Inspection->save();
        $response = array(
            'status' => true,
            'message' => 'Update Success',
        );

    }

    public function addDetail2(Request $request)
    {
        $id_user = Auth::id();
        $Inspection = Inspection::where('id_checksheet', '=', $request->get('id'))
            ->first();
        $a = $request->get('remark');
        $Inspection->$a = $request->get('text');
        $Inspection->created_by = $id_user;
        $Inspection->save();
        $response = array(
            'status' => true,
            'message' => 'Update Success',
        );

    }

    public function check_nomor(Request $request)
    {
        $kolom = $request->get('kolom');

        $Inspection = MasterChecksheet::where('id_checksheet', '=', $request->get('id'))
            ->where(str_replace("closure_", "", $kolom), '=', strtoupper($request->get('isi')))
            ->first();

        if ($Inspection) {
            $response = array(
                'status' => true,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Not Match',
            );
            return Response::json($response);
        }

    }

    public function nomor(Request $request)
    {
        $id_user = Auth::id();
        $Inspection = MasterChecksheet::where('id_checksheet', '=', $request->get('id'))->first();

        $kolom = $request->get('kolom');
        $Inspection->$kolom = strtoupper($request->get('isi'));
        $Inspection->check_by = $id_user;
        $Inspection->save();

        $response = array(
            'status' => true,
            'message' => 'Update Success',
            'id' => $kolom,
            'value' => strtoupper($request->get('isi')),
        );
        return Response::json($response);
    }

    public function bara(Request $request)
    {
        $id_user = Auth::id();
        $Inspection = DetailChecksheet::where('id', '=', $request->get('id'))
            ->first();

        $Inspection->bara = $request->get('isi');
        $Inspection->created_by = $id_user;
        $Inspection->save();

        $start = MasterChecksheet::where('id_checkSheet', '=', $Inspection->id_checkSheet)->select('id', 'start_stuffing', 'period', 'ycj_ref_number')
            ->first();

        if ($start->start_stuffing == null) {
            $start2 = MasterChecksheet::find($start->id);
            $start2->start_stuffing = date('Y-m-d H:i:s');
            $start2->save();
        }

        $start2 = MasterChecksheet::find($start->id);
        $start2->finish_stuffing = date('Y-m-d H:i:s');
        $start2->save();

        if (($start->period != null) && ($start->ycj_ref_number != null)) {
            $booking = ShipmentReservation::where('period', $start->period)
                ->where('ycj_ref_number', $start->ycj_ref_number)
                ->where('status', 'BOOKING CONFIRMED')
                ->update([
                    'actual_stuffing' => date('Y-m-d'),
                ]);
        }

        $response = array(
            'status' => true,
            'message' => 'Update Success',
        );

    }

    public function getReason(Request $request)
    {
        $reason = MasterChecksheet::where('id_checksheet', '=', $request->get('id'))
            ->select('reason', 'invoice_date')
            ->first();

        $response = array(
            'status' => true,
            'message' => 'Update Success',
            'reason' => $reason,
        );
        return Response::json($response);
    }

    public function edit($checksheet, Request $request)
    {
        $id_user = Auth::id();
        $master = MasterChecksheet::where('id_checksheet', '=', $checksheet)->first();

        $towards = $request->get('edit_toward');
        $toward_length = count($towards);
        $toward = "";

        for ($x = 0; $x < $toward_length; $x++) {
            $toward = $toward . "" . $towards[$x] . "";
            if ($x != $toward_length - 1) {
                $toward = $toward . "-";
            }
        }

        $master->period = $request->get('edit_period');
        $master->destination_code = $request->get('edit_destination_code');
        $master->Stuffing_date = $request->get('edit_stuffing_date');
        $master->etd_sub = $request->get('edit_etd_sub');
        $master->ycj_ref_number = $request->get('edit_ycj_ref_number');
        $master->destination = strtoupper($request->get('edit_destination'));
        $master->shipped_to = $request->get('edit_shipped_to');
        $master->toward = $toward;
        $master->carier = $request->get('edit_carier');
        $master->ct_size = $request->get('edit_ct_size');
        $master->no_pol = $request->get('edit_nopol');
        $master->countainer_number = $request->get('edit_countainer_number');
        $master->seal_number = $request->get('edit_seal_number');
        $master->do_number = $request->get('edit_do_number');
        $master->invoice = $request->get('edit_invoice');
        $master->invoice_date = $request->get('edit_invoice_date');
        $master->payment = $request->get('edit_payment');
        $master->reason = $request->get('edit_reason');
        // $master->created_by = $id_user;

        $master->save();

        $response = array(
            'status' => true,
            'message' => 'Update Success',
        );

        return redirect('/index/CheckSheet')->with('status', 'Check Sheet has been updated.')->with('page', 'Check Sheet');
    }

    public function marking(Request $request)
    {
        $id_user = Auth::id();
        $Inspection = DetailChecksheet::where('id', '=', $request->get('id_detail'))
            ->first();

        $Inspection->markingcheck = $request->get('marking');
        $Inspection->created_by = $id_user;
        $Inspection->save();
        $response = array(
            'status' => true,
            'message' => 'Update Success',
        );

    }

    public function directIfShipment($ship_list_no)
    {

        $shipment = db::connection('ympimis_2')->select("SELECT * FROM direct_register_shipments");

        $mpdl = db::table('material_plant_data_lists')
            ->where('valcl', '9010')
            ->get();

        foreach ($shipment as $row) {
            $is_9010 = false;
            $description = '';
            for ($i = 0; $i < count($mpdl); $i++) {
                if (strtoupper($row->material_number) == $mpdl[$i]->material_number) {
                    $is_9010 = true;
                    $description = $mpdl[$i]->material_description;
                    break;
                }
            }

            if ($is_9010) {
                $is_sn_exist = db::connection('ymes')
                    ->table('vd_mes0290')
                    ->where('serial_no', strtoupper($row->serial_number))
                    ->where('item_code', strtoupper($row->material_number))
                    ->where('shipped_flg', 0)
                    ->where('location_code', 'FSTK')
                    ->first();

                if (!$is_sn_exist) {
                    $message = $description . ' with serial number ' . strtoupper($row->serial_number) . ' in  slip ' . strtoupper($row->slip) . ' does not exist';

                    $response = array(
                        'status' => false,
                        'message' => $message,
                    );
                    return Response::json($response);

                }
            }
        }

        // YMES SHIPMENT
        foreach ($shipment as $row) {

            $is_9010 = false;
            for ($i = 0; $i < count($mpdl); $i++) {
                if (strtoupper($row->material_number) == $mpdl[$i]->material_number) {
                    $is_9010 = true;
                    break;
                }
            }

            if ($is_9010) {
                $plant_code = '8190';
                $ship_list_no = $ship_list_no;
                $ship_to_code = strtoupper($row->destination_code);
                $invoice_no = strtoupper($row->invoice_number);
                $container_no = strtoupper($row->countainer_number);
                $vanning_date = $row->stuffing_date;
                $etd_date = $row->etd_sub;
                $memo = '';
                $ship_reg_type = '1';
                $serial_no = strtoupper($row->serial_number);
                $item_code = strtoupper($row->material_number);
                $instid = 'iot';
                $instdt = date('Y-m-d H:i:s');
                $instterm = '';
                $instprgnm = '';
                $updtid = 'iot';
                $updtdt = date('Y-m-d H:i:s');
                $updtterm = '';
                $updtprgnm = '';

                $category = 'shipment';
                $action = 'register_shipment';
                $function = 'save';
                $remark = 'MIRAI';

                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;

                app('App\Http\Controllers\YMESController')->register_shipment(
                    $plant_code,
                    $ship_list_no,
                    $ship_to_code,
                    $invoice_no,
                    $container_no,
                    $vanning_date,
                    $etd_date,
                    $memo,
                    $ship_reg_type,
                    $serial_no,
                    $item_code,
                    $instid,
                    $instdt,
                    $instterm,
                    $instprgnm,
                    $updtid,
                    $updtdt,
                    $updtterm,
                    $updtprgnm,
                    $category,
                    $action,
                    $function,
                    $remark,
                    $created_by,
                    $created_by_name
                );
            }
        }

        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function save(Request $request)
    {
        $master = MasterChecksheet::where('id_checksheet', '=', $request->get('id'))->first();
        if ($master->checklist_checked != 1 && $master->carier == 'C1') {
            return redirect('/check/CheckSheet/' . $master->id)->with('error', 'The checklist for container conditions has not been checked')->with('page', 'Check Sheet');
        }

        $null_container_checklist_photos = db::connection('ympimis_2')
            ->table('container_checklist_photos')
            ->where('checksheet_id', $request->get('id'))
            ->whereNull('source')
            ->get();

        if (count($null_container_checklist_photos) > 0 && $master->carier == 'C1') {
            return redirect('/check/CheckSheet/' . $master->id)->with('error', 'There is an empty evidence checklist photo')->with('page', 'Check Sheet');
        }

        $mpdl = db::table('material_plant_data_lists')
            ->where('valcl', '9010')
            ->get();

        $shipment = db::select("SELECT flos.flo_number AS slip, flos.destination_code, flos.invoice_number, flo_details.serial_number, flo_details.material_number FROM flos
               LEFT JOIN flo_details ON flo_details.flo_number = flos.flo_number
               WHERE container_id = '" . $request->get('id') . "'
               UNION ALL
               SELECT seq.eo_number_sequence AS slip, eo.destination_code, seq.invoice_number, seq.serial_number, seq.material_number FROM extra_order_detail_sequences seq
               LEFT JOIN extra_orders eo ON eo.eo_number = seq.eo_number
               WHERE seq.container_id = '" . $request->get('id') . "'
               UNION ALL
               SELECT kdd.kd_number AS slip, st.destination_code, kd.invoice_number, kdd.serial_number, kdd.material_number FROM knock_down_details kdd
               LEFT JOIN knock_downs kd ON kd.kd_number = kdd.kd_number
               LEFT JOIN shipment_schedules st ON st.id = kdd.shipment_schedule_id
               WHERE kd.container_id = '" . $request->get('id') . "'");

        foreach ($shipment as $row) {
            $is_9010 = false;
            $description = '';
            for ($i = 0; $i < count($mpdl); $i++) {
                if (strtoupper($row->material_number) == $mpdl[$i]->material_number) {
                    $is_9010 = true;
                    $description = $mpdl[$i]->material_description;
                    break;
                }
            }

            if ($is_9010) {
                $is_sn_exist = db::connection('ymes')
                    ->table('vd_mes0290')
                    ->where('serial_no', strtoupper($row->serial_number))
                    ->where('item_code', strtoupper($row->material_number))
                    ->where('shipped_flg', 0)
                    ->where('location_code', 'FSTK')
                    ->first();

                if (!$is_sn_exist) {
                    $message = $description . ' with serial number ' . strtoupper($row->serial_number) . ' in  slip ' . strtoupper($row->slip) . ' does not exist';
                    return redirect('/check/CheckSheet/' . $master->id)->with('error', $message)->with('page', 'Check Sheet');
                }
            }
        }

        // UPDATE CHECKSHEET
        $id_user = Auth::id();
        $check = $master->status;
        $email = $master->sent_email;

        $master->status = date('Y-m-d H:i:s');
        $master->check_by = $id_user;
        $master->sent_email = 1;
        $master->save();

        // UPDATE EO SEND APP
        $send_app = SendingApplication::where('container_id', $request->get('id'))->first();
        if ($send_app) {
            if ($send_app->status < 5) {
                $update_send_app = SendingApplication::where('container_id', $request->get('id'))
                    ->update([
                        'status' => 5,
                    ]);

                $send_app_log = new SendingApplicationLog([
                    'send_app_no' => $send_app->send_app_no,
                    'status' => 5,
                    'created_by' => Auth::user()->username,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $send_app_log->save();
            }
        }
        // END UPDATE EO SEND APP

        if (($master->period != null) && ($master->ycj_ref_number != null)) {
            $booking = ShipmentReservation::where('period', $master->period)
                ->where('ycj_ref_number', $master->ycj_ref_number)
                ->where('status', 'BOOKING CONFIRMED')
                ->update([
                    'actual_on_board' => date('Y-m-d'),
                ]);
        }

        if (($check == null) && ($email != 1)) {
            self::mailStuffing($master->Stuffing_date);
        }

        // YMES SHIPMENT
        $ck_details = db::select("SELECT id_checkSheet, GROUP_CONCAT(invoice) AS invoice FROM
               (SELECT DISTINCT id_checkSheet, invoice FROM detail_checksheets
                    WHERE deleted_at IS NULL
                    AND id_checkSheet = '" . $request->get('id') . "') AS detail
                    GROUP BY id_checkSheet");

        $invoice_no = '';
        if (count($ck_details) > 0) {
            $invoice_no = $ck_details[0]->invoice;
        }

        foreach ($shipment as $row) {

            $is_9010 = false;
            for ($i = 0; $i < count($mpdl); $i++) {
                if (strtoupper($row->material_number) == $mpdl[$i]->material_number) {
                    $is_9010 = true;
                    break;
                }
            }

            if ($is_9010) {
                $plant_code = '8190';
                $ship_list_no = $request->get('id');
                $ship_to_code = $master->destination_code;

                if ($ship_to_code == 'Y1000XJ') {
                    $ship_to_code = 'Y1000X';
                }

                if (strlen($invoice_no) >= 20) {
                    $invoice_no = substr($master->invoice, 0, 6);
                }

                $container_no = $master->countainer_number;
                $vanning_date = $master->Stuffing_date;
                $etd_date = $master->etd_sub;
                $memo = '';
                $ship_reg_type = '1';
                $serial_no = strtoupper($row->serial_number);
                $item_code = strtoupper($row->material_number);
                $instid = 'iot';
                $instdt = date('Y-m-d H:i:s');
                $instterm = '';
                $instprgnm = '';
                $updtid = 'iot';
                $updtdt = date('Y-m-d H:i:s');
                $updtterm = '';
                $updtprgnm = '';

                $category = 'shipment';
                $action = 'register_shipment';
                $function = 'save';
                $remark = 'MIRAI';

                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;

                app('App\Http\Controllers\YMESController')->register_shipment(
                    $plant_code,
                    $ship_list_no,
                    $ship_to_code,
                    $invoice_no,
                    $container_no,
                    $vanning_date,
                    $etd_date,
                    $memo,
                    $ship_reg_type,
                    $serial_no,
                    $item_code,
                    $instid,
                    $instdt,
                    $instterm,
                    $instprgnm,
                    $updtid,
                    $updtdt,
                    $updtterm,
                    $updtprgnm,
                    $category,
                    $action,
                    $function,
                    $remark,
                    $created_by,
                    $created_by_name
                );
            }
        }

        $category = 'shipment';
        $action = 'trigger_shipment';
        $function = 'save';
        $remark = 'MIRAI';

        app('App\Http\Controllers\YMESController')->run_shipment_trigger($category, $action, $function, $remark);

        // YMES END

        return redirect('/index/CheckSheet')->with('status', 'Check Sheet has been saved.')->with('page', 'Check Sheet');

    }

    public function mailStuffing($st_date)
    {
        $mail_to = db::table('send_emails')
            ->where('remark', '=', 'stuffing')
            ->WhereNull('deleted_at')
            ->orWhere('remark', '=', 'superman')
            ->WhereNull('deleted_at')
            ->select('email')
            ->get();

        $query = "SELECT
               master_checksheets.Stuffing_date,
               IF
               (
                    master_checksheets.`status` IS NOT NULL,
                    'DEPARTED',
                    IF
                    ( actual_stuffing.total_actual > 0, 'LOADING', '-' )) AS stats,
               master_checksheets.`status`,
               master_checksheets.id_checkSheet,
               master_checksheets.destination,
               shipment_conditions.shipment_condition_name,
               actual_stuffing.total_plan,
               actual_stuffing.total_actual,
               master_checksheets.reason,
               master_checksheets.start_stuffing,
               master_checksheets.finish_stuffing,
               TIMESTAMPDIFF( MINUTE, master_checksheets.start_stuffing, master_checksheets.finish_stuffing ) AS duration
               FROM
               master_checksheets
               LEFT JOIN shipment_conditions ON shipment_conditions.shipment_condition_code = master_checksheets.carier
               LEFT JOIN (
                    SELECT
                    id_checkSheet,
                    sum( plan_loading ) AS total_plan,
                    sum( actual_loading ) AS total_actual
                    FROM
                    (
                         SELECT
                         id_checkSheet,
                         qty_qty AS plan_loading,
                         (
                              qty_qty /
                              IF
                              ( package_qty = '-' OR package_qty IS NULL, 1, package_qty ))*
                         IF
                         ( confirm = 0 AND bara = 0, 1, confirm ) AS actual_loading
                         FROM
                         detail_checksheets
                         WHERE
                         deleted_at IS NULL
                         ) AS stuffings
                    GROUP BY
                    id_checkSheet
                    ) AS actual_stuffing ON actual_stuffing.id_checkSheet = master_checksheets.id_checkSheet
               WHERE
               master_checksheets.deleted_at IS NULL
               AND master_checksheets.Stuffing_date = '" . $st_date . "'
               ORDER BY
               field(
               stats,
               'LOADING',
               'INSPECTION',
               '-',
               'DEPARTED')";

        $stuffings = db::select($query);

        if ($stuffings != null) {
            Mail::to($mail_to)->send(new SendEmail($stuffings, 'stuffing'));
        }
    }

    public function delete($id)
    {
        $time = MasterChecksheet::find($id);

        $master = MasterChecksheet::where('id_checkSheet', '=', $time->id_checkSheet)
            ->delete();

        $Inspection = Inspection::where('id_checkSheet', '=', $time->id_checkSheet)
            ->delete();

        $response = array(
            'status' => true,
            'message' => 'Delete Success',
        );
        $time2 = MasterChecksheet::orderBy('created_at', 'desc')->get();

        return redirect('/index/CheckSheet')->with('status', 'Check Sheet has been Deleted.')->with('page', 'Check Sheet');
    }

    public function persen($id)
    {

        $ceksheet = DetailChecksheet::where('id_checkSheet', '=', $id);

        $total = $ceksheet->sum('detail_checksheets.package_qty');
        $cek = $ceksheet->sum('detail_checksheets.confirm');

        $response = array(
            'status' => true,
            'total' => $total,
            'cek' => $cek,
        );
        return Response::json($response);
    }

    public function deleteReimport(Request $request)
    {

        try {
            $detail = DetailChecksheet::where('id_checkSheet', '=', $request->get('id'))
                ->delete();

            $Inspection = Inspection::where('id_checkSheet', '=', $request->get('id'))
                ->delete();

            $response = array(
                'status' => true,
                'message' => 'Update Success',
                'reason' => 'ok',
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

    public function importDriverPhoto(Request $request)
    {

        try {
            $directory = 'files\checksheet\driver';

            $file = $request->file('file_datas');
            $name = $file->getClientOriginalName();
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $filename = $request->input('id_checkSheet') . '.' . $extension;

            $file->move($directory, $filename);

            $ck = MasterChecksheet::where('id_checkSheet', '=', $request->input('id_checkSheet'))->first();
            $ck->driver_photo = $filename;
            $ck->save();

            $response = array(
                'status' => true,
                'photo' => asset("/files/checksheet/driver/" . $filename),
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

    public function importSealPhoto(Request $request)
    {

        try {
            $directory = 'files\checksheet\seal';

            $filename = $request->input('id_checkSheet');
            $filename = $request->input('id_checkSheet');

            $file = $request->file('file_datas');
            $name = $file->getClientOriginalName();
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $filename = $request->input('id_checkSheet') . '.' . $extension;

            $file->move($directory, $filename);

            $ck = MasterChecksheet::where('id_checkSheet', '=', $request->input('id_checkSheet'))->first();
            $ck->seal_photo = $filename;
            $ck->save();

            $response = array(
                'status' => true,
                'photo' => asset("/files/checksheet/seal/" . $filename),
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

    public function importContainerPhoto(Request $request)
    {

        try {
            $directory = 'files\checksheet\container';

            $filename = $request->input('id_checkSheet');
            $filename = $request->input('id_checkSheet');

            $file = $request->file('file_datas');
            $name = $file->getClientOriginalName();
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $filename = $request->input('id_checkSheet') . '.' . $extension;

            $file->move($directory, $filename);

            $ck = MasterChecksheet::where('id_checkSheet', '=', $request->input('id_checkSheet'))->first();
            $ck->container_photo = $filename;
            $ck->save();

            $response = array(
                'status' => true,
                'photo' => asset("/files/checksheet/container/" . $filename),
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

    public function importChecklistEvidence(Request $request)
    {

        $checksheet = $request->input('id_checkSheet');
        $photo_id = explode("__", str_replace('-', ' ', $request->input('photo_id')))[1];
        $area = str_replace('ime', ',', explode("_", $photo_id)[0]);
        $area_photo_id = explode("_", $photo_id)[1];

        try {
            $directory = 'files\checksheet\checklist_evidence';

            $file = $request->file('file_datas');
            $name = $file->getClientOriginalName();
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $filename = $checksheet . '_' . $photo_id . '_' . uniqid() . '.' . $extension;
            $file->move($directory, $filename);

            $update = db::connection('ympimis_2')
                ->table('container_checklist_photos')
                ->where('checksheet_id', $checksheet)
                ->where('area', $area)
                ->where('area_photo_id', $area_photo_id)
                ->update([
                    'source' => $filename,
                    'created_by' => Auth::id(),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'photo' => asset("/files/checksheet/checklist_evidence/" . $filename),
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

    public function inputChecklistContainer(Request $request)
    {

        $now = date('Y-m-d H:i:s');
        $checksheet = $request->get('id_checkSheet');
        $pic_id = $request->get('pic_id');
        $leader_id = $request->get('leader_id');
        $checklist = $request->get('checklist_answer');
        $ng_status = false;

        try {

            db::table('master_checksheets')
                ->where('id_checkSheet', $checksheet)
                ->update([
                    'checklist_pic_by' => strtoupper($pic_id),
                    'checklist_known_by' => strtoupper($leader_id),
                    'checklist_checked' => 1,
                ]);

            for ($i = 0; $i < count($checklist); $i++) {
                db::connection('ympimis_2')
                    ->table('container_checklist_results')
                    ->where('checksheet_id', $checksheet)
                    ->where('checklist_id', $checklist[$i]['id'])
                    ->update([
                        'result' => strtoupper($checklist[$i]['result']),
                        'note' => strtoupper($checklist[$i]['note']),
                        'updated_at' => $now,
                    ]);

                if ($checklist[$i]['result'] == 'NG') {
                    $ng_status = true;
                }

            }

            if ($ng_status) {

                $ng = db::connection('ympimis_2')
                    ->table('container_checklist_results')
                    ->where('checksheet_id', $checksheet)
                    ->where('result', 'NG')
                    ->get();

                $photo = db::connection('ympimis_2')
                    ->table('container_checklist_photos')
                    ->where('checksheet_id', $checksheet)
                    ->get();

                $master_checksheet = MasterChecksheet::where('id_checkSheet', $checksheet)->first();

                $data = [
                    'id' => $master_checksheet->id,
                    'ng' => $ng,
                    'photo' => $photo,
                ];

                Mail::to($this->logistic_team)
                    ->bcc(['ympi-mis-ML@music.yamaha.com'])
                    ->send(new SendEmail($data, 'ng_container_checklist'));
            }

            $response = array(
                'status' => true,
                'message' => 'Checklist saved',
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

    public function inputSecurityEvidence(Request $request)
    {
        $checklist_id = $request->input('checklist_id');
        $status = $request->input('status');
        $photo_id = $request->input('photo_id');

        try {
            $directory = 'files\checksheet\checklist_security';

            $file = $request->file('file_datas');
            $name = $file->getClientOriginalName();
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $filename = $checklist_id . '_' . $status . '_' . $photo_id . '_' . uniqid() . '.' . $extension;
            $file->move($directory, $filename);

            $response = array(
                'status' => true,
                'filename' => $filename,
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

    public function inputSecurityChecklist(Request $request)
    {

        $now = date('Y-m-d H:i:s');
        $status = $request->get('status');
        $pic = $request->get('pic');
        $category = $request->get('category');
        $checklist_id = $request->get('checklist_id');
        $driver_name = $request->get('driver_name');
        $vehicle_registration_number = $request->get('vehicle_registration_number');
        $container_number = $request->get('container_number');
        $note = $request->get('note');
        $checklist_answer = json_decode($request->get('checklist_answer'));
        $ng_status = false;

        DB::beginTransaction();
        DB::connection('ympimis_2')->beginTransaction();
        try {

            if ($status == 'CHECK-IN') {
                $insert_checklist = db::connection('ympimis_2')
                    ->table('container_security_checklist_results')
                    ->insert([
                        'category' => $category,
                        'checklist_id' => $checklist_id,
                        'driver_name' => strtoupper($driver_name),
                        'vehicle_registration_number' => str_replace(' ', '', strtoupper($vehicle_registration_number)),
                        'container_number' => str_replace(' ', '', strtoupper($container_number)),
                        'check_in_at' => $now,
                        'check_in_by' => strtoupper($pic),
                        'check_in_note' => strtoupper($note),
                        'created_by' => Auth::id(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                for ($i = 0; $i < count($checklist_answer); $i++) {
                    $insert_result = db::connection('ympimis_2')
                        ->table('container_security_checklist_points')
                        ->insert([
                            'checklist_id' => $checklist_id,
                            'point_check' => $checklist_answer[$i]->point_check,
                            'guidelines' => $checklist_answer[$i]->guidelines,
                            'check_in_result' => $checklist_answer[$i]->result,
                            'check_in_source' => $checklist_answer[$i]->source,
                            'created_by' => Auth::id(),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);

                    if ($checklist_answer[$i]->result == 'NG') {
                        $ng_status = true;
                    }

                }

            } elseif ($status == 'CHECK-OUT') {

                $update_checklist = db::connection('ympimis_2')
                    ->table('container_security_checklist_results')
                    ->where('checklist_id', $checklist_id)
                    ->update([
                        'check_out_at' => $now,
                        'check_out_by' => strtoupper($pic),
                        'check_out_note' => strtoupper($note),
                    ]);

                for ($i = 0; $i < count($checklist_answer); $i++) {
                    $update_result = db::connection('ympimis_2')
                        ->table('container_security_checklist_points')
                        ->where('checklist_id', $checklist_id)
                        ->where('point_check', $checklist_answer[$i]->point_check)
                        ->update([
                            'check_out_result' => $checklist_answer[$i]->result,
                            'check_out_source' => $checklist_answer[$i]->source,
                            'updated_at' => $now,
                        ]);

                    if ($checklist_answer[$i]->result == 'NG') {
                        $ng_status = true;
                    }

                }

            }

            if ($ng_status) {

                $checklist = db::connection('ympimis_2')
                    ->table('container_security_checklist_results')
                    ->where('checklist_id', $checklist_id)
                    ->first();

                $employees = db::table('employee_syncs')->get();
                $employees_formated = [];
                for ($i = 0; $i < count($employees); $i++) {
                    $employees_formated[$employees[$i]->employee_id] = $employees[$i];
                }

                $name = '';
                if ($status == 'CHECK-IN') {
                    if (isset($employees_formated[$checklist->check_in_by])) {
                        $name = $employees_formated[$checklist->check_in_by]->name;
                    }

                } elseif ($status == 'CHECK-OUT') {
                    if (isset($employees_formated[$checklist->check_out_by])) {
                        $name = $employees_formated[$checklist->check_out_by]->name;
                    }

                }

                $data = [
                    'status' => $status,
                    'checklist_id' => $checklist_id,
                    'checklist' => $checklist,
                    'check_by' => $name,
                ];

                $to = array_merge($this->ir_team, $this->logistic_team);
                Mail::to($to)
                    ->bcc(['ympi-mis-ML@music.yamaha.com'])
                    ->send(new SendEmail($data, 'ng_container_checklist_security'));
            }

            DB::commit();
            DB::connection('ympimis_2')->commit();

            $response = array(
                'status' => true,
                'message' => 'Checklist pengecekan truck kontainer berhasil disimpan',
            );
            return Response::json($response);

        } catch (\Exception$e) {
            DB::rollback();
            DB::connection('ympimis_2')->rollback();

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }
    }

}
