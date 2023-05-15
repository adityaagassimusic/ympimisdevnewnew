<?php

namespace App\Http\Controllers;

use App\Accident;
use App\AccidentDetail;
use App\AccidentYokotenkai;
use App\AccidentYokotenkaiDetail;
use App\Approver;
use App\CodeGenerator;
use App\Employee;
use App\EmployeeSync;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\SafetyHolidayForm;
use App\User;
use App\WeeklyCalendar;
use Excel;
use File;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Response;

class StandardizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->dgm = 'PI0109004';
        $this->gm = 'PI1206001';
        $this->gm_acc = 'PI1712018';
        $this->manager_acc = 'PI9902017/Romy Agung Kurniawan';
        $this->dir_acc = 'PI1712018/Kyohei Iida';
        $this->presdir = 'PI1301001/Hiroshi Ura';

        $this->location = [
            'Yamaha Music Manufacturing',
            'Sakuraba Mokuzai',
            'Kitami Mokuzai',
            'Yamaha Piano Sevice',
            'Yamaha Music Japan',
            'Yamaha Music Retailing',
            'Yamaha Sound System',
            'Yamaha Music Entertaiment Holdings',
            'Yamaha Music Comunications',
            'Hi-tech Desain',
            'Yamaha Fine Tech',
            'Yamaha Eye Works',
            'Yamaha Resort',
            'Yamaha Corporate Service',
            'Yamaha Music Foundation',
            'Yamaha Huangzhou (HY)',
            'Yamaha Xiaosan (XY)',
            'Yamaha Tientsin (TY)',
            'Yamaha Electronics Suzhou (YES)',
            'Yamaha Indonesia (YI)',
            'Yamaha Musical Manufacturing Indonesia (YMMI)',
            'Yamaha Musical Products Indonesia (YMPI)',
            'Yamaha Musical Manufacturing Asia (YMMA)',
            'Yamaha Electronics Manufacturing Indonesia (YEMI)',
            'Yamaha Musical Products Asia (YMPA)',
            'Yamaha Electronics Manufacturing (YEM)',
            'Yamaha Music India (YMIN)',
            'Bösendorfer',
            'Nexo',
            'YCJ Head Office',
            'YCJ Kakegawa',
            'YCJ Tokyo',
            'YCJ Osaka',
        ];

        $this->vehicle_inspection_4 = [
            ['code' => 'roda_4_1', 'description' => 'SIM dan STNK'],
            ['code' => 'roda_4_2', 'description' => 'Kondisi Ban Utama dan Cadangan'],
            ['code' => 'roda_4_3', 'description' => 'Lampu Depan dan Sein'],
            ['code' => 'roda_4_4', 'description' => 'Lampu Rem'],
            ['code' => 'roda_4_5', 'description' => 'Klakson'],
            ['code' => 'roda_4_6', 'description' => 'Wiper'],
            ['code' => 'roda_4_7', 'description' => 'Segitiga Pengaman'],
            ['code' => 'roda_4_8', 'description' => 'Dongkrak'],
            ['code' => 'roda_4_9', 'description' => 'Kotak P3K'],
            ['code' => 'roda_4_10', 'description' => 'Sabuk Pengaman'],
            ['code' => 'roda_4_11', 'description' => 'Pembuka Roda'],
            ['code' => 'roda_4_12', 'description' => 'APAR'],
        ];

        $this->vehicle_inspection_2 = array(
            ['code' => 'roda_2_1', 'description' => 'SIM dan STNK'],
            ['code' => 'roda_2_2', 'description' => 'Kondisi Ban'],
            ['code' => 'roda_2_3', 'description' => 'Lampu Depan dan Sein'],
            ['code' => 'roda_2_4', 'description' => 'Lampu Rem'],
            ['code' => 'roda_2_5', 'description' => 'Klakson'],
            ['code' => 'roda_2_6', 'description' => 'Spion'],
            ['code' => 'roda_2_7', 'description' => 'Spedometer'],
            ['code' => 'roda_2_8', 'description' => 'Cover Rantai'],
            ['code' => 'roda_2_9', 'description' => 'Stang Motor'],
            ['code' => 'roda_2_10', 'description' => 'Helm Standar'],
            ['code' => 'roda_2_11', 'description' => 'Plat Kendaraan'],
            ['code' => 'roda_2_12', 'description' => 'Stiker Parking Pass YMPI'],
        );

        $this->ympi_location = ['Assembly', 'Accounting', 'Body Process', 'Exim', 'Material Process', 'Surface Treatment', 'Educational Instrument', 'Standardization', 'QA Process', 'Chemical Process Control', 'Human Resources', 'General Affairs', 'Workshop and Maintenance Molding', 'Production Engineering', 'Maintenance', 'Procurement', 'Production Control', 'Warehouse Material', 'Warehouse Finished Goods', 'Welding Process', 'Case Tanpo CL Body 3D Room', 'Halte dan Trotoar', 'Area Parkir Motor', 'Area Ceklog LCQ – Plating', 'Area Ceklog Buffing ', 'Area Ceklog Assy – Soldering ', 'Area Ceklog Bpro – Pianica', 'Area Lobby dan Office', 'Area Ceklog Recorder', 'Area Ceklog Key Part Process', 'Klinik', 'Area Loker Produksi', 'Kantin', 'OMI', 'Oil Storage (Barat Pianica)', 'Oil Storage (Barat KPP)', 'Flammable Storage', 'Security'];

        $this->ppp = [
            ['code' => '(I)-1', 'name' => ' Amendment of company deed'],
            ['code' => '(I)-2', 'name' => ' Capital increase, capital reduction'],
            ['code' => '(I)-3', 'name' => ' Executive personnel and wage standards'],
            ['code' => '(I)-4', 'name' => ' Selection and revocation of accounting auditor'],
            ['code' => '(I)-5', 'name' => ' Establishment of subsidiaries, capital additions, capital reductions, company mergers, company closures, company sales, company acquisitions, changes from subsidiaries to non-subsidiaries, company business transfers, company restructuring actions '],
            ['code' => '(I)-6', 'name' => ' Transfer of work processes between companies'],
            ['code' => '(I)-7', 'name' => ' Establishment of a new office, office relocation, office deletion'],
            ['code' => '(II)-1', 'name' => ' Management plan and budget (including personnel requirements planning)'],
            ['code' => '(II)-2', 'name' => ' Financial proposal (excluding dividends)'],
            ['code' => '(II)-3', 'name' => ' Dividend'],
            ['code' => '(II)-4', 'name' => ' Recruitment of permanent employees and other employees (limit on the number of employees recruited)'],
            ['code' => '(III)-1', 'name' => ' Enter new business / withdraw existing business'],
            ['code' => '(III)-2', 'name' => ' Use of Yamaha brand'],
            ['code' => '(III)-3', 'name' => ' Licensing of intellectual property rights (outgoing / receiving), transfer'],
            ['code' => '(III)-4', 'name' => ' Litigation, suit, withdrawal, settlement, dispute settlement'],
            ['code' => '(III)-5', 'name' => ' Changes in accounting policies'],
            ['code' => '(III)-6', 'name' => ' Website operation management'],
            ['code' => '(III)-7', 'name' => ' Changes in Organization, PKB, titles. Voluntary retirement or dismissal for reorganization'],
            ['code' => '(III)-8', 'name' => ' Enactment and revision of important rules affecting group management'],
            ['code' => '(III)-9', 'name' => ' Exception handling when matters cannot be complied with Group regulations'],
            ['code' => '(III)-10', 'name' => ' Contract (except procurement agreement)'],
            ['code' => '(III)-11', 'name' => ' Information system development and network construction'],
            ['code' => '(III)-12', 'name' => ' IT system investment'],
            ['code' => '(III)-13', 'name' => ' Changes in accounting system'],
            ['code' => '(III)-14', 'name' => ' Recognition / Disciplinary'],
            ['code' => '(III)-15', 'name' => ' Other important matters affecting group management'],
            ['code' => '(III)-16', 'name' => ' Reward/punishment'],
            ['code' => '(III)-17', 'name' => ' Additions and amendments to the Global Privacy Policy article'],
            ['code' => '(III)-18', 'name' => ' Other matters that can have an important influence on group management'],
            ['code' => '(IV)-1', 'name' => ' Purchase, disposal, leasing and cancellation of land'],
            ['code' => '(IV)-2', 'name' => ' Lease and cancellation of land'],
            ['code' => '(IV)-3', 'name' => ' Purchase, construction, disposal, leasing and cancellation of buildings'],
            ['code' => '(IV)-4', 'name' => ' Lease and cancellation of building'],
            ['code' => '(IV)-5', 'name' => ' Acquisition, remodeling, repair, disposal, lease of fixed assets (machines, molds, etc)'],
            ['code' => '(V)-1', 'name' => ' Financing from new lenders and increase / decrease in borrowing capacity'],
            ['code' => '(V)-2', 'name' => ' Start transactions with new banks and suspend transactions with existing banks'],
            ['code' => '(V)-3', 'name' => ' Funding from Yamaha Corporation and group companies'],
            ['code' => '(V)-4', 'name' => ' Capital turnover without principal collateral (in principle not allowed)'],
            ['code' => '(V)-5', 'name' => ' Debt guarantee (in principle not allowed)'],
            ['code' => '(V)-6', 'name' => ' Provide guarantees and amendments (not allowed in principle)'],
            ['code' => '(V)-7', 'name' => ' Capital investment, capital increase payment, (financing) investment and loan, disposal'],
            ['code' => '(V)-8', 'name' => ' Purchase and sale of rights such as golf membership etc.'],
            ['code' => '(V)-9', 'name' => ' Advance payment'],
            ['code' => '(V)-10', 'name' => ' Bad debt write-offs'],
            ['code' => '(V)-11', 'name' => ' Disposal, write-down and loss of inventory'],
            ['code' => '(V)-12', 'name' => ' Donations and other free grants'],
            ['code' => '(V)-13', 'name' => ' Payments to people who are part of government institutions or public institutions, excluding political contributions or other types of payments (including party tickets)'],
            ['code' => '(V)-14', 'name' => ' Implementation of new payment methods other than bank transfer'],
            ['code' => 'KG01', 'name' => ' Conclusion / revision / cancellation of basic transaction contract'],
            ['code' => 'KS01', 'name' => ' Change of internal organization, transfer of internal business'],
            ['code' => 'KS02', 'name' => ' Revision and elimination of various procedures and rules, establishment of company internal procedures, and special procedures'],
            ['code' => 'KS03', 'name' => ' Revision and elimination of various procedures and rules, establishment of departmental procedures'],
            ['code' => 'KG01', 'name' => ' Establish, revise and terminate the basic transaction agreement'],
            ['code' => 'KG02', 'name' => ' Conclusion / revision / cancellation of OEM / ODM Maser transaction agreement'],
            ['code' => 'KG03', 'name' => ' Technical alliances and joint R & D'],
            ['code' => 'KG04', 'name' => ' Confidentiality agreement (without financial guarantee)'],
            ['code' => 'KG05', 'name' => ' Personal delegation agreement (contract with an individual)'],
            ['code' => 'KG06', 'name' => ' Staffing contract (inter-company contract)'],
            ['code' => 'KG07', 'name' => ' Business consignment (contract) contract (contract with company)'],
            ['code' => 'KG08', 'name' => ' Contract for collection/transportation/disposal of waste (incl. purchase/sale of valuables)'],
            ['code' => 'KG09', 'name' => ' Contracts that do not fall under any of the other rules set forth in the authority rules'],
            ['code' => 'KG10', 'name' => ' Decision on start / change / stop of production technology R & D theme'],
            ['code' => 'KG11', 'name' => ' Sales prices of products (FG, KD, service parts)'],
            ['code' => 'KG12', 'name' => ' Determination and change of Standard Price'],
            ['code' => 'KG13', 'name' => ' Determination and change of unit price/unit price of production materials and purchasing requirements'],
            ['code' => 'KG14', 'name' => 'Determination and changes to production plans and purchase of accompanying material parts'],
            ['code' => 'KG15', 'name' => ' Pre-ordering of production materials without deciding or revising production plans'],
            ['code' => 'KG16', 'name' => ' Determination of product discontinuation'],
            ['code' => 'KG17', 'name' => ' Decision to stop operations'],
            ['code' => 'KG18', 'name' => ' '],
            ['code' => 'KG19', 'name' => ' Entertainment fee'],
            ['code' => 'KG20', 'name' => ' Losses (compensation for loss, theft, loss, etc.)'],
            ['code' => 'KG21', 'name' => ' Purchase of goods and services that don`t fall under any of the other rules and use of expenses'],
            ['code' => 'KG22', 'name' => ' Market complaint measures'],
            ['code' => 'KG23', 'name' => ' Borrowing funds'],
            ['code' => 'KG24', 'name' => ' Credit deferral'],
            ['code' => 'KG25', 'name' => ' Currency exchange rate agreement'],
            ['code' => 'KG26', 'name' => ' Group membership'],
            ['code' => 'KG27', 'name' => ' Delegate representatives from industry organizations, civic groups, and similar institutions'],
            ['code' => 'KG28', 'name' => ' Application for overseas service'],
            ['code' => 'KG29', 'name' => ' Pengajuan dinas luar di dalam negeri'],
            ['code' => 'KG30', 'name' => ' Invite YCJ supporters'],
            ['code' => 'KJ01', 'name' => ' Individual recruitment'],
            ['code' => 'KJ02', 'name' => ' Transfer, individual task dispatch'],
            ['code' => 'KJ03', 'name' => ' Mutation'],
            ['code' => 'KJ04', 'name' => ' Position Promotion'],
            ['code' => 'KJ05', 'name' => ' Wages and Bonuses'],
            ['code' => 'KJ06', 'name' => ' Termination of Employment'],
        ];

        $this->storage_location = [
            'CL21',
            'CL51',
            'CL91',
            'CLA0',
            'CLA2',
            'CLB9',
            'CS91',
            'FA0R',
            'FA1R',
            'FL21',
            'FL51',
            'FL91',
            'FLA0',
            'FLA1',
            'FLA2',
            'FLT9',
            'LA0R',
            'PN91',
            'PNR9',
            'RC11',
            'RC91',
            'SA0R',
            'SA1R',
            'SX21',
            'SX51',
            'SX91',
            'SXA0',
            'SXA1',
            'SXA2',
            'SXT9',
            'VN11',
            'VN21',
            'VN51',
            'VN91',
            'PXMP',
            'VNA0',
            'ZPA0',
            'MSTK',
            'OTHR',
            'WPCS',
            'WPPN',
            'WPRC',
            '214',
            'MMJR',
        ];
    }

    public function indexVehicleMenu()
    {
        return view('standardization.vehicle.index_vehicle_menu')
        ->with('title', 'Vehicle Menu')
        ->with('title_jp', '品保')
        ->with('page', 'Vehicle Menu')
        ->with('jpn', '品保');
    }


    public function indexCalibration()
    {
        $title = 'Kontrol Kalibrasi Alat Ukur';
        $title_jp = '';

        $departments = db::table('departments')->whereNull('deleted_at')
        ->orderBy('department_name', 'ASC')->get();

        return view('standardization.calibration', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'departments' => $departments,
        ));
    }

    public function mailCalibration()
    {
        $calibrations = db::connection('ympimis_2')->select("SELECT
            *,
            TIMESTAMPDIFF( DAY, now(), valid_to ) AS date_diff
            FROM
            calibrations
            WHERE
            status IN (
            'Akan Kalibrasi',
            'Harus Kalibrasi')");

        if (count($calibrations) > 0) {
            $data = [
                'calibrations' => $calibrations,
            ];

            Mail::to(['rani.nurdiyana.sari@music.yamaha.com'])
            ->cc(['widura@music.yamaha.com', 'yayuk.wahyuni@music.yamaha.com'])
            ->bcc('ympi-mis-ML@music.yamaha.com')
            ->send(new SendEmail($data, 'calibration_reminder'));
        }
    }

    public function fetchCalibration(Request $request)
    {
        $calibrations = db::connection('ympimis_2')->select("SELECT
            *,
            TIMESTAMPDIFF( DAY, now(), valid_to ) AS date_diff,
            DATE_FORMAT( valid_to, '%Y-%m' ) AS month_to
            FROM
            calibrations
            WHERE deleted_at IS NULL");
        $calibration_logs = db::connection('ympimis_2')->table('calibration_logs')->whereNull('deleted_at')->orderBy('updated_at', 'DESC')->get();
        $calibration_attachments = db::connection('ympimis_2')->table('calibration_attachments')->whereNull('deleted_at')->orderBy('updated_at', 'DESC')->get();

        $response = array(
            'status' => true,
            'calibrations' => $calibrations,
            'calibration_logs' => $calibration_logs,
            'calibration_attachments' => $calibration_attachments,
        );
        return Response::json($response);
    }

    public function inputCalibration(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', 'calibration_id')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $calibration_id = $code_generator->prefix . $number;

            $valid_from = date('Y-m-d', strtotime($request->input('from')));
            $valid_to = date('Y-m-d', strtotime('+' . $request->input('frequency'), strtotime($valid_from)));

            $filename = "";
            if (count($request->file('attachment')) > 0) {
                $file_destination = 'files/calibrations';
                $file = $request->file('attachment');
                $filename = $calibration_id . date('YmdHis') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);

                db::connection('ympimis_2')->table('calibration_attachments')
                ->insert([
                    'calibration_id' => $calibration_id,
                    'file_name' => $filename,
                    'vendor_name' => $request->input('vendor'),
                    'valid_from' => $valid_from,
                    'valid_to' => $valid_to,
                    'created_by' => Auth::user()->username,
                    'created_by_name' => Auth::user()->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $imagename = "default.png";
            if (count($request->file('image')) > 0) {
                $file_destination = 'files/calibrations';
                $file = $request->file('image');
                $imagename = $calibration_id . '.' . $request->input('image_extension');
                $file->move($file_destination, $imagename);
            }

            db::connection('ympimis_2')->table('calibrations')
            ->insert([
                'calibration_id' => $calibration_id,
                'category' => $request->input('category'),
                'instrument_name' => $request->input('name'),
                'instrument_brand' => $request->input('brand'),
                'instrument_type' => $request->input('type'),
                'serial_number' => $request->input('serial'),
                'range' => $request->input('range'),
                'unit' => $request->input('unit'),
                'department' => $request->input('department'),
                'location' => $request->input('location'),
                'frequency' => $request->input('frequency'),
                'reminder' => $request->input('reminder'),
                'valid_from' => $valid_from,
                'valid_to' => $valid_to,
                'tolerance' => $request->input('tolerance'),
                'correction' => $request->input('correction'),
                'calibration_result' => $request->input('result'),
                'vendor_name' => $request->input('vendor'),
                'status' => $request->input('status'),
                'remark' => $request->input('remark'),
                'image_file' => $imagename,
                'created_by' => Auth::user()->username,
                'created_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            db::connection('ympimis_2')->table('calibration_logs')
            ->insert([
                'calibration_id' => $calibration_id,
                'category' => $request->input('category'),
                'instrument_name' => $request->input('name'),
                'instrument_brand' => $request->input('brand'),
                'instrument_type' => $request->input('type'),
                'serial_number' => $request->input('serial'),
                'range' => $request->input('range'),
                'unit' => $request->input('unit'),
                'department' => $request->input('department'),
                'location' => $request->input('location'),
                'frequency' => $request->input('frequency'),
                'reminder' => $request->input('reminder'),
                'valid_from' => $valid_from,
                'valid_to' => $valid_to,
                'tolerance' => $request->input('tolerance'),
                'correction' => $request->input('correction'),
                'calibration_result' => $request->input('result'),
                'vendor_name' => $request->input('vendor'),
                'status' => $request->input('status'),
                'remark' => $request->input('remark'),
                'image_file' => $imagename,
                'log' => 'Created',
                'created_by' => Auth::user()->username,
                'created_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $response = array(
                'status' => true,
                'message' => 'Data berhasil tersimpan',
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

    public function editCalibration(Request $request)
    {
        try {
            $calibration_id = $request->get('calibration_id');
            $calibration = db::connection('ympimis_2')->table('calibrations')->where('calibration_id', '=', $calibration_id)->first();

            $valid_from = date('Y-m-d', strtotime($request->input('from')));
            $valid_to = date('Y-m-d', strtotime('+' . $request->input('frequency'), strtotime($valid_from)));

            if (count($request->file('attachment')) > 0) {
                $filename = "";
                $file_destination = 'files/calibrations';
                $file = $request->file('attachment');
                $filename = $calibration_id . date('YmdHis') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);

                db::connection('ympimis_2')->table('calibration_attachments')
                ->insert([
                    'calibration_id' => $calibration_id,
                    'file_name' => $filename,
                    'vendor_name' => $request->input('vendor'),
                    'valid_from' => $valid_from,
                    'valid_to' => $valid_to,
                    'created_by' => Auth::user()->username,
                    'created_by_name' => Auth::user()->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $imagename = $calibration->image_file;
            if (count($request->file('image')) > 0) {
                $file_destination = 'files/calibrations';
                $file = $request->file('image');
                $imagename = $calibration_id . '.' . $request->input('image_extension');
                $file->move($file_destination, $imagename);
            }

            db::connection('ympimis_2')->table('calibrations')
            ->where('calibration_id', '=', $calibration_id)
            ->update([
                'category' => $request->input('category'),
                'instrument_name' => $request->input('name'),
                'instrument_brand' => $request->input('brand'),
                'instrument_type' => $request->input('type'),
                'serial_number' => $request->input('serial'),
                'range' => $request->input('range'),
                'unit' => $request->input('unit'),
                'department' => $request->input('department'),
                'location' => $request->input('location'),
                'frequency' => $request->input('frequency'),
                'reminder' => $request->input('reminder'),
                'valid_from' => $valid_from,
                'valid_to' => $valid_to,
                'tolerance' => $request->input('tolerance'),
                'correction' => $request->input('correction'),
                'calibration_result' => $request->input('result'),
                'vendor_name' => $request->input('vendor'),
                'status' => $request->input('status'),
                'remark' => $request->input('remark'),
                'image_file' => $imagename,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            db::connection('ympimis_2')->table('calibration_logs')
            ->insert([
                'calibration_id' => $calibration_id,
                'category' => $request->input('category'),
                'instrument_name' => $request->input('name'),
                'instrument_brand' => $request->input('brand'),
                'instrument_type' => $request->input('type'),
                'serial_number' => $request->input('serial'),
                'range' => $request->input('range'),
                'unit' => $request->input('unit'),
                'department' => $request->input('department'),
                'location' => $request->input('location'),
                'frequency' => $request->input('frequency'),
                'reminder' => $request->input('reminder'),
                'valid_from' => $valid_from,
                'valid_to' => $valid_to,
                'tolerance' => $request->input('tolerance'),
                'correction' => $request->input('correction'),
                'calibration_result' => $request->input('result'),
                'vendor_name' => $request->input('vendor'),
                'status' => $request->input('status'),
                'remark' => $request->input('remark'),
                'image_file' => $imagename,
                'log' => 'Updated',
                'created_by' => Auth::user()->username,
                'created_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'Data berhasil tersimpan',
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

    public function indexVehicleMonitoring()
    {
        $title = 'Monitoring Pemeriksaan Kendaraan';
        $title_jp = '';

        $periods = db::connection('ympimis_2')->select("SELECT DISTINCT
            DATE_FORMAT( inspection_date, '%Y-%m' ) AS period_date,
            DATE_FORMAT( inspection_date, '%Y %M' ) AS period
            FROM
            vehicle_inspections
            ORDER BY
            inspection_date DESC");

        return view('standardization.vehicle.monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'periods' => $periods,
        ));
    }

    public function fetchVehicleMonitoring(Request $request)
    {

        $period = date('Y-m');

        if (strlen($request->get('period')) > 0) {
            $period = date('Y-m', strtotime($request->get('period')));
        }
        $where_period = "AND DATE_FORMAT(inspection_date, '%Y-%m') = '" . $period . "'";

        $vehicle_inspections = db::connection('ympimis_2')
        ->select("SELECT
            vi.id,
            vi.category,
            vi.inspection_date,
            vi.vehicle_number,
            vi.employee_id,
            vi.employee_name,
            vi.department_shortname,
            vi.department,
            vi.remark,
            vi.upload_image,
            vi.created_by,
            vi.created_by_name,
            vi.created_at,
            GROUP_CONCAT(vn.registration_number) AS registration_number
            FROM
            vehicle_inspections AS vi
            LEFT JOIN vehicle_numbers AS vn ON vn.employee_id = vi.employee_id
            WHERE
            vi.deleted_at IS NULL
            " . $where_period . "
            GROUP BY
            vi.id,
            vi.category,
            vi.inspection_date,
            vi.vehicle_number,
            vi.employee_id,
            vi.employee_name,
            vi.department_shortname,
            vi.department,
            vi.remark,
            vi.upload_image,
            vi.created_by,
            vi.created_by_name,
            vi.created_at
            ORDER BY
            vi.employee_id ASC");

        $vehicle_inspection_details = db::connection('ympimis_2')
        ->select("SELECT
            vehicle_inspection_details.id,
            vehicle_inspection_details.inspection_date,
            vehicle_inspection_details.employee_id,
            vehicle_inspection_details.description,
            vehicle_inspection_details.status,
            vehicle_inspection_details.evidence_file
            FROM
            vehicle_inspection_details
            WHERE
            deleted_at IS NULL
            " . $where_period . "
            ORDER BY
            employee_id ASC");

//  return DataTables::of($temp)
// ->addColumn('sync', function($temp){
//     return '<button style="width: 50%; height: 100%;" onclick="sync(\''.$temp->id.'\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-remove"></i></span></button>';
// })
// ->rawColumns([
//     'sync' => 'sync'
// ])
// ->make(true);

        $response = array(
            'status' => true,
            'vehicle_inspections' => $vehicle_inspections,
            'vehicle_inspection_details' => $vehicle_inspection_details,
            'period' => date('F Y', strtotime($period)),
        );
        return Response::json($response);
    }

    public function indexVehicleForm($id)
    {
        $title = 'Berita Acara Pemeriksaan Kendaraan';
        $title_jp = '';

        $employee_syncs = db::select("SELECT
            es.employee_id,
            es.name,
            d.department_shortname
            FROM
            employee_syncs AS es
            LEFT JOIN departments AS d ON d.department_name = es.department
            WHERE
            (
            end_date IS NULL
            OR end_date >= date(
            now()))
            AND es.department IS NOT NULL
            AND es.grade_code <> 'J0-'");

        if ($id == 'roda_4') {
            $vehicle_inspections = $this->vehicle_inspection_4;
        }

        if ($id == 'roda_2') {
            $vehicle_inspections = $this->vehicle_inspection_2;
        }

        $employee_vehicle = DB::connection('ympimis_2')->SELECT("
            SELECT
                * 
            FROM
            `employee_vehicles` 
            left join (SELECT
            employee_id as emp_id,
            nopol as nopol_2,
            date_stnk as date_stnk_2,
            file_stnk as file_stnk_2,
            file_kendaraan as file_kendaraan_2
            FROM
            `employee_vehicles` 
            WHERE
            date_sim is null) as dua on dua.emp_id = employee_vehicles.employee_id
            WHERE
            date_sim is not null
            order by id desc");

        return view('standardization.vehicle.form', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'vehicle_inspections' => $vehicle_inspections,
            'employee_syncs' => $employee_syncs,
            'employee_vehicle' => $employee_vehicle,
            'category' => $id,
        ));
    }

    public function fetchVehicleForm(Request $request)
    {
        $vehicle_inspections = db::connection('ympimis_2')->select("SELECT
            vi.department_shortname,
            vi.employee_id,
            vi.employee_name,
            count( vid.id ) AS jumlah,
            vi.created_at,
            vi.category,
            vi.remark,
            vi.upload_image
            FROM
            vehicle_inspections AS vi
            LEFT JOIN vehicle_inspection_details AS vid ON vid.employee_id = vi.employee_id
            AND vid.inspection_date = vi.inspection_date
            WHERE
            vi.created_by = '" . Auth::user()->username . "'
            GROUP BY
            vi.department_shortname,
            vi.employee_id,
            vi.employee_name,
            vi.created_at,
            vi.category,
            vi.remark,
            vi.upload_image");

        $response = array(
            'status' => true,
            'vehicle_inspections' => $vehicle_inspections,
        );
        return Response::json($response);
    }

    public function deleteVehicleInspection(Request $request)
    {
        try {
            $inspection_detail = db::connection('ympimis_2')->table('vehicle_inspection_details')
            ->where('id', '=', $request->input('id'))
            ->first();

            if (count($inspection_detail) == 1) {
                $delete_inspection = db::connection('ympimis_2')->table('vehicle_inspections')
                ->where('inspection_date', '=', $inspection_detail->inspection_date)
                ->where('employee_id', '=', $inspection_detail->employee_id)
                ->delete();
            }

            $delete_inspection_detail = db::connection('ympimis_2')->table('vehicle_inspection_details')
            ->where('id', '=', $request->input('id'))
            ->delete();

            $response = array(
                'status' => true,
                'message' => 'Pelanggaran berhasil dihapus',
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

    public function updateVehicleInspection(Request $request)
    {
        try {
            $file_destination = 'files/vehicle_inspection';
            $filename = null;

            if ($request->file('attachment')) {
                $file = $request->file('attachment');
                $filename = 'ev_' . $request->input('id') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);
            }

            $update_inspection = db::connection('ympimis_2')->table('vehicle_inspection_details')
            ->where('id', '=', $request->input('id'))
            ->update([
                'status' => date('Y-m-d H:i:s'),
                'evidence_file' => $filename,
            ]);

            $response = array(
                'status' => true,
                'message' => 'Pelanggaran berhasil ditutup',
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

    public function inputVehicleForm(Request $request)
    {
        try {
            $employee_sync = db::table('employee_syncs')
            ->leftJoin('departments', 'departments.department_name', '=', 'employee_syncs.department')
            ->where('employee_id', '=', $request->input('employee_id'))
            ->select('employee_syncs.employee_id', 'employee_syncs.name', 'employee_syncs.department', 'employee_syncs.section', 'employee_syncs.group', 'employee_syncs.sub_group', 'departments.department_shortname')
            ->first();

            $file_destination = 'files/vehicle_inspection';
            $filename = null;

            if ($request->file('attachment')) {
                $file = $request->file('attachment');
                $filename = $request->input('employee_id') . '_' . date('YmdHis') . '_.' . $request->input('extension');
                $file->move($file_destination, $filename);
            }

            $input_inspection = db::connection('ympimis_2')
            ->table('vehicle_inspections')
            ->insert([
                'category' => $request->input('category'),
                'inspection_date' => date('Y-m-d'),
                'vehicle_number' => strtoupper($request->input('vehicle_number')),
                'employee_id' => $request->input('employee_id'),
                'employee_name' => $employee_sync->name,
                'department_shortname' => $employee_sync->department_shortname,
                'department' => $employee_sync->department,
                'section' => $employee_sync->section,
                'group' => $employee_sync->group,
                'sub_group' => $employee_sync->sub_group,
                'remark' => $request->input('remark'),
                'upload_image' => $filename,
                'created_by' => Auth::user()->username,
                'created_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $inspections = explode(',', $request->input('inspections'));

            foreach ($inspections as $inspection) {
                $input_inspection_detail = db::connection('ympimis_2')
                ->table('vehicle_inspection_details')
                ->insert([
                    'inspection_date' => date('Y-m-d'),
                    'employee_id' => $request->input('employee_id'),
                    'description' => $inspection,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $response = array(
                'status' => true,
                'message' => 'Hasil inspeksi kendaraan berhasil disimpan',
                'employee_sync' => $employee_sync,
                'jumlah' => count($inspections),
                'created_at' => date('Y-m-d H:i:s'),
                'category' => $request->input('category'),
                'remark' => $request->input('remark'),
                'upload_image' => $filename,
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

    public function indexDocumentPublish()
    {
        $title = 'Penerbitan IK DM DL';
        $title_jp = '';

        $departments = db::table('departments')->whereNull('deleted_at')
        ->orderBy('department_name', 'ASC')->get();
        $role = Auth::user()->role_code;

        return view('standardization.document_control.document_index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'departments' => $departments,
            'role' => $role
        ));
    }

    public function indexDocument()
    {
        $title = 'IK DM DL Document Control';
        $title_jp = 'IK・DM・DLの管理';

        $departments = db::table('departments')->whereNull('deleted_at')
        ->orderBy('department_name', 'ASC')->get();
        $role = Auth::user()->role_code;

        return view('standardization.document_control.document_index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'departments' => $departments,
            'role' => $role
        ));
    }

    public function deleteDocument(Request $request)
    {
        try {
            db::connection('ympimis_2')->table('documents')
            ->where('document_id', '=', $request->get('document_id'))
            ->update([
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            db::connection('ympimis_2')->table('document_attachments')
            ->where('document_id', '=', $request->get('document_id'))
            ->update([
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'Data berhasil dihapus.',
                'document_id' => $request->get('document_id'),
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

    public function deleteDocumentVersion(Request $request)
    {
        try {
            $id = $request->get('id');
            $delete = DB::connection('ympimis_2')->table('document_attachments')->where('id',$id)->delete();
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

    public function fetchDocument()
    {

        $documents = db::connection('ympimis_2')->table('documents')
        ->whereNull('deleted_at')
        ->orderBy('version_date', 'DESC')
        ->get();

        $document_attachments = db::connection('ympimis_2')->table('document_attachments')
        ->whereNull('deleted_at')
        ->orderBy('version_date', 'DESC')
        ->get();

        $response = array(
            'status' => true,
            'documents' => $documents,
            'document_attachments' => $document_attachments,
        );
        return Response::json($response);
    }

    public function editDocument(Request $request)
    {
        try {
            $update_document = db::connection('ympimis_2')->table('documents')
            ->where('document_id', '=', $request->get("document_id"))
            ->update([
                'department_name' => $request->get("department_name"),
                'department_shortname' => $request->get("department_shortname"),
                'category' => $request->get("category"),
                'document_category' => $request->input("document_category"),
                'document_number' => $request->get("document_number"),
                'title' => $request->get("title"),
                'keywords' => '',
                'status' => $request->get("status_document"),
                'remark' => '',
                'created_by' => Auth::user()->username,
                'created_by_name' => Auth::user()->name,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'Perubahan dokumen berhasil tersimpan',
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

    public function versionDocument(Request $request)
    {
        try {
            $document_id = $request->input('document_id');

            $document_attachment = db::connection('ympimis_2')->table('document_attachments')
            ->where('version', '=', $request->input('version'))
            ->where('document_id', '=', $document_id)
            ->first();

            $file_destination = 'files/standardization/documents';

            $file_pdf = $request->file('attachment_pdf');
            $filename_pdf = $document_id . '_' . $request->input("version") . '_' . $request->input('file_name_pdf') . '.' . $request->input('extension_pdf');
            $file_pdf->move($file_destination, $filename_pdf);

            $filename_xls = "";
            if ($document_attachment != null) {
                $filename_xls = $document_attachment->file_name_xls;
            }

            if (count($request->file('attachment_xls')) > 0) {
                $file_xls = $request->file('attachment_xls');
                $filename_xls = $document_id . '_' . $request->input("version") . '_' . $request->input('file_name_xls') . '.' . $request->input('extension_xls');
                $file_xls->move($file_destination, $filename_xls);
            }

            if ($document_attachment != null) {
                $update_document_attachment = db::connection('ympimis_2')->table('document_attachments')
                ->where('version', '=', $request->input('version'))
                ->where('document_id', '=', $document_id)
                ->update([
                    'file_name_pdf' => $filename_pdf,
                    'file_name_xls' => $filename_xls,
                    'created_by' => Auth::user()->username,
                    'created_by_name' => Auth::user()->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $insert_document_attachment = db::connection('ympimis_2')->table('document_attachments')
                ->insert([
                    'document_id' => $document_id,
                    'version' => $request->input("version"),
                    'version_date' => $request->input("version_date"),
                    'file_name_pdf' => $filename_pdf,
                    'file_name_xls' => $filename_xls,
                    'created_by' => Auth::user()->username,
                    'created_by_name' => Auth::user()->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $max_document_attachment = db::connection('ympimis_2')->table('document_attachments')
            ->where('document_id', '=', $document_id)
            ->orderBy('version', 'DESC')
            ->first();

            $update_document = db::connection('ympimis_2')->table('documents')
            ->where('document_id', '=', $document_id)
            ->update([
                'version' => $max_document_attachment->version,
                'version_date' => $max_document_attachment->version_date,
                'file_name_pdf' => $max_document_attachment->file_name_pdf,
                'file_name_xls' => $max_document_attachment->file_name_xls,
                'keywords' => '',
                'remark' => '',
                'created_by' => Auth::user()->username,
                'created_by_name' => Auth::user()->name,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'Revisi dokumen berhasil diperbaharui.',
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

    public function inputDocument(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', 'std_document')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $document_id = $number;

            $file_destination = 'files/standardization/documents';

            $file_pdf = $request->file('attachment_pdf');
            $filename_pdf = 'SDS_' . $request->input("version") . '_' . $request->input('file_name_pdf') . '.' . $request->input('extension_pdf');
            $file_pdf->move($file_destination, $filename_pdf);

            $filename_xls = "";

            if (count($request->file('attachment_xls')) > 0) {
                $file_xls = $request->file('attachment_xls');
                $filename_xls = $document_id . '_' . $request->input("version") . '_' . $request->input('file_name_xls') . '.' . $request->input('extension_xls');
                $file_xls->move($file_destination, $filename_xls);
            }

            $insert_document_attachment = db::connection('ympimis_2')->table('document_attachments')
            ->insert([
                'document_id' => $document_id,
                'version' => $request->input("version"),
                'version_date' => $request->input("version_date"),
                'file_name_pdf' => $filename_pdf,
                'file_name_xls' => $filename_xls,
                'created_by' => Auth::user()->username,
                'created_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $insert_document = db::connection('ympimis_2')->table('documents')->insert([
                'document_id' => $document_id,
                'department_name' => $request->input("department_name"),
                'department_shortname' => $request->input("department_shortname"),
                'category' => $request->input("category"),
                'document_category' => $request->input("document_category"),
                'document_number' => $request->input("document_number"),
                'title' => $request->input("title"),
                'version' => $request->input("version"),
                'version_date' => $request->input("version_date"),
                'status' => $request->input("status"),
                'file_name_pdf' => $filename_pdf,
                'file_name_xls' => $filename_xls,
                'keywords' => '',
                'remark' => '',
                'created_by' => Auth::user()->username,
                'created_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $response = array(
                'status' => true,
                'message' => 'Dokumen baru berhasil tersimpan',
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

    public function index()
    {
        return view('standardization.index_kecelakaan')
        ->with('title', 'Kecelakaan Kerja')
        ->with('title_jp', '労働災害')
        ->with('page', 'Kecelakaan Kerja')
        ->with('jpn', '');
    }

    public function indexKecelekaan($category)
    {
        if ($category == 'kerja') {
            $title = 'Kecelakaan Kerja';
            $title_jp = '労働災害';

            $emp = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
            ->first();

            // if ($emp) {

            // }else{
            //     $emp = "";
            // }

            return view('standardization.kecelakaan_kerja', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'employee' => $emp,
                'location' => $this->location,
            ))->with('page', 'Kecelakaan Kerja')
            ->with('head', 'Laporan Kecelakaan');
        }

        if ($category == 'lalu_lintas') {
            $title = 'Kecelakaan Lalu Lintas';
            $title_jp = '';

            $emp = EmployeeSync::select('employee_id', 'name', 'position', 'department', 'section', 'group')
            ->get();
// ->whereNull('end_date')

            return view('standardization.kecelakaan_lalu_lintas', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'employee' => $emp,
                'location' => $this->location,
            ))->with('page', 'Kecelakaan Lalu Lintas')
            ->with('head', 'Laporan Kecelakaan');
        }
    }

    public function fetchKecelakaan($category)
    {
        if ($category == 'kerja') {
            $accident = db::select("SELECT * FROM accidents where category = 'Kerja' order by date_incident desc");
        }
        if ($category == 'lalu_lintas') {
            $accident = db::select("SELECT * FROM accidents where category = 'Lalu Lintas' order by date_incident desc");
        }
        $response = array(
            'status' => true,
            'accident' => $accident,
        );
        return Response::json($response);

    }

    public function createKecelakaan(Request $request, $id)
    {
        try {
            if ($id == "kerja") {

                $tujuan_upload = 'files/kecelakaan/kecelakaan_kerja';

                $lop = $request->input('lop');

                for ($i = 1; $i <= $lop; $i++) {
                    if (count($request->file('illustration_image' . $i)) > 0) {
                        $file = $request->file('illustration_image' . $i);
                        $nama = $file->getClientOriginalName();

                        $filename = pathinfo($nama, PATHINFO_FILENAME);
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);
                        $filename = md5($filename . date('YmdHisa')) . '.' . $extension;

                        $file->move($tujuan_upload, $filename);

                        $data[] = $filename;
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Please select image to attach',
                        );
                        return Response::json($response);
                    }

                    $illustration_image = json_encode($data);

                    $data_detail[] = $request->input('illustration_detail' . $i);

                    $illustration_detail = json_encode($data_detail);
                }

                $accident = new Accident([
                    'category' => 'Kerja',
                    'accident_number' => null,
                    'submission_date' => $request->input('submission_date'),
                    'employee_id' => $request->input('emp_id'),
                    'employee_name' => $request->input('emp_name'),
                    'employee_department' => $request->input('emp_department'),
                    'position' => $request->input('position'),
                    'location' => $request->input('location'),
                    'area' => $request->input('area'),
                    'date_incident' => $request->input('date_incident'),
                    'time_incident' => $request->input('time_incident'),
                    'detail_incident' => $request->input('detail_incident'),
                    'condition' => $request->input('condition'),
                    'loss_time' => $request->input('loss_time'),
                    'recovery_time' => $request->input('recovery_time'),
                    'loss_cost' => $request->input('loss_cost'),
                    'illustration_image' => $illustration_image,
                    'illustration_detail' => $illustration_detail,
                    'yokotenkai' => $request->input('yokotenkai'),
                    'created_by' => Auth::id(),
                ]);

                $accident->save();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'potrait');

                $pdf->loadView('standardization.report_kecelakaan', array(
                    'data' => $accident,
                ));

                $pdf->save(public_path() . "/kecelakaan_list/kecelakaan_kerja/Kecelakaan " . date('d-M-y', strtotime($accident->date_incident)) . " " . $accident->location . ".pdf");

                $response = array(
                    'status' => true,
                    'message' => 'Data Kecelakaan Kerja Berhasil Ditambahkan',
                );
                return Response::json($response);
            } else if ($id == "lalu_lintas") {
                $tujuan_upload = 'files/kecelakaan/kecelakaan_lalu_lintas';

                $lop = $request->input('lop');
                for ($i = 1; $i <= $lop; $i++) {
                    if (count($request->file('illustration_image')) > 0) {
                        $file = $request->file('illustration_image');
                        $nama = $file->getClientOriginalName();

                        $filename = pathinfo($nama, PATHINFO_FILENAME);
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);
                        $filename = md5($filename . date('YmdHisa')) . '.' . $extension;

                        $file->move($tujuan_upload, $filename);

                        $data[] = $filename;
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Please select image to attach',
                        );
                        return Response::json($response);
                    }

                    $illustration_image = json_encode($data);
                    $illustration_detail = null;
                }

                $accident = new Accident([
                    'category' => 'Lalu Lintas',
                    'accident_number' => null,
                    'submission_date' => $request->input('submission_date'),
                    'employee_id' => $request->input('emp_id'),
                    'employee_name' => $request->input('emp_name'),
                    'employee_department' => $request->input('emp_department'),
                    'accident_number' => $request->input('accident_number'),
                    'position' => $request->input('position'),
                    'location' => $request->input('location'),
                    'area' => $request->input('area'),
                    'date_incident' => $request->input('date_incident'),
                    'time_incident' => $request->input('time_incident'),
                    'detail_incident' => $request->input('detail_incident'),
                    'condition' => $request->input('condition'),
                    'loss_time' => $request->input('loss_time'),
                    'recovery_time' => $request->input('recovery_time'),
                    'loss_cost' => $request->input('loss_cost'),
                    'illustration_image' => $illustration_image,
                    'illustration_detail' => $request->input('poin_penting'),
                    'yokotenkai' => $request->input('yokotenkai'),
                    'created_by' => Auth::id(),
                ]);

                $accident->save();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'potrait');

                $pdf->loadView('standardization.report_kecelakaan', array(
                    'data' => $accident,
                ));

                $pdf->save(public_path() . "/kecelakaan_list/kecelakaan_lalu_lintas/Kecelakaan " . date('d-M-y', strtotime($accident->date_incident)) . " " . $accident->location . ".pdf");

                $response = array(
                    'status' => true,
                    'message' => 'Data Kecelakaan Lalu Lintas Berhasil Ditambahkan',
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

    public function detailKecelakaan(Request $request)
    {

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        $accident = Accident::find($request->get('id'));

        $response = array(
            'status' => true,
            'accident' => $accident,
            'location' => $this->location,
        );
        return Response::json($response);
    }

    public function editKecelakaan(Request $request)
    {
// $filename = "";
// $tujuan_upload = 'files/kecelakaan/kecelakaan_kerja';

// if (count($request->file('illustration_image')) > 0) {
//     $file = $request->file('illustration_image');
//     $nama = $file->getClientOriginalName();

//     $filename = pathinfo($nama, PATHINFO_FILENAME);
//     $extension = pathinfo($nama, PATHINFO_EXTENSION);
//     $filename = md5($filename.date('YmdHisa')).'.'.$extension;

//     $file->move($tujuan_upload,$filename);
// }
// else{
//    $file = null;
// }

        try {
            $accident = Accident::where('id', '=', $request->get('id_edit'))->first();

            $accident->submission_date = $request->input('submission_date');
            $accident->employee_id = $request->input('emp_id');
            $accident->employee_name = $request->input('emp_name');
            $accident->employee_department = $request->input('emp_department');
            $accident->accident_number = $request->input('accident_number');
            $accident->position = $request->input('position');
            $accident->location = $request->input('location');
            $accident->area = $request->input('area');
            $accident->date_incident = $request->input('date_incident');
            $accident->time_incident = $request->input('time_incident');
            $accident->detail_incident = $request->input('detail_incident');
            $accident->condition = $request->input('condition');
            $accident->loss_time = $request->input('loss_time');
            $accident->recovery_time = $request->input('recovery_time');
            $accident->loss_cost = $request->input('loss_cost');
            // $accident->loss_cost = $request->input('loss_cost');
            if ($accident->category == "Lalu Lintas") {
                $accident->illustration_detail = $request->input('poin_penting');
            }
            // $accident->illustration_detail = $request->input('illustration_detail');
            $accident->yokotenkai = $request->input('yokotenkai');
            $accident->created_by = Auth::id();
            $accident->save();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('standardization.report_kecelakaan', array(
                'data' => $accident,
            ));

            $pdf->save(public_path() . "/kecelakaan_list/kecelakaan_kerja/Kecelakaan " . date('d-M-y', strtotime($accident->date_incident)) . " " . $accident->location . ".pdf");

            $response = array(
                'status' => true,
                'message' => 'Data Kecelakaan Kerja Berhasil Di Update',
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

    public function fetchMonitoringKecelakaan($category, Request $request)
    {

        if ($category == 'kerja') {
            $data = db::select("
                SELECT
                count( id ) AS jumlah,
                MONTHNAME(date_incident) as bulan,
                YEAR ( date_incident ) AS tahun
                FROM
                accidents
                WHERE
                category = 'Kerja'
                GROUP BY
                bulan,tahun
                ORDER BY
                tahun,
                month(date_incident) ASC
                ");

            $detail = DB::select("SELECT
                accidents.id,date_incident, location, area, detail_incident,`condition`
                FROM
                `accidents`
                WHERE
                category = 'Kerja' AND
                accidents.deleted_at IS NULL
                ");
        } else if ($category == 'lalu_lintas') {
            $data = db::select("
                SELECT
                count( id ) AS jumlah,
                MONTHNAME(date_incident) as bulan,
                YEAR ( date_incident ) AS tahun
                FROM
                accidents
                WHERE
                category = 'Lalu Lintas'
                GROUP BY
                bulan,tahun
                ORDER BY
                tahun,
                month(date_incident) ASC
                ");

            $detail = DB::select("SELECT
                accidents.id,date_incident, location, area, detail_incident,`condition`,accident_number
                FROM
                `accidents`
                WHERE
                category = 'Lalu Lintas' AND
                accidents.deleted_at IS NULL
                ");
        }
        $response = array(
            'status' => true,
            'datas' => $data,
            'detail' => $detail,
        );

        return Response::json($response);
    }

    public function reportPDFKecelakaan($id)
    {
        $accident = Accident::where('id', '=', $id)->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('standardization.report_kecelakaan', array(
            'data' => $accident,
        ));

        return $pdf->stream("Report Kecelakaan Kerja YMPI");
    }

    public function sendEmailAll($category, Request $request)
    {

        $acc = Accident::find($request->get('id'));

        try {
            if ($category == 'kerja') {
                // $mails = "select distinct email from users join employee_syncs on users.username = employee_syncs.employee_id WHERE email LIKE '%@music.yamaha.com%' and end_date is null";
                // $mailtoo = DB::select($mails);

                $acc->status = "sendall";
                $acc->save();

                $mail = [
                    'ympi-chief-ML@music.yamaha.com',
                    'ympi-staff-ML@music.yamaha.com',
                    'ympi-coordinator-ML@music.yamaha.com',
                    'ympi-leader-ML@music.yamaha.com',
                    'ympi-manager-ML@music.yamaha.com',
                ];

                // $mail = [
                //     'rio.irvansyah@music.yamaha.com',
                //     'widura@music.yamaha.com'
                // ];

                // $isimail = Accident::find($request->get('id'));

                $isimail = "select * from accidents where id = " . $request->get('id');
                $accident = db::select($isimail);

                // ->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])

                Mail::to($mail)->send(new SendEmail($accident, 'kecelakaan_all'));

                $response = array(
                    'status' => true,
                    'datas' => "Berhasil",
                );

                return Response::json($response);
            } else if ($category == 'foreman') {
                $mails = "select distinct email from users join employee_syncs on users.username = employee_syncs.employee_id WHERE position = 'Foreman' or position = 'chief' and end_date is null";
                $mailtoo = DB::select($mails);

                $acc->status_foreman = "sendall";
                $acc->save();

                $isimail = "select * from accidents where id = " . $request->get('id');
                $accident = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com', 'aditya.agassi@music.yamaha.com'])->send(new SendEmail($accident, 'kecelakaan_foreman'));

                $response = array(
                    'status' => true,
                    'datas' => "Berhasil",
                );
                return Response::json($response);
            } else if ($category == 'lalu_lintas') {
                // $mails = "select distinct email from users join employee_syncs on users.username = employee_syncs.employee_id WHERE email LIKE '%@music.yamaha.com%' and end_date is null";
                // $mailtoo = DB::select($mails);

                $acc->status = "sendall";
                $acc->save();

                $mail = [
                    'ympi-chief-ML@music.yamaha.com',
                    'ympi-staff-ML@music.yamaha.com',
                    'ympi-coordinator-ML@music.yamaha.com',
                    'ympi-leader-ML@music.yamaha.com',
                    'ympi-manager-ML@music.yamaha.com',
                ];

                // $mail = [
                //     'rio.irvansyah@music.yamaha.com',
                //     'widura@music.yamaha.com'
                // ];

                // $isimail = Accident::find($request->get('id'));

                // ->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])

                $isimail = "select * from accidents where id = " . $request->get('id');
                $accident = db::select($isimail);

                Mail::to($mail)->send(new SendEmail($accident, 'kecelakaan_lalu_lintas'));

                $response = array(
                    'status' => true,
                    'datas' => "Berhasil",
                );

                return Response::json($response);
            }

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'datas' => "Gagal",
            );
            return Response::json($response);
        }
    }

    public function indexYokotenkai($id)
    {
        $title = 'Form Yokotenkai';
        $title_jp = '';

        $accident = Accident::find($id);

        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'department')
        ->first();

        // $emp_dept->department == "Standardization Department" ||

        if ($emp_dept->department == "Management Information System Department") {
            $group = EmployeeSync::select('group')
            ->join('departments', 'employee_syncs.department', '=', 'departments.department_name')
            ->distinct()
            ->where('remark', '=', 'production')
            ->whereNotNull('group')
            ->orderBy('group', 'asc')
            ->get();
        } else {
            if ($emp_dept->department == "Logistic Department") {
                $group = EmployeeSync::where('department', $emp_dept->department)
                ->select('section as group')
                ->distinct()
                ->whereNotNull('section')
                ->orderBy('section', 'asc')
                ->get();
            } else {
                $group = EmployeeSync::where('department', $emp_dept->department)
                ->select('group')
                ->distinct()
                ->whereNotNull('group')
                ->orderBy('group', 'asc')
                ->get();
            }

        }

// if (count($group) == 1 && $group[0]->group == null) {
//     $group = null;
// }else{
//     $group = $group;
// }

        return view('standardization.yokotenkai', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'accident' => $accident,
            'emp_dept' => $emp_dept,
            'group' => $group,
            'id' => $id,
        ))->with('page', 'Yokotenkai')
        ->with('head', 'Laporan Kecelakaan');
    }

    public function postYokotenkai(Request $request, $id)
    {
        try {

            for ($i = 0; $i < $request->get('jumlah_grup'); $i++) {

                $tujuan_upload = 'files/kecelakaan/yokotenkai';
                $filename_pekerjaan_serupa = null;
                $filename_peralatan_sejenis = null;
                $filename_kaizen_sebelum = null;
                $filename_kaizen_sesudah = null;

                if (count($request->file('pekerjaan_serupa_file_' . $i)) > 0) {
                    $file_pekerjaan_serupa = $request->file('pekerjaan_serupa_file_' . $i);
                    $nama_pekerjaan_serupa = $file_pekerjaan_serupa->getClientOriginalName();
                    $extension_pekerjaan_serupa = pathinfo($nama_pekerjaan_serupa, PATHINFO_EXTENSION);
                    $filename_pekerjaan_serupa = md5($nama_pekerjaan_serupa . date('YmdHisa')) . '.' . $extension_pekerjaan_serupa;
                    $file_pekerjaan_serupa->move($tujuan_upload, $filename_pekerjaan_serupa);
                }

                if (count($request->file('peralatan_sejenis_file_' . $i)) > 0) {
                    $file_peralatan_sejenis = $request->file('peralatan_sejenis_file_' . $i);
                    $nama_peralatan_sejenis = $file_peralatan_sejenis->getClientOriginalName();
                    $extension_peralatan_sejenis = pathinfo($nama_peralatan_sejenis, PATHINFO_EXTENSION);
                    $filename_peralatan_sejenis = md5($nama_peralatan_sejenis . date('YmdHisa')) . '.' . $extension_peralatan_sejenis;
                    $file_peralatan_sejenis->move($tujuan_upload, $filename_peralatan_sejenis);
                }

                if (count($request->file('kaizen_before_' . $i)) > 0) {
                    $file_kaizen_sebelum = $request->file('kaizen_before_' . $i);
                    $nama_kaizen_sebelum = $file_kaizen_sebelum->getClientOriginalName();
                    $extension_kaizen_sebelum = pathinfo($nama_kaizen_sebelum, PATHINFO_EXTENSION);
                    $filename_kaizen_sebelum = md5($nama_kaizen_sebelum . date('YmdHisa')) . '.' . $extension_kaizen_sebelum;
                    $file_kaizen_sebelum->move($tujuan_upload, $filename_kaizen_sebelum);
                }

                if (count($request->file('kaizen_after_' . $i)) > 0) {
                    $file_kaizen_sesudah = $request->file('kaizen_after_' . $i);
                    $nama_kaizen_sesudah = $file_kaizen_sesudah->getClientOriginalName();
                    $extension_kaizen_sesudah = pathinfo($nama_kaizen_sesudah, PATHINFO_EXTENSION);
                    $filename_kaizen_sesudah = md5($nama_kaizen_sesudah . date('YmdHisa')) . '.' . $extension_kaizen_sesudah;
                    $file_kaizen_sesudah->move($tujuan_upload, $filename_kaizen_sesudah);
                }

                $data = AccidentYokotenkai::firstOrNew(['accident_id' => $id, 'group' => $request->get('group_' . $i)]);
                $data->accident_id = $id;
                $data->department = $request->get('department');
                $data->group = $request->get('group_' . $i);
                $data->pekerjaan_serupa = $request->get('pekerjaan_serupa_' . $i);
                $data->pekerjaan_serupa_detail = $request->get('pekerjaan_serupa_detail_' . $i);
                if ($filename_pekerjaan_serupa != null) {
                    $data->pekerjaan_serupa_foto = $filename_pekerjaan_serupa;
                }
                $data->peralatan_sejenis = $request->get('peralatan_sejenis_' . $i);
                $data->peralatan_sejenis_detail = $request->get('peralatan_sejenis_detail_' . $i);
                if ($filename_peralatan_sejenis != null) {
                    $data->peralatan_sejenis_foto = $filename_peralatan_sejenis;
                }
                $data->standar_k3 = $request->get('standar_k3_' . $i);
                $data->kaizen = $request->get('kaizen_' . $i);
                $data->kaizen_detail = $request->get('kaizen_detail_' . $i);
                if ($filename_kaizen_sebelum != null) {
                    $data->kaizen_sebelum = $filename_kaizen_sebelum;
                }

                if ($filename_kaizen_sesudah != null) {
                    $data->kaizen_sesudah = $filename_kaizen_sesudah;
                }
                $data->tanggal_pengecekan = $request->get('tanggal_pengecekan_' . $i);
                $data->created_by = Auth::id();
                $data->save();

                $update = AccidentYokotenkai::where('accident_id', $id)->where('group', $request->get('group_' . $i))
                ->whereNotNull('pekerjaan_serupa')
                ->whereNotNull('peralatan_sejenis')
                ->whereNotNull('standar_k3')
                ->whereNotNull('kaizen')
                ->whereNotNull('tanggal_pengecekan')
                ->update(['status' => 'sudah']);

            }

            return redirect('/index/yokotenkai/' . $id)
            ->with('status', 'Data Yokotenkai Berhasil Disimpan')
            ->with('page', 'Yokotenkai');

        } catch (\Exception$e) {
            return redirect('/index/yokotenkai/' . $id)
            ->with('error', $e->getMessage())
            ->with('page', 'Yokotenkai');
        }

    }

    public function fetchYokotenkai(Request $request)
    {
        try {
            $yokotenkai = AccidentYokotenkai::where('accident_id', $request->get('id'))
            ->where('department', $request->get('dept'))
            ->orderBy('group', 'asc')
            ->get();

            $yokotenkai_detail = AccidentYokotenkaiDetail::where('accident_id', $request->get('id'))
            ->select(DB::raw('count(id) as jumlah'))->first();

            $response = array(
                'status' => true,
                'yokotenkai' => $yokotenkai,
                'yokotenkai_detail' => $yokotenkai_detail,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchYokotenkaiAttendance(Request $request)
    {

        $accident = Accident::where('accidents.id', '=', $request->get('id'))
        ->select('accidents.id', 'accidents.condition')
        ->first();

        $accident_details = AccidentYokotenkaiDetail::where('accident_yokotenkai_details.accident_id', '=', $request->get('id'))
        ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'accident_yokotenkai_details.employee_id')
        ->select('accident_yokotenkai_details.id', 'accident_yokotenkai_details.accident_id', 'accident_yokotenkai_details.employee_id', 'accident_yokotenkai_details.attend_time', 'employee_syncs.name', 'employee_syncs.department', 'accident_yokotenkai_details.status')
        ->orderBy('accident_yokotenkai_details.id', 'asc')
        ->get();

        $response = array(
            'status' => true,
            'accident' => $accident,
            'accident_details' => $accident_details,
        );
        return Response::json($response);
    }

    public function scanEmployeeAttendance(Request $request)
    {
        $id = Auth::id();

        $employee = db::table('employees')
        ->where('tag', '=', $request->get('tag'))
        ->orWhere('employee_id', '=', $request->get('tag'))
        ->first();

        if ($employee == null) {
            $response = array(
                'status' => false,
                'message' => 'ID Card not found',
            );
            return Response::json($response);
        }

        try {

            $form_detail = AccidentYokotenkaiDetail::where('accident_id', '=', $request->get('accident_id'))
            ->where('employee_id', '=', $employee->employee_id)
            ->first();

            if ($form_detail != null) {
                $response = array(
                    'status' => false,
                    'message' => 'Already attended',
                );
                return Response::json($response);
            } else {

                $form_detail = new AccidentYokotenkaiDetail([
                    'accident_id' => $request->get('accident_id'),
                    'employee_tag' => $employee->tag,
                    'employee_id' => $employee->employee_id,
                    'employee_name' => $employee->name,
                    'attend_time' => date('Y-m-d H:i:s'),
                    'status' => 'ok',
                    'created_by' => $id,
                ]);

                $att_log = AccidentDetail::firstOrNew(
                    array(
                        'employee_id' => $employee->employee_id,
                        'accident_id' => $request->get('accident_id'),
                    ));
                $att_log->employee_tag = $employee->tag;
                $att_log->employee_name = $employee->name;
                $att_log->attend_time = date('Y-m-d H:i:s');
                $att_log->created_by = Auth::user()->id;
                $att_log->status = 'ok';
                $att_log->save();

            }
            $form_detail->save();

        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'Attendance success',
        );
        return Response::json($response);
    }

    public function fetch_chart_sosialisasi(Request $request)
    {
        try {
            $sosialisasi = AccidentDetail::where('accident_id', $request->get('id'))->get();
            $accident = Accident::where('id', $request->get('id'))->first();

            $department = DB::SELECT("SELECT DISTINCT
                ( department ),
                department_name,
                COALESCE ( department_shortname, 'MGT' ) AS department_shortname
                FROM
                employee_syncs
                LEFT JOIN departments ON departments.department_name = employee_syncs.department
                ORDER BY
                department");

            $employees = EmployeeSync::where('end_date', null)->get();

            $response = array(
                'status' => true,
                'sosialisasi' => $sosialisasi,
                'department' => $department,
                'employees' => $employees,
                'accident' => $accident,
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

    public function fetch_chart_sosialisasi_detail(Request $request)
    {
        try {
            if ($request->get('stat') == 'Belum Sosialisasi') {
                $status = null;
            } else if ($request->get('stat') == 'Sudah Sosialisasi') {
                $status = "yes";
            }

            if ($request->get('dept') == null) {
                if ($status == null) {
                    $details = DB::SELECT("SELECT
                        employee_syncs.employee_id,
                        employee_syncs.name,
                        'Management' AS department,
                        'Belum Sosialisasi' AS stat,
                        '' AS attend_time
                        FROM
                        employee_syncs
                        WHERE
                        employee_syncs.end_date IS NULL
                        AND employee_syncs.department IS NULL
                        AND employee_syncs.employee_id not in (SELECT accident_details.employee_id FROM accident_details
                        WHERE accident_id = '" . $request->get('id') . "')
                        ");
                } else {
                    $details = DB::SELECT("SELECT
                        employee_syncs.employee_id,
                        employee_syncs.name,
                        'Management' AS department,
                        'Sudah Sosialisasi' AS stat,
                        attend_time
                        FROM
                        employee_syncs
                        LEFT JOIN accident_details ON employee_syncs.employee_id = accident_details.employee_id
                        WHERE
                        employee_syncs.end_date IS NULL
                        AND accident_id = '" . $request->get('id') . "'
                        AND employee_syncs.department IS NULL
                        AND accident_details.employee_id IS NOT NULL
                        ");
                }
            } else {
                if ($status == null) {
                    $details = DB::SELECT("SELECT
                        employee_syncs.employee_id,
                        employee_syncs.name,
                        department_shortname AS department,
                        'Belum Sosialisasi' AS stat,
                        '' AS attend_time
                        FROM
                        employee_syncs
                        JOIN departments ON department_name = employee_syncs.department
                        WHERE
                        employee_syncs.end_date IS NULL
                        AND employee_syncs.department = '" . $request->get('dept') . "'
                        AND employee_syncs.employee_id not in (SELECT accident_details.employee_id FROM accident_details
                        WHERE accident_id = '" . $request->get('id') . "')
                        ");
                } else {
                    $details = DB::SELECT("
                        SELECT
                        employee_syncs.employee_id,
                        employee_syncs.name,
                        department_shortname AS department,
                        'Sudah Sosialisasi' AS stat,
                        attend_time
                        FROM
                        employee_syncs
                        LEFT JOIN accident_details ON employee_syncs.employee_id = accident_details.employee_id
                        JOIN departments ON department_name = employee_syncs.department
                        WHERE
                        employee_syncs.end_date IS NULL
                        AND accident_id = '" . $request->get('id') . "'
                        AND accident_details.employee_id IS NOT NULL
                        AND employee_syncs.department = '" . $request->get('dept') . "'
                        ");
                }
            }

            $response = array(
                'status' => true,
                'details' => $details,
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

    public function fetch_chart_sosialisasi_yokotenkai(Request $request)
    {
        try {
            $sosialisasi = AccidentYokotenkaiDetail::where('accident_id', $request->get('id'))->get();
            $accident = Accident::where('id', $request->get('id'))->first();

            $department = DB::SELECT("SELECT DISTINCT
                ( department ),
                department_name,
                COALESCE ( department_shortname, 'MGT' ) AS department_shortname
                FROM
                employee_syncs
                LEFT JOIN departments ON departments.department_name = employee_syncs.department
                ORDER BY
                department");

            $employees = EmployeeSync::where('end_date', null)->get();

            $response = array(
                'status' => true,
                'sosialisasi' => $sosialisasi,
                'department' => $department,
                'employees' => $employees,
                'accident' => $accident,
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

    public function indexKecelakaanSosialisasi($id)
    {
        $title = "Sosialiasi Kecelakaan YMPI";
        $title_jp = "";

        $accident = Accident::find($id);

        if ($accident->category == "Kerja") {
            $path = '/kecelakaan_list/kecelakaan_kerja/Kecelakaan ' . date('d-M-y', strtotime($accident->date_incident)) . ' ' . $accident->location . '.pdf';
        } else if ($accident->category == "Lalu Lintas") {
            $path = '/kecelakaan_list/kecelakaan_lalu_lintas/Kecelakaan ' . date('d-M-y', strtotime($accident->date_incident)) . ' ' . $accident->location . '.pdf';
        }

        $file_path = asset($path);

        return view('standardization.sosialisasi_kecelakaan', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'accident' => $accident,
            'file_path' => $file_path,
        ));
    }

    public function postEmployeeData(Request $request)
    {
        try {
            $att_log = AccidentDetail::firstOrNew(
                array(
                    'employee_id' => $request->get('employee_id'),
                    'accident_id' => $request->get('id'),
                ));
            $att_log->employee_tag = $request->get('employee_tag');
            $att_log->employee_name = $request->get('name');
            $att_log->attend_time = date('Y-m-d H:i:s');
            $att_log->created_by = Auth::user()->id;
            $att_log->status = 'ok';

            $att_log->save();

            $response = array(
                'status' => true,
                'message' => 'Success',
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

    public function fetchEmployeeHistory(Request $request)
    {
        $data_log = AccidentDetail::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'accident_details.employee_id')->where('accident_id', $request->get('id'));

        if (strlen($request->get('username')) > 0) {
            $dpt = EmployeeSync::where('employee_id', '=', $request->get('username'))->first();
            $data_log = $data_log->whereRaw('(employee_syncs.department = "' . $dpt->department . '" OR accident_details.created_by = "' . Auth::user()->id . '")');
        }

        $data_log = $data_log->select('accident_details.updated_at', 'accident_details.employee_id', 'employee_syncs.name', 'accident_details.status')->get();

        $response = array(
            'status' => true,
            'datas' => $data_log,
// 'query' => DB::getQueryLog()
        );
        return Response::json($response);
    }

    public function indexMonitoringYokotenkai()
    {
        $title = "Monitoring Form Yokotenkai";
        $title_jp = "";

        $accident = Accident::select('*')
        ->where('category', 'Kerja')
        ->whereNull('deleted_at')
        ->orderby('id', 'desc')
        ->get();

        return view('standardization.monitoring_yokotenkai', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'accident' => $accident,
        ))->with('page', 'Monitoring Yokotenkai');
    }

    public function fetchMonitoringYokotenkai(Request $request)
    {
        $accident = db::select("
            SELECT
            A.department_name,
            A.department_shortname,
            A.jumlah_group,
            COALESCE(B.count_belum,A.jumlah_group) as count_belum,
            COALESCE(B.count_sudah,0) as count_sudah
            FROM
            (
            SELECT
            department_name,
            department_shortname,
            COUNT( `group` ) AS jumlah_group,
            0 AS count_sudah,
            0 AS count_belum
            FROM
            `departments`
            JOIN ( SELECT department, `group` FROM employee_syncs WHERE department IS NOT NULL AND `group` IS NOT NULL AND end_date IS NULL GROUP BY department, `group` ) AS emp ON emp.department = departments.department_name
            WHERE
            id_division = '5'
            GROUP BY
            `department_name`,
            `department_shortname`
            ) AS A
            LEFT JOIN (
            SELECT
            department,
            SUM( IF ( `status` IS NULL, 1, 0 ) ) AS count_belum,
            SUM( IF ( `status` IS NOT NULL, 1, 0 ) ) AS count_sudah
            FROM
            accident_yokotenkais
            WHERE
            deleted_at IS NULL
            and accident_id = '" . $request->get('id') . "'
            GROUP BY
            department
            ) B ON A.department_name = B.department
            ");

        $sosialisasi = AccidentYokotenkaiDetail::where('accident_id', $request->get('id'))->get();
// $accident = Accident::where('id', $request->get('id'))->first();

        $department = DB::SELECT("SELECT DISTINCT
            ( department ),
            department_name,
            COALESCE ( department_shortname, 'MGT' ) AS department_shortname
            FROM
            employee_syncs
            LEFT JOIN departments ON departments.department_name = employee_syncs.department
            ORDER BY
            department"
        );

        $employees = EmployeeSync::where('end_date', null)->get();

// $accident = db::select("SELECT * FROM accidents where id = '".$request->get('id')."'");

        $response = array(
            'status' => true,
            'accidents' => $accident,
            'sosialisasi' => $sosialisasi,
            'employees' => $employees,
        );
        return Response::json($response);
    }

    public function fetchMonitoringYokotenkaiDetail(Request $request)
    {
        try {
            $dept = $request->get('dept');
            $stat = $request->get('stat');
            $id = $request->get('id');

            $status = "";
            $idall = "";

            if ($stat == 'Belum') {
                $status = "and `status` is null";
                $idall = "and accident_yokotenkais.accident_id = '" . $id . "' ";
            } else {
                $status = "and `status` = 'sudah'";
                $idall = "and accident_yokotenkais.accident_id = '" . $id . "' ";
            }

            $detail_yokotenkai = DB::SELECT("
             SELECT
             department_name,
             department_shortname,
             emp.`group`,
             COALESCE(`status`,null,'Belum Mengisi') as `status`
             FROM
             `departments`
             JOIN ( SELECT department, `group` FROM employee_syncs WHERE department IS NOT NULL AND `group` IS NOT NULL AND end_date IS NULL GROUP BY department, `group` ) AS emp ON emp.department = departments.department_name
             LEFT JOIN accident_yokotenkais on accident_yokotenkais.`group` = emp.`group`
             WHERE
             id_division = '5'
             and department_shortname = '" . $dept . "'
             " . $status . "
             " . $idall . "
             ");

            $response = array(
                'status' => true,
                'detail_yokotenkai' => $detail_yokotenkai,
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

    public function indexMonitoringKecelakaanKerja()
    {
        $title = "Monitoring Form Kecelakaan Kerja";
        $title_jp = "";

        $accident = Accident::select('*')
        ->where('category', 'Kerja')
        ->whereNull('deleted_at')
        ->get();

        return view('standardization.monitoring_kecelakaan', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'accident' => $accident,
        ))->with('page', 'Monitoring Kecelakaan Kerja');
    }

    public function fetchMonitoringKecelakaanKerja(Request $request)
    {

        if ($request->get('month') == null) {
            $period = date('Y-m');
        } else {
            $period = date('Y-m', strtotime($request->get('month')));
        }

        $accident = db::select("
            SELECT
            COUNT(id) as jumlah,
            location
            FROM
            accidents
            WHERE
            category = 'kerja'
            and DATE_FORMAT(date_incident, '%Y-%m') = '" . $period . "'
            GROUP BY location
            ");

// $employees = EmployeeSync::where('end_date',null)->get();
        $accident_yokotenkai = db::select("
            SELECT * FROM accidents where id = '" . $request->get('id') . "'
            ");

        $response = array(
            'status' => true,
            'accidents' => $accident,
            'period' => $period,
            // 'employees' => $employees,
        );
        return Response::json($response);
    }

    public function fetchMonitoringKecelakaanKerjaDetail(Request $request)
    {

        try {
            $location = $request->get('location');
            $period = $request->get('period');

            $detail = DB::SELECT("
                SELECT
                location,
                area,
                date_incident,
                time_incident,
                detail_incident,
                `condition`
                FROM
                accidents
                WHERE
                location = '" . $location . "'
                AND DATE_FORMAT( date_incident, '%Y-%m' ) = '" . $period . "'
                ");

            $response = array(
                'status' => true,
                'detail' => $detail,
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

    public function indexYokotenkaiPDF($id)
    {
        $accident = Accident::find($id);
        $yokotenkai = AccidentYokotenkai::where('accident_id', $id)->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A3', 'landscape');

        $pdf->loadView('standardization.yokotenkai_pdf', array(
            'accident' => $accident,
            'yokotenkai' => $yokotenkai,
            'id' => $id,
        ));
        return $pdf->stream("Yokotenkai ID " . $id . ".pdf");
    }

    public function indexSga()
    {
        return view('standardization.sga.index')
        ->with('title', 'Small Group Activity (SGA)')
        ->with('title_jp', '小グループ活動（SGA）')
        ->with('page', 'Small Group Activity (SGA)')
        ->with('jpn', '');
    }

    public function indexSgaAssessment()
    {
        $auditor_id = Auth::user()->username;
        $auditor_name = Auth::user()->name;
        $periode = DB::connection('ympimis_2')
        ->table('sga_teams')
        ->select('periode')
        ->distinct()
        ->orderby('periode', 'desc')
        ->where('remark', null)
        ->get();
        $pattern = '/YE/i';
        $employee_syncs = EmployeeSync::where('employee_id', 'like', '%' . strtoupper($auditor_id) . '%')->first();
        if (count($employee_syncs) == 0) {
            if (preg_match($pattern, $auditor_id)) {
                return view('standardization.sga.index_assessment')
                ->with('title', 'Small Group Activity (SGA) Assessment')
                ->with('title_jp', '小グループ活動アセスメント')
                ->with('page', 'SGA Assessment')
                ->with('jpn', '小グループ活動アセスメント')
                ->with('auditor_id', $auditor_id)
                ->with('auditor_name', $auditor_name)
                ->with('username', Auth::user()->username)
                ->with('role_code', Auth::user()->role_code)
                ->with('pattern', '/YE/i')
                ->with('pattern_periode', '/Final/i')
                ->with('periode', $periode);
            } else {
                return view('404');
            }
        } else {
            if ($employee_syncs->position == 'Manager') {
                return view('standardization.sga.index_assessment')
                ->with('title', 'Small Group Activity (SGA) Assessment')
                ->with('title_jp', '小グループ活動アセスメント')
                ->with('page', 'SGA Assessment')
                ->with('jpn', '小グループ活動アセスメント')
                ->with('auditor_id', $auditor_id)
                ->with('auditor_name', $auditor_name)
                ->with('username', Auth::user()->username)
                ->with('role_code', Auth::user()->role_code)
                ->with('pattern', '/YE/i')
                ->with('pattern_periode', '/Final/i')
                ->with('periode', $periode);
            } else if (Auth::user()->role_code == 'S-MIS') {
                return view('standardization.sga.index_assessment')
                ->with('title', 'Small Group Activity (SGA) Assessment')
                ->with('title_jp', '小グループ活動アセスメント')
                ->with('page', 'SGA Assessment')
                ->with('jpn', '小グループ活動アセスメント')
                ->with('auditor_id', $auditor_id)
                ->with('auditor_name', $auditor_name)
                ->with('username', Auth::user()->username)
                ->with('role_code', Auth::user()->role_code)
                ->with('pattern', '/YE/i')
                ->with('pattern_periode', '/Final/i')
                ->with('periode', $periode);
            } else {
                return view('404');
            }
        }

    }

    public function fetchSgaPoint(Request $request)
    {
        try {
            $cek_temp = DB::connection('ympimis_2')
            ->table('sga_results')
            ->where('periode', $request->get('periode'))
            ->where('asesor_id', $request->get('asesor_id'))
            ->get();

            $teams1 = DB::connection('ympimis_2')
            ->table('sga_teams')
            ->where('periode', $request->get('periode'))
            ->where('day', 1)
            ->get();
            $teams2 = DB::connection('ympimis_2')
            ->table('sga_teams')
            ->where('periode', $request->get('periode'))
            ->where('day', 2)
            ->get();

            $points = DB::connection('ympimis_2')
            ->table('sga_points')
            ->get();

            if (count($cek_temp) > 0) {
                // if ($cek_temp[0]->final_result == null) {
                $response = array(
                    'status' => true,
                    'teams1' => $teams1,
                    'teams2' => $teams2,
                    'points' => $points,
                    'cek_temp' => $cek_temp,
                );
                return Response::json($response);
                // }else{
                //     $response = array(
                //       'status' => false,
                //       'message' => 'Anda telah melakukan penilaian pada Periode '.$request->get('periode'),
                //     );
                //     return Response::json($response);
                // }
            } else {
                $response = array(
                    'status' => true,
                    'teams1' => $teams1,
                    'teams2' => $teams2,
                    'points' => $points,
                    'cek_temp' => null,
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

    public function inputSgaAssessmentTemp(Request $request)
    {
        try {
            $results = $request->get('results');
            $cek_temp = DB::connection('ympimis_2')
            ->table('sga_results')
            ->where('periode', $results[0]['periode'])
            ->where('asesor_id', $results[0]['asesor_id'])
            ->get();
            if (count($cek_temp) > 0) {
                for ($i = 0; $i < count($cek_temp); $i++) {
                    $sga_result = DB::connection('ympimis_2')
                    ->table('sga_results')
                    ->where('id', $cek_temp[$i]->id)
                    ->update([
                        'periode' => $results[$i]['periode'],
                        'assessment_date' => $results[$i]['assessment_date'],
                        'criteria_category' => $results[$i]['criteria_category'],
                        'day' => $results[$i]['day'],
                        'criteria' => $results[$i]['criteria'],
                        'team_no' => $results[$i]['team_no'],
                        'team_name' => $results[$i]['team_name'],
                        'result' => $results[$i]['result'],
                        'asesor_id' => $results[$i]['asesor_id'],
                        'asesor_name' => $results[$i]['asesor_name'],
                        'created_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            } else {
                for ($i = 0; $i < count($results); $i++) {
                    $sga_result = DB::connection('ympimis_2')
                    ->table('sga_results')
                    ->insert([
                        'periode' => $results[$i]['periode'],
                        'assessment_date' => $results[$i]['assessment_date'],
                        'criteria_category' => $results[$i]['criteria_category'],
                        'day' => $results[$i]['day'],
                        'criteria' => $results[$i]['criteria'],
                        'team_no' => $results[$i]['team_no'],
                        'team_name' => $results[$i]['team_name'],
                        'result' => $results[$i]['result'],
                        'asesor_id' => $results[$i]['asesor_id'],
                        'asesor_name' => $results[$i]['asesor_name'],
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
            $response = array(
                'status' => true,
                'message' => 'Save Temporary Success',
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

    public function inputSgaAssessmentResult(Request $request)
    {
        try {
            $id = $request->get('id');
            $nilai = $request->get('nilai');
            $update = DB::connection('ympimis_2')->table('sga_results')
            ->where('id', $id)
            ->update([
                'result' => $nilai,
            ]);

            $response = array(
                'status' => true,
                'message' => 'Save Temporary Success',
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

    public function inputSgaAssessment(Request $request)
    {
        try {
            $results = $request->get('results');
            $cek_temp = DB::connection('ympimis_2')
            ->table('sga_results')
            ->where('periode', $results[0]['periode'])
            ->where('asesor_id', $results[0]['asesor_id'])
            ->get();
            $id = [];
            if (count($cek_temp) > 0) {
                for ($i = 0; $i < count($cek_temp); $i++) {
                    $sga_result = DB::connection('ympimis_2')
                    ->table('sga_results')
                    ->where('id', $cek_temp[$i]->id)
                    ->update([
                        'periode' => $results[$i]['periode'],
                        'assessment_date' => $results[$i]['assessment_date'],
                        'criteria_category' => $results[$i]['criteria_category'],
                        'day' => $results[$i]['day'],
                        'criteria' => $results[$i]['criteria'],
                        'team_no' => $results[$i]['team_no'],
                        'team_name' => $results[$i]['team_name'],
                        'result' => $results[$i]['result'],
                        'asesor_id' => $results[$i]['asesor_id'],
                        'asesor_name' => $results[$i]['asesor_name'],
                        'final_result' => 'Final',
                        'created_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    array_push($id, $cek_temp[$i]->id);
                }
            } else {
                for ($i = 0; $i < count($results); $i++) {
                    $sga_result = DB::connection('ympimis_2')
                    ->table('sga_results')
                    ->insertGetId([
                        'periode' => $results[$i]['periode'],
                        'assessment_date' => $results[$i]['assessment_date'],
                        'criteria_category' => $results[$i]['criteria_category'],
                        'day' => $results[$i]['day'],
                        'criteria' => $results[$i]['criteria'],
                        'team_no' => $results[$i]['team_no'],
                        'team_name' => $results[$i]['team_name'],
                        'result' => $results[$i]['result'],
                        'asesor_id' => $results[$i]['asesor_id'],
                        'asesor_name' => $results[$i]['asesor_name'],
                        'final_result' => 'Final',
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    array_push($id, $sga_result);
                }
            }
            $response = array(
                'status' => true,
                'message' => 'Save Data Success',
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

    public function indexSgaReport()
    {
        $periode = DB::connection('ympimis_2')
        ->table('sga_teams')
        ->select('periode')
        ->distinct()
        ->orderby('periode', 'desc')
        ->get();
        return view('standardization.sga.index_report')
        ->with('title', 'Small Group Activity (SGA) Report')
        ->with('title_jp', '小グループ活動報告')
        ->with('page', 'SGA Report')
        ->with('jpn', '小グループ活動報告')
        ->with('periode', $periode);
    }

    public function fetchSgaReport(Request $request)
    {
        try {
            $sga_asesor = DB::connection('ympimis_2')
            ->select("SELECT DISTINCT
                ( asesor_id ),
                asesor_name
                FROM
                `sga_results`
                WHERE
                periode = '" . $request->get('periode') . "'
                AND final_result = 'Final'");

            $teams_all = DB::connection('ympimis_2')
            ->select("SELECT
        *
                FROM
                sga_teams
                WHERE
                periode = '" . $request->get('periode') . "'");

            $pattern = "/Final/i";

            if (preg_match($pattern, $request->get('periode'))) {
                $teams = DB::connection('ympimis_2')
                ->select("SELECT
                    sga_results.periode,
                    sga_results.team_no,
                    sga_results.team_name,
                    ( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $request->get('periode')) . "' ) AS total_nilai_seleksi,
                    ROUND(0.4*( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $request->get('periode')) . "' ),1) as persen_seleksi,
                    sum( sga_results.result ) AS total_nilai_final,
                    ROUND(0.6*sum( sga_results.result ),1) as persen_final,
                    (ROUND(0.4*( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $request->get('periode')) . "' ),1) + ROUND(0.6*sum( sga_results.result ),1)) as totals,
                    sga_teams.hadiah,
                    sga_teams.team_title,
                    sga_teams.file_pdf,
                    sga_teams.id
                    FROM
                    `sga_results`
                    JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                    AND sga_teams.periode = sga_results.periode
                    WHERE
                    sga_results.periode = '" . $request->get('periode') . "'
                    AND sga_results.final_result = 'Final'
                    GROUP BY
                    sga_results.periode,
                    sga_results.team_no,
                    sga_results.team_name,
                    sga_teams.hadiah,
                    sga_teams.team_title,
                    sga_teams.file_pdf,
                    sga_teams.id
                    ORDER BY
                    totals DESC");
            } else {
                $teams = DB::connection('ympimis_2')
                ->select("SELECT
                    sga_results.periode,
                    sga_results.team_no,
                    sga_results.team_name,
                    sum( sga_results.result ) AS total_nilai,
                    sga_teams.team_title,
                    sga_teams.file_pdf,
                    sga_teams.id
                    FROM
                    `sga_results`
                    JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                    AND sga_teams.periode = sga_results.periode
                    WHERE
                    sga_results.periode = '" . $request->get('periode') . "'
                    AND final_result = 'Final'
                    GROUP BY
                    sga_results.periode,
                    sga_results.team_no,
                    sga_results.team_name,
                    sga_teams.team_title,
                    sga_teams.file_pdf,
                    sga_teams.id
                    ORDER BY
                    sga_teams.selection_result asc,total_nilai DESC");
            }

            $sga_result = DB::connection('ympimis_2')
            ->select("SELECT
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name,
                sum( result ) AS total_nilai
                FROM
                `sga_results`
                WHERE
                periode = '" . $request->get('periode') . "'
                AND final_result = 'Final'
                GROUP BY
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name");
            $response = array(
                'status' => true,
                'sga_result' => $sga_result,
                'sga_asesor' => $sga_asesor,
                'teams' => $teams,
                'teams_all' => $teams_all,
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

    public function uploadSgaPdf(Request $request)
    {
        $filename = "";
        $file_destination = 'data_file/sga/pdf';

        if (count($request->file('newAttachment')) > 0) {
            try {
                $file = $request->file('newAttachment');
                $filename = 'sga_' . $request->get('periode') . '_' . $request->get('id') . '_' . date('YmdHisa') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);
                $upload = DB::connection('ympimis_2')->table('sga_teams')->where('id', $request->get('id'))->update(
                    [
                        'file_pdf' => $filename,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );

                $response = array(
                    'status' => true,
                    'message' => 'PDF Succesfully Uploaded',
                );
                return Response::json($response);
            } catch (\Exception$e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => 'Please select file to attach',
            );
            return Response::json($response);
        }
    }

    public function selectionSgaReport(Request $request)
    {
        try {
            $sel = $request->get('selection_result');
            $pattern = "/Final/i";

            $check = DB::connection('ympimis_2')
            ->select("SELECT
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name
                FROM
                `sga_results`
                WHERE
                ( periode = '" . $request->get('periode') . "' AND result IS NULL )
                OR ( periode = '" . $request->get('periode') . "' AND result = 'null' )
                GROUP BY
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name ");
            if (count($check) > 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Ada Juri yang belum melengkapi penilaian',
                    'check' => $check,
                );
                return Response::json($response);
            } else {
                for ($i = 0; $i < count($sel); $i++) {
                    if (preg_match($pattern, $request->get('periode'))) {
                        $update = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('id', $sel[$i]['id'])
                        ->update([
                            'selection_result' => $sel[$i]['selection'],
                            'hadiah' => $sel[$i]['hadiah'],
                        ]);
                    } else {
                        $update = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('id', $sel[$i]['id'])
                        ->update([
                            'selection_result' => $sel[$i]['selection'],
                        ]);
                    }
                }
                $response = array(
                    'status' => true,
                    'message' => 'Save Selection Success',
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

    public function approvalSgaReport($periode, $remark)
    {
        try {
            $pattern = "/Final/i";
            if (preg_match($pattern, $periode)) {
                if ($remark == 'secretariat') {
                    $cek = DB::connection('ympimis_2')
                    ->table('sga_teams')
                    ->where('periode', $periode)
                    ->where('secretariat_approver_status', null)
                    ->get();

                    if (count($cek) > 0) {
                        $manager_qa = Approver::where('department', 'Standardization Department')->where('remark', 'Manager')->first();
                        $dgm = Approver::where('department', 'Standardization Department')->where('remark', 'Deputy General Manager')->first();
                        $gm = Approver::where('department', 'Standardization Department')->where('remark', 'General Manager')->first();
                        // $vice = Approver::where('remark','Vice President')->first();
                        $presdir = Approver::where('remark', 'President Director')->first();
                        $update_secretariat = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->update([
                            'secretariat_approver_id' => Auth::user()->username,
                            'secretariat_approver_name' => Auth::user()->name,
                            'secretariat_approver_status' => 'Approved_' . date('Y-m-d H:i:s') . '_' . Auth::user()->username,
                            'manager_qa_approver_id' => $manager_qa->approver_id,
                            'manager_qa_approver_name' => $manager_qa->approver_name,
                            'manager_qa_approver_status' => null,
                            'dgm_approver_id' => $dgm->approver_id,
                            'dgm_approver_name' => $dgm->approver_name,
                            'dgm_approver_status' => null,
                            'gm_approver_id' => $gm->approver_id,
                            'gm_approver_name' => $gm->approver_name,
                            'gm_approver_status' => null,
                                // 'vice_approver_id' => $vice->approver_id,
                                // 'vice_approver_name' => $vice->approver_name,
                                // 'vice_approver_status' => null,
                            'presdir_approver_id' => $presdir->approver_id,
                            'presdir_approver_name' => $presdir->approver_name,
                            'presdir_approver_status' => null,
                            'remark' => 'Approval',
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $data_all = [];
                        $sga_asesor = DB::connection('ympimis_2')
                        ->select("SELECT DISTINCT
                            ( asesor_id ),
                            asesor_name
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'");

                        $teams_all = DB::connection('ympimis_2')
                        ->select("SELECT
                    *
                            FROM
                            sga_teams
                            WHERE
                            periode = '" . $periode . "'");

                        $teams = DB::connection('ympimis_2')
                        ->select("SELECT
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            ( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $periode) . "' ) AS total_nilai_seleksi,
                            sum( sga_results.result ) AS total_nilai_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            ) AS persen_seleksi,
                            ROUND( 0.6 * sum( sga_results.result ), 1 ) AS persen_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) AS totals,
                            sum( sga_results.result ) AS total_nilai_final,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            FROM
                            `sga_results`
                            JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                            AND sga_teams.periode = sga_results.periode
                            WHERE
                            sga_results.periode = '" . $periode . "'
                            AND sga_results.final_result = 'Final'
                            GROUP BY
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            ORDER BY
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                        )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) DESC");

                        $sga_result = DB::connection('ympimis_2')
                        ->select("SELECT
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name,
                            sum( result ) AS total_nilai
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'
                            GROUP BY
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name");

                        $data = array(
                            'teams' => $teams,
                            'teams_all' => $teams_all,
                            'sga_asesor' => $sga_asesor,
                            'sga_result' => $sga_result,
                            'next_remark' => 'manager_qa',
                            'periode' => $periode,
                        );
                        $mail_to = $manager_qa->approver_email;
                        Mail::to($mail_to)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->send(new SendEmail($data, 'sga_approval_final'));

                        $response = array(
                            'status' => true,
                            'message' => 'SGA Approved Successfully and Sent',
                        );
                        return Response::json($response);
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Error!',
                        );
                        return Response::json($response);
                    }
                }

                if ($remark == 'manager_qa') {
                    $cek = DB::connection('ympimis_2')
                    ->table('sga_teams')
                    ->where('periode', $periode)
                    ->where('manager_qa_approver_status', null)
                    ->get();

                    if (count($cek) > 0) {
                        $datateams = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->first();

                        $update = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->update([
                            'manager_qa_approver_status' => 'Approved_' . date('Y-m-d H:i:s') . '_' . Auth::user()->username,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $data_all = [];
                        $sga_asesor = DB::connection('ympimis_2')
                        ->select("SELECT DISTINCT
                            ( asesor_id ),
                            asesor_name
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'");

                        $teams_all = DB::connection('ympimis_2')
                        ->select("SELECT
                    *
                            FROM
                            sga_teams
                            WHERE
                            periode = '" . $periode . "'");

                        $teams = DB::connection('ympimis_2')
                        ->select("SELECT
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            ( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $periode) . "' ) AS total_nilai_seleksi,
                            sum( sga_results.result ) AS total_nilai_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            ) AS persen_seleksi,
                            ROUND( 0.6 * sum( sga_results.result ), 1 ) AS persen_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) AS totals,
                            sum( sga_results.result ) AS total_nilai_final,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            FROM
                            `sga_results`
                            JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                            AND sga_teams.periode = sga_results.periode
                            WHERE
                            sga_results.periode = '" . $periode . "'
                            AND sga_results.final_result = 'Final'
                            GROUP BY
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            ORDER BY
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                        )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) DESC");

                        $sga_result = DB::connection('ympimis_2')
                        ->select("SELECT
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name,
                            sum( result ) AS total_nilai
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'
                            GROUP BY
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name");

                        $data = array(
                            'teams' => $teams,
                            'teams_all' => $teams_all,
                            'sga_asesor' => $sga_asesor,
                            'sga_result' => $sga_result,
                            'next_remark' => 'dgm',
                            'periode' => $periode,
                        );
                        $mails = Approver::where('approver_id', $datateams->dgm_approver_id)->first();
                        Mail::to($mails->approver_email)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->send(new SendEmail($data, 'sga_approval_final'));

                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Approved Successfully SGA承認完了')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    } else {
                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Has Been Approved SGA承認済み')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    }
                }

                if ($remark == 'dgm') {
                    $cek = DB::connection('ympimis_2')
                    ->table('sga_teams')
                    ->where('periode', $periode)
                    ->where('dgm_approver_status', null)
                    ->get();

                    if (count($cek) > 0) {
                        $datateams = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->first();

                        $update = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->update([
                            'dgm_approver_status' => 'Approved_' . date('Y-m-d H:i:s') . '_' . Auth::user()->username,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $data_all = [];
                        $sga_asesor = DB::connection('ympimis_2')
                        ->select("SELECT DISTINCT
                            ( asesor_id ),
                            asesor_name
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'");

                        $teams_all = DB::connection('ympimis_2')
                        ->select("SELECT
                    *
                            FROM
                            sga_teams
                            WHERE
                            periode = '" . $periode . "'");

                        $teams = DB::connection('ympimis_2')
                        ->select("SELECT
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            ( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $periode) . "' ) AS total_nilai_seleksi,
                            sum( sga_results.result ) AS total_nilai_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            ) AS persen_seleksi,
                            ROUND( 0.6 * sum( sga_results.result ), 1 ) AS persen_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) AS totals,
                            sum( sga_results.result ) AS total_nilai_final,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            FROM
                            `sga_results`
                            JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                            AND sga_teams.periode = sga_results.periode
                            WHERE
                            sga_results.periode = '" . $periode . "'
                            AND sga_results.final_result = 'Final'
                            GROUP BY
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            ORDER BY
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                        )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) DESC");

                        $sga_result = DB::connection('ympimis_2')
                        ->select("SELECT
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name,
                            sum( result ) AS total_nilai
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'
                            GROUP BY
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name");

                        $data = array(
                            'teams' => $teams,
                            'teams_all' => $teams_all,
                            'sga_asesor' => $sga_asesor,
                            'sga_result' => $sga_result,
                            'next_remark' => 'gm',
                            'periode' => $periode,
                        );
                        $mails = Approver::where('approver_id', $datateams->gm_approver_id)->first();
                        Mail::to($mails->approver_email)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->send(new SendEmail($data, 'sga_approval_final'));

                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Approved Successfully SGA承認完了')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    } else {
                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Has Been Approved SGA承認済み')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    }
                }

                if ($remark == 'gm') {
                    $cek = DB::connection('ympimis_2')
                    ->table('sga_teams')
                    ->where('periode', $periode)
                    ->where('gm_approver_status', null)
                    ->get();

                    if (count($cek) > 0) {
                        $datateams = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->first();

                        $update = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->update([
                            'gm_approver_status' => 'Approved_' . date('Y-m-d H:i:s') . '_' . Auth::user()->username,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $data_all = [];
                        $sga_asesor = DB::connection('ympimis_2')
                        ->select("SELECT DISTINCT
                            ( asesor_id ),
                            asesor_name
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'");

                        $teams_all = DB::connection('ympimis_2')
                        ->select("SELECT
                    *
                            FROM
                            sga_teams
                            WHERE
                            periode = '" . $periode . "'");

                        $teams = DB::connection('ympimis_2')
                        ->select("SELECT
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            ( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $periode) . "' ) AS total_nilai_seleksi,
                            sum( sga_results.result ) AS total_nilai_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            ) AS persen_seleksi,
                            ROUND( 0.6 * sum( sga_results.result ), 1 ) AS persen_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) AS totals,
                            sum( sga_results.result ) AS total_nilai_final,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            FROM
                            `sga_results`
                            JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                            AND sga_teams.periode = sga_results.periode
                            WHERE
                            sga_results.periode = '" . $periode . "'
                            AND sga_results.final_result = 'Final'
                            GROUP BY
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            ORDER BY
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                        )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) DESC");

                        $sga_result = DB::connection('ympimis_2')
                        ->select("SELECT
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name,
                            sum( result ) AS total_nilai
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'
                            GROUP BY
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name");

                        $data = array(
                            'teams' => $teams,
                            'teams_all' => $teams_all,
                            'sga_asesor' => $sga_asesor,
                            'sga_result' => $sga_result,
                            'next_remark' => 'presdir',
                            'periode' => $periode,
                        );
                        $mails = Approver::where('approver_id', $datateams->presdir_approver_id)->first();
                        Mail::to($mails->approver_email)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->send(new SendEmail($data, 'sga_approval_final'));

                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Approved Successfully SGA承認完了')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    } else {
                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Has Been Approved SGA承認済み')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    }
                }
                if ($remark == 'vice') {
                    $cek = DB::connection('ympimis_2')
                    ->table('sga_teams')
                    ->where('periode', $periode)
                    ->where('vice_approver_status', null)
                    ->get();

                    if (count($cek) > 0) {
                        $datateams = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->first();

                        $update = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->update([
                            'vice_approver_status' => 'Approved_' . date('Y-m-d H:i:s') . '_' . Auth::user()->username,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $data_all = [];
                        $sga_asesor = DB::connection('ympimis_2')
                        ->select("SELECT DISTINCT
                            ( asesor_id ),
                            asesor_name
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'");

                        $teams_all = DB::connection('ympimis_2')
                        ->select("SELECT
                    *
                            FROM
                            sga_teams
                            WHERE
                            periode = '" . $periode . "'");

                        $teams = DB::connection('ympimis_2')
                        ->select("SELECT
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            ( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $periode) . "' ) AS total_nilai_seleksi,
                            sum( sga_results.result ) AS total_nilai_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            ) AS persen_seleksi,
                            ROUND( 0.6 * sum( sga_results.result ), 1 ) AS persen_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) AS totals,
                            sum( sga_results.result ) AS total_nilai_final,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            FROM
                            `sga_results`
                            JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                            AND sga_teams.periode = sga_results.periode
                            WHERE
                            sga_results.periode = '" . $periode . "'
                            AND sga_results.final_result = 'Final'
                            GROUP BY
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            ORDER BY
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                        )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) DESC");

                        $sga_result = DB::connection('ympimis_2')
                        ->select("SELECT
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name,
                            sum( result ) AS total_nilai
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'
                            GROUP BY
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name");

                        $data = array(
                            'teams' => $teams,
                            'teams_all' => $teams_all,
                            'sga_asesor' => $sga_asesor,
                            'sga_result' => $sga_result,
                            'next_remark' => 'presdir',
                            'periode' => $periode,
                        );
                        $mails = Approver::where('approver_id', $datateams->presdir_approver_id)->first();
                        Mail::to($mails->approver_email)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->send(new SendEmail($data, 'sga_approval_final'));

                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Approved Successfully SGA承認完了')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    } else {
                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Has Been Approved SGA承認済み')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    }
                }

                if ($remark == 'presdir') {
                    $cek = DB::connection('ympimis_2')
                    ->table('sga_teams')
                    ->where('periode', $periode)
                    ->where('presdir_approver_status', null)
                    ->get();

                    if (count($cek) > 0) {
                        $datateams = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->first();

                        $update = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->update([
                            'presdir_approver_status' => 'Approved_' . date('Y-m-d H:i:s') . '_' . Auth::user()->username,
                            'remark' => 'Closed',
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $data_all = [];
                        $sga_asesor = DB::connection('ympimis_2')
                        ->select("SELECT DISTINCT
                            ( asesor_id ),
                            asesor_name
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'");

                        $teams_all = DB::connection('ympimis_2')
                        ->select("SELECT
                    *
                            FROM
                            sga_teams
                            WHERE
                            periode = '" . $periode . "'");

                        $teams = DB::connection('ympimis_2')
                        ->select("SELECT
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            ( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $periode) . "' ) AS total_nilai_seleksi,
                            sum( sga_results.result ) AS total_nilai_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            ) AS persen_seleksi,
                            ROUND( 0.6 * sum( sga_results.result ), 1 ) AS persen_final,
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                            )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) AS totals,
                            sum( sga_results.result ) AS total_nilai_final,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            FROM
                            `sga_results`
                            JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                            AND sga_teams.periode = sga_results.periode
                            WHERE
                            sga_results.periode = '" . $periode . "'
                            AND sga_results.final_result = 'Final'
                            GROUP BY
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            sga_teams.hadiah,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            ORDER BY
                            ROUND(
                            0.4 *(
                            SELECT
                            sum( a.result ) 
                            FROM
                            sga_results a 
                            WHERE
                            a.team_no = sga_results.team_no 
                            AND a.periode = '" . str_replace('_Final', '', $periode) . "' 
                            ),
                            1 
                        )+ ROUND( 0.6 * sum( sga_results.result ), 1 ) DESC");

                        $sga_result = DB::connection('ympimis_2')
                        ->select("SELECT
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name,
                            sum( result ) AS total_nilai
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'
                            GROUP BY
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name");

                        $data = array(
                            'teams' => $teams,
                            'teams_all' => $teams_all,
                            'sga_asesor' => $sga_asesor,
                            'sga_result' => $sga_result,
                            'next_remark' => 'secretariat',
                            'finish' => 'Finish',
                            'periode' => $periode,
                        );
                        $mails = User::where('username', $datateams->secretariat_approver_id)->first();
                        $mail_to_app = [];
                        array_push($mail_to_app, $mails->email);
                        array_push($mail_to_app, 'ympi-manager-ML@music.yamaha.com');
                        array_push($mail_to_app, 'ympi-chief-ML@music.yamaha.com');
                        Mail::to($mail_to_app)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->send(new SendEmail($data, 'sga_approval_final'));

                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Approved Successfully SGA承認完了')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    } else {
                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Has Been Approved SGA承認済み')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    }
                }
            } else {
                if ($remark == 'secretariat') {
                    $cek = DB::connection('ympimis_2')
                    ->table('sga_teams')
                    ->where('periode', $periode)
                    ->where('secretariat_approver_status', null)
                    ->get();

                    if (count($cek) > 0) {
                        $approver = Approver::where('department', 'Standardization Department')->where('remark', 'Deputy General Manager')->first();
                        $update_secretariat = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->update([
                            'secretariat_approver_id' => Auth::user()->username,
                            'secretariat_approver_name' => Auth::user()->name,
                            'secretariat_approver_status' => 'Approved_' . date('Y-m-d H:i:s') . '_' . Auth::user()->username,
                            'dgm_approver_id' => $approver->approver_id,
                            'dgm_approver_name' => $approver->approver_name,
                            'dgm_approver_status' => null,
                            'remark' => 'Approval',
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $data_all = [];
                        $sga_asesor = DB::connection('ympimis_2')
                        ->select("SELECT DISTINCT
                            ( asesor_id ),
                            asesor_name
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'");

                        $teams_all = DB::connection('ympimis_2')
                        ->select("SELECT
                    *
                            FROM
                            sga_teams
                            WHERE
                            periode = '" . $periode . "'");

                        $teams = DB::connection('ympimis_2')
                        ->select("SELECT
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            sum( sga_results.result ) AS total_nilai,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            FROM
                            `sga_results`
                            JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                            AND sga_teams.periode = sga_results.periode
                            WHERE
                            sga_results.periode = '" . $periode . "'
                            AND final_result = 'Final'
                            GROUP BY
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            ORDER BY
                            sga_teams.selection_result asc,total_nilai DESC");

                        $sga_result = DB::connection('ympimis_2')
                        ->select("SELECT
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name,
                            sum( result ) AS total_nilai
                            FROM
                            `sga_results`
                            WHERE
                            sga_results.periode = '" . $periode . "'
                            AND final_result = 'Final'
                            GROUP BY
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name");

                        $data = array(
                            'teams' => $teams,
                            'teams_all' => $teams_all,
                            'sga_asesor' => $sga_asesor,
                            'sga_result' => $sga_result,
                            'next_remark' => 'dgm',
                            'periode' => $periode,
                        );
                        $mail_to = $approver->approver_email;
                        Mail::to($mail_to)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->send(new SendEmail($data, 'sga_approval'));
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'SGA Has Been Approved and Sent',
                        );
                        return Response::json($response);
                    }
                }

                if ($remark == 'dgm') {
                    $cek = DB::connection('ympimis_2')
                    ->table('sga_teams')
                    ->where('periode', $periode)
                    ->where('dgm_approver_status', null)
                    ->get();

                    if (count($cek) > 0) {
                        $update_secretariat = DB::connection('ympimis_2')
                        ->table('sga_teams')
                        ->where('periode', $periode)
                        ->update([
                            'dgm_approver_status' => 'Approved_' . date('Y-m-d H:i:s') . '_' . Auth::user()->username,
                            'remark' => 'Closed',
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $data_all = [];
                        $sga_asesor = DB::connection('ympimis_2')
                        ->select("SELECT DISTINCT
                            ( asesor_id ),
                            asesor_name
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'");

                        $teams_all = DB::connection('ympimis_2')
                        ->select("SELECT
                    *
                            FROM
                            sga_teams
                            WHERE
                            periode = '" . $periode . "'");

                        $teams = DB::connection('ympimis_2')
                        ->select("SELECT
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            sum( sga_results.result ) AS total_nilai,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            FROM
                            `sga_results`
                            JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                            AND sga_teams.periode = sga_results.periode
                            WHERE
                            sga_results.periode = '" . $periode . "'
                            AND final_result = 'Final'
                            GROUP BY
                            sga_results.periode,
                            sga_results.team_no,
                            sga_results.team_name,
                            sga_teams.team_title,
                            sga_teams.file_pdf
                            ORDER BY
                            sga_teams.selection_result asc,total_nilai DESC");

                        $sga_result = DB::connection('ympimis_2')
                        ->select("SELECT
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name,
                            sum( result ) AS total_nilai
                            FROM
                            `sga_results`
                            WHERE
                            periode = '" . $periode . "'
                            AND final_result = 'Final'
                            GROUP BY
                            periode,
                            asesor_id,
                            asesor_name,
                            team_no,
                            team_name");

                        for ($i = 0; $i < 5; $i++) {
                            $input_final = DB::connection('ympimis_2')
                            ->table('sga_teams')
                            ->insert([
                                'periode' => $periode . '_Final',
                                'team_no' => $teams[$i]->team_no,
                                'team_name' => $teams[$i]->team_name,
                                'team_title' => $teams[$i]->team_title,
                                'file_pdf' => $teams[$i]->file_pdf,
                                'day' => 1,
                                'created_by' => 1,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                        }

                        $data = array(
                            'teams' => $teams,
                            'teams_all' => $teams_all,
                            'sga_asesor' => $sga_asesor,
                            'sga_result' => $sga_result,
                            'next_remark' => 'secretariat',
                            'periode' => $periode,
                            'finish' => 'Finish',
                        );

                        $users = User::where('username', $teams_all[0]->secretariat_approver_id)->first();
                        $mail_to = $users->email;
                        Mail::to($mail_to)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->send(new SendEmail($data, 'sga_approval'));

                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Approved Successfully SGA承認完了')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    } else {
                        return view('standardization.sga.approved')
                        ->with('title', 'Small Group Activity (SGA) Approval')
                        ->with('title_jp', 'スモールグループ活動の承認')
                        ->with('message', 'SGA Has Been Approved SGA承認済み')
                        ->with('head', 'Small Group Activity (SGA) Approval')
                        ->with('page', 'Small Group Activity (SGA)')
                        ->with('jpn', 'スモールグループ活動の承認');
                    }
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Success Approved',
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

    public function rejectSgaReport($periode, $remark)
    {
        $pattern = "/Final/i";
        if (preg_match($pattern, $periode)) {
            if ($remark == 'manager_qa') {
                $kolom = 'manager_qa_approver_status';
                $reason = 'SGA Final Approval telah direject oleh Manager QA';
            } else if ($remark == 'dgm') {
                $kolom = 'dgm_approver_status';
                $reason = 'SGA Final Approval telah direject oleh Deputy General Manager';
            } else if ($remark == 'gm') {
                $kolom = 'gm_approver_status';
                $reason = 'SGA Final Approval telah direject oleh General Manager';
            }
// else if ($remark == 'vice') {
//     $kolom = 'vice_approver_status';
//     $reason = 'SGA Final Approval telah direject oleh Vice President';
// }
            else if ($remark == 'presdir') {
                $kolom = 'presdir_approver_status';
                $reason = 'SGA Final Approval telah direject oleh President Director';
            }
            $cek = DB::connection('ympimis_2')
            ->table('sga_teams')
            ->where('periode', $periode)
            ->where($kolom, null)
            ->get();

            if (count($cek) > 0) {
                $update_secretariat = DB::connection('ympimis_2')
                ->table('sga_teams')
                ->where('periode', $periode)
                ->update([
                        // $kolom => 'Rejected_'.date('Y-m-d H:i:s'),
                    $kolom => null,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $data_all = [];
                $sga_asesor = DB::connection('ympimis_2')
                ->select("SELECT DISTINCT
                    ( asesor_id ),
                    asesor_name
                    FROM
                    `sga_results`
                    WHERE
                    periode = '" . $periode . "'
                    AND final_result = 'Final'");

                $teams_all = DB::connection('ympimis_2')
                ->select("SELECT
            *
                    FROM
                    sga_teams
                    WHERE
                    periode = '" . $periode . "'");

                $teams = DB::connection('ympimis_2')
                ->select("SELECT
                    sga_results.periode,
                    sga_results.team_no,
                    sga_results.team_name,
                    ( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $periode) . "' ) AS total_nilai_seleksi,
                    sum( sga_results.result ) AS total_nilai_final,
                    sga_teams.hadiah,
                    sga_teams.team_title,
                    sga_teams.file_pdf
                    FROM
                    `sga_results`
                    JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                    AND sga_teams.periode = sga_results.periode
                    WHERE
                    sga_results.periode = '" . $periode . "'
                    AND sga_results.final_result = 'Final'
                    GROUP BY
                    sga_results.periode,
                    sga_results.team_no,
                    sga_results.team_name,
                    sga_teams.hadiah,
                    sga_teams.team_title,
                    sga_teams.file_pdf
                    ORDER BY
                    total_nilai_final DESC");

                $sga_result = DB::connection('ympimis_2')
                ->select("SELECT
                    periode,
                    asesor_id,
                    asesor_name,
                    team_no,
                    team_name,
                    sum( result ) AS total_nilai
                    FROM
                    `sga_results`
                    WHERE
                    periode = '" . $periode . "'
                    AND final_result = 'Final'
                    GROUP BY
                    periode,
                    asesor_id,
                    asesor_name,
                    team_no,
                    team_name");

                $data = array(
                    'teams' => $teams,
                    'teams_all' => $teams_all,
                    'sga_asesor' => $sga_asesor,
                    'sga_result' => $sga_result,
                    'next_remark' => 'secretariat',
                    'periode' => $periode,
                    'reject' => 'Rejected',
                    'reason' => $reason,
                );

                $users = User::where('username', $teams_all[0]->secretariat_approver_id)->first();
                $mail_to = $users->email;
                Mail::to($mail_to)
                ->bcc(['ympi-mis-ML@music.yamaha.com'])
                ->send(new SendEmail($data, 'sga_reject_final'));

                return view('standardization.sga.reject')
                ->with('title', 'Small Group Activity (SGA) Rejection')
                ->with('title_jp', 'SGAを却下')
                ->with('message', 'SGA Rejected Successfully SGA却下完了')
                ->with('periode', $periode)
                ->with('head', 'Small Group Activity (SGA) Rejection')
                ->with('page', 'Small Group Activity (SGA)')
                ->with('jpn', 'SGAを却下');
            } else {
                return view('standardization.sga.reject')
                ->with('title', 'Small Group Activity (SGA) Rejection')
                ->with('title_jp', 'SGAを却下')
                ->with('periode', $periode)
                ->with('message', 'SGA Has Been Rejected SGA却下済み')
                ->with('head', 'Small Group Activity (SGA) Rejection')
                ->with('page', 'Small Group Activity (SGA)')
                ->with('jpn', 'SGAを却下');
            }
        } else {
            $cek = DB::connection('ympimis_2')
            ->table('sga_teams')
            ->where('periode', $periode)
            ->where('dgm_approver_status', null)
            ->get();

            if (count($cek) > 0) {
                // $update_secretariat = DB::connection('ympimis_2')
                // ->table('sga_teams')
                // ->where('periode',$periode)
                // ->update([
                //     'dgm_approver_status' => 'Approved_'.date('Y-m-d H:i:s').'_'.Auth::user()->username,
                //     'updated_at' => date('Y-m-d H:i:s'),
                // ]);
                $data_all = [];
                $sga_asesor = DB::connection('ympimis_2')
                ->select("SELECT DISTINCT
                    ( asesor_id ),
                    asesor_name
                    FROM
                    `sga_results`
                    WHERE
                    periode = '" . $periode . "'
                    AND final_result = 'Final'");

                $teams_all = DB::connection('ympimis_2')
                ->select("SELECT
            *
                    FROM
                    sga_teams
                    WHERE
                    periode = '" . $periode . "'");

                $teams = DB::connection('ympimis_2')
                ->select("SELECT
                    sga_results.periode,
                    sga_results.team_no,
                    sga_results.team_name,
                    sum( sga_results.result ) AS total_nilai,
                    sga_teams.team_title,
                    sga_teams.file_pdf
                    FROM
                    `sga_results`
                    JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                    AND sga_teams.periode = sga_results.periode
                    WHERE
                    sga_results.periode = '" . $periode . "'
                    AND final_result = 'Final'
                    GROUP BY
                    sga_results.periode,
                    sga_results.team_no,
                    sga_results.team_name,
                    sga_teams.team_title,
                    sga_teams.file_pdf
                    ORDER BY
                    sga_teams.selection_result asc,total_nilai DESC");

                $sga_result = DB::connection('ympimis_2')
                ->select("SELECT
                    periode,
                    asesor_id,
                    asesor_name,
                    team_no,
                    team_name,
                    sum( result ) AS total_nilai
                    FROM
                    `sga_results`
                    WHERE
                    periode = '" . $periode . "'
                    AND final_result = 'Final'
                    GROUP BY
                    periode,
                    asesor_id,
                    asesor_name,
                    team_no,
                    team_name");

                $data = array(
                    'teams' => $teams,
                    'teams_all' => $teams_all,
                    'sga_asesor' => $sga_asesor,
                    'sga_result' => $sga_result,
                    'next_remark' => 'secretariat',
                    'periode' => $periode,
                    'finish' => 'Finish',
                );

                return view('standardization.sga.reject')
                ->with('title', 'Small Group Activity (SGA) Rejection')
                ->with('title_jp', 'SGAを却下')
                ->with('data', $data)
                ->with('periode', $periode)
                ->with('head', 'Small Group Activity (SGA) Rejection')
                ->with('page', 'Small Group Activity (SGA)')
                ->with('jpn', 'SGAを却下');
            } else {
                return view('standardization.sga.reject')
                ->with('title', 'Small Group Activity (SGA) Rejection')
                ->with('title_jp', 'SGAを却下')
                ->with('periode', $periode)
                ->with('message', 'SGA Has Been Rejected SGA却下済み')
                ->with('head', 'Small Group Activity (SGA) Rejection')
                ->with('page', 'Small Group Activity (SGA)')
                ->with('jpn', 'SGAを却下');
            }
        }
    }

    public function rejectReasonSgaReport(Request $request)
    {
        try {
            $periode = $request->get('periode');
            $reason = $request->get('reason');

            $update_secretariat = DB::connection('ympimis_2')
            ->table('sga_teams')
            ->where('periode', $periode)
            ->update([
                'secretariat_approver_status' => null,
                'dgm_approver_status' => null,
                'reason' => $reason,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $sga_asesor = DB::connection('ympimis_2')
            ->select("SELECT DISTINCT
                ( asesor_id ),
                asesor_name
                FROM
                `sga_results`
                WHERE
                periode = '" . $periode . "'
                AND final_result = 'Final'");

            $teams_all = DB::connection('ympimis_2')
            ->select("SELECT
        *
                FROM
                sga_teams
                WHERE
                periode = '" . $periode . "'");

            $teams = DB::connection('ympimis_2')
            ->select("SELECT
                sga_results.periode,
                sga_results.team_no,
                sga_results.team_name,
                sum( sga_results.result ) AS total_nilai,
                sga_teams.team_title,
                sga_teams.file_pdf
                FROM
                `sga_results`
                JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                AND sga_teams.periode = sga_results.periode
                WHERE
                sga_results.periode = '" . $periode . "'
                AND final_result = 'Final'
                GROUP BY
                sga_results.periode,
                sga_results.team_no,
                sga_results.team_name,
                sga_teams.team_title,
                sga_teams.file_pdf
                ORDER BY
                sga_teams.selection_result asc,total_nilai DESC");

            $sga_result = DB::connection('ympimis_2')
            ->select("SELECT
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name,
                sum( result ) AS total_nilai
                FROM
                `sga_results`
                WHERE
                periode = '" . $periode . "'
                AND final_result = 'Final'
                GROUP BY
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name");

            $data = array(
                'teams' => $teams,
                'teams_all' => $teams_all,
                'sga_asesor' => $sga_asesor,
                'sga_result' => $sga_result,
                'next_remark' => 'secretariat',
                'periode' => $periode,
                'reject' => 'Rejected',
                'reason' => $reason,
            );

            $users = User::where('username', $teams_all[0]->secretariat_approver_id)->first();
            $mail_to = $users->email;
            Mail::to($mail_to)
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'sga_approval'));

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

    public function indexSgaMonitoring()
    {
        $periode = DB::connection('ympimis_2')
        ->table('sga_teams')
        ->select('periode')
        ->distinct()
        ->where('periode', 'not like', '%Final%')
        ->orderby('periode', 'desc')
        ->get();

        $auditor_id = Auth::user()->username;

        return view('standardization.sga.monitoring')
        ->with('title', 'Small Group Activity (SGA) Monitoring')
        ->with('title_jp', 'SGAのモニタリング')
        ->with('page', 'SGA Monitoring')
        ->with('jpn', 'SGAのモニタリング')
        ->with('username', strtoupper(Auth::user()->username))
        ->with('role', Auth::user()->role_code)
        ->with('periode', $periode);

        // $pattern = '/YE/i';
        // $employee_syncs = EmployeeSync::where('employee_id', 'like', '%' . strtoupper($auditor_id) . '%')->first();
        // if (count($employee_syncs) == 0) {
        //     if (preg_match($pattern, $auditor_id)) {
        //         return view('standardization.sga.monitoring')
        //             ->with('title', 'Small Group Activity (SGA) Monitoring')
        //             ->with('title_jp', 'SGAのモニタリング')
        //             ->with('page', 'SGA Monitoring')
        //             ->with('jpn', 'SGAのモニタリング')
        //             ->with('username', strtoupper(Auth::user()->username))
        //             ->with('role', Auth::user()->role_code)
        //             ->with('periode', $periode);
        //     } else {
        //         return view('404');
        //     }
        // } else {
        //     if ($employee_syncs->position == 'Manager') {
        //         return view('standardization.sga.monitoring')
        //             ->with('title', 'Small Group Activity (SGA) Monitoring')
        //             ->with('title_jp', 'SGAのモニタリング')
        //             ->with('page', 'SGA Monitoring')
        //             ->with('jpn', 'SGAのモニタリング')
        //             ->with('username', strtoupper(Auth::user()->username))
        //             ->with('role', Auth::user()->role_code)
        //             ->with('periode', $periode);
        //     } else if (str_contains(Auth::user()->role_code,'MIS')) {
        //         return view('standardization.sga.monitoring')
        //             ->with('title', 'Small Group Activity (SGA) Monitoring')
        //             ->with('title_jp', 'SGAのモニタリング')
        //             ->with('page', 'SGA Monitoring')
        //             ->with('jpn', 'SGAのモニタリング')
        //             ->with('username', strtoupper(Auth::user()->username))
        //             ->with('role', Auth::user()->role_code)
        //             ->with('periode', $periode);
        //     } else {
        //         return view('404');
        //     }
        // }
    }

    public function fetchSgaMonitoring(Request $request)
    {
        try {
            $periodes = DB::connection('ympimis_2')
            ->select("SELECT DISTINCT
                ( periode )
                FROM
                sga_teams
                WHERE
                periode NOT LIKE '%Final%'
                ORDER BY
                created_at DESC
                LIMIT 1");
            if ($request->get('periode') == '') {
                $periode = $periodes[0]->periode;
            } else {
                $periode = $request->get('periode');
            }

            $teams_all = DB::connection('ympimis_2')
            ->select("SELECT
                    *,
                ( SELECT sum( sga_results.result ) FROM sga_results WHERE periode = sga_teams.periode AND team_no = sga_teams.team_no ) AS seleksi,
                ( SELECT sum( sga_results.result ) FROM sga_results WHERE periode = CONCAT( sga_teams.periode, '_Final' ) AND team_no = sga_teams.team_no ) AS final,
                round(( 0.4 *( SELECT sum( sga_results.result ) FROM sga_results WHERE periode = sga_teams.periode AND team_no = sga_teams.team_no )), 1 ) AS persen_seleksi,
                ROUND(
                0.6 *(
                SELECT
                sum( sga_results.result ) 
                FROM
                sga_results 
                WHERE
                periode = CONCAT( sga_teams.periode, '_Final' ) 
                AND team_no = sga_teams.team_no 
                ),
                1 
                ) AS persen_final,
                round(( 0.4 *( SELECT sum( sga_results.result ) FROM sga_results WHERE periode = sga_teams.periode AND team_no = sga_teams.team_no )), 1 )+ ROUND(
                0.6 *(
                SELECT
                sum( sga_results.result ) 
                FROM
                sga_results 
                WHERE
                periode = CONCAT( sga_teams.periode, '_Final' ) 
                AND team_no = sga_teams.team_no 
                ),
                1 
                ) AS totals 
                FROM
                sga_teams 
                WHERE
                periode = '".$periode."' 
                ORDER BY
                round(( 0.4 *( SELECT sum( sga_results.result ) FROM sga_results WHERE periode = sga_teams.periode AND team_no = sga_teams.team_no )), 1 )+ ROUND(
                0.6 *(
                SELECT
                sum( sga_results.result ) 
                FROM
                sga_results 
                WHERE
                periode = CONCAT( sga_teams.periode, '_Final' ) 
                AND team_no = sga_teams.team_no 
                ),
                1 
            ) DESC,
            round(( 0.4 *( SELECT sum( sga_results.result ) FROM sga_results WHERE periode = sga_teams.periode AND team_no = sga_teams.team_no )), 1 ) DESC");
            $teams = DB::connection('ympimis_2')
            ->select("SELECT
                sga_results.periode,
                sga_results.team_no,
                sga_results.team_name,
                sum( sga_results.result ) AS total_nilai_seleksi,
                COALESCE (( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . $periode . "_Final' ), 0 ) AS total_nilai_final,
                sga_teams.team_title,
                sga_teams.file_pdf
                FROM
                `sga_results`
                JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                AND sga_teams.periode = sga_results.periode
                WHERE
                sga_results.periode = '" . $periode . "'
                AND final_result = 'Final'
                GROUP BY
                sga_results.periode,
                sga_results.team_no,
                sga_results.team_name,
                sga_teams.team_title,
                sga_teams.file_pdf
                ORDER BY
                total_nilai_seleksi ASC,
                total_nilai_final ASC");

            $sga_asesor = DB::connection('ympimis_2')
            ->select("SELECT DISTINCT
                ( asesor_id ),
                asesor_name
                FROM
                `sga_results`
                WHERE
                periode = '" . $periode . "'
                AND final_result = 'Final'");

            $pattern = "/Final/i";

            $sga_result = DB::connection('ympimis_2')
            ->select("SELECT
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name,
                sum( result ) AS total_nilai
                FROM
                `sga_results`
                WHERE
                periode = '" . $periode . "'
                AND final_result = 'Final'
                GROUP BY
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name");

            $approval = DB::connection('ympimis_2')->select("SELECT
                periode,
                GROUP_CONCAT( selection_result ) AS selection_result,
                secretariat_approver_id,
                secretariat_approver_name,
                secretariat_approver_status,
                manager_qa_approver_id,
                manager_qa_approver_name,
                manager_qa_approver_status,
                dgm_approver_id,
                dgm_approver_name,
                dgm_approver_status,
                gm_approver_id,
                gm_approver_name,
                gm_approver_status,
                presdir_approver_id,
                presdir_approver_name,
                presdir_approver_status
                FROM
                sga_teams
                WHERE
                periode = '" . $periode . "'
                AND remark ='Approval'
                GROUP BY
                periode,
                secretariat_approver_id,
                secretariat_approver_name,
                secretariat_approver_status,
                manager_qa_approver_id,
                manager_qa_approver_name,
                manager_qa_approver_status,
                dgm_approver_id,
                dgm_approver_name,
                dgm_approver_status,
                gm_approver_id,
                gm_approver_name,
                gm_approver_status,
                presdir_approver_id,
                presdir_approver_name,
                presdir_approver_status
                UNION ALL
                SELECT
                periode,
                GROUP_CONCAT( selection_result ) AS selection_result,
                secretariat_approver_id,
                secretariat_approver_name,
                secretariat_approver_status,
                manager_qa_approver_id,
                manager_qa_approver_name,
                manager_qa_approver_status,
                dgm_approver_id,
                dgm_approver_name,
                dgm_approver_status,
                gm_approver_id,
                gm_approver_name,
                gm_approver_status,
                presdir_approver_id,
                presdir_approver_name,
                presdir_approver_status
                FROM
                sga_teams
                WHERE
                periode = '" . $periode . "_Final'
                AND remark ='Approval'
                GROUP BY
                periode,
                secretariat_approver_id,
                secretariat_approver_name,
                secretariat_approver_status,
                manager_qa_approver_id,
                manager_qa_approver_name,
                manager_qa_approver_status,
                dgm_approver_id,
                dgm_approver_name,
                dgm_approver_status,
                gm_approver_id,
                gm_approver_name,
                gm_approver_status,
                presdir_approver_id,
                presdir_approver_name,
                presdir_approver_status");

            $response = array(
                'status' => true,
                'teams' => $teams,
                'teams_all' => $teams_all,
                'approval' => $approval,
                'periode' => str_replace('_', ' ', $periode),
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

    public function indexSgaMaster()
    {
        $periode = DB::connection('ympimis_2')
        ->table('sga_teams')
        ->select('periode')
        ->distinct()
        ->get();
        return view('standardization.sga.master')
        ->with('title', 'Small Group Activity (SGA) Master')
        ->with('title_jp', 'SGAのマスター')
        ->with('page', 'SGA Master')
        ->with('jpn', 'SGAのマスター')
        ->with('periode', $periode);
    }

    public function fetchSgaMaster(Request $request)
    {
        try {
            $periodes = DB::connection('ympimis_2')
            ->select("SELECT DISTINCT
                ( periode )
                FROM
                sga_teams
                ORDER BY
                periode DESC
                LIMIT 1");
            if ($request->get('periode') == '') {
                $periode = $periodes[0]->periode;
            } else {
                $periode = $request->get('periode');
            }
            $teams = DB::connection('ympimis_2')
            ->table('sga_teams')
            ->where('periode', $periode)
            ->get();

            $teams =
            $response = array(
                'status' => true,
                'teams' => $teams,
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

    public function downloadSgaMaster()
    {
        $file_path = public_path('data_file/TemplateSGA.xlsx');
        return response()->download($file_path);
    }

    public function uploadSgaMaster(Request $request)
    {
        $filename = "";
        $file_destination = 'data_file/sga';

        if (count($request->file('newAttachment')) > 0) {
            try {
                $file = $request->file('newAttachment');
                $filename = 'sga_' . $request->get('periode') . '_' . date('YmdHisa') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);

                $excel = 'data_file/sga/' . $filename;
                $rows = Excel::load($excel, function ($reader) {
                    $reader->noHeading();
                    $reader->skipRows(1);

                    $reader->each(function ($row) {
                    });
                })->toObject();

                $delete = DB::connection('ympimis_2')
                ->table('sga_teams')
                ->where('periode', $request->get('periode'))
                ->delete();

                for ($i = 0; $i < count($rows); $i++) {
                    $upload = DB::connection('ympimis_2')->table('sga_teams')->insert(
                        [
                            'periode' => $request->get('periode'),
                            'team_no' => $rows[$i][0],
                            'team_name' => $rows[$i][1],
                            'team_title' => $rows[$i][2],
                            'day' => $rows[$i][3],
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                }

                $response = array(
                    'status' => true,
                    'message' => 'Team Succesfully Uploaded',
                );
                return Response::json($response);
            } catch (\Exception$e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => 'Please select file to attach',
            );
            return Response::json($response);
        }
    }

    public function deleteSgaMaster(Request $request)
    {
        try {

            $id = $request->get('id');
            $delete = DB::connection('ympimis_2')
            ->table('sga_teams')
            ->where('id', $id)
            ->delete();
            $response = array(
                'status' => true,
                'message' => 'Team Succesfully Uploaded',
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

    public function updateSgaMaster(Request $request)
    {
        try {
            $id = $request->get('id');
            $team_no = $request->get('team_no');
            $team_name = $request->get('team_name');
            $team_title = $request->get('team_title');
            $day = $request->get('day');

            $update = DB::connection('ympimis_2')->table('sga_teams')->where('id', $id)->update([
                'team_no' => $team_no,
                'team_name' => $team_name,
                'team_title' => $team_title,
                'day' => $day,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'Update SGA Succeeded',
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

    public function pdfSgaReport($periode)
    {
        $pattern = '/Final/i';
        if (preg_match($pattern, $periode)) {
            $sga_asesor = DB::connection('ympimis_2')
            ->select("SELECT DISTINCT
                ( asesor_id ),
                asesor_name
                FROM
                `sga_results`
                WHERE
                periode = '" . $periode . "'
                AND final_result = 'Final'");

            $teams_all = DB::connection('ympimis_2')
            ->select("SELECT
        *,
                DATE_FORMAT( SPLIT_STRING ( secretariat_approver_status, '_', 2 ), '%d-%b-%Y<br>%H:%i:%s' ) AS sec_status,
                DATE_FORMAT( SPLIT_STRING ( dgm_approver_status, '_', 2 ), '%d-%b-%Y' ) AS dgm_status,
                DATE_FORMAT( SPLIT_STRING ( manager_qa_approver_status, '_', 2 ), '%d-%b-%Y<br>%H:%i:%s' ) AS manager_qa_status,
                DATE_FORMAT( SPLIT_STRING ( gm_approver_status, '_', 2 ), '%d-%b-%Y' ) AS gm_status,
                DATE_FORMAT( SPLIT_STRING ( presdir_approver_status, '_', 2 ), '%d-%b-%Y' ) AS presdir_status
                FROM
                sga_teams
                WHERE
                periode = '" . $periode . "'");

            $teams = DB::connection('ympimis_2')
            ->select("SELECT
                sga_results.periode,
                sga_results.team_no,
                sga_results.team_name,
                ( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $periode) . "' ) AS total_nilai_seleksi,
                ROUND(0.4*( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $periode) . "' ),1) AS persen_seleksi,
                sum( sga_results.result ) AS total_nilai_final,
                ROUND(0.6*sum( sga_results.result ),1) AS persen_final,
                (ROUND(0.4*( SELECT sum( a.result ) FROM sga_results a WHERE a.team_no = sga_results.team_no AND a.periode = '" . str_replace('_Final', '', $periode) . "' ),1) + ROUND(0.6*sum( sga_results.result ),1)) as totals,
                sga_teams.hadiah,
                sga_teams.team_title,
                sga_teams.file_pdf
                FROM
                `sga_results`
                JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                AND sga_teams.periode = sga_results.periode
                WHERE
                sga_results.periode = '" . $periode . "'
                AND sga_results.final_result = 'Final'
                GROUP BY
                sga_results.periode,
                sga_results.team_no,
                sga_results.team_name,
                sga_teams.hadiah,
                sga_teams.team_title,
                sga_teams.file_pdf
                ORDER BY
                totals DESC");

            $sga_result = DB::connection('ympimis_2')
            ->select("SELECT
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name,
                sum( result ) AS total_nilai
                FROM
                `sga_results`
                WHERE
                periode = '" . $periode . "'
                AND final_result = 'Final'
                GROUP BY
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name");

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->getDomPDF()->set_option("enable_css_float", true);
            $pdf->setPaper('A4', 'landscape');

// return view('standardization.sga.pdf_seleksi')
// ->with('teams',$teams)
// ->with('teams_all',$teams_all)
// ->with('sga_asesor',$sga_asesor)
// ->with('sga_result',$sga_result)
// ->with('periode',$periode);

            $pdf->loadView('standardization.sga.pdf_final', array(
                'teams' => $teams,
                'teams_all' => $teams_all,
                'sga_asesor' => $sga_asesor,
                'sga_result' => $sga_result,
                'periode' => $periode,
            ));

            return $pdf->stream("SGA Selection Report - "+$periode+".pdf");
        } else {
            $sga_asesor = DB::connection('ympimis_2')
            ->select("SELECT DISTINCT
                ( asesor_id ),
                asesor_name,
                DATE_FORMAT( MAX( created_at ), '%d-%b-%Y<br>%H:%i:%s' ) AS created_at
                FROM
                `sga_results`
                WHERE
                periode = '" . $periode . "'
                AND final_result = 'Final'
                GROUP BY
                asesor_id,
                asesor_name");

            $teams_all = DB::connection('ympimis_2')
            ->select("SELECT
        *,
                DATE_FORMAT( SPLIT_STRING ( secretariat_approver_status, '_', 2 ), '%d-%b-%Y<br>%H:%i:%s' ) AS sec_status,
                DATE_FORMAT( SPLIT_STRING ( dgm_approver_status, '_', 2 ), '%d-%b-%Y' ) AS dgm_status
                FROM
                sga_teams
                WHERE
                periode = '" . $periode . "'");

            $teams = DB::connection('ympimis_2')
            ->select("SELECT
                sga_results.periode,
                sga_results.team_no,
                sga_results.team_name,
                sum( sga_results.result ) AS total_nilai,
                sga_teams.team_title,
                sga_teams.file_pdf
                FROM
                `sga_results`
                JOIN sga_teams ON sga_teams.team_no = sga_results.team_no
                AND sga_teams.periode = sga_results.periode
                WHERE
                sga_results.periode = '" . $periode . "'
                AND final_result = 'Final'
                GROUP BY
                sga_results.periode,
                sga_results.team_no,
                sga_results.team_name,
                sga_teams.team_title,
                sga_teams.file_pdf
                ORDER BY
                sga_teams.selection_result asc,total_nilai DESC");

            $sga_result = DB::connection('ympimis_2')
            ->select("SELECT
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name,
                sum( result ) AS total_nilai
                FROM
                `sga_results`
                WHERE
                periode = '" . $periode . "'
                AND final_result = 'Final'
                GROUP BY
                periode,
                asesor_id,
                asesor_name,
                team_no,
                team_name");

            $pdf2 = \App::make('dompdf.wrapper');
            $pdf2->getDomPDF()->set_option("enable_php", true);
            $pdf2->getDomPDF()->set_option("enable_css_float", true);
            $pdf2->setPaper('A4', 'potrait');

// return view('standardization.sga.pdf_seleksi')
// ->with('teams',$teams)
// ->with('teams_all',$teams_all)
// ->with('sga_asesor',$sga_asesor)
// ->with('sga_result',$sga_result)
// ->with('periode',$periode);

            $pdf2->loadView('standardization.sga.pdf_seleksi', array(
                'teams' => $teams,
                'teams_all' => $teams_all,
                'sga_asesor' => $sga_asesor,
                'sga_result' => $sga_result,
                'periode' => $periode,
            ));

            return $pdf2->stream("SGA Selection Report - "+$periode+".pdf");
        }
    }

    public function getNotifSGA()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);

            $manager = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
                ( manager_qa_approver_id )
                FROM
                sga_teams
                WHERE
                manager_qa_approver_id = '" . $user . "'
                AND secretariat_approver_status IS NOT NULL
                AND manager_qa_approver_status IS NULL
                AND deleted_at IS NULL");

            $dgm = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
                ( dgm_approver_id )
                FROM
                sga_teams
                WHERE
                dgm_approver_id = '" . $user . "'
                AND manager_qa_approver_status IS NOT NULL
                AND dgm_approver_status IS NULL
                AND deleted_at IS NULL");

            $gm = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
                ( gm_approver_id )
                FROM
                sga_teams
                WHERE
                gm_approver_id = '" . $user . "'
                AND dgm_approver_status IS NOT NULL
                AND gm_approver_status IS NULL
                AND deleted_at IS NULL");

// $vice = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
//     ( vice_approver_id )
// FROM
//     sga_teams
// WHERE
//     vice_approver_id = '".$user."'
//     AND gm_approver_status IS NOT NULL
//     AND vice_approver_status IS NULL
//     AND deleted_at IS NULL");

            $presdir = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
                ( presdir_approver_id )
                FROM
                sga_teams
                WHERE
                presdir_approver_id = '" . $user . "'
                AND gm_approver_status IS NOT NULL
                AND presdir_approver_status IS NULL
                AND deleted_at IS NULL");

            $notif = 0;

            if (count($manager) > 0 || count($dgm) > 0 || count($gm) > 0 || count($presdir) > 0) {
                $notif = count($manager) + count($dgm) + count($gm) + count($presdir);
            }
            return $notif;
        }
    }

    public function indexSgaPointCheck()
    {
        return view('standardization.sga.point_check')
        ->with('title', 'Small Group Activity (SGA) Point Check')
        ->with('title_jp', 'SGAチェック項目')
        ->with('page', 'SGA Point Check')
        ->with('jpn', 'SGAチェック項目');
    }

    public function inputSgaPointCheck(Request $request)
    {
        try {
            $criteria_category = $request->get('criteria_category');
            $criteria = $request->get('criteria');
            $result_1 = $request->get('result_1');
            $result_2 = $request->get('result_2');
            $result_3 = $request->get('result_3');
            $result_4 = $request->get('result_4');

            $input = DB::connection('ympimis_2')->table('sga_points')->insert([
                'criteria_category' => $criteria_category,
                'criteria' => $criteria,
                'result_1' => $result_1,
                'result_2' => $result_2,
                'result_3' => $result_3,
                'result_4' => $result_4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
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

    public function fetchSgaPointCheck(Request $request)
    {
        try {
            $point = DB::connection('ympimis_2')->table('sga_points')->get();
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

    public function updateSgaPointCheck(Request $request)
    {
        try {
            $id = $request->get('id');
            $criteria_category = $request->get('criteria_category');
            $criteria = $request->get('criteria');
            $result_1 = $request->get('result_1');
            $result_2 = $request->get('result_2');
            $result_3 = $request->get('result_3');
            $result_4 = $request->get('result_4');

            $update = DB::connection('ympimis_2')->table('sga_points')->where('id', $id)->update([
                'criteria_category' => $criteria_category,
                'criteria' => $criteria,
                'result_1' => $result_1,
                'result_2' => $result_2,
                'result_3' => $result_3,
                'result_4' => $result_4,
                'updated_at' => date('Y-m-d H:i:s'),
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

    public function deleteSgaPointCheck(Request $request)
    {
        try {
            $id = $request->get('id');

            $delete = DB::connection('ympimis_2')->table('sga_points')->where('id', $id)->delete();
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

// ---------- SAFETY CHECK ----------

    public function indexSafetyCheck()
    {
        $title = 'Holiday Safety Check';
        $title_jp = 'Holiday Safety Check';

        return view('standardization.safety_check.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ));
    }

    public function indexSafetyCheckForm()
    {
        $title = 'Holiday Safety Check Form';
        $title_jp = 'Holiday Safety Check Form';

        $form = db::select('SELECT * from safety_holiday_masters');

        $pic = [];
        $bagian2 = [];

        $ofc = db::select('SELECT employees.employee_id, employees.name, IF(employees.employee_id = "PI0109004" OR employees.employee_id = "PI9905001" OR employees.employee_id = "PI9709001", division, department) department, employees.remark from employees
            left join employee_syncs on employees.employee_id = employee_syncs.employee_id
            where employees.end_date is null and employees.remark = "OFC" and employees.employee_id not in ("PI2111045","PI2111044")');
        $foreman = EmployeeSync::select('employee_id', 'name', 'department', db::raw('"OFC" as remark2'))->where('position', 'foreman')->whereNull('end_date')->get();

        $prd = EmployeeSync::select('employee_id', 'name', db::raw('"PRD" as remark2'), 'department')->whereIn('position', ['Leader', 'Sub Leader'])->whereNull('end_date')->get();

        foreach ($ofc as $office) {
            array_push($pic, ['emp_id' => $office->employee_id, 'name' => $office->name, 'remark' => $office->remark, 'department' => $office->department]);
        }

        foreach ($foreman as $frm) {
            array_push($pic, ['emp_id' => $frm->employee_id, 'name' => $frm->name, 'remark' => $frm->remark2, 'department' => $frm->department]);
        }

        foreach ($prd as $production) {
            array_push($pic, ['emp_id' => $production->employee_id, 'name' => $production->name,
                'remark' => $production->remark2, 'department' => $production->department]);
        }

        $grp = EmployeeSync::select('group', 'department')
        ->whereRaw('(division = "Production Division" OR department = "Logistic Department" OR department = "Standardization Department")')
        ->whereNotNull('group')
        ->where('group', '<>', 'Standardization Group')
        ->groupBy('group', 'department')
        ->get();

        $dpt = EmployeeSync::select('department')->whereNotNull('department')->groupBy('department')->get();
        $div = EmployeeSync::select('division')->whereNotNull('division')->groupBy('division')->get();

        foreach ($grp as $group) {
            array_push($bagian2, ['bagian' => $group->group, 'category' => 'group', 'department' => $group->department]);
        }

        foreach ($dpt as $department) {
            array_push($bagian2, ['bagian' => $department->department, 'category' => 'department', 'department' => '']);
        }

        foreach ($div as $division) {
            array_push($bagian2, ['bagian' => $division->division, 'category' => 'division', 'department' => '']);
        }

        return view('standardization.safety_check.index_form', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'form' => $form,
            'list_user' => $pic,
            'list_bagian' => $bagian2,
        ));
    }

    public function postSafetyCheckForm(Request $request)
    {
        try {
// $safety_data = SafetyHolidayForm::where('')->get();

            $poin_cek = json_decode($request->get('point_check'));
            $kondisi = json_decode($request->get('condition'));
            $ket = json_decode($request->get('note'));

            $poin_cek_new = json_decode($request->get('point_check_new'));
            $kondisi_new = json_decode($request->get('condition_new'));
            $ket_new = json_decode($request->get('note_new'));

            $poin_cek_sb = json_decode($request->get('point_check_sb'));
            $kondisi_sb = json_decode($request->get('condition_sb'));
            $ket_sb = json_decode($request->get('note_sb'));

            $supp_at = null;

            $get_pic = EmployeeSync::where('employee_id', $request->get('employee_id'))->select(db::raw('UPPER(employee_id) as emp_id'), 'name', 'position', 'department', 'division')->first();

            if ($get_pic->emp_id == 'PI0109004' || $get_pic->emp_id == 'PI9902017' || $get_pic->emp_id == 'PI9709001') {
                $superior = db::select("SELECT users.email, UPPER(users.username) as emp_id, users.name FROM users where username = '" . $get_pic->emp_id . "'");
// $supp_at = date('Y-m-d H:i:s');
            } else if ($get_pic->position == 'Manager') {
                if ($get_pic->division == 'Human Resources & General Affairs Division') {
                    $superior = db::select("SELECT users.email, UPPER(users.username) as emp_id, users.name FROM users where username = 'PI9709001'");
                } else if ($get_pic->division == 'Production Division') {
                    $superior = db::select("SELECT users.email, UPPER(users.username) as emp_id, users.name FROM users where username = 'PI0109004'");
                } else if ($get_pic->division == 'Production Support Division') {
                    $superior = db::select("SELECT users.email, UPPER(users.username) as emp_id, users.name FROM users where username = 'PI9905001'");
                }
            } else {
                $superior = db::select("SELECT send_emails.email, UPPER(users.username) as emp_id, users.name FROM send_emails LEFT JOIN users on users.email = send_emails.email where remark = '" . $get_pic->department . "'");
            }

// dd($request->file('photo_0'));
            $code_generator = CodeGenerator::where('note', '=', 'safety_holiday')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $form_id = 'SH' . $number;

            $tujuan_upload = 'files/safety_holiday/photo';
            for ($i = 0; $i < count($poin_cek); $i++) {
                $file_name = [];
                for ($z = 0; $z < count($request->file('photo_' . $i)); $z++) {
                    $file = $request->file('photo_' . $i)[$z];
                    // dd($_FILES['photo_'.$i.'['.$z.']']);

                    $nama = $file->getClientOriginalName();

                    $filename = pathinfo($nama, PATHINFO_FILENAME);
                    $extension = pathinfo($nama, PATHINFO_EXTENSION);

                    $filename = '_regular_' . $z . '_' . $i.'_'.$request->get('employee_id').'_'.date('YmdHis') . '.' . $extension;

                    // $imageTemp = $_FILES['photo_'.$i]["tmp_name"];
                    // $imageUploadPath = $tujuan_upload.$filename;

                    // $compressedImage = compressImage($imageTemp, $imageUploadPath, 75);

                    array_push($file_name, $filename);

                    $file->move($tujuan_upload, $filename);
                }

                $safety_form = SafetyHolidayForm::insert([
                    'form_id' => $form_id,
                    'pic' => $get_pic->emp_id . '/' . $get_pic->name . '/' . $get_pic->position,
                    'location' => $request->input("bagian"),
                    'date_create' => date('Y-m-d'),
                    'check_point' => $poin_cek[$i],
                    'condition' => $kondisi[$i],
                    'note' => $ket[$i],
                    'status' => 'Approval',
                    'category' => $request->input("kategori"),
                    'pic_sign' => $get_pic->emp_id . '/' . $get_pic->name,
                    'pic_sign_at' => date('Y-m-d H:i:s'),
                    'superior_sign' => $superior[0]->emp_id . '/' . $superior[0]->name,
                    'superior_at' => $supp_at,
                    'photo' => implode(',', $file_name),
                    'created_by' => $get_pic->emp_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

// --------  NEW  ------------

            for ($i = 0; $i < count($poin_cek_new); $i++) {
                $file_name = [];
                for ($z = 0; $z < count($request->file('photo_new_' . $i)); $z++) {
                    $file = $request->file('photo_new_' . $i)[$z];

                    $nama = $file->getClientOriginalName();

                    $filename = pathinfo($nama, PATHINFO_FILENAME);
                    $extension = pathinfo($nama, PATHINFO_EXTENSION);

                    $filename = '_new_' . $z . '_' . $i . '_' . $request->get('employee_id').'_'.date('YmdHis') . '.' . $extension;

                    // $imageTemp = $_FILES['photo_new_'.$i][$z]["tmp_name"];
                    // $imageUploadPath = $tujuan_upload.$filename;

                    // $compressedImage = compressImage($imageTemp, $imageUploadPath, 75);

                    array_push($file_name, $filename);

                    $file->move($tujuan_upload, $filename);
                }

                $safety_form = SafetyHolidayForm::insert([
                    'form_id' => $form_id,
                    'pic' => $get_pic->emp_id . '/' . $get_pic->name . '/' . $get_pic->position,
                    'location' => $request->input("bagian"),
                    'date_create' => date('Y-m-d'),
                    'check_point' => $poin_cek_new[$i],
                    'condition' => $kondisi_new[$i],
                    'note' => $ket_new[$i],
                    'status' => 'Approval',
                    'category' => $request->input("kategori"),
                    'remark' => 'Additional',
                    'pic_sign' => $get_pic->emp_id . '/' . $get_pic->name,
                    'pic_sign_at' => date('Y-m-d H:i:s'),
                    'superior_sign' => $superior[0]->emp_id . '/' . $superior[0]->name,
                    'superior_at' => $supp_at,
                    'photo' => implode(',', $file_name),
                    'created_by' => $get_pic->emp_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

// ---------- Standby 24 Jam -------------

            for ($i = 0; $i < count($poin_cek_sb); $i++) {
                $file_name = [];
                for ($z = 0; $z < count($request->file('photo_safe_' . $i)); $z++) {
                    $file = $request->file('photo_safe_' . $i)[$z];

                    $nama = $file->getClientOriginalName();

                    $filename = pathinfo($nama, PATHINFO_FILENAME);
                    $extension = pathinfo($nama, PATHINFO_EXTENSION);

                    $filename = '_safe_' . $z . '_' . $i . '_' . $request->get('employee_id').'_'.date('YmdHis') . '.' . $extension;

                    // $imageTemp = $_FILES['photo_safe_'.$i][$z]["tmp_name"];
                    // $imageUploadPath = $tujuan_upload.$filename;

                    // $compressedImage = compressImage($imageTemp, $imageUploadPath, 75);

                    array_push($file_name, $filename);

                    $file->move($tujuan_upload, $filename);
                }

                $safety_form = SafetyHolidayForm::insert([
                    'form_id' => $form_id,
                    'pic' => $get_pic->emp_id . '/' . $get_pic->name . '/' . $get_pic->position,
                    'location' => $request->input("bagian"),
                    'date_create' => date('Y-m-d'),
                    'check_point' => $poin_cek_sb[$i],
                    'condition' => $kondisi_sb[$i],
                    'note' => $ket_sb[$i],
                    'status' => 'Approval',
                    'category' => $request->input("kategori"),
                    'remark' => 'Standby',
                    'pic_sign' => $get_pic->emp_id . '/' . $get_pic->name,
                    'pic_sign_at' => date('Y-m-d H:i:s'),
                    'superior_sign' => $superior[0]->emp_id . '/' . $superior[0]->name,
                    'superior_at' => $supp_at,
                    'photo' => implode(',', $file_name),
                    'created_by' => $get_pic->emp_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $detail_check = SafetyHolidayForm::where('form_id', $form_id)->select('form_id', 'pic', 'location', 'date_create', 'check_point', 'condition', 'note', 'category', 'remark', 'pic_sign', 'pic_sign_at', 'superior_sign', 'superior_at', 'created_at')->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            if ($request->input("kategori") == 'Office') {
                $pdf->setPaper('A4', 'landscape');
            } else {
                $pdf->setPaper('A4', 'potrait');
            }

            $pdf->loadView('standardization.safety_check.report', array(
                'safety_check' => $detail_check,
            ));

            $pdf->save(public_path() . "/files/safety_holiday/" . $form_id . ".pdf");

// $fill = SafetyHolidayPic::where('employee_id', '=', $reqest->get('employee_id'))
// ->update([
//     'create_form_at' => date('Y-m-d H:i:s'),
//     'remark' => 'Approval',
//     'approve_by' => $superior[0]->emp_id.'/'.$superior[0]->name,
//     'updated_at' => date('Y-m-d H:i:s')
// ]);

            if ($get_pic->emp_id != 'PI0109004' && $get_pic->emp_id != 'PI9902017' && $get_pic->emp_id != 'PI9709001') {
// $resume_check = db::select();

                $data = [
                    'safety_check' => $resume_check,
                ];
                Mail::to([$superior[0]->email])
                ->bcc(['nasiqul.ibat@music.yamaha.com'])
                ->send(new SendEmail($data, 'holiday_check'));
            }

            $response = array(
                'status' => true,
                'message' => 'Form Pengecekan Berhasil Disimpan',
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

    public function postSafetyCheckForm2(Request $request)
    {
        try {
// if ($request->input("kategori") == 'OFC') {

//     $safety = SafetyHolidayForm::where('created_by', '=', $request->get('employee_id'))->first();
//     if (count($safety) > 0) {
//         $response = array(
//             'status' => false,
//             'message' => 'Sudah Pernah Mengisi',
//             'cek' => $request->get('cek'),
//             'cek_all' => $request->get('total_check')
//         );
//         return Response::json($response);
//     }
// } else if ($request->input("kategori") == 'PRD') {
//     $safety = SafetyHolidayForm::where('location', '=', $request->get('bagian'))->first();
//     if (count($safety) > 0) {
//         $response = array(
//             'status' => false,
//             'message' => 'Sudah Pernah Mengisi',
//             'cek' => $request->get('cek'),
//             'cek_all' => $request->get('total_check')
//         );
//         return Response::json($response);
//     }
// }

            $poin_cek = json_decode($request->get('point_check'));
            $kondisi = json_decode($request->get('condition'));
            $ket = json_decode($request->get('note'));

// $poin_cek_new = json_decode($request->get('point_check_new'));
// $kondisi_new = json_decode($request->get('condition_new'));
// $ket_new = json_decode($request->get('note_new'));

// $poin_cek_sb = json_decode($request->get('point_check_sb'));
// $kondisi_sb = json_decode($request->get('condition_sb'));
// $ket_sb = json_decode($request->get('note_sb'));

            $supp_at = null;
            $file_name = [];

            $get_pic = EmployeeSync::where('employee_id', $request->get('employee_id'))->select(db::raw('UPPER(employee_id) as emp_id'), 'name', 'position', 'department', 'division')->first();

            if ($get_pic->emp_id == 'PI0109004' || $get_pic->emp_id == 'PI9902017' || $get_pic->emp_id == 'PI9709001') {
                $superior = db::select("SELECT users.email, UPPER(users.username) as emp_id, users.name FROM users where username = '" . $get_pic->emp_id . "'");
            } else if ($get_pic->position == 'Manager') {
                if ($get_pic->division == 'Human Resources & General Affairs Division') {
                    $superior = db::select("SELECT users.email, UPPER(users.username) as emp_id, users.name FROM users where username = 'PI9709001'");
                } else if ($get_pic->division == 'Production Division') {
                    $superior = db::select("SELECT users.email, UPPER(users.username) as emp_id, users.name FROM users where username = 'PI0109004'");
                } else if ($get_pic->division == 'Production Support Division') {
                    $superior = db::select("SELECT users.email, UPPER(users.username) as emp_id, users.name FROM users where username = 'PI9905001'");
                }
            } else {
                $superior = db::select("SELECT send_emails.email, UPPER(users.username) as emp_id, users.name FROM send_emails LEFT JOIN users on users.email = send_emails.email where remark = '" . $get_pic->department . "'");
            }
            $code_generator = CodeGenerator::where('note', '=', 'safety_holiday')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $form_id = 'SH' . $number;

            $tujuan_upload = 'files/safety_holiday/photo';

            for ($z = 0; $z < count($request->file('photo')); $z++) {
                $file = $request->file('photo')[$z];
                // dd($_FILES['photo_'.$i.'['.$z.']']);

                $nama = $file->getClientOriginalName();

                $filename = pathinfo($nama, PATHINFO_FILENAME);
                $extension = pathinfo($nama, PATHINFO_EXTENSION);

                $filename = md5('Photo_' . $request->get('cek') . '_'. $request->get('employee_id')  .'_' . date('YmdHisu')) . '.' . $extension;

                array_push($file_name, $filename);

                $file->move($tujuan_upload, $filename);
            }

            $safety_form = SafetyHolidayForm::insert([
                'form_id' => $form_id,
                'pic' => $get_pic->emp_id . '/' . $get_pic->name . '/' . $get_pic->position,
                'location' => $request->input("bagian"),
                'date_create' => date('Y-m-d'),
                'check_point' => $request->get('point_check'),
                'condition' => $request->get('condition'),
                'note' => $request->get('note'),
                'remark' => $request->get('cat'),
                'status' => 'Approval',
                'category' => $request->input("kategori"),
                'pic_sign' => $get_pic->emp_id . '/' . $get_pic->name,
                'pic_sign_at' => date('Y-m-d H:i:s'),
                'superior_sign' => $superior[0]->emp_id . '/' . $superior[0]->name,
                'superior_at' => $supp_at,
                'photo' => implode(',', $file_name),
                'created_by' => $get_pic->emp_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($request->get('cek') == $request->get('total_check')) {


                if ($get_pic->emp_id != 'PI0109004' && $get_pic->emp_id != 'PI9902017' && $get_pic->emp_id != 'PI9709001') {
                    $resume_check = db::select('SELECT pic, location, GROUP_CONCAT(created_at) as create_at from safety_holiday_forms where superior_sign = "' . $superior[0]->emp_id . '/' . $superior[0]->name . '" and DATE_FORMAT(date_create,"%Y-%m") = "'. date('Y-m') .'" group by pic, location');

                    $no_safe = db::select('SELECT pic, location, check_point, note, created_at from safety_holiday_forms where superior_sign = "' . $superior[0]->emp_id . '/' . $superior[0]->name . '" and `condition` = "Tidak Ada" and DATE_FORMAT(date_create,"%Y-%m") = "'. date('Y-m') .'"');

                    $data = [
                        'resume_check' => $resume_check,
                        'not_safe' => $no_safe,
                    ];
                    Mail::to([$superior[0]->email])
                    ->bcc(['nasiqul.ibat@music.yamaha.com', 'widura@music.yamaha.com'])
                    ->send(new SendEmail($data, 'holiday_check'));
                }

                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Form Pengecekan Berhasil Disimpan',
                'cek' => $request->get('cek'),
                'cek_all' => $request->get('total_check'),
            );
            return Response::json($response);

        } catch (Exception $e) {

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
                'cek' => $request->get('cek'),
                'cek_all' => $request->get('total_check'),
            );
            return Response::json($response);
        }
    }

    public function ApprovalSafetyCheck($form_id)
    {
        $safety_app = SafetyHolidayForm::where('form_id', '=', $form_id)
        ->update([
            'superior_at' => date('Y-m-d H:i:s'),
            'status' => 'Approved',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $detail_check = SafetyHolidayForm::where('form_id', $form_id)->select('form_id', 'pic', 'location', 'date_create', 'check_point', 'condition', 'note', 'category', 'remark', 'pic_sign', 'pic_sign_at', 'superior_sign', 'superior_at', 'created_at')->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        if ($detail_check[0]->category == 'Office') {
            $pdf->setPaper('A4', 'landscape');
        } else {
            $pdf->setPaper('A4', 'potrait');
        }

        $pdf->loadView('standardization.safety_check.report', array(
            'safety_check' => $detail_check,
        ));

        $pdf->save(public_path() . "/files/safety_holiday/" . $form_id . ".pdf");

        $title = "Form Pengecekan Safety Saat Akan Libur";

        return view('standardization.safety_check.approval_message', array(
            'title' => $title,
            'form' => $detail_check,
        ));
    }

    public function indexSafetyCheckMonitoring()
    {
        $title = 'Holiday Safety Check Monitoring';
        $title_jp = '';

        return view('standardization.safety_check.index_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ));
    }

    public function fetchSafetyCheckMonitoring(Request $request)
    {
        $tgl = '';

        if ($request->get('tgl')) {
            $tgl = $request->get('tgl');
        } else {
            $tgl = date('Y-m');
        }


        $office = db::select("SELECT IFNULL(department_shortname,IF(bagian = 'Human Resources & General Affairs Division' OR bagian = 'Production Support Division', 'DIV','')) as bagian2, SUM(tot) as tot, SUM(sudah) as sudah from
            (SELECT dept.bagian, COUNT(employee_id) as tot, SUM(IF(form.`status` is not null, 1,0)) as sudah from
            (
            SELECT employees.employee_id, employees.name , department as bagian from employees
            left join employee_syncs on employees.employee_id = employee_syncs.employee_id
            where remark = 'OFC' and employees.end_date is null
            and employees.employee_id not in ('PI2111045','PI2111044','PI0109004','PI9905001','PI9709001')
            UNION ALL
            SELECT employee_id, `name`, division from employee_syncs where employee_id in ('PI0109004','PI9905001','PI9709001')
            ) as dept
            left join (
            SELECT created_by, `status` from safety_holiday_forms where DATE_FORMAT(date_create,'%Y-%m') = '".$tgl."' group by created_by, status
            ) form on dept.employee_id = form.created_by
            group by bagian
            ) as semua
            left join departments on semua.bagian = departments.department_name
            group by bagian2
            order by bagian2 asc");

        $detail_office = db::select("SELECT dept.bagian, IFNULL(department_shortname,IF(bagian = 'Human Resources & General Affairs Division' OR bagian = 'Production Support Division', 'DIV','')) as bagian2, employee_id, name, IF(form.`status` is not null, 'sudah','belum') as stat from
            (
            SELECT employees.employee_id, employees.name , department as bagian from employees
            left join employee_syncs on employees.employee_id = employee_syncs.employee_id
            where remark = 'OFC' and employees.end_date is null
            and employees.employee_id not in ('PI2111045','PI2111044','PI0109004','PI9905001','PI9709001')
            UNION ALL
            SELECT employee_id, `name`, division from employee_syncs where employee_id in ('PI0109004','PI9905001','PI9709001')
            ) as dept
            left join (
            SELECT created_by, `status` from safety_holiday_forms where DATE_FORMAT(date_create,'%Y-%m') = '".$tgl."' group by created_by, status
            ) form on dept.employee_id = form.created_by
            left join departments on departments.department_name = dept.bagian
            order by stat asc");

// SELECT dept.employee_id, dept.`name`, dept.bagian, form.`status` from
//     (
//     SELECT employees.employee_id, employees.name , department as bagian from employees
//     left join employee_syncs on employees.employee_id = employee_syncs.employee_id
//     where remark = 'OFC' and employees.end_date is null
//     and employees.employee_id not in ('PI2111045','PI2111044','PI0109004','PI9905001','PI9709001')
//     UNION ALL
//     SELECT employee_id, `name`, division from employee_syncs where employee_id in ('PI0109004','PI9905001','PI9709001')
//     ) as dept
//     left join (
//     SELECT created_by, `status` from safety_holiday_forms group by created_by, status
// ) form on dept.employee_id = form.created_by

        $production = db::select("SELECT semua.department, departments.department_shortname as bagian, jml_grp, sudah from
            (SELECT employee.department, jml_grp, IFNULL(sum(form.sudah), 0) as sudah from
            (
            SELECT department, count(`Group`) as jml_grp from
            (SELECT department, `group` from employee_syncs where end_date is null and (division = 'Production Division' or department = 'Logistic Department') and `group` is not null and `group` <> 'Standardization Group' group by `group`, department) as emp
            group by department
            ) as employee
            LEFT JOIN
            (
            SELECT location, emps.department, 1 as sudah from
            (select location, pic, `status` from safety_holiday_forms where category = 'PRD' and DATE_FORMAT(date_create,'%Y-%m') = '".$tgl."' group by location, pic, `status`) as form_holiday
            left join (SELECT `group`, department from employee_syncs group by `group`, department) as emps on form_holiday.location = emps.`Group`
            ) as form on employee.department = form.department
            group by employee.department, jml_grp) as semua
            left join departments on departments.department_name = semua.department
            order by semua.department asc");

        $detail_production = db::select("SELECT employee.department as bagian, department_shortname as bagian2, `group`, form.pic, IF(form.sudah is not null, 'sudah', 'belum') as stat from
            (
            SELECT department, `Group` from
            (SELECT department, `group` from employee_syncs where end_date is null and (division = 'Production Division' or department = 'Logistic Department') and `group` is not null and `group` <> 'Standardization Group' group by `group`, department) as emp
            group by department, `Group`
            ) as employee
            LEFT JOIN
            (
            SELECT location, pic, emps.department, 1 as sudah from
            (select location, pic, `status` from safety_holiday_forms where category = 'PRD' and DATE_FORMAT(date_create,'%Y-%m') = '".$tgl."' group by location, pic, `status`) as form_holiday
            left join (SELECT `group`, department from employee_syncs group by `group`, department) as emps on form_holiday.location = emps.`Group`
            ) as form on employee.`group` = form.location
            left join departments on departments.department_name = employee.department
            order by stat asc");

// SELECT employee.section, pic, `status` from
// (SELECT section, CONCAT(section,' - ', department ) as bagian from employee_syncs where end_date is null and division = 'Production Division' and section is not null group by section, department) as employee
// LEFT JOIN
// (select SUBSTRING_INDEX(location, '-', 1) as section, location, pic, `status` from safety_holiday_forms where category = 'PRD' group by location, pic, `status`) as form on employee.section = form.section

        $response = array(
            'status' => true,
            'office' => $office,
            'production' => $production,
            'office_detail' => $detail_office,
            'production_detail' => $detail_production,
        );
        return Response::json($response);
    }

    public function indexSafetyCheckDetail($location, $param, $date)
    {
        $title = 'Holiday Safety Check Monitoring';
        $title_jp = 'Holiday Safety Check Monitoring';

         $tgl = '';

        if ($date) {
            $tgl = $date;
        } else {
            $tgl = date('Y-m');
        }


        if ($location == 'Office') {
            $data = db::select('SELECT pic, location, date_create, check_point, `condition`, note, photo, category, remark from safety_holiday_forms where created_by = "' . $param . '" AND DATE_FORMAT(date_create,"%Y-%m") = "'.$tgl.'"');
        } else {
            $data = db::select('SELECT pic, location, date_create, check_point, `condition`, note, photo, category, remark from safety_holiday_forms where location = "' . $param . '" AND DATE_FORMAT(date_create,"%Y-%m") = "'.$tgl.'"');
        }

        return view('standardization.safety_check.report', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'data' => $data,
        ));
    }

// ---------- SAFETY KY HH ----------

    public function IndexMonitoringKyHH()
    {

        $title = 'Kiken Yochi & Hiyari Hatto (KYT)';
        $title_jp = '(危険予知とヒヤリハット)';

        $fy = db::select('SELECT DISTINCT
            fiscal_year 
            FROM
            weekly_calendars 
            ORDER BY
            fiscal_year DESC');

        return view('standardization.ky_hh.monitoring_ky_hh', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'fy' => $fy
        ));

        // $nik = Auth::user()->username;

        // $role = Auth::user()->username;

        // $data_user = EmployeeSync::where('employee_id', $nik)->select('department', 'section', 'position')->first();

        // $nama_tim = db::connection('ympimis_2')->select('select nama_tim, nik from std_teams where nik = "' . $nik . '"');

        // $divisi = db::select('select division from employee_syncs where employee_id = "' . $nik . '"');

        // $lokasi = db::connection('ympimis_2')->select('select * from ympi_locations order by location ASC');

        // $dept = db::select('SELECT
        //     employee_id,
        //     `name`,
        //     department_shortname,
        //     department,
        //     section,
        //     `group`,
        //     sub_group
        //     FROM
        //     employee_syncs
        //     LEFT JOIN departments ON employee_syncs.department = departments.department_name
        //     WHERE end_date IS NULL
        //     AND employee_id LIKE "%PI%"
        //     AND department IS NOT NULL
        //     ORDER BY
        //     `name` ASC
        //     ');
        // if (count($nama_tim) > 0) {
        //     $user = '';

        //     $user_team = db::connection('ympimis_2')->select("SELECT
        //         nik,
        //         nama,
        //         department_short,
        //         department,
        //         section,
        //         `group`,
        //         sub_group
        //         FROM `std_teams` WHERE nama_tim = '" . $nama_tim[0]->nama_tim . "'");

        //     $karyawan = db::select("SELECT
        //         employee_id,
        //         `name`,
        //         department_shortname,
        //         department,
        //         section,
        //         `group`,
        //         sub_group
        //         FROM
        //         employee_syncs
        //         LEFT JOIN departments ON employee_syncs.department = departments.department_name
        //         WHERE end_date IS NULL
        //         AND employee_id LIKE '%PI%'
        //         AND department IS NOT NULL
        //         ORDER BY
        //         `name` ASC
        //         ");

        //     if ($nik == 'PI2101044') {
        //         $user = db::select("SELECT
        //             employee_id,
        //             `name`,
        //             department_shortname,
        //             department,
        //             section,
        //             `group`,
        //             sub_group
        //             FROM
        //             employee_syncs
        //             LEFT JOIN departments ON employee_syncs.department = departments.department_name
        //             WHERE end_date IS NULL
        //             ORDER BY
        //             employee_id ASC
        //             ");

        //     } else {
        //         $user = db::select("SELECT
        //             employee_id,
        //             `name`,
        //             department_shortname,
        //             department,
        //             section,
        //             `group`,
        //             sub_group
        //             FROM
        //             employee_syncs
        //             LEFT JOIN departments ON employee_syncs.department = departments.department_name
        //             WHERE
        //             department = '" . $data_user->department . "'
        //             AND end_date IS NULL
        //             ORDER BY
        //             employee_id ASC
        //             ");
        //     }

        //     if (count($nama_tim) > 0) {
        //         $data_tim = db::connection('ympimis_2')->select('select nama_tim, urutan, nik, nama, department_short, department, section, `group`, sub_group, posisi, remark from std_teams where nama_tim = "' . $nama_tim[0]->nama_tim . '" and remark is null');

        //         $urutan = db::connection('ympimis_2')->select('select urutan from std_teams where nama_tim = "' . $nama_tim[0]->nama_tim . '" order by urutan desc limit 1');

        //         $cek_soal = db::connection('ympimis_2')->select('SELECT
        //             kode_soal
        //             FROM
        //             std_questions
        //             WHERE
        //             `view` = "Tampil"
        //             GROUP BY
        //             kode_soal');

        //         $open_soal = 0;

        //         if (count($cek_soal) > 0) {
        //             $open_soal = count($cek_soal);
        //         }

        //         return view('standardization.ky_hh.monitoring_ky_hh', array(
        //             'title' => $title,
        //             'title_jp' => $title_jp,
        //             'user' => $user,
        //             'nama_tim' => $nama_tim,
        //             'data_tim' => $data_tim,
        //             'urutan' => $urutan,
        //             'open_soal' => $open_soal,
        //             'role' => $role,
        //             'karyawan' => $karyawan,
        //             'user_team' => $user_team,
        //             'dept' => $dept,
        //             'location' => $lokasi,
        //         ));
        //     }
        // }
    }

    public function indexKyHh()
    {
        $title = 'Kiken Yochi & Hiyari Hatto (KYT)';
        $title_jp = '(危険予知とヒヤリハット)';
        $nik = Auth::user()->username;
        $role = Auth::user()->username;
        $data_user = EmployeeSync::where('employee_id', $nik)
        ->select('department', 'section', 'position')->first();
        $nama_tim = db::connection('ympimis_2')->select('select nama_tim, nik from std_teams where nik = "' . $nik . '"');
        $divisi = db::select('select division from employee_syncs where employee_id = "' . $nik . '"');
        $lokasi = db::connection('ympimis_2')->select('select * from ympi_locations order by location ASC');

        $dept = db::select('SELECT
            employee_id,
            `name`,
            department_shortname,
            department,
            section,
            `group`,
            sub_group
            FROM
            employee_syncs
            LEFT JOIN departments ON employee_syncs.department = departments.department_name
            WHERE end_date IS NULL
            AND employee_id LIKE "%PI%"
            AND department IS NOT NULL
            ORDER BY
            `name` ASC
            ');

        if (($data_user->position == 'Chief') || ($data_user->position == 'Coordinator') || ($data_user->position == 'Foreman') || ($data_user->position == 'Manager') || ($data_user->position == 'Senior Staff') || ($data_user->position == 'Staff') || ($data_user->position == 'Operator Contract')) {

            $username = Auth::user()->username;
            $role_code = EmployeeSync::where('employee_id', $nik)
            ->select('department', 'section', 'position')->first();

            $department = db::select('select department from employee_syncs where employee_id = "' . $username . '"');
            $user = db::select('SELECT employee_id, name, position, department, section, `group`, sub_group FROM employee_syncs where end_date is null order by name ASC');
            $tim = db::connection('ympimis_2')->select('select nama_tim from std_teams where nik = "' . $username . '" and remark is null group by nama_tim order by nama_tim asc');

            $employee_sync = db::select('select employee_id, `name`, department, section, `group`, sub_group from employee_syncs where end_date is null and section = "' . $data_user->section . '" order by employee_id asc');
            $employee_record = db::connection('ympimis_2')->select('select distinct nik, nama from std_teams where id_leader = "'.$username.'"');

            return view('standardization.ky_hh.index_staff', array(
                'title' => 'Kiken Yochi & Hiyari Hatto (KYT)',
                'title_jp' => '(危険予知とヒヤリハット)',
                'user' => $user,
                'location' => $lokasi,
                'tim' => $tim,
                'username' => $username,
                'employee_sync' => $employee_sync,
                'employee_record' => $employee_record,
                'role_code' => $role_code
            ));

        } else if (($data_user->position == 'Leader')) {
            $username = Auth::user()->username;
            $role_code = EmployeeSync::where('employee_id', $nik)
            ->select('department', 'section', 'position')->first();

            $section = db::select('select section from employee_syncs where employee_id = "' . $username . '"');
            $department = db::select('select department from employee_syncs where employee_id = "' . $username . '"');
            $user = db::select('SELECT employee_id, name, position, department, section, `group`, sub_group FROM employee_syncs where end_date is null and department = "' . $department[0]->department . '" order by name ASC');
            $tim = db::connection('ympimis_2')->select('select nama_tim from std_teams where id_leader = "' . $username . '" and remark is null group by nama_tim order by nama_tim asc');

            $employee_sync = db::select('select employee_id, `name`, department, section, `group`, sub_group from employee_syncs where end_date is null and section = "' . $data_user->section . '" order by employee_id asc');
            $employee_record = db::connection('ympimis_2')->select('select distinct nik, nama from std_teams where id_leader = "'.$username.'"');

            return view('standardization.ky_hh.index_leader', array(
                'title' => 'Kiken Yochi & Hiyari Hatto (KYT)',
                'title_jp' => '(危険予知とヒヤリハット)',
                'user' => $user,
                'location' => $lokasi,
                'tim' => $tim,
                'employee_record' => $employee_record, 
                'employee_sync' => $employee_sync,
                'role_code' => $role_code
            ));
        }
    }

    public function FetchHomeLeader(Request $request)
    {
        try {
            $username = Auth::user()->username;
            $bulan_sekarang = date('Y-m');
            $data_user = EmployeeSync::where('employee_id', $username)->select('department', 'section', 'position')->first();

            if (($data_user->position == 'Chief') || ($data_user->position == 'Coordinator') || ($data_user->position == 'Foreman') || ($data_user->position == 'Manager') || ($data_user->position == 'Senior Staff') || ($data_user->position == 'Staff')) {

                $data = db::connection('ympimis_2')->select('
                    SELECT
                    nama_tim,
                    GROUP_CONCAT( DISTINCT COALESCE ( nama, " " ) ORDER BY std_teams.urutan ASC ) AS nama,
                    GROUP_CONCAT( DISTINCT COALESCE ( nik, " " ) ORDER BY std_teams.urutan ASC ) AS nik,
                    std_questions.periode 
                    FROM
                    std_teams
                    LEFT JOIN std_questions ON std_teams.soal = std_questions.kode_soal 
                    WHERE
                    std_teams.remark IS NULL 
                    GROUP BY
                    nama_tim,
                    DATE_FORMAT( std_teams.created_at, "%Y-%m" ), std_questions.periode
                    HAVING
                    NIK LIKE "%' . $username . '%" 
                    ORDER BY
                    std_questions.periode');

                $data_resume = db::connection('ympimis_2')->select('
                    SELECT
                    std_teams.nama_tim,
                    GROUP_CONCAT( COALESCE ( nama, " " ) ORDER BY std_teams.urutan ASC ) AS nama,
                    GROUP_CONCAT( COALESCE ( nik, " " ) ORDER BY std_teams.urutan ASC ) AS nik,
                    periode,
                    id_jawaban,
                    score,
                    atasan,
                    std_teams.soal
                    FROM
                    std_teams
                    LEFT JOIN std_questions AS sq ON sq.kode_soal = std_teams.soal
                    LEFT JOIN std_answers AS sa ON sa.id = std_teams.id_jawaban
                    WHERE
                    std_teams.remark IS NOT NULL
                    GROUP BY
                    nama_tim,
                    periode,
                    id_jawaban,
                    score,
                    atasan,
                    soal
                    HAVING
                    NIK LIKE "%' . $username . '%"
                    ORDER BY
                    periode DESC');

                $tim = $request->get('tim');

                $list = db::connection('ympimis_2')->select('SELECT id, nama, nama_tim, SUBSTRING_INDEX( urutan, "_", - 1 ) AS urutan FROM std_teams WHERE nama_tim = "' . $tim . '" and remark is null ORDER BY CAST(SUBSTRING_INDEX( urutan, "_", - 1 ) AS UNSIGNED) ASC');

                if ($request->get('jenis') == 'Edit Tim') {
                    $nama_tim = $request->get('nama_tim');
                    $list_edit = db::connection('ympimis_2')->select('SELECT id, nama, nama_tim, SUBSTRING_INDEX( urutan, "_", - 1 ) AS urutan FROM std_teams WHERE nama_tim = "' . $nama_tim . '" and remark is null ORDER BY CAST(SUBSTRING_INDEX( urutan, "_", - 1 ) AS UNSIGNED) ASC');

                    $response = array(
                        'status' => true,
                        'list_edit' => $list_edit,
                    );
                    return Response::json($response);
                }

                $employee_sync = db::select('select employee_id, `name`, department, section, `group`, sub_group from employee_syncs where end_date is null and employee_id like "%PI%" and department = "' . $data_user->department . '" order by employee_id asc');
                $employee_record = db::connection('ympimis_2')->select('select distinct nik, nama from std_teams where department = "' . $data_user->department . '"');

            } else {
                // $data = db::connection('ympimis_2')->select('SELECT nama_tim, GROUP_CONCAT(COALESCE( nama, " " ) ORDER BY urut ASC) as nama from (SELECT *, CAST(SUBSTRING_INDEX( urutan, " ", - 1 ) AS UNSIGNED) as urut from std_teams where remark is null and id_leader = "'.$username.'" AND DATE_FORMAT( created_at, "%Y-%m" ) = "'.$bulan_sekarang.'") as mstr GROUP BY nama_tim');
                // $data = db::connection('ympimis_2')->select('SELECT
                // nama_tim,
                // GROUP_CONCAT( COALESCE ( nama, " " ) ORDER BY urutan ASC ) AS nama,
                // DATE_FORMAT(created_at,"%Y-%m") as periode
                // FROM
                // std_teams
                // WHERE
                // remark IS NULL
                // AND id_leader = "' . $username . '"
                // GROUP BY
                // nama_tim, DATE_FORMAT(created_at,"%Y-%m")
                // ORDER BY periode');

                $data = db::connection('ympimis_2')->select('
                    SELECT
                    nama_tim,
                    GROUP_CONCAT( DISTINCT COALESCE ( nama, " " ) ORDER BY std_teams.urutan ASC ) AS nama,
                    GROUP_CONCAT( DISTINCT COALESCE ( nik, " " ) ORDER BY std_teams.urutan ASC ) AS nik,
                    std_questions.periode 
                    FROM
                    std_teams
                    LEFT JOIN std_questions ON std_teams.soal = std_questions.kode_soal 
                    WHERE
                    std_teams.remark IS NULL AND id_leader = "' . $username . '"
                    GROUP BY
                    nama_tim,
                    DATE_FORMAT( std_teams.created_at, "%Y-%m" ), std_questions.periode
                    ORDER BY
                    std_questions.periode');

                $data_resume = db::connection('ympimis_2')->select('SELECT
                    std_teams.nama_tim,
                    GROUP_CONCAT( COALESCE ( nama, " " ) ORDER BY std_teams.urutan ASC ) AS nama,
                    periode,
                    id_jawaban,
                    score,
                    atasan,
                    std_teams.soal
                    FROM
                    std_teams
                    LEFT JOIN std_questions AS sq ON sq.kode_soal = std_teams.soal
                    LEFT JOIN std_answers AS sa ON sa.id = std_teams.id_jawaban
                    WHERE
                    std_teams.remark IS NOT NULL
                    AND id_leader = "' . $username . '"
                    GROUP BY
                    nama_tim,
                    periode,
                    id_jawaban,
                    score,
                    atasan,
                    soal
                    ORDER BY
                    periode DESC');

                $tim = $request->get('tim');

                $list = db::connection('ympimis_2')->select('SELECT id, nama, nama_tim, SUBSTRING_INDEX( urutan, "_", - 1 ) AS urutan FROM std_teams WHERE nama_tim = "' . $tim . '" and remark is null ORDER BY CAST(SUBSTRING_INDEX( urutan, "_", - 1 ) AS UNSIGNED) ASC');

                if ($request->get('jenis') == 'Edit Tim') {
                    $nama_tim = $request->get('nama_tim');
                    $list_edit = db::connection('ympimis_2')->select('SELECT id, nama, nama_tim, SUBSTRING_INDEX( urutan, "_", - 1 ) AS urutan FROM std_teams WHERE nama_tim = "' . $nama_tim . '" and remark is null ORDER BY CAST(SUBSTRING_INDEX( urutan, "_", - 1 ) AS UNSIGNED) ASC');

                    $response = array(
                        'status' => true,
                        'list_edit' => $list_edit,
                    );
                    return Response::json($response);
                }

                $employee_sync = db::select('select employee_id, `name`, department, section, `group`, sub_group from employee_syncs where end_date is null and employee_id like "%PI%" and section = "' . $data_user->section . '" order by employee_id asc');
                $employee_record = db::connection('ympimis_2')->select('select distinct nik, nama from std_teams where id_leader = "'.$username.'"');
            }

            $response = array(
                'status' => true,
                'data' => $data,
                'data_resume' => $data_resume,
                'list' => $list,
                'employee_record' => $employee_record,
                'employee_sync' => $employee_sync
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

    public function DetailKaryawanMonitoring(Request $request)
    {
        try {

            $department = $request->get('id');
            
            $employee_sync = db::select('select employee_id, `name`, department, section, `group`, sub_group from employee_syncs where end_date is null and employee_id like "%PI%" and department = "' . $department . '" order by employee_id asc');
            $employee_record = db::connection('ympimis_2')->select('select distinct nik, nama from std_teams where department = "' . $department . '"');

            $response = array(
                'status' => true,
                'employee_record' => $employee_record,
                'employee_sync' => $employee_sync
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

    public function FetchResumeKYAll(Request $request)
    {
        try {
            $data_resume = db::connection('ympimis_2')->select('
                SELECT
                std_teams.nama_tim,
                GROUP_CONCAT( COALESCE ( nama, " " ) ORDER BY std_teams.urutan ASC ) AS nama,
                GROUP_CONCAT( COALESCE ( nik, " " ) ORDER BY std_teams.urutan ASC ) AS nik,
                periode,
                id_jawaban,
                score,
                atasan,
                std_teams.soal
                FROM
                std_teams
                LEFT JOIN std_questions AS sq ON sq.kode_soal = std_teams.soal
                LEFT JOIN std_answers AS sa ON sa.id = std_teams.id_jawaban
                WHERE
                std_teams.remark IS NOT NULL
                GROUP BY
                nama_tim,
                periode,
                id_jawaban,
                score,
                atasan,
                std_teams.soal
                ORDER BY
                periode DESC');

            $response = array(
                'status' => true,
                'data_resume' => $data_resume,
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

    public function FetchMonitoringKyHH(Request $request)
    {
        try {
            $fy_select = $request->get('fiscal_year');
            $date_now = date('Y-m-d');
            $month_now = date('F Y');
            $fy = db::select('SELECT fiscal_year FROM weekly_calendars where week_date = "'.$date_now.'"');
            $wc = db::select('SELECT DISTINCT date_format( week_date, "%Y-%m" ) as bulan FROM weekly_calendars WHERE fiscal_year = "'.$fy_select.'" ORDER BY id ASC');


            $data = db::connection('ympimis_2')->select('SELECT DISTINCT
                (DATE_FORMAT( std_hiyarihatos.tanggal, "%Y-%m" )) AS periode,
                count( id ) AS jumlah,
                sum( CASE WHEN std_hiyarihatos.remark = "Open" THEN 1 ELSE 0 END ) AS open,
                sum( CASE WHEN std_hiyarihatos.remark = "Close" THEN 1 ELSE 0 END ) AS close
                FROM
                std_hiyarihatos 
                GROUP BY
                DATE_FORMAT(
                std_hiyarihatos.tanggal,
                "%Y-%m")');

            $belum = db::connection('ympimis_2')->select('SELECT
                count( id ) AS jumlah,
                DATE_FORMAT( created_at, "%Y-%m" ) AS periode 
                FROM
                std_teams 
                WHERE
                remark IS NULL 
                GROUP BY
                DATE_FORMAT(
                created_at,
                "%Y-%m")');

            $sudah = db::connection('ympimis_2')->select('SELECT
                count( id ) AS jumlah,
                DATE_FORMAT( created_at, "%Y-%m" ) AS periode 
                FROM
                std_teams 
                WHERE
                remark IS NOT NULL 
                GROUP BY
                DATE_FORMAT(
                created_at,
                "%Y-%m")');

            $hh_open = db::connection('ympimis_2')->select('SELECT
                count( id ) jumlah
                FROM
                std_hiyarihatos 
                WHERE
                remark = "Open"');

            $hh_close = db::connection('ympimis_2')->select('SELECT
                count( id ) jumlah
                FROM
                std_hiyarihatos 
                WHERE
                remark = "Close"');

            $ky_sudah = db::connection('ympimis_2')->select('SELECT
                count( p.nama_tim ) AS jml,
                periode 
                FROM
                (
                SELECT
                nama_tim,
                periode 
                FROM
                std_teams AS st
                LEFT JOIN std_questions sq ON st.soal = sq.kode_soal 
                WHERE
                st.remark IS NOT NULL 
                AND posisi = "Ketua" 
                GROUP BY
                nama_tim,
                periode 
                ) AS p 
                GROUP BY
                p.periode');

            $bulan_periode = date('Y-m');

            $ky_ppp = db::connection('ympimis_2')->select('SELECT
                count( p.nama_tim ) AS jml,
                periode 
                FROM
                (
                SELECT
                nama_tim,
                periode 
                FROM
                std_teams AS st
                LEFT JOIN std_questions sq ON st.soal = sq.kode_soal 
                WHERE
                st.remark IS NOT NULL 
                AND posisi = "Ketua" 
                GROUP BY
                nama_tim,
                periode 
                ) AS p 
                where p.periode = "'.$bulan_periode.'"
                GROUP BY
                p.periode');

            $jumlah_tim_ky = db::connection('ympimis_2')->select('SELECT DISTINCT
                nama_tim,
                nama,
                department 
                FROM
                std_teams 
                WHERE
                posisi = "Ketua"');
            // $tim_open_now = count($jumlah_tim_ky) - $ky_ppp[0]->jml;

            $tim_open_now = '';
            if (count($ky_ppp) < 1) {
                $tim_open_now = 0;
            }else{
                $tim_open_now = count($jumlah_tim_ky) - $ky_ppp[0]->jml;                
            }


            $response = array(
                'status' => true,
                'wc' => $wc,
                'data' => $data,
                'belum' => $belum,
                'sudah' => $sudah,
                'hh_open' => $hh_open,
                'hh_close' => $hh_close,
                'ky_sudah' => $ky_sudah,
                'jumlah_tim_ky' => count($jumlah_tim_ky),
                'tim_open_now' => $tim_open_now,
                // 'month_now' => $month_now
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

    public function FetchAnggotaTim(Request $request)
    {
        try {
            // disini yaa

            $username = Auth::user()->username;
            $tim = $request->get('tim');

            $data_user = EmployeeSync::where('employee_id', $username)
            ->select('department', 'section', 'position')->first();

            // if(($data_user->position == 'Chief')||($data_user->position == 'Coordinator')||($data_user->position == 'Foreman')||($data_user->position == 'Manager')||($data_user->position == 'Senior Staff')||($data_user->position == 'Staff')){
            //     $anggota_tim = db::connection('ympimis_2')->select('select nik, nama, department_short, department, section, `group`, sub_group from std_teams where nama_tim = "'.$tim.'" and remark is null order by nama asc');
            // }else{
            $anggota_tim = db::connection('ympimis_2')->select('select nik, nama, department_short, department, section, `group`, sub_group from std_teams where nama_tim = "' . $tim . '" and remark is null order by nama asc');
            // }

            $saksi = db::select('select name from employee_syncs where end_date is null order by name asc');

            $lokasi = db::connection('ympimis_2')->select('select * from ympi_locations order by location asc');

            $response = array(
                'status' => true,
                'anggota_tim' => $anggota_tim,
                'saksi' => $saksi,
                'lokasi' => $lokasi,
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

    public function CreateTimLeader(Request $request)
    {
        try {
            $username = Auth::user()->username;
            $prefix_now = 'STDKY';
            $code_generator = CodeGenerator::where('note', '=', 'STD KY TIM')->first();
            if ($prefix_now != $code_generator->prefix) {
                $code_generator->prefix = $prefix_now;
                $code_generator->save();
            }

            $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $slip = $code_generator->prefix . $numbers;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $response = array(
                'status' => true,
                'slip' => $slip,
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

    public function InjectTimLeader(Request $request)
    {
        try {
            $username = Auth::user()->username;
            // $kode = db::connection('ympimis_2')->table('std_questions')
            //     ->where('view', '=', 'Tampil')
            //     ->select('kode_soal')
            //     ->first();

            // $kode_soal = $kode->kode_soal;

            if ($request->get('jenis') == 'Edit Tim') {
                $tim_edit = $request->get('tim_edit');
                $user_edit = $request->get('user_edit');
                $header_edit = $request->get('header_edit');

                $explode_edit = explode('/', $user_edit);

                $dept_short = db::select('select department_shortname from departments where department_shortname != "JPN" and department_name = "' . $explode_edit[3] . '" order by  department_name asc');

                $std_team_edit = db::connection('ympimis_2')->select('select * from std_teams where nama_tim = "' . $tim_edit . '" order by id desc limit 1');
                $urutan_edit = $std_team_edit[0]->urutan;
                $q_edit = $urutan_edit + 1;

                $insert = db::connection('ympimis_2')->table('std_teams')
                ->insert([
                    'nama_tim' => $tim_edit,
                    'urutan' => $q_edit,
                    'nik' => $explode_edit[0],
                    'nama' => $explode_edit[1],
                    'posisi' => $header_edit,
                        // 'soal' => $kode_soal,
                    'department' => $explode_edit[3],
                    'section' => $explode_edit[4],
                    'group' => $explode_edit[5],
                    'sub_group' => $explode_edit[6],
                    'department_short' => $dept_short[0]->department_shortname,
                    'id_leader' => $username,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $tim = $request->get('tim');
                $user = $request->get('user');
                $header = $request->get('header');

                $explode_user = explode('/', $user);

                $std_team = db::connection('ympimis_2')->select('select * from std_teams where nama_tim = "' . $tim . '" order by id desc limit 1');

                $dept_short = db::select('select department_shortname from departments where department_shortname != "JPN" and department_name = "' . $explode_user[3] . '" order by  department_name asc');

                if (isset($std_team[0]->urutan) != 0) {
                    $urutan = $std_team[0]->urutan;
                    $q = $urutan + 1;
                } else {
                    $q = 1;
                }

                $insert = db::connection('ympimis_2')->table('std_teams')
                ->insert([
                    'nama_tim' => $tim,
                    'urutan' => $q,
                    'nik' => $explode_user[0],
                    'nama' => $explode_user[1],
                    'posisi' => $header,
                        // 'soal' => $kode_soal,
                    'id_leader' => $username,
                    'department' => $explode_user[3],
                    'section' => $explode_user[4],
                    'group' => $explode_user[5],
                    'sub_group' => $explode_user[6],
                    'department_short' => $dept_short[0]->department_shortname,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
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

    public function DeleteListAnggota(Request $request)
    {
        try {
            $tim = $request->get('tim');
            $delete = '';
            if ($jenis = $request->get('jenis') == 'Hapus Tim') {
                $delete = db::connection('ympimis_2')->delete('DELETE FROM std_teams WHERE nama_tim = "' . $tim . '"');
            } else {
                $id = $request->get('id');
                $delete = db::connection('ympimis_2')->delete('DELETE FROM std_teams WHERE id = "' . $id . '"');
                $cek = db::connection('ympimis_2')->select('select id from std_teams where nama_tim = "' . $tim . '"');

                for ($i = 0; $i < count($cek); $i++) {
                    $update_urutan = db::connection('ympimis_2')->table('std_teams')
                    ->where('id', $cek[$i]->id)
                    ->update([
                        'urutan' => ($i + 1),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
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

    public function MovePositionAnggota(Request $request)
    {
        try {
            $id_user = Auth::user()->id;
            $id = $request->get('id');
            $tim = $request->get('tim');

            if ($request->get('jenis') == 'Naikkan') {
                $select = db::connection('ympimis_2')->select('select id, urutan from std_teams where id = "' . $id . '"');
                $urutan = $select[0]->urutan;
                $q = $urutan - 1;
                $w = $q + 1;

                $up = db::connection('ympimis_2')->table('std_teams')
                ->where('nama_tim', $tim)
                ->where('urutan', $q)
                ->update([
                    'urutan' => $w,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $update_urutan = db::connection('ympimis_2')->table('std_teams')
                ->where('id', $id)
                ->update([
                    'urutan' => $q,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $select = db::connection('ympimis_2')->select('select id, urutan from std_teams where id = "' . $id . '"');
                $urutan = $select[0]->urutan;
                $q = $urutan + 1;
                $w = $q - 1;

                $up = db::connection('ympimis_2')->table('std_teams')
                ->where('nama_tim', $tim)
                ->where('urutan', $q)
                ->update([
                    'urutan' => $w,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $update_urutan = db::connection('ympimis_2')->table('std_teams')
                ->where('id', $id)
                ->update([
                    'urutan' => $q,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
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

    public function uploadKyHh(Request $request)
    {
        $id = Auth::id();
        try {
            $periode_kyt = $request->get('periode_kyt');
            $gambar = $request->file('file_gambar');

            $prefix_now = 'KY';
            $code_generator = CodeGenerator::where('note', '=', 'STD_KY')->first();
            if ($prefix_now != $code_generator->prefix) {
                $code_generator->prefix = $prefix_now;
                $code_generator->save();
            }

            $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $slip = $code_generator->prefix . $numbers;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            if ($gambar != null) {
                $test = $gambar->getClientOriginalName();
                $extension = pathinfo($test, PATHINFO_EXTENSION);

                $nama_gambar = $slip . '_gambar.' . $extension;
                $gambar->move('data_file/std/ky', $nama_gambar);

                $data_gambar = db::connection('ympimis_2')->table('std_images')->insert([
                    'kode_soal' => $slip,
                    'nama_gambar' => $nama_gambar,
                    'remark' => $request->get('judul'),
                    'created_by' => $id,
                    // 'view' => 'Hide',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // $update = db::connection('ympimis_2')->table('std_questions')->where('view', '=', 'Tampil')->update([
                //     'view' => 'Hide']);

                $lop = $request->get('lop');
                $lop_jawaban = $request->get('lop_jawaban');
                $jawaban = array();

                for ($i = 1; $i <= $lop; $i++) {
                    $soal = "header" . $i;

                    for ($z = 1; $z <= $lop_jawaban; $z++) {
                        $jawab = "header" . $z . "_jawaban";
                        array_push($jawaban, $request->get($jawab));
                    }

                    $data_soal = db::connection('ympimis_2')->table('std_questions')->insert([
                        'kode_soal' => $slip,
                        'urutan' => $i,
                        'soal' => $request->get($soal),
                        'remark' => $request->get('judul'),
                        'view' => 'Tampil',
                        'created_by' => $id,
                        'periode' => $periode_kyt,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'jawaban' => json_encode($jawaban),
                        'count' => $lop_jawaban,
                    ]);
                }

                // $update = db::connection('ympimis_2')->table('std_teams')->whereNull('soal')->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m'))"), "=", $periode_kyt)->update([
                //     'soal' => $slip]);

                // $username = Auth::user()->username;
                // $nama = db::connection('ympimis_2')->select("select nama_tim, nik from std_teams where nik = '".$username."'");
                // $nama_tim = $nama[0]->nama_tim;

                // $update_kehadiran = db::connection('ympimis_2')->table('std_teams')->update([
                //   'remark' => null]);

                return redirect('/index/ky_hh')->with('status', 'Berhasil Di Upload')->with('page', 'Success');
            } else {
                return redirect('/index/ky_hh')->with('error', 'Isikan Data Dengan Lengkap!')->with('page', 'Error');
            }
        } catch (QueryException $e) {
            return redirect('/index/ky_hh')->with('error', $e->getMessage())->with('page', 'Error');
        }
    }

    public function logKyHh()
    {
        $title = 'Kiken Yochi & Hiyari Hatto (KYT)';
        $title_jp = '(危険予知とヒヤリハット)';

        return view('standardization.ky_hh.log_soal', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ));
    }

    public function fatchLogKyHh(Request $request)
    {
        $log = db::connection('ympimis_2')->select("SELECT
            nama_gambar,
            std_images.kode_soal,
            std_images.remark,
            `view`,
            periode
            FROM
            std_questions
            LEFT JOIN std_images ON std_questions.kode_soal = std_images.kode_soal
            GROUP BY
            nama_gambar,
            kode_soal,
            remark,
            `view`,
            periode");

        $response = array(
            'status' => true,
            'resumes' => $log,
        );
        return Response::json($response);
    }

    public function UpdateBukaSoal(Request $request)
    {
        try {
            $kode_soal = $request->get('kode_soal');

            $update_soal = db::connection('ympimis_2')->table('std_questions')->where('std_questions.kode_soal', '=', $kode_soal)->update([
                'view' => 'Tampil']);

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

    public function UpdateTutupSoal(Request $request)
    {
        try {
            $cek = db::connection('ympimis_2')->select('select id from std_teams where remark is null');

            // if (count($cek) == 0) {
            $kode_soal = $request->get('kode_soal');
            $jawaban = db::connection('ympimis_2')->select('select jawaban, `count` from std_questions where kode_soal = "' . $kode_soal . '"');
            // dd($jawaban[0]->jawaban); // sampe sini dulu ya (6 september 2022)

            $std_teams = db::connection('ympimis_2')->select('select nama_tim, urutan, nik, nama, department_short, department, section, `group`, sub_group, posisi, remark, created_at from std_teams');

            // for ($i=0; $i < count($std_teams); $i++) {
            //     $input = db::connection('ympimis_2')->table('std_log_teams')->insert([
            //         'kode_soal' => $kode_soal,
            //         'nama_tim' => $std_teams[$i]->nama_tim,
            //         'urutan' => $std_teams[$i]->urutan,
            //         'nik' => $std_teams[$i]->nik,
            //         'nama' => $std_teams[$i]->nama,
            //         'department_short' => $std_teams[$i]->department_short,
            //         'department' => $std_teams[$i]->department,
            //         'section' => $std_teams[$i]->section,
            //         'group' => $std_teams[$i]->group,
            //         'sub_group' => $std_teams[$i]->sub_group,
            //         'posisi' => $std_teams[$i]->posisi,
            //         'remark' => $std_teams[$i]->remark,
            //         'created_at' => $std_teams[$i]->created_at,
            //         'updated_at' => date('Y-m-d H:i:s'),
            //     ]);
            // }

            // $update_kehadiran = db::connection('ympimis_2')->table('std_teams')->whereNotNull('std_teams.remark')->update([
            //     'remark' => null]);

            // DB::connection('ympimis_2')->table('std_answers')->truncate();

            $update_soal = db::connection('ympimis_2')->table('std_questions')->where('std_questions.kode_soal', '=', $kode_soal)->update([
                'view' => 'Hide']);

            $response = array(
                'status' => true,
            );
            return Response::json($response);
            // }else{
            //     $response = array(
            //         'status' => false,
            //         'message' => 'Soal tidak dapat di tutup.',
            //     );
            //     return Response::json($response);
            // }
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function DeleteSoalKyt(Request $request)
    {
        try {
            $kode_soal = $request->get('kode_soal');

            $delete_images = db::connection('ympimis_2')->table('std_images')
            ->where('kode_soal', '=', $kode_soal)
            ->delete();

            $delete_soal = db::connection('ympimis_2')->table('std_questions')
            ->where('kode_soal', '=', $kode_soal)
            ->delete();

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

    public function detailLogKyHh(Request $request)
    {
        $kode_soal = $request->get('kode_soal');
        $data = db::connection('ympimis_2')->select("select nama_file, urutan, soal, remark from std_questions where kode_soal = '" . $kode_soal . "'");

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function IndexSoal($nama_tim, $periode)
    {
        $title = 'Kiken Yochi & Hiyari Hatto (KYT)';
        $title_jp = '(危険予知とヒヤリハット)';
        $username = Auth::user()->username;
        $day = date('l');
        $hari = '';
        if ($day == 'Sunday') {
            $hari = 'Minggu';
        } else if ($day == 'Monday') {
            $hari = 'Senin';
        } else if ($day == 'Tuesday') {
            $hari = 'Selasa';
        } else if ($day == 'Wednesday') {
            $hari = 'Rabu';
        } else if ($day == 'Thursday') {
            $hari = 'Kamis';
        } else if ($day == 'Friday') {
            $hari = "Jum'at";
        } else if ($day == 'Saturday') {
            $hari = 'Sabtu';
        }
        $today = $hari . ', ' . date('d-m-Y');
        $nama = db::connection('ympimis_2')->select("select nama_tim, nik from std_teams where nik = '" . $username . "'");
        // $kode = $nama[0]->nama_tim;
        $kode = $nama_tim;
        $kode_soal = db::connection('ympimis_2')->select('select kode_soal, view, remark from std_questions where periode = "' . $periode . '"');

        $cek_data = db::connection('ympimis_2')->select('select nama_tim from std_teams where nama_tim = "'.$nama_tim.'" and remark is null and id_jawaban is null and soal is not null');

        if (count($kode_soal) > 0) {
            // $cek = '';
            // if (count($test_cek) > 0) {
            $cek = db::connection('ympimis_2')->select('select kode_soal, nama_tim from std_answers where kode_soal = "' . $kode_soal[0]->kode_soal . '" and nama_tim = "' . $nama_tim . '"');
            // }else{
            //   $cek = 'Item Tidak Ada';
            // }

            // $ketua = db::connection('ympimis_2')->select('select nama, remark, updated_at from std_teams where posisi = "Ketua" and nama_tim = "'.$kode.'"');
            // $wakil = db::connection('ympimis_2')->select('select nama, remark, updated_at from std_teams where posisi = "Wakil" and nama_tim = "'.$kode.'"');

            $ketua = db::connection('ympimis_2')->select('select nama, remark, updated_at from std_teams where posisi = "Ketua" and nama_tim = "' . $nama_tim . '"');
            $wakil = db::connection('ympimis_2')->select('select nama, remark, updated_at from std_teams where posisi = "Wakil" and nama_tim = "' . $nama_tim . '"');

            $a = '';
            if (count($wakil) == 0) {
                $a = ' ';
            } else {
                $a = $wakil[0]->nama;
            }

            $data_isian = db::connection('ympimis_2')->select('select faktor_bahaya from std_answers where kode_soal = "' . $kode_soal[0]->kode_soal . '" and nama_tim = "' . $nama_tim . '"');
            $faktor_bahaya = '';
            if (count($data_isian) == 0) {
                $faktor_bahaya = ' ';
            } else {
                $faktor_bahaya = explode('/', $data_isian[0]->faktor_bahaya);
            }

            $periode_ky = db::connection('ympimis_2')->select('select kode_soal, view, remark, DATE_FORMAT( CONCAT(periode,"-01"), "%M %Y" ) AS bulan from std_questions where periode = "'. $periode .'"');

            $list_soal = db::connection('ympimis_2')->select('SELECT DISTINCT
                kode_soal,
                remark,
                DATE_FORMAT( CONCAT(periode,"-01"), "%M %Y" ) AS bulan,
                periode 
                FROM
                std_questions 
                ORDER BY
                kode_soal DESC');

            return view('standardization.ky_hh.index_soal', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'kode' => $kode,
                'cek' => $cek,
                'today' => $today,
                'ketua' => $ketua,
                'a' => $a,
                'data_isian' => $data_isian,
                'faktor_bahaya' => $faktor_bahaya,
                'kode_soal' => $kode_soal,
                'list_soal' => $list_soal,
                'periode_ky' => $periode_ky
            ));
        } else {
            $list_soal = db::connection('ympimis_2')->select('SELECT DISTINCT
                kode_soal,
                remark,
                DATE_FORMAT( CONCAT(periode,"-01"), "%M %Y" ) AS bulan,
                periode 
                FROM
                std_questions 
                ORDER BY
                kode_soal DESC');

            $id_team = $nama_tim;

            return view('standardization.ky_hh.tidak_ada_tim', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'list_soal' => $list_soal,
                'id_team' => $id_team
            ));
        }
    }

    public function FetchSoalKy(Request $request)
    {
        $kode_soal = $request->get('kode_soal');

        $show = db::connection('ympimis_2')->select("select std_images.kode_soal, nama_gambar, soal, std_questions.remark, jawaban from std_questions left join std_images on std_questions.kode_soal = std_images.kode_soal where std_questions.kode_soal = '" . $kode_soal . "'");

        $jawaban = db::connection('ympimis_2')->select("select jawaban from std_questions where kode_soal = '" . $kode_soal . "'");
        $a = $jawaban[0]->jawaban;

        $response = array(
            'status' => true,
            'resumes' => $show,
            'a' => $a,
        );
        return Response::json($response);
    }

    public function InsertJawaban(Request $request)
    {
        try {
            $id = Auth::id();

            $isian_1 = $request->get('isian_1');
            $isian_2 = $request->get('isian_2');
            $isian_3 = $request->get('isian_3');
            $isian_4 = $request->get('isian_4');
            $isian_5 = $request->get('isian_5');

            $jenis_1 = $request->get('jenis_1');
            $jenis_2 = $request->get('jenis_2');
            $jenis_3 = $request->get('jenis_3');
            $jenis_4 = $request->get('jenis_4');
            $jenis_5 = $request->get('jenis_5');

            $benda_1 = $request->get('benda_1');
            $benda_2 = $request->get('benda_2');
            $benda_3 = $request->get('benda_3');
            $benda_4 = $request->get('benda_4');
            $benda_5 = $request->get('benda_5');

            $kesimpulan_1 = $request->get('kesimpulan_1');
            $kesimpulan_2 = $request->get('kesimpulan_2');
            $kesimpulan_3 = $request->get('kesimpulan_3');
            $kesimpulan_4 = $request->get('kesimpulan_4');
            $kesimpulan_5 = $request->get('kesimpulan_5');

            $kode_soal = $request->get('kode_soal');
            $nama_tim = $request->get('nama_tim');

            $konkrit_1 = $request->get('konkrit_1');
            $konkrit_2 = $request->get('konkrit_2');
            $konkrit_3 = $request->get('konkrit_3');
            $konkrit_4 = $request->get('konkrit_4');
            $konkrit_5 = $request->get('konkrit_5');

            $tindakan_tim = $request->get('tindakan_tim');
            $ikrar = $request->get('ikrar');

            $cek = db::connection('ympimis_2')->select('select nama_tim, nik from std_teams where nama_tim = "' . $nama_tim . '" and remark is null');
            $cek_log = db::connection('ympimis_2')->select('select kode_soal, nama_tim from std_answers where kode_soal = "' . $kode_soal . '"');

            $score = (count($isian_1) + count($isian_2) + count($isian_3) + count($isian_4) + count($isian_5));

            $jawaban = db::connection('ympimis_2')->select('select `count` from std_questions where kode_soal = "' . $kode_soal . '"');
            $p = ($score / $jawaban[0]->count) * 100;

            if (count($cek) == 0) {
                $insert = db::connection('ympimis_2')->table('std_answers')->insert([
                    'kode_soal' => $kode_soal,
                    'nama_tim' => $nama_tim,
                    'faktor_bahaya' => $isian_1 . '/' . $isian_2 . '/' . $isian_3 . '/' . $isian_4 . '/' . $isian_5,
                    'faktor_benda' => $benda_1 . '/' . $benda_2 . '/' . $benda_3 . '/' . $benda_4 . '/' . $benda_5,
                    'jenis_kecelakaan' => $jenis_1 . '/' . $jenis_2 . '/' . $jenis_3 . '/' . $jenis_4 . '/' . $jenis_5,
                    'kesimpulan' => $kesimpulan_1 . '/' . $kesimpulan_2 . '/' . $kesimpulan_3 . '/' . $kesimpulan_4 . '/' . $kesimpulan_5,
                    'konkrit' => $konkrit_1 . '/' . $konkrit_2 . '/' . $konkrit_3 . '/' . $konkrit_4 . '/' . $konkrit_5,
                    'target_tindakan' => $tindakan_tim,
                    'ikrar' => $ikrar,
                    'score' => number_format($p, 2),
                    'created_by' => $id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $l = db::connection('ympimis_2')->select('select id from std_answers where kode_soal = "' . $kode_soal . '" and nama_tim = "' . $nama_tim . '" and created_by = "' . $id . '"');

                $update_id = db::connection('ympimis_2')->table('std_teams')->where('soal', '=', $kode_soal)->where('nama_tim', '=', $nama_tim)->update(['id_jawaban' => $l[0]->id]);

                // $update_pengajuan = db::connection('ympimis_2')->table('std_teams')->where('nama_tim', '=', $nama_tim)->update([
                //     'soal' => $kode_soal]);

                //create tim otomatis
                $data_tim = db::connection('ympimis_2')->select('select * from std_teams where nama_tim = "' . $nama_tim . '" and soal = "' . $kode_soal . '"');
                for ($i = 0; $i < count($data_tim); $i++) {
                    $bulan_a = $data_tim[$i]->created_at;
                    $bulan_b = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($bulan_a)));
                    $periode = date('Y-m', strtotime('+1 month', strtotime($bulan_a)));

                    $select_kode_soal = db::connection('ympimis_2')->select('select kode_soal, periode from std_questions where periode = "'.$periode.'"');

                    $pp = '';
                    if (count($select_kode_soal) > 0) {
                        $pp = $select_kode_soal[0]->kode_soal;
                    }else{
                        $pp = null;
                    }

                    $insert_tim = db::connection('ympimis_2')->table('std_teams')->insert([
                        'nama_tim' => $data_tim[$i]->nama_tim,
                        'urutan' => $data_tim[$i]->urutan,
                        'nik' => $data_tim[$i]->nik,
                        'nama' => $data_tim[$i]->nama,
                        'department_short' => $data_tim[$i]->department_short,
                        'department' => $data_tim[$i]->department,
                        'section' => $data_tim[$i]->section,
                        'group' => $data_tim[$i]->group,
                        'sub_group' => $data_tim[$i]->sub_group,
                        'posisi' => $data_tim[$i]->posisi,
                        'remark' => null,
                        'id_leader' => $data_tim[$i]->id_leader,
                        'created_at' => $bulan_b,
                        'updated_at' => $bulan_b,
                        'soal' => $pp
                    ]);
                }

                // $data = db::connection('ympimis_2')->select('SELECT
                //     DATE_FORMAT( std_answers.created_at, "%d %b %Y" ) AS tanggal,
                //     std_answers.nama_tim,
                //     department_short,
                //     (select distinct nama from std_teams where posisi = "Ketua" and std_teams.nama_tim = "'.$nama_tim.'" and remark is not null) as ketua,
                //         COALESCE ( (select distinct nama from std_teams where posisi = "Wakil" and std_teams.nama_tim = "'.$nama_tim.'" and remark is not null), "-" ) as wakil,
                //         std_answers.kode_soal,
                //         std_questions.remark,
                //         faktor_bahaya,
                //         faktor_benda,
                //         jenis_kecelakaan,
                //         kesimpulan,
                //         konkrit,
                //         target_tindakan,
                //         ikrar
                //         FROM
                //         `std_answers`
                //         LEFT JOIN std_teams ON std_teams.nama_tim = std_answers.nama_tim
                //         LEFT JOIN std_questions ON std_questions.kode_soal = std_answers.kode_soal
                //         WHERE
                //         std_answers.kode_soal = "'.$kode_soal.'"
                //         AND std_answers.nama_tim = "'.$nama_tim.'"
                //         LIMIT 1');

                // $pdf = \App::make('dompdf.wrapper');
                // $pdf->getDomPDF()->set_option("enable_php", true);
                // $pdf->setPaper('A4', 'potrait');

                // $pdf->loadView('standardization.ky_hh.report_pengisian_ky', array(
                //     'data' => $data
                // ));

                // $pdf->save(public_path() . "/data_file/pengisian_ky/".$kode_soal."-".$nama_tim.".pdf");

                if (number_format($p, 2) <= 70.00) {
                    $ketua_tim = db::connection('ympimis_2')->select('select nik from std_teams where nama_tim = "' . $nama_tim . '" and soal = "' . $kode_soal . '"');
                    $st = db::select('select position, section from employee_syncs where employee_id = "' . $ketua_tim[0]->nik . '"');
                    $atasan = '';

                    if (preg_match('/\bOprator\b/', $st[0]->position) || preg_match('/\bLeader\b/', $st[0]->position)) {
                        $atasan = db::select('select approver_id, approver_name, approver_email from approvers where section = "' . $st[0]->section . '" and position = "Foreman"');
                    } else {
                        $atasan = db::select('select approver_id, approver_name, approver_email from approvers where department = "' . $data_tim[0]->department . '" and position = "Chief"');
                    }

                    $insert_atasan = db::connection('ympimis_2')->table('std_answers')->where('nama_tim', '=', $nama_tim)->where('kode_soal', '=', $kode_soal)->update([
                        'atasan' => $atasan[0]->approver_id . '/' . $atasan[0]->approver_name . '/' . $atasan[0]->approver_email]);

                    // $atasan = db::select('select approver_id, approver_name, approver_email from approvers where department = "'.$data_tim[0]->department.'"');

                    $resume_data = db::connection('ympimis_2')->select('SELECT
                        st.soal,
                        st.nama_tim,
                        nama,
                        department,
                        score,
                        sq.remark
                        FROM
                        std_teams AS st
                        LEFT JOIN std_answers AS sa ON st.nama_tim = sa.nama_tim
                        LEFT JOIN std_questions AS sq ON st.soal = sq.kode_soal
                        WHERE
                        st.nama_tim = "' . $nama_tim . '"
                        AND posisi = "Ketua"
                        LIMIT 1');

                    // Mail::to(['widura@music.yamaha.com', $atasan[0]->approver_email])
                    //     ->bcc(['lukmannul.arif@music.yamaha.com'])
                    //     ->send(new SendEmail($resume_data, 'ky_sosialisasi_ulang'));
                }

                $response = array(
                    'status' => true,
                    'message' => 'KY Berhasil Disimpan',
                );
                return Response::json($response);
                // return redirect('/index/ky_hh')->with('status', 'KY Berhasil Disimpan');
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Peserta Harus Hadir Semua',
                );
                return Response::json($response);
            }

            $response = array(
                'status' => true,
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => 'Gagal Simpan',
            );
            return Response::json($response);
        }
    }

    public function FetchCobaEmail()
    {
        $title = 'Test';
        $title_jp = '作業依頼書の管理';

        $resume_data = db::connection('ympimis_2')->select('SELECT
            st.nama_tim,
            nama,
            department,
            score,
            sq.remark
            FROM
            std_teams AS st
            LEFT JOIN std_answers AS sa ON st.nama_tim = sa.nama_tim
            LEFT JOIN std_questions AS sq ON sa.kode_soal = sa.kode_soal
            WHERE
            st.nama_tim = "STDKY00010"
            AND posisi = "Ketua"
            AND st.remark IS NOT NULL
            LIMIT 1');

        // $resume_data = db::connection('ympimis_2')->select('select request_id, nama_tim, karyawan, saksi, DATE_FORMAT(tanggal, "%a, %d %b %Y") as tanggal, lokasi, ringkasan, perbaikan, lain_lain, created_by, DATE_FORMAT(created_at, "%d %b %Y") as created_at, detail, level, remark, id_ketua, penanganan from std_hiyarihatos where request_id = "STDHH00071"');

        return view('mails.ky_notif', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'data' => $resume_data,
        ));
    }

    public function IndexPenangananHh($request_id, $id_ketua)
    {
        //upload disini
        $title = 'Kiken Yochi & Hiyari Hatto (KYT)';
        $title_jp = '(危険予知とヒヤリハット)';

        $id = Auth::id();

        $resume_data = db::connection('ympimis_2')->select('select request_id, nama_tim, karyawan, saksi, DATE_FORMAT(tanggal, "%a, %d %b %Y") as tanggal, lokasi, ringkasan, perbaikan, lain_lain, created_by, DATE_FORMAT(created_at, "%a, %d %b %Y") as created_at, detail, level from std_hiyarihatos where request_id = "' . $request_id . '"');
        // if ($id_ketua == $id) {
        return view('standardization.ky_hh.penanganan_hh', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'data' => $resume_data,
        ));
        // } else {
        //     return view('standardization.ky_hh.akses_gagal', array(
        //         'title' => $title,
        //         'title_jp' => $title_jp,
        //         'data' => $resume_data,
        //     ));
        // }
    }

    public function UpdatePenangananHh(Request $request)
    {
        try {
            $id = Auth::id();
            $request_id = $request->get('id');
            $penanganan = $request->get('penanganan');


            $gambar = $request->file('file_gambar');
            $test = $gambar->getClientOriginalName();
            $extension = pathinfo($test, PATHINFO_EXTENSION);

            $nama_gambar = $request_id . '_penanganan.' . $extension;
            $gambar->move('data_file/std/ky', $nama_gambar);

            $update_kehadiran = db::connection('ympimis_2')->table('std_hiyarihatos')->where('request_id', '=', $request_id)->update([
                'remark' => 'Close',
                'penanganan' => $penanganan,
                'bukti_penanganan' => $nama_gambar,
                'id_ketua' => Auth::user()->name,
                'updated_at' => date('Y-m-d H:i:s')]);

            $resume_data = db::connection('ympimis_2')->select('select request_id, nama_tim, karyawan, saksi, DATE_FORMAT(tanggal, "%a, %d %b %Y") as tanggal, lokasi, ringkasan, perbaikan, lain_lain, created_by, DATE_FORMAT(created_at, "%d %b %Y") as created_at, detail, level, remark, id_ketua, penanganan from std_hiyarihatos where request_id = "' . $request_id . '"');

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('standardization.ky_hh.report_pengisian_hh', array(
                'data' => $resume_data,
            ));

            $pdf->save(public_path() . "/data_file/pengisian_hh/" . $request_id . ".pdf");

            // Mail::to('widura@music.yamaha.com')
            //     ->bcc(['lukmannul.arif@music.yamaha.com'])
            //     ->send(new SendEmail($resume_data, 'penanganan_selesai'));

            $response = array(
                'status' => true,
            );
            return Response::json($response);
            // return redirect('/index/ky_hh')->with('status', 'Penanganan Telah Dilakukan.')->with('page', 'Hiyari Hatto');
        } catch (QueryException $e) {
            // return redirect('/index/ky_hh')->with('error', $e->getMessage())->with('page', 'Hiyari Hatto');
            // } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => 'Masukkan Penanganan Terlebih Dahulu',
            );
            return Response::json($response);
        }
    }

    public function InsertTim(Request $request)
    {
        try {
            $prefix_now = 'STDKY';
            $code_generator = CodeGenerator::where('note', '=', 'STD KY TIM')->first();
            if ($prefix_now != $code_generator->prefix) {
                $code_generator->prefix = $prefix_now;
                $code_generator->save();
            }

            $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $slip = $code_generator->prefix . $numbers;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $lop = $request->get('test');
// dd($lop);

            for ($i = 1; $i <= $lop; $i++) {
                $user = "description" . $i;
                $posisi = "header" . $i;
                $value_user = explode('/', $request->get($user));

                $insert_tim = db::connection('ympimis_2')->table('std_teams')->insert([
                    'nama_tim' => $slip,
                    'urutan' => $i,
                    'nik' => $value_user[0],
                    'nama' => $value_user[1],
                    'department_short' => $value_user[2],
                    'department' => $value_user[3],
                    'section' => $value_user[4],
                    'group' => $value_user[5],
                    'sub_group' => $value_user[6],
                    'posisi' => $request->get($posisi),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            return redirect('/index/ky_hh')->with('status', 'Tim Berhasil Ditambahkan')->with('page', 'Success');
        } catch (Exception $e) {
            return redirect('/index/ky_hh')->with('error', $e->getMessage())->with('page', 'Error');
        }
    }

    public function EditTim(Request $request)
    {
        try {
            $id_tim = $request->get('id_tim');
            $nomor = $request->get('nomor');
            $q = explode(',', $request->get('karyawan'));
            $a = explode(',', $request->get('kategori'));

            $kode_soal = db::connection('ympimis_2')->select('select soal from std_teams where nama_tim = "' . $id_tim . '" and posisi = "Ketua" and remark is null');

            for ($i = 0; $i < count($q); $i++) {
                $nomor += 1;
                $arr_dept = explode('/', $q[$i]);
                $arr_kategori = $a[$i];

                $insert_tim = db::connection('ympimis_2')->table('std_teams')->insert([
                    'nama_tim' => $id_tim,
                    'urutan' => $nomor,
                    'nik' => $arr_dept[0],
                    'nama' => $arr_dept[1],
                    'department_short' => $arr_dept[2],
                    'department' => $arr_dept[3],
                    'section' => $arr_dept[4],
                    'group' => $arr_dept[5],
                    'sub_group' => $arr_dept[6],
                    'posisi' => $arr_kategori,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'soal' => $kode_soal[0]->soal,
                ]);
            }
            $response = array(
                'status' => true,
                'message' => 'Karyawan Berhasil Di Tambahkan Ke Tim Anda',
            );

            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage() . ' on Line ' . $e->getLine(),
            );
            return Response::json($response);
        }
    }

    public function FetchTim(Request $request)
    {
        $nama_tim = $request->get('nama_tim');
        $k_soal = $request->get('kode_soal');

        $username = Auth::user()->username;

        $nama = db::connection('ympimis_2')->select("select nama_tim, nik, nama, posisi from std_teams where nik = '" . $username . "'");
        // $kode = $nama[0]->nama_tim;
        $kode = $nama_tim;

        $kode_soal = db::connection('ympimis_2')->select("select kode_soal from std_questions where view = 'Tampil'");

        // $tim = db::connection('ympimis_2')->select("select nama_tim, nik, nama, posisi, remark from std_teams where nama_tim = '".$nama[0]->nama_tim."' and soal = '".$kode_soal[0]->kode_soal."'");
        $tim = db::connection('ympimis_2')->select("select nama_tim, nik, nama, posisi, remark from std_teams where nama_tim = '" . $nama_tim . "' and soal = '".$k_soal."' and remark is null");

        $tim_hadir = db::connection('ympimis_2')->select("select nama_tim, nik, nama, posisi, remark from std_teams where nama_tim = '" . $nama_tim . "' and soal = '".$k_soal."' and remark is not null");

        $response = array(
            'status' => true,
            'tim' => $tim,
            'kode' => $kode,
            'tim_hadir' => $tim_hadir
        );
        return Response::json($response);
    }

    public function FetchKeyword(Request $request)
    {
        $username = Auth::user()->username;
        $kode_soal = db::connection('ympimis_2')->select("select kode_soal from std_questions where view = 'Tampil'");

        $keywords = db::connection('ympimis_2')->select("select kata_kunci from std_keywords where kode_soal = '" . $kode_soal[0]->kode_soal . "'");

        $response = array(
            'status' => true,
            'keywords' => $keywords,
        );
        return Response::json($response);
    }

    public function ConfirmKehadiran(Request $request)
    {
        try {
            $input = $request->get('nik');
            $sub = substr($input, 0, 2);

            $tim = $request->get('nama_tim');
            $nik = '';
            $ket = '';

            // if (($sub == 'PI') && ($request->get('value') != '')) {
            //     $nik = db::select('select employee_id from employees where employee_id = "'.$input.'"');
            //     $ket = $request->get('value');
            // }else if(($sub == 'PI') && ($request->get('value') == '')){
            //     $nik = db::select('select employee_id from employees where employee_id = "'.$input.'"');
            //     $ket = 'Hadir';
            // }else if (($sub != 'PI') && ($request->get('value') == '')){
            //     $nik = db::select('select employee_id from employees where tag = "'.$input.'"');
            //     $ket = 'Hadir';
            // }

            if (str_contains($input, 'PI') || str_contains($input, 'pi')) {
                $nik = db::select('select employee_id from employees where employee_id = "' . $input . '"');
            } else {
                $nik = db::select('select employee_id from employees where tag = "' . $input . '"');
            }

            if ($nik) {
                $cek_data = db::connection('ympimis_2')->select('select nik from std_teams where nama_tim = "' . $tim . '" and nik = "' . $nik[0]->employee_id . '" and remark is null');

                if ($request->get('value') != '') {
                    $ket = $request->get('value');
                } else {
                    $ket = 'Hadir';
                }

                if (count($cek_data) != 0) {
                    $update_kehadiran = db::connection('ympimis_2')->table('std_teams')->where('nama_tim', $tim)->where('remark', null)->where('nik', '=', $nik[0]->employee_id)->update([
                        'remark' => $ket,
                        'updated_at' => date('Y-m-d H:i:s')]);
                    $response = array(
                        'status' => true,
                        'message' => 'Scan Berhasil',
                    );
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'NIK Tidak Ditemukan',
                    );
                }
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'NIK Tidak Ditemukan',
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

    public function ConfirmSosialisasiUlang(Request $request)
    {
        try {
            $input = strtoupper($request->get('nik'));
            $sub = substr($input, 0, 2);

            $tim = $request->get('nama_tim');
            $nik = '';
            $ket = '';

            // dd($sub);

            $kode_soal = $request->get('kode_soal');

            if (str_contains($input, 'PI') || str_contains($input, 'pi')) {
                $nik = db::select('select employee_id from employees where employee_id = "' . $input . '"');
            } else {
                $nik = db::select('select employee_id from employees where tag = "' . $input . '"');
            }

            $cek_data = db::connection('ympimis_2')->select('select nik from std_teams where nama_tim = "' . $tim . '" and nik = "' . $nik[0]->employee_id . '" and soal = "' . $kode_soal . '" and remark = "Habis Sosialiasi"');

            if ($request->get('value') != '') {
                $ket = $request->get('value');
            } else {
                $ket = 'Telah Disosialiasi Ulang';
            }

            if (count($cek_data) == 0) {
                $update_kehadiran = db::connection('ympimis_2')->table('std_teams')->where('nik', '=', $nik[0]->employee_id)->where('nama_tim', $tim)->where('soal', $kode_soal)->update([
                    'remark' => $ket,
                    'updated_at' => date('Y-m-d H:i:s')]);
                $response = array(
                    'status' => true,
                    'message' => 'Scan Berhasil',
                );
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Gagal!',
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

    public function FetchResumeKY(Request $request)
    {
        try {
            $username = Auth::user()->username;
            $bulann = $request->get('bulan');
            $grafik_ky = db::connection('ympimis_2')->select('SELECT
                a.department_short,
                a.jumlah,
                a.sudah,
                a.belum
                FROM
                (
                SELECT
                department_short,
                count( nama_tim ) AS jumlah,
                sum( CASE WHEN remark IS NOT NULL THEN 1 ELSE 0 END ) AS sudah,
                sum( CASE WHEN remark IS NULL THEN 1 ELSE 0 END ) AS belum
                FROM
                std_teams
                WHERE
                posisi = "Ketua"
                AND DATE_FORMAT( created_at, "%Y-%m" ) = "' . $bulann . '"
                GROUP BY
                department_short
                ) AS a
                GROUP BY
                department_short,
                jumlah,
                sudah,
                belum');

            $not_ok = db::connection('ympimis_2')->select('SELECT DISTINCT
                std_teams.nama_tim,
                score
                FROM
                std_teams
                LEFT JOIN std_answers ON std_answers.nama_tim = std_teams.nama_tim
                WHERE
                posisi = "Ketua"
                AND std_answers.score <= 70
                AND DATE_FORMAT( std_answers.created_at, "%Y-%m" ) = "' . $bulann . '"');

            $coba = db::connection('ympimis_2')->select('select nama_tim, department, nik from std_teams where posisi = "Ketua" AND DATE_FORMAT( created_at, "%Y-%m" ) = "' . $bulann . '" order by nama_tim asc');

            $department = db::select('select department_name, department_shortname from departments where department_shortname != "JPN" order by  department_name asc');

            $jumlah = count($coba);

            $series = db::select('SELECT DISTINCT
                monthname( week_date ) AS bulan,
                YEAR ( week_date ) AS tahun
                FROM
                weekly_calendars
                WHERE
            fiscal_year = "FY199" -- inputan FY
            ORDER BY id ASC');

            //HH
            $grafik_hh = db::connection('ympimis_2')->select('SELECT
                count( id ) AS jumlah,
                monthname( created_at ) AS bulan2,
                YEAR ( created_at ) AS tahun
                FROM
                std_hiyarihatos
                GROUP BY
                bulan2,
                tahun
                ORDER BY
                tahun,
                MONTH ( created_at ) ASC');

            $grafik_open = db::connection('ympimis_2')->select('SELECT
                count( id ) AS jumlah,
                monthname( created_at ) AS bulan2,
                YEAR ( created_at ) AS tahun
                FROM
                std_hiyarihatos
                WHERE
                remark = "Open" and DATE_FORMAT( created_at, "%Y-%m" ) = "' . $bulann . '"
                GROUP BY
                bulan2,
                tahun
                ORDER BY
                tahun,
                MONTH ( created_at ) ASC');

            $grafik_close = db::connection('ympimis_2')->select('SELECT
                count( id ) AS jumlah,
                monthname( created_at ) AS bulan2,
                YEAR ( created_at ) AS tahun
                FROM
                std_hiyarihatos
                WHERE
                remark = "Close" and DATE_FORMAT( created_at, "%Y-%m" ) = "' . $bulann . '"
                GROUP BY
                bulan2,
                tahun
                ORDER BY
                tahun,
                MONTH ( created_at ) ASC');

            $grafik_score = db::connection('ympimis_2')->select('SELECT
                nama_tim, count(score) as jumlah
                FROM
                std_answers
                where DATE_FORMAT( created_at, "%Y-%m" ) = "' . $bulann . '"
                GROUP BY
                nama_tim');

            $score = db::connection('ympimis_2')->select('SELECT
                score, count(score) as jumlah
                FROM
                std_answers
                where DATE_FORMAT( created_at, "%Y-%m" ) = "' . $bulann . '"
                GROUP BY
                score');

            $bulan = date('F');

            $kode_soal = db::connection('ympimis_2')->select('select kode_soal from std_questions where periode = "' . $bulann . '" limit 1');

            $data_1 = db::select('select count(employee_id) jumlah from employee_syncs where end_date is null and employee_id like "%PI%"');
            $data_2 = db::connection('ympimis_2')->select('select count(id) as jumlah from std_teams WHERE remark is not null AND DATE_FORMAT( created_at, "%Y-%m" ) = "' . $bulann . '"');

            $data_grafik = db::connection('ympimis_2')->select("select
                sum( CASE WHEN remark = 'Hadir' THEN 1 ELSE 0 END ) AS hadir, sum( CASE WHEN remark is null THEN 1 ELSE 0 END ) AS tidak_hadir, COALESCE ( department, '' ) AS department
                from std_teams where DATE_FORMAT( created_at, '%y-%m' ) = '" . $bulann . "'
                group by department");

            // $all_team = db::connection('ympimis_2')->select('select posisi from std_teams where posisi = "Ketua" and DATE_FORMAT( created_at, "%Y-%m" ) = "'.$bulann.'"');
            // $team_sudah = db::connection('ympimis_2')->select('select posisi from std_teams where posisi = "Ketua" and DATE_FORMAT( created_at, "%Y-%m" ) = "'.$bulann.'" and remark is not null');
            // $team_belum = db::connection('ympimis_2')->select('select posisi from std_teams where posisi = "Ketua" and DATE_FORMAT( created_at, "%Y-%m" ) = "'.$bulann.'" and remark is null');
            $all_team = db::connection('ympimis_2')->select('SELECT posisi,std_teams.soal,nama FROM std_teams LEFT JOIN std_questions ON std_questions.kode_soal = std_teams.soal WHERE posisi = "Ketua" AND std_teams.soal IS NOT NULL AND std_questions.periode = "' . $bulann . '"');
            $team_sudah = db::connection('ympimis_2')->select('SELECT posisi,std_teams.soal,nama FROM std_teams LEFT JOIN std_questions ON std_questions.kode_soal = std_teams.soal WHERE posisi = "Ketua" AND std_teams.remark IS NOT NULL AND std_teams.soal IS NOT NULL AND std_questions.periode = "' . $bulann . '"');
            $team_belum = db::connection('ympimis_2')->select('SELECT posisi,std_teams.soal,nama FROM std_teams LEFT JOIN std_questions ON std_questions.kode_soal = std_teams.soal WHERE posisi = "Ketua" AND std_teams.remark IS NULL AND std_teams.soal IS NOT NULL AND std_questions.periode = "' . $bulann . '"');
            $jumlah_all = count($all_team);
            $jumlah_sudah = count($team_sudah);
            $jumlah_belum = count($team_belum);

            $response = array(
                'status' => true,
                // 'survey' => $survey,
                'coba' => $coba,
                'department' => $department,
                'jumlah' => $jumlah,
                'data_grafik' => $data_grafik,
                'grafik_update' => $grafik_ky,
                'grafik_hh' => $grafik_hh,
                'data_1' => $data_1,
                'data_2' => $data_2,
                'score' => $score,
                'series' => $series,
                'grafik_open' => $grafik_open,
                'grafik_close' => $grafik_close,
                'bulan' => $bulan,
                'grafik_score' => $grafik_score,
                'not_ok' => $not_ok,
                'username' => $username,
                'jumlah_all' => $jumlah_all,
                'jumlah_sudah' => $jumlah_sudah,
                'jumlah_belum' => $jumlah_belum,
                'kode_soal' => $kode_soal,
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

    public function FetchResumeHH(Request $request)
    {
        try {
            $bulann = $request->get('bulan');
            // dd($bulann);

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

    public function CreateHiyariHatto(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', 'HIYARI HATTO')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $request_id = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $apd = '';
            if (count($request->get('apd')) > 0) {
                $apd = 'Ada, ' . $request->get('apd');
            } else {
                $apd = 'Tidak Ada APD';
            }

            $level = "";
            $detail_keparahan = '';
            $detail_kemungkinan = '';

            if (($request->get('keparahan') == 'Keparahan Tinggi') && ($request->get('kemungkinan') == 'Kemungkinan Tinggi')) {
                $level = "Tinggi/Significant/<span class='label label-danger' style='color: white'>TINGGI";
                $detail_keparahan = 'Fatal, cacat permanent atau mengakibatkan  kerugian uang lebih dari Rp 10 juta';
                $detail_kemungkinan = 'Kejadian sering muncul (2 kali/minggu) dan atau terjadi pada beberapa orang (lebih dari 2 orang/minggu)';

            } else if (($request->get('keparahan') == 'Keparahan Tinggi') && ($request->get('kemungkinan') == 'Kemungkinan Sedang')) {
                $level = "Tinggi/Significant/<span class='label label-danger' style='color: white'>TINGGI";
                $detail_keparahan = 'Fatal, cacat permanent atau mengakibatkan  kerugian uang lebih dari Rp 10 juta';
                $detail_kemungkinan = 'Kejadian terjadi dalam waktu tidak terlalu sering (< 2 kali/minggu) dan kejadian < 2 orang /minggu';

            } else if (($request->get('keparahan') == 'Keparahan Tinggi') && ($request->get('kemungkinan') == 'Kemungkinan Rendah')) {
                $level = "Tinggi/Significant/<span class='label label-danger' style='color: white'>TINGGI";
                $detail_keparahan = 'Fatal, cacat permanent atau mengakibatkan  kerugian uang lebih dari Rp 10 juta';
                $detail_kemungkinan = 'Kecelakaan jarang terjadi dan hanya orang tertentu (diluar definisi High dan Low)';

            } else if (($request->get('keparahan') == 'Keparahan Sedang') && ($request->get('kemungkinan') == 'Kemungkinan Tinggi')) {
                $level = "Sedang/Moderately Significant/<span class='label label-warning' style='color: white'>SEDANG";
                $detail_keparahan = 'Cacat tidak permanent atau mengakibatkan kerugian kurang dari Rp 10 juta';
                $detail_kemungkinan = 'Kejadian sering muncul (2 kali/minggu) dan atau terjadi pada beberapa orang (lebih dari 2 orang/minggu)';

            } else if (($request->get('keparahan') == 'Keparahan Sedang') && ($request->get('kemungkinan') == 'Kemungkinan Sedang')) {
                $level = "Sedang/Moderately Significant/<span class='label label-warning' style='color: white'>SEDANG";
                $detail_keparahan = 'Cacat tidak permanent atau mengakibatkan kerugian kurang dari Rp 10 juta';
                $detail_kemungkinan = 'Kejadian terjadi dalam waktu tidak terlalu sering (< 2 kali/minggu) dan kejadian < 2 orang /minggu';

            } else if (($request->get('keparahan') == 'Keparahan Sedang') && ($request->get('kemungkinan') == 'Kemungkinan Rendah')) {
                $level = "Rendah/InSignificant/<span class='label label-info' style='color: white'>RENDAH";
                $detail_keparahan = 'Cacat tidak permanent atau mengakibatkan kerugian kurang dari Rp 10 juta';
                $detail_kemungkinan = 'Kecelakaan jarang terjadi dan hanya orang tertentu (diluar definisi High dan Low)';

            } else if (($request->get('keparahan') == 'Keparahan Rendah') && ($request->get('kemungkinan') == 'Kemungkinan Tinggi')) {
                $level = "Rendah/InSignificant/<span class='label label-info' style='color: white'>RENDAH";
                $detail_keparahan = 'Kecelakaan kecil atau tidak ada luka dan tidak ada kerugian';
                $detail_kemungkinan = 'Kejadian sering muncul (2 kali/minggu) dan atau terjadi pada beberapa orang (lebih dari 2 orang/minggu)';

            } else if (($request->get('keparahan') == 'Keparahan Rendah') && ($request->get('kemungkinan') == 'Kemungkinan Sedang')) {
                $level = "Rendah/InSignificant/<span class='label label-info' style='color: white'>RENDAH";
                $detail_keparahan = 'Kecelakaan kecil atau tidak ada luka dan tidak ada kerugian';
                $detail_kemungkinan = 'Kejadian terjadi dalam waktu tidak terlalu sering (< 2 kali/minggu) dan kejadian < 2 orang /minggu';

            } else if (($request->get('keparahan') == 'Keparahan Rendah') && ($request->get('kemungkinan') == 'Kemungkinan Rendah')) {
                $level = "Rendah/InSignificant/<span class='label label-info' style='color: white'>RENDAH";
                $detail_keparahan = 'Kecelakaan kecil atau tidak ada luka dan tidak ada kerugian';
                $detail_kemungkinan = 'Kecelakaan jarang terjadi dan hanya orang tertentu (diluar definisi High dan Low)';

            }

            $nik_ketua = db::connection('ympimis_2')->select('select nik from std_teams where nama_tim = "' . $request->get('team') . '" and posisi = "Ketua"');

            $id_ketua = db::select('select id from users where username = "' . $nik_ketua[0]->nik . '"');

            $insert_data = db::connection('ympimis_2')->table('std_hiyarihatos')->insert([
                'request_id' => $request_id,
                'nama_tim' => $request->get('team'),
                'karyawan' => $request->get('karyawan'),
                'saksi' => $request->get('saksi'),
                'tanggal' => $request->get('tanggal'),
                'lokasi' => $request->get('lokasi'),
                'ringkasan' => $request->get('ringkasan') . '/' . $apd . '/' . $request->get('keparahan') . '/' . $request->get('kemungkinan'),
                'perbaikan' => $request->get('perbaikan'),
                'lain_lain' => $request->get('lain'),
                'remark' => 'Open',
                'id_ketua' => $id_ketua[0]->id,
                'created_by' => Auth::id(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'detail' => $detail_keparahan . '/' . $detail_kemungkinan,
                'level' => $level,
            ]);

            $resume_data = db::connection('ympimis_2')->select('select request_id, nama_tim, karyawan, saksi, DATE_FORMAT(tanggal, "%a, %d %b %Y") as tanggal, lokasi, ringkasan, perbaikan, lain_lain, created_by, DATE_FORMAT(created_at, "%a, %d %b %Y") as created_at, penanganan, id_ketua, detail, level from std_hiyarihatos where request_id = "'.$request_id.'"');

            //    $pdf = \App::make('dompdf.wrapper');
            //    $pdf->getDomPDF()->set_option("enable_php", true);
            //    $pdf->setPaper('A4', 'potrait');

            //    $pdf->loadView('standardization.ky_hh.report_pengisian_hh', array(
            //     'data' => $resume_data
            // ));

            // $pdf->save(public_path() . "/data_file/pengisian_hh/".$request_id.".pdf");

            // $email_atasan = db::connection('ympimis_2')->select('select approver_email, approver_id from ympi_locations where location = "'.$resume_data[0]->lokasi.'"');

            // $id_approver = db::select('select id from users where username = "'.$email_atasan[0]->approver_id.'"');

            //    $update_id = db::connection('ympimis_2')->table('std_hiyarihatos')
            //    ->where('request_id', $request_id)
            //    ->update([
            //     'id_ketua' => $id_approver[0]->id
            // ]);
            $mail_to = [
                'widura@music.yamaha.com',
                'lukmannul.arif@music.yamaha.com'
            ];

            Mail::to($mail_to)
            ->send(new SendEmail($resume_data, 'email_hiyarihatto'));

            $response = array(
                'status' => true,
                'message' => 'Berhasil Insert Hiyari Hatto',
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

    public function FetchResumeIndex(Request $request)
    {
        try {
            $user = Auth::user()->username;

            $cek_tim = db::connection('ympimis_2')->select('select nama_tim from std_teams where nik = "' . $user . '"');

            $resume = '';

            $bulann = $request->get('bulan');

            // if (($user = Auth::user()->role_code) == 'S-MIS') {
            if (($user = Auth::user()->username) == 'PI2101044') {
                $resume = db::connection('ympimis_2')->select('SELECT
                    std_teams.id,
                    (select kode_soal from std_questions where view = "Tampil" and periode = "' . $bulann . '" limit 1) as kode_soal,
                    std_teams.nama_tim,
                    nama,
                    posisi,
                    department,
                    jml_tim,
                    jml_hadir,
                    ( jml_tim - jml_hadir ) AS jml_belum,
                    std_teams.remark,
                    score
                    FROM
                    std_teams
                    LEFT JOIN ( SELECT std_teams.nama_tim, count( id ) AS jml_tim FROM std_teams where soal = ( SELECT kode_soal FROM std_questions WHERE VIEW = "Tampil" AND periode = "' . $bulann . '" LIMIT 1 ) GROUP BY nama_tim ) AS jumlah ON jumlah.nama_tim = std_teams.nama_tim
                    LEFT JOIN ( SELECT std_teams.nama_tim, count( remark ) AS jml_hadir FROM std_teams GROUP BY nama_tim ) AS hadir ON hadir.nama_tim = std_teams.nama_tim
                    LEFT JOIN std_answers ON std_answers.id = std_teams.id_jawaban
                    WHERE
                    posisi = "Ketua" AND DATE_FORMAT( std_teams.created_at, "%Y-%m" ) = "' . $bulann . '" order by CAST(score AS int) asc');

                $resume2 = db::connection('ympimis_2')->select('SELECT
                    id,
                    (select kode_soal from std_questions where view = "Tampil" and periode = "' . $bulann . '" limit 1) as kode_soal,
                    std_teams.nama_tim,
                    nama,
                    posisi,
                    department,
                    jml_tim,
                    jml_hadir,
                    ( jml_tim - jml_hadir ) AS jml_belum,
                    remark,
                    pengisian_hh
                    FROM
                    std_teams
                    LEFT JOIN ( SELECT std_teams.nama_tim, count( id ) AS jml_tim FROM std_teams GROUP BY nama_tim ) AS jumlah ON jumlah.nama_tim = std_teams.nama_tim
                    LEFT JOIN ( SELECT std_teams.nama_tim, count( remark ) AS jml_hadir FROM std_teams GROUP BY nama_tim ) AS hadir ON hadir.nama_tim = std_teams.nama_tim
                    WHERE
                    posisi = "Ketua" AND DATE_FORMAT( std_teams.created_at, "%Y-%m" ) = "' . $bulann . '" order by remark desc');

                $resume3 = db::connection('ympimis_2')->select('select * from std_hiyarihatos where DATE_FORMAT( std_hiyarihatos.created_at, "%Y-%m" ) = "' . $bulann . '" order by remark desc');
            } else {
                $resume = db::connection('ympimis_2')->select('SELECT
                    std_teams.id,
                    ( SELECT kode_soal FROM std_questions WHERE VIEW = "Tampil" AND periode = "' . $bulann . '" LIMIT 1 ) AS kode_soal,
                    std_teams.nama_tim,
                    nama,
                    posisi,
                    department,
                    jml_tim,
                    jml_hadir,
                    ( jml_tim - jml_hadir ) AS jml_belum,
                    std_teams.remark,
                    score
                    FROM
                    std_teams
                    LEFT JOIN ( SELECT std_teams.nama_tim, count( id ) AS jml_tim FROM std_teams where soal = ( SELECT kode_soal FROM std_questions WHERE VIEW = "Tampil" AND periode = "' . $bulann . '" LIMIT 1 ) GROUP BY nama_tim ) AS jumlah ON jumlah.nama_tim = std_teams.nama_tim
                    LEFT JOIN ( SELECT std_teams.nama_tim, count( remark ) AS jml_hadir FROM std_teams GROUP BY nama_tim ) AS hadir ON hadir.nama_tim = std_teams.nama_tim
                    LEFT JOIN std_answers ON std_answers.id = std_teams.id_jawaban
                    WHERE
                    posisi = "Ketua"
                    AND std_teams.nik = "' . $user . '"
                    AND DATE_FORMAT( std_teams.created_at, "%Y-%m" ) = "' . $bulann . '" order by CAST(score AS int) asc');

                // AND std_teams.nama_tim = "'.$cek_tim[0]->nama_tim.'"
                // $resume = db::connection('ympimis_2')->select('SELECT
                //     st.nama_tim,
                //     nama,
                //     posisi,
                //     department,
                //     ( SELECT count( id ) FROM std_teams WHERE nama_tim = "'.$cek_tim[0]->nama_tim.'" ) AS jumlah_anggota,
                //         st.soal,
                //         periode,
                //         st.created_at
                //         FROM
                //         std_teams AS st
                //         LEFT JOIN std_questions AS sq ON sq.kode_soal = st.soal
                //         WHERE
                //         nama_tim = "'.$cek_tim[0]->nama_tim.'"
                //         AND posisi = "Ketua"
                //         ORDER BY
                //         created_at DESC');

                $resume2 = db::connection('ympimis_2')->select('SELECT
                    id,
                    (select kode_soal from std_questions where view = "Tampil" and periode = "' . $bulann . '" limit 1) as kode_soal,
                    std_teams.nama_tim,
                    nama,
                    posisi,
                    department,
                    jml_tim,
                    jml_hadir,
                    ( jml_tim - jml_hadir ) AS jml_belum,
                    remark,
                    pengisian_hh
                    FROM
                    std_teams
                    LEFT JOIN ( SELECT std_teams.nama_tim, count( id ) AS jml_tim FROM std_teams GROUP BY nama_tim ) AS jumlah ON jumlah.nama_tim = std_teams.nama_tim
                    LEFT JOIN ( SELECT std_teams.nama_tim, count( remark ) AS jml_hadir FROM std_teams GROUP BY nama_tim ) AS hadir ON hadir.nama_tim = std_teams.nama_tim
                    WHERE
                    posisi = "Ketua"
                    and std_teams.nama_tim = "' . $cek_tim[0]->nama_tim . '" AND DATE_FORMAT( std_teams.created_at, "%Y-%m" ) = "' . $bulann . '"');

                $resume3 = db::connection('ympimis_2')->select('select * from std_hiyarihatos where nama_tim = "' . $cek_tim[0]->nama_tim . '" AND DATE_FORMAT( std_hiyarihatos.created_at, "%Y-%m" ) = "' . $bulann . '" order by remark desc');
            }

            $cek_soal = db::connection('ympimis_2')->select('SELECT
                kode_soal
                FROM
                std_questions
                WHERE
                `view` = "Tampil"
                and periode = "' . $bulann . '"
                GROUP BY
                kode_soal');

            $open_soal = 0;

            if (count($cek_soal) > 0) {
                $open_soal = count($cek_soal);
            }

            $response = array(
                'status' => true,
                'resume' => $resume,
                'open_soal' => $open_soal,
                'cek_tim' => $cek_tim,
                'resume2' => $resume2,
                'resume3' => $resume3,
                'username' => $user,
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

    public function FetchDetailJumlahTim(Request $request)
    {
        try {
            $id = $request->get('id');
            $soal = $request->get('kode_soal');
            $nm = db::connection('ympimis_2')->select('select nama_tim from std_teams where id = "' . $id . '"');

            $resumes = db::connection('ympimis_2')->select('select nama_tim, nama, posisi, remark from std_teams where nama_tim = "' . $nm[0]->nama_tim . '" and soal = "' . $soal . '"');

            $response = array(
                'status' => true,
                'resumes' => $resumes,
                'nm' => $nm,
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

    public function FetchDataScore(Request $request)
    {
        try {
            $category = $request->get('category');
            $name = $request->get('name');

            $resumes = db::connection('ympimis_2')->select('SELECT
                std_teams.nama_tim,
                nama,
                department,
                department_short
                FROM
                std_answers
                LEFT JOIN std_teams ON std_teams.nama_tim = std_answers.nama_tim
                WHERE
                std_teams.posisi = "Ketua"
                AND score = "' . $category . '"
                GROUP BY
                std_teams.nama_tim');

            $grafik_ky = '';
            if ($name == 'Sudah Mengisi') {
                $grafik_ky = db::connection('ympimis_2')->select('select nama_tim, nama, department from std_teams where posisi = "Ketua" and remark is not null and department_short = "' . $category . '"');
            } else {
                $grafik_ky = db::connection('ympimis_2')->select('select nama_tim, nama, department from std_teams where posisi = "Ketua" and remark is null and department_short = "' . $category . '"');
            }

            $grafik_hh = '';
            if ($name == 'Total') {
                $grafik_hh = db::connection('ympimis_2')->select('select request_id, karyawan, remark, id_ketua from std_hiyarihatos where MONTHNAME(tanggal) = "' . $category . '"');
            } else if ($name == 'Open') {
                $grafik_hh = db::connection('ympimis_2')->select('select request_id, karyawan, remark, id_ketua from std_hiyarihatos where MONTHNAME(tanggal) = "' . $category . '" and remark = "Open"');
            } else if ($name == 'Close') {
                $grafik_hh = db::connection('ympimis_2')->select('select request_id, karyawan, remark, id_ketua from std_hiyarihatos where MONTHNAME(tanggal) = "' . $category . '" and remark = "Close"');
            }

            $response = array(
                'status' => true,
                'resumes' => $resumes,
                'grafik_ky' => $grafik_ky,
                'grafik_hh' => $grafik_hh,
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

    public function IndexSosialisasiUlangKyt($nama_tim, $kode_soal)
    {
        try {
            $title = 'Sosialisasi Ulang KYT';
            $title_jp = '(???)';

            // $nama = db::connection('ympimis_2')->select("select nama_tim, nik from std_teams where nik = '".$username."'");
            // $kode = $nama[0]->nama_tim;
            // $kode_soal = db::connection('ympimis_2')->select('select kode_soal, view, remark from std_questions where view = "Tampil"');
            // $cek = db::connection('ympimis_2')->select('select kode_soal, nama_tim from std_answers where kode_soal = "'.$kode_soal[0]->kode_soal.'" and nama_tim = "'.$kode.'"');

            // $data_isian = db::connection('ympimis_2')->select('select faktor_bahaya from std_answers where kode_soal = "'.$kode_soal[0]->kode_soal.'" and nama_tim = "'.$kode.'"');

            $data = db::connection('ympimis_2')->select('SELECT std_images.kode_soal, `view`, nama_gambar, jawaban FROM std_questions LEFT JOIN std_images ON std_images.kode_soal = std_questions.kode_soal WHERE std_images.kode_soal = "' . $kode_soal . '"');

            // dd($data[0]->jawaban);

            // return view('standardization.ky_hh.index_sosialisasi', array(
            return view('standardization.ky_hh.index_kunci_jawaban_ky', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data' => $data,
                'nama_tim' => $nama_tim,
                // 'kode' => $kode,
                // 'data_isian' => $data_isian,
                // 'kode_soal' => $kode_soal
            ));
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function FetchSosialisasiUlangKyt(Request $request)
    {
        try {
            $nama_tim = $request->get('nama_tim');
            $kode_soal = $request->get('kode_soal');

            $tim = db::connection('ympimis_2')->select('select nama_tim, nama, posisi, remark from std_teams where nama_tim = "' . $nama_tim . '" and soal = "' . $kode_soal . '"');

            $response = array(
                'status' => true,
                'tim' => $tim,
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

    public function indexSlogan()
    {
        $fy_all = WeeklyCalendar::select('fiscal_year')->distinct()->orderBy('week_date', 'desc')->get();
        $emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();
        return view('standardization.slogan.index')
        ->with('title', 'Slogan Kebijakan Mutu')
        ->with('title_jp', 'YMPIの品質スローガン')
        ->with('page', 'Slogan Kebijakan Mutu')
        ->with('jpn', 'YMPIの品質スローガン')
        ->with('emp', $emp)
        ->with('fy_all', $fy_all);
    }

    public function fetchSlogan(Request $request)
    {
        try {
            if ($request->get('fiscal_year') != '') {
                $fy = $request->get('fiscal_year');
            } else {
                $fys = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
                $fy = $fys->fiscal_year;
            }

            $assessor = DB::table('miraimobile.std_slogan_assessors')->where('periode', $fy)->get();
            $asesor = '';
            for ($i = 0; $i < count($assessor); $i++) {
                if (strtoupper(Auth::user()->username) == $assessor[$i]->assessor_id) {
                    $asesor = 'Yes';
                }
            }

            $slogan = DB::Select("SELECT
                ympimis.employee_syncs.*,
                COALESCE ( department, 'Management' ) AS department_name,
                slogan.slogan_1
                FROM
                ympimis.employee_syncs
                LEFT JOIN ( SELECT * FROM miraimobile.std_slogans WHERE periode = '" . $fy . "' ) AS slogan ON slogan.employee_id = ympimis.employee_syncs.employee_id
                WHERE
                ympimis.employee_syncs.end_date IS NULL
                AND grade_code != 'J0-'");

            $department = DB::SELECT("SELECT DISTINCT
                ( COALESCE ( department, 'Management' ) ) AS department,
                COALESCE ( department_shortname, 'MGT' ) AS department_shortname
                FROM
                employee_syncs
                LEFT JOIN departments ON departments.department_name = employee_syncs.department");

            $response = array(
                'status' => true,
                'assessor' => $assessor,
                'asesor' => $asesor,
                'slogan' => $slogan,
                'department' => $department,
                'periode' => $fy,
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

    public function indexSloganAssessment()
    {
        $fy = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
        $periode = $fy->fiscal_year;
        $emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();
        return view('standardization.slogan.assessment')
        ->with('title', 'Slogan Kebijakan Mutu Assessment')
        ->with('title_jp', 'YMPIの品質スローガン')
        ->with('page', 'Slogan Kebijakan Mutu Assessment')
        ->with('jpn', 'YMPIの品質スローガン')
        ->with('emp', $emp)
        ->with('periode', $periode);
    }

    public function fetchSloganAssessment(Request $request)
    {
        try {
            $periode = $request->get('periode');
            $assessor_id = $request->get('assessor_id');
            $check = DB::table('miraimobile.std_slogans')->where('periode', $periode)->first();

            if ($check->selection_status == null) {
                $assessor = DB::table('miraimobile.std_slogan_assessors')->where('periode', $periode)->where('assessor_id', $assessor_id)->where('category', 'Selection')->first();
                $slogan = DB::table('miraimobile.std_slogans')->where('periode', $periode)->where('selection_assessor_id', $assessor_id)->where('selection_result', null)->get();

                $response = array(
                    'status' => true,
                    'slogan' => $slogan,
                    'assessor' => $assessor,
                    'periode' => $periode,
                    'process' => 'Seleksi',
                );
                return Response::json($response);
            } else if ($check->final_status == null && !str_contains($check->final_assessor_id, strtoupper($assessor_id))) {
                $assessor = DB::table('miraimobile.std_slogan_assessors')->where('periode', $periode)->where('assessor_id', $assessor_id)->where('category', 'Final')->first();
                $slogan = DB::table('miraimobile.std_slogans')->where('periode', $periode)->whereNotNull('selection_status')->whereNull('final_status')->where('selection_checks', 'OK')->limit(20)->get();

                $response = array(
                    'status' => true,
                    'slogan' => $slogan,
                    'assessor' => $assessor,
                    'periode' => $periode,
                    'process' => 'Final',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Process has been closed',
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

    public function inputSloganAssessment(Request $request)
    {
        try {
            $id = $request->get('id');
            $cond = $request->get('cond');

            $result_1 = $request->get('result_1');
            $result_2 = $request->get('result_2');
            $result_3 = $request->get('result_3');

            $process = $request->get('process');

            if ($process == 'Seleksi') {
                $selection = DB::table('miraimobile.std_slogans')->where('id', $id)->update([
                    'selection_checks' => $cond,
                    'selection_result' => $result_1 . '_' . $result_2 . '_' . $result_3,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();
                $final = DB::table('miraimobile.std_slogans')->where('id', $id)->first();
                if ($final->final_assessor_id != null) {
                    $final_update = DB::table('miraimobile.std_slogans')->where('id', $id)->update([
                        'final_assessor_id' => $final->final_assessor_id . '_' . $emp->employee_id,
                        'final_assessor_name' => $final->final_assessor_name . '_' . $emp->name,
                        'final_checks' => $final->final_checks . '_' . $cond,
                        'final_result' => $final->final_result . ',' . $result_1 . '_' . $result_2 . '_' . $result_3,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $final_update = DB::table('miraimobile.std_slogans')->where('id', $id)->update([
                        'final_assessor_id' => $emp->employee_id,
                        'final_assessor_name' => $emp->name,
                        'final_checks' => $cond,
                        'final_result' => $result_1 . '_' . $result_2 . '_' . $result_3,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
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

    public function inputSloganAssessor(Request $request)
    {
        try {
            $periode = $request->get('periode');
            $assessor_seleksi = explode(',', $request->get('assessor_seleksi'));
            $assessor_final = explode(',', $request->get('assessor_final'));

            if ($request->get('assessor_seleksi') != '') {
                if (str_contains($request->get('assessor_seleksi'), ',')) {
                    for ($i = 0; $i < count($assessor_seleksi); $i++) {
                        $input = DB::table('miraimobile.std_slogan_assessors')->insert([
                            'periode' => $periode,
                            'category' => 'Selection',
                            'assessor_id' => explode('_', $assessor_seleksi[$i])[0],
                            'assessor_name' => explode('_', $assessor_seleksi[$i])[1],
                            'created_by' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                } else {
                    $input = DB::table('miraimobile.std_slogan_assessors')->insert([
                        'periode' => $periode,
                        'category' => 'Selection',
                        'assessor_id' => explode('_', $assessor_seleksi[0])[0],
                        'assessor_name' => explode('_', $assessor_seleksi[0])[1],
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            if ($request->get('assessor_final') != '') {
                if (str_contains($request->get('assessor_final'), ',')) {
                    for ($i = 0; $i < count($assessor_final); $i++) {
                        $input = DB::table('miraimobile.std_slogan_assessors')->insert([
                            'periode' => $periode,
                            'category' => 'Final',
                            'assessor_id' => explode('_', $assessor_final[$i])[0],
                            'assessor_name' => explode('_', $assessor_final[$i])[1],
                            'created_by' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                } else {
                    $input = DB::table('miraimobile.std_slogan_assessors')->insert([
                        'periode' => $periode,
                        'category' => 'Final',
                        'assessor_id' => explode('_', $assessor_final[0])[0],
                        'assessor_name' => explode('_', $assessor_final[0])[1],
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
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

    public function updateSloganAssessor(Request $request)
    {
        try {
            $periode = $request->get('periode');
            $category = $request->get('category');
            $assessor_id = $request->get('assessor_id');
            $assessor_name = $request->get('assessor_name');
            $id = $request->get('id');

            $update = DB::table('miraimobile.std_slogan_assessors')->where('id', $id)->update([
                'periode' => $periode,
                'category' => $category,
                'assessor_id' => $assessor_id,
                'assessor_name' => $assessor_name,
                'updated_at' => date('Y-m-d H:i:s'),
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

    public function FetchDetailPresentasi(Request $request)
    {
        try {
            $bulan = $request->get('bulan');
            $judul = $request->get('judul');

            $nik = Auth::user()->username;
            $id = Auth::user()->id;
            $data_user = EmployeeSync::where('employee_id', $nik)
            ->select('department', 'section', 'position')->first();

            if ($data_user->position == 'Leader') {
                if ($judul == 'Sudah Mengerjakan KY') {
                    $resumes = db::connection('ympimis_2')->select('select nama, nama_tim from std_teams where id_leader = "' . $nik . '" and remark is not null and posisi = "Ketua"');
                } else if ($judul == 'Belum Mengerjakan KY') {
                    $resumes = db::connection('ympimis_2')->select('select nama, nama_tim from std_teams where id_leader = "' . $nik . '" and remark is null and posisi = "Ketua"');
                } else if ($judul == 'Temuan Open HH') {
                    $resumes = db::connection('ympimis_2')->select('select request_id, karyawan, ringkasan from std_hiyarihatos where remark = "Open" and created_by = "' . $id . '"');
                } else if ($judul == 'Temuan Close HH') {
                    $resumes = db::connection('ympimis_2')->select('select request_id, karyawan, ringkasan from std_hiyarihatos where remark = "Close" and created_by = "' . $id . '"');
                }
            } else {
                if ($judul == 'Sudah Mengerjakan KY') {

                } else if ($judul == 'Belum Mengerjakan KY') {

                } else if ($judul == 'Temuan Open HH') {

                } else if ($judul == 'Temuan Close HH') {

                }
            }

            // $category = $request->get('category');
            // $kode_soal = $request->get('kode_soal');
            // $periode = $request->get('periode');

            // if ($category == 'Sudah Mengisi') {
            //     $resumes = db::connection('ympimis_2')->select('select nama, nama_tim from std_teams where soal = "'.$kode_soal.'" and remark is not null and posisi = "Ketua"');
            // }else{
            //     $resumes = db::connection('ympimis_2')->select('select nama, nama_tim from std_teams where soal = "'.$kode_soal.'" and remark is null and posisi = "Ketua" and DATE_FORMAT( created_at, "%Y-%m" ) = "'.$periode.'"');
            // }
            $response = array(
                'status' => true,
                'resumes' => $resumes,
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

    public function IndexResumeKy($id)
    {
        $select = db::connection('ympimis_2')->select('select nama_tim, soal from std_teams where id_jawaban = "' . $id . '"');

        $data = db::connection('ympimis_2')->select('SELECT
            DATE_FORMAT( std_answers.created_at, "%d %b %Y" ) AS tanggal,
            std_answers.nama_tim,
            department_short,
            (select distinct nama from std_teams where posisi = "Ketua" and std_teams.nama_tim = "' . $select[0]->nama_tim . '" and remark is not null) as ketua,
            COALESCE ( (select distinct nama from std_teams where posisi = "Wakil" and std_teams.nama_tim = "' . $select[0]->nama_tim . '" and remark is not null), "-" ) as wakil,
            std_answers.kode_soal,
            std_questions.remark,
            faktor_bahaya,
            faktor_benda,
            jenis_kecelakaan,
            kesimpulan,
            konkrit,
            target_tindakan,
            ikrar
            FROM
            `std_answers`
            LEFT JOIN std_teams ON std_teams.nama_tim = std_answers.nama_tim
            LEFT JOIN std_questions ON std_questions.kode_soal = std_answers.kode_soal
            WHERE
            `std_answers`.id = "' . $id . '"
            LIMIT 1');
        $data_kehadiran = db::connection('ympimis_2')->select('select nama, nik, nama_tim, soal, remark from std_teams where nama_tim = "' . $data[0]->nama_tim . '" and soal = "' . $data[0]->kode_soal . '" and remark is not null');

        $resume = [
            'data' => $data,
            'data_kehadiran' => $data_kehadiran,
            'id' => $id
        ];

        return view('standardization.ky_hh.index_resume_ky', array(
            'resume' => $resume
        ));
    }

    public function PrintReportKy($id){
        try {
            $select = db::connection('ympimis_2')->select('select nama_tim, soal from std_teams where id_jawaban = "' . $id . '"');

            $data = db::connection('ympimis_2')->select('SELECT
                DATE_FORMAT( std_answers.created_at, "%d %b %Y" ) AS tanggal,
                std_answers.nama_tim,
                department_short,
                (select distinct nama from std_teams where posisi = "Ketua" and std_teams.nama_tim = "' . $select[0]->nama_tim . '" and remark is not null) as ketua,
                COALESCE ( (select distinct nama from std_teams where posisi = "Wakil" and std_teams.nama_tim = "' . $select[0]->nama_tim . '" and remark is not null), "-" ) as wakil,
                std_answers.kode_soal,
                std_questions.remark,
                faktor_bahaya,
                faktor_benda,
                jenis_kecelakaan,
                kesimpulan,
                konkrit,
                target_tindakan,
                ikrar
                FROM
                `std_answers`
                LEFT JOIN std_teams ON std_teams.nama_tim = std_answers.nama_tim
                LEFT JOIN std_questions ON std_questions.kode_soal = std_answers.kode_soal
                WHERE
                `std_answers`.id = "' . $id . '"
                LIMIT 1');
            $data_kehadiran = db::connection('ympimis_2')->select('select nama, nik, nama_tim, soal, remark from std_teams where nama_tim = "' . $data[0]->nama_tim . '" and soal = "' . $data[0]->kode_soal . '"');

            $resume = [
                'data' => $data,
                'data_kehadiran' => $data_kehadiran,
                'id' => $id
            ];

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');
            $pdf->loadView('standardization.ky_hh.report_ky', array(
                'resume' => $resume
            ));
            return $pdf->stream("KY.pdf");
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function deleteSloganAssessor(Request $request)
    {
        try {
            $delete = DB::table('miraimobile.std_slogan_assessors')->where('id', $request->get('id'))->delete();
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

    public function downloadSlogan()
    {
        $file_path = public_path('data_file/std/TemplateSloganMutu.xlsx');
        return response()->download($file_path);
    }

    public function uploadSlogan(Request $request)
    {
        $filename = "";
        $file_destination = 'data_file/std';

        if (count($request->file('fileSlogan')) > 0) {
            try {
                $file = $request->file('fileSlogan');
                $filename = 'slogan_' . date('YmdHisa') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);

                $excel = 'data_file/std/' . $filename;
                $rows = Excel::load($excel, function ($reader) {
                    $reader->noHeading();
                    $reader->skipRows(1);

                    $reader->each(function ($row) {
                    });
                })->toObject();

                $emp_error = [];

                for ($i = 0; $i < count($rows); $i++) {

                    $emp = EmployeeSync::where('employee_id', strtoupper($rows[$i][1]))->first();

                    if ($emp) {
                        $input = DB::table('miraimobile.std_slogans')->insert([
                            'periode' => $rows[$i][0],
                            'employee_id' => $emp->employee_id,
                            'name' => $emp->name,
                            'slogan_1' => $rows[$i][2],
                            'created_by' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    } else {
                        $input = DB::table('miraimobile.std_slogans')->insert([
                            'periode' => $rows[$i][0],
                            'employee_id' => strtoupper($rows[$i][1]),
                            'name' => '',
                            'slogan_1' => $rows[$i][2],
                            'created_by' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }

                $message = 'Slogan succesfully uploaded';

                $response = array(
                    'status' => true,
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
        } else {
            $response = array(
                'status' => false,
                'message' => 'Please select file to attach',
            );
            return Response::json($response);
        }
    }

    public function TestSendEmail(Request $request)
    {
        try {
            $datas = db::connection('ympimis_2')->select('SELECT
                nama_tim,
                nama,
                GROUP_CONCAT(DATE_FORMAT(created_at, "%M")) as bulan,
                count(nama_tim) jumlah
                FROM
                std_teams 
                WHERE
                remark IS NULL 
                and posisi = "Ketua"
                GROUP BY
                nama_tim, nama
                ORDER BY jumlah DESC');

            $data = array(
                'datas' => $datas
            );

            $mail_to = [
                'widura@music.yamaha.com'
            ];

            Mail::to($mail_to)->cc(['yayuk.wahyuni@music.yamaha.com'])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'reminder_ky'));

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

    public function GrafikMonitoringKaryawan(Request $request){
        try{
            // dd($request->get('p'));
         $date_now = date('Y-m-d');
         $fy = db::select('SELECT fiscal_year FROM weekly_calendars WHERE week_date = "'.$date_now.'"');

         $p = '';
         if ( $request->get('fy') == null) {
             $p = $fy[0]->fiscal_year;
         }else{
             $p = $request->get('fy');
         }

         $data = db::connection('sunfish')->select("SELECT COUNT
            ( A.employment_enddate ) AS jumlah,
            IIF(end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' )) AS bulan 
            FROM
            TEODEMPCOMPANY AS A 
            WHERE
            A.emp_no LIKE '%PI%' 
            AND A.employ_code != 'PERMANENT' 
            AND A.employment_enddate IS NOT NULL
            GROUP BY
            IIF(end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' ))");


         $wc = db::select('SELECT DISTINCT date_format( week_date, "%Y-%m" ) as bulan FROM weekly_calendars WHERE fiscal_year = "'.$p.'" ORDER BY id ASC');

         $department = db::select('SELECT
            department_shortname,
            department_name
            FROM
            departments 
            ORDER BY
            department_shortname ASC');

         $employee_sync = db::select('SELECT
            count( employee_id ) as jumlah,
            department 
            FROM
            employee_syncs 
            WHERE
            end_date IS NULL 
            AND department IS NOT NULL
            and employee_id like "%PI%" 
            GROUP BY
            department 
            ORDER BY
            department ASC');

         $employee_record = db::connection('ympimis_2')->select('select count(nama) as jumlah, department from (
            SELECT
            DISTINCT
            nama,
            department 
            FROM
            std_teams 
            WHERE
            department IS NOT NULL) as p
            GROUP BY p.department');

         $response = array(
            'status' => true,
            'data' => $data,
            'wc' => $wc,
            'fy' => $p,
            'employee_sync' => $employee_sync,
            'employee_record' => $employee_record,
            'department' => $department
        );
         return Response::json($response);
     }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

function ReportHiyarihattoAll(Request $request){
    try {
        $data = db::connection('ympimis_2')->select('select request_id, nama_tim, karyawan, saksi, tanggal, lokasi, ringkasan, perbaikan, lain_lain, remark, detail, level from std_hiyarihatos where karyawan is not null order by id desc');
        $response = array(
            'status' => true,
            'data' => $data
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

function IndexReportHiyarihatto($request_id){
    try {
        $resume_data = db::connection('ympimis_2')->select('select request_id, nama_tim, karyawan, saksi, DATE_FORMAT(tanggal, "%a, %d %b %Y") as tanggal, lokasi, ringkasan, perbaikan, lain_lain, created_by, DATE_FORMAT(created_at, "%a, %d %b %Y") as created_at, penanganan, id_ketua, detail, level from std_hiyarihatos where request_id = "'.$request_id.'"');

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');
        $pdf->loadView('standardization.ky_hh.report_pengisian_hh', array(
            'data' => $resume_data));
        return $pdf->stream("HH.pdf");

        // $response = array(
        //     'status' => true
        // );
        // return Response::json($response);
    } catch (\Exception$e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response);
    }
}

function UpdateKodeSoalInput(Request $request){
    try {
        $periode = $request->get('periode');
        $id_team = $request->get('id_team');



        db::connection('ympimis_2')->table('std_teams')
        ->where('nama_tim', $id_team)
        ->where('soal', null)
        ->orWhere('id_jawaban', null)
        ->update([
            'soal' => $periode
        ]);

        $periode_soal = db::connection('ympimis_2')->select('select periode from std_questions where kode_soal = "'.$periode.'"');

        $response = array(
            'status' => true,
            'periode' => $periode_soal[0]->periode,
            'message' => 'Success'
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

function GetTimDetailMonitoring(Request $request){
    try {
        $periode = $request->get('periode');
        $ket = $request->get('ket');
        $month_now = date('Y-m');

        if ($periode == 'all') {
            $data = db::connection('ympimis_2')->select('SELECT DISTINCT
                nama_tim,
                nama,
                department 
                FROM
                std_teams 
                WHERE
                posisi = "Ketua"');
        }else if ($periode == 'open_now') {
            $where = db::connection('ympimis_2')->select('SELECT
                nama_tim
                FROM
                std_teams AS st
                LEFT JOIN std_questions sq ON st.soal = sq.kode_soal 
                WHERE
                st.remark IS NOT NULL 
                AND posisi = "Ketua" 
                AND periode = "'.$month_now.'" 
                GROUP BY
                nama_tim');

            $push_where = [];
            for ($i=0; $i < count($where) ; $i++) {
                array_push($push_where, "'".$where[$i]->nama_tim."'");
            }

            $include_where = join($push_where, ',');

            $data = db::connection('ympimis_2')->select('SELECT DISTINCT
                nama_tim,
                nama,
                department 
                FROM
                std_teams 
                WHERE
                posisi = "Ketua" 
                AND nama_tim NOT IN ("'.$include_where.'")');
        }else{
            if ($ket == 'Open') {
                $where = db::connection('ympimis_2')->select('SELECT
                    nama_tim
                    FROM
                    std_teams AS st
                    LEFT JOIN std_questions sq ON st.soal = sq.kode_soal 
                    WHERE
                    st.remark IS NOT NULL 
                    AND posisi = "Ketua" 
                    AND periode = "'.$periode.'" 
                    GROUP BY
                    nama_tim');

                $push_where = [];
                for ($i=0; $i < count($where) ; $i++) {
                    array_push($push_where, "'".$where[$i]->nama_tim."'");
                }

                $include_where = join($push_where, ',');

                $data = db::connection('ympimis_2')->select('SELECT DISTINCT
                    nama_tim,
                    nama,
                    department 
                    FROM
                    std_teams 
                    WHERE
                    posisi = "Ketua" 
                    AND nama_tim NOT IN ('.$include_where.')');
            }else{
                $data = db::connection('ympimis_2')->select('SELECT
                    nama_tim,
                    nama,
                    department
                    FROM
                    std_teams AS st
                    LEFT JOIN std_questions sq ON st.soal = sq.kode_soal
                    WHERE
                    st.remark IS NOT NULL and posisi = "Ketua" and periode = "'.$periode.'"
                    GROUP BY
                    nama_tim,
                    nama,
                    department');
            }
        }

        $response = array(
            'status' => true,
            'data' => $data
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

function GetHiyarihattoDetailMonitoring(Request $request){
    try {
        $bulan = $request->get('bulan');
        $ket = $request->get('ket');

        if ($ket == 'Open') {
            $data = db::connection('ympimis_2')->select('SELECT id, karyawan, lokasi, tanggal, ringkasan, level, remark from std_hiyarihatos where tanggal like "%'.$bulan.'%" and remark = "Open"');
        }else{
            $data = db::connection('ympimis_2')->select('SELECT id, karyawan, lokasi, tanggal, ringkasan, level, remark from std_hiyarihatos where tanggal like "%'.$bulan.'%" and remark = "Close"');
        }

        $response = array(
            'status' => true,
            'data' => $data
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

function GetDetailPenangananHH(Request $request){
    try {
        $id = $request->get('id');

        $data = db::connection('ympimis_2')->table('std_hiyarihatos')->where('id', $id)->first();

        $response = array(
            'status' => true,
            'data' => $data
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



public function indexVehicleReport()
{
  $title = 'Report Temuan Pemeriksaan Kendaraan';
  $title_jp = '';

  return view('standardization.vehicle.report_vehicle', array(
    'title' => $title,
    'title_jp' => $title_jp
))->with('page', 'Report Vehicle')->with('head','Report Vehicle');
}

public function fetchVehicleReport()
{
  try {
    $vehicle = db::connection('ympimis_2')
    ->table('vehicle_inspections')
    ->select('vehicle_inspections.*')
    // ->join('vehicle_inspection_details', function($join) {
    //     $join->on('vehicle_inspections.employee_id', '=', 'vehicle_inspection_details.employee_id');
    //     $join->on('vehicle_inspections.inspection_date','=', 'vehicle_inspection_details.inspection_date');
    // })
    ->orderBy('id','desc')
    ->get();

    $response = array(
      'status' => true,
      'vehicle' => $vehicle
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


public function indexVehicleAttendance()
{
    $title = 'Absensi Pemeriksaan Kendaraan';
    $title_jp = '';

    return view('standardization.vehicle.vehicle_attendance', array(
        'title' => $title,
        'title_jp' => $title_jp
    ))->with('page', 'Absensi Kendaraan')->with('head', 'Absensi Kendaraan');
}

public function fetchVehicleAttendance(Request $request)
{

    try {
        $emp = Employee::select('employee_id')
        ->where('tag', $request->get('tag'))
        ->Orwhere('employee_id', $request->get('tag'))
        ->first();

        if (count($emp) > 0) {
            $data = DB::connection('ympimis_2')
            ->table('employee_vehicles')
            ->where('employee_id', $emp->employee_id)
            ->get();

            if (count($data) > 0) {

              if ($data[0]->attend_date_pemeriksaan != null) {
                  $response = array(
                      'status' => false,
                      'message' => 'Anda Sudah Mengambil Sticker',
                  );
                  return Response::json($response);
              } else {

                $update = DB::connection('ympimis_2')
                ->table('employee_vehicles')
                ->where('employee_id', $emp->employee_id)
                ->update([
                    'attend_date_pemeriksaan' => date('Y-m-d H:i:s'),
                ]);

                $response = array(
                  'status' => true,
                  'emp' => $data,
              );
                return Response::json($response);
            }
        }
        else{
          $response = array(
              'status' => false,
              'message' => 'Anda Tidak Terdaftar',
          );
          return Response::json($response);
      }
  } else {

    $response = array(
        'status' => false,
        'message' => 'Anda Sudah Mengambil Sticker',
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


public function fetchVehicleAttendanceQueue()
{
    try {

        $total = DB::connection('ympimis_2')->SELECT("SELECT
                    * 
            FROM
            `employee_vehicles` 
            left join (SELECT
            employee_id as emp_id,
            nopol as nopol_2,
            date_stnk as date_stnk_2,
            file_stnk as file_stnk_2,
            file_kendaraan as file_kendaraan_2
            FROM
            `employee_vehicles` 
            WHERE
            date_sim is null) as dua on dua.emp_id = employee_vehicles.employee_id
            WHERE
            date_sim is not null
            ORDER BY `attend_date_pemeriksaan` DESC");

        $response = array(
            'status' => true,
            'emp' => $total
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

public function indexVehicleMonitoringAttendance()
{
    $title = 'Monitoring Absensi Kendaraan';
    $title_jp = '';

    $periods = db::connection('ympimis_2')->select("SELECT DISTINCT
        DATE_FORMAT( inspection_date, '%Y-%m' ) AS period_date,
        DATE_FORMAT( inspection_date, '%Y %M' ) AS period
        FROM
        vehicle_inspections
        ORDER BY
        inspection_date DESC");

    return view('standardization.vehicle.monitoring_attendance', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'periods' => $periods,
    ));
}

public function fetchVehicleMonitoringAttendance(Request $request)
{

    $period = date('Y-m');

    if (strlen($request->get('period')) > 0) {
        $period = date('Y-m', strtotime($request->get('period')));
    }
    $where_period = "AND DATE_FORMAT(inspection_date, '%Y-%m') = '" . $period . "'";

    $vehicle_inspections = db::connection('ympimis_2')
    ->select("
        SELECT
        sum(case when a.attend_date_pemeriksaan is null then 1 else 0 end) as open,
        sum(case when a.attend_date_pemeriksaan is not null then 1 else 0 end) as close,
        a.department_shortname
        FROM
        (
        SELECT DISTINCT employee_id, attend_date_pemeriksaan,department_shortname from employee_vehicles
        JOIN ympimis.departments ON employee_vehicles.department = departments.department_name 
        ) as a
        GROUP BY a.department_shortname
        ");

    $response = array(
        'status' => true,
        'vehicle_inspections' => $vehicle_inspections,
        'period' => date('F Y', strtotime($period)),
    );
    return Response::json($response);
}

public function indexEmergency()
{
    $periode = '2023-04-03 Shift-1';
    $periodes = DB::connection('ympimis_2')->table('std_emergency_periodes')->where('status','Active')->first();
    if ($periodes) {
        $periode = $periodes->periode;
    }
    $locations = DB::connection('ympimis_2')->table('std_emergency_locations')->get();
    return view('standardization.emergency.index')
    ->with('title', 'Emergency Simulation')
    ->with('title_jp', '')
    ->with('page', 'Emergency Simulation')
    ->with('jpn', '')
    ->with('periode',$periode)
    ->with('locations',$locations);
}

public function fetchEmergency(Request $request)
{
    try {
        $location = $request->get('location');
        $periode = $request->get('periode');
        $emergency = DB::connection('ympimis_2')->where('location',$location)->where('periode',$periode)->get();
        $response = array(
            'status' => true,
            'location' => $location,
            'periode' => $periode,
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

public function indexYPM()
{
    $point = DB::connection('ympimis_2')->table('std_ypm_points')->get();
    $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
    $periode = DB::connection('ympimis_2')->table('std_ypm_teams')->select('periode')->distinct()->get();
    if (Auth::user()->role_code == 'JPN' || Auth::user()->role_code == 'DGM' || Auth::user()->role_code == 'GM' || Auth::user()->role_code == 'D' ||  Auth::user()->username == 'PI0904007' ||  Auth::user()->username == 'PI1910002') {
        return view('standardization.ypm.index')
        ->with('title', 'YPM Evaluation')
        ->with('title_jp', 'YPM評価')
        ->with('page', 'YPM Evaluation')
        ->with('jpn', 'YPM評価')
        ->with('periode',$periode)
        ->with('point',$point)
        ->with('emp',$emp)
        ->with('role',Auth::user()->role_code);
    }else{
        return view('404');
    }
}

public function fetchYPM(Request $request)
{
    try {
        $fiscal_year = WeeklyCalendar::select('fiscal_year')->distinct()->orderby('id','desc')->get();
        if ($request->get('periode') != '') {
            $periode = $request->get('periode');
        }else{
            $periodes = DB::connection('ympimis_2')
            ->select("SELECT DISTINCT
                ( periode )
                FROM
                std_ypm_teams
                ORDER BY
                created_at DESC
                LIMIT 1");
            if (count($periodes) > 0) {
                $periode = $periodes[0]->periode;
            }else{
                $periode = $fiscal_year[0]->fiscal_year;
            }
        }
            // $ypm = DB::connection('ympimis_2')->SELECT("SELECT
            //     std_ypm_teams.team_id,
            //     std_ypm_teams.team_no,
            //     std_ypm_teams.team_name,
            //     std_ypm_teams.team_title,
            //     std_ypm_teams.file_pdf_q1,
            //     std_ypm_teams.file_pdf_q2,
            //     std_ypm_teams.file_pdf_q3,
            //     std_ypm_teams.file_pdf_contest
            //     -- COALESCE ( q1.result, 0 ) AS q1,
            //     -- COALESCE ( q2.result, 0 ) AS q2,
            //     -- COALESCE ( q3.result, 0 ) AS q3,
            //     COALESCE ( contest.result, 0 ) AS contest,
            //     COALESCE ( contest_asesor.result, 0 ) AS contest_asesor,
            //     -- COALESCE ( q1.id, '' ) AS idq1,
            //     -- COALESCE ( q2.id, '' ) AS idq2,
            //     -- COALESCE ( q3.id, '' ) AS idq3
            // FROM
            //     `std_ypm_teams`
            //     LEFT JOIN (
            //     SELECT
            //         periode,
            //         team_no,
            //         team_name,
            //         sum( result ) AS result,
            //         id 
            //     FROM
            //         std_ypm_results 
            //     WHERE
            //         periode LIKE '%Q1_".$periode."%' 
            //     GROUP BY
            //         periode,
            //         team_no,
            //         team_name 
            //     ORDER BY
            //         sum( result ) DESC 
            //     ) AS q1 ON q1.team_no = std_ypm_teams.team_no 
            //     AND std_ypm_teams.team_name = q1.team_name
            //     LEFT JOIN (
            //     SELECT
            //         periode,
            //         team_no,
            //         team_name,
            //         sum( result ) AS result,
            //         id 
            //     FROM
            //         std_ypm_results 
            //     WHERE
            //         periode LIKE '%Q2_".$periode."%' 
            //     GROUP BY
            //         periode,
            //         team_no,
            //         team_name 
            //     ORDER BY
            //         sum( result ) DESC 
            //     ) AS q2 ON q2.team_no = std_ypm_teams.team_no 
            //     AND std_ypm_teams.team_name = q2.team_name
            //     LEFT JOIN (
            //     SELECT
            //         periode,
            //         team_no,
            //         team_name,
            //         sum( result ) AS result,
            //         id 
            //     FROM
            //         std_ypm_results 
            //     WHERE
            //         periode LIKE '%Q3_".$periode."%' 
            //     GROUP BY
            //         periode,
            //         team_no,
            //         team_name 
            //     ORDER BY
            //         sum( result ) DESC 
            //     ) AS q3 ON q3.team_no = std_ypm_teams.team_no 
            //     AND std_ypm_teams.team_name = q3.team_name
            //     LEFT JOIN (
            //     SELECT
            //         periode,
            //         team_no,
            //         team_name,
            //         sum( result ) AS result 
            //     FROM
            //         std_ypm_results 
            //     WHERE
            //         periode LIKE '%CONTEST_".$periode."%' 
            //     GROUP BY
            //         periode,
            //         team_no,
            //         team_name 
            //     ORDER BY
            //         sum( result ) DESC 
            //     ) AS contest ON contest.team_no = std_ypm_teams.team_no 
            //     AND std_ypm_teams.team_name = contest.team_name
            //     LEFT JOIN ( SELECT periode, team_no, team_name, sum(result) as result FROM std_ypm_results WHERE periode LIKE '%CONTEST_".$periode."%' AND asesor_id = '".Auth::user()->username."' GROUP BY periode, team_no, team_name ) AS contest_asesor ON contest_asesor.team_no = std_ypm_teams.team_no 
            //     AND std_ypm_teams.team_name = contest_asesor.team_name 
            // WHERE
            //     std_ypm_teams.periode = '".$periode."' 
            // ORDER BY
            // contest desc,
            //     q1 DESC,
            //     q2 DESC,
            //     q3 DESC");
        $ypm = DB::connection('ympimis_2')->SELECT("SELECT
            std_ypm_teams.team_id,
            std_ypm_teams.team_dept,
            std_ypm_teams.team_name,
            std_ypm_teams.team_title,
            std_ypm_teams.file_pdf_contest,
            COALESCE ( contest.result, 0 ) AS contest,
            COALESCE ( contest_asesor.result, 0 ) AS contest_asesor
            FROM
            `std_ypm_teams`
            LEFT JOIN (
            SELECT
            periode,
            team_dept,
            team_id,
            team_name,
            sum( result ) AS result 
            FROM
            std_ypm_results 
            WHERE
            periode LIKE '%CONTEST_".$periode."%' 
            GROUP BY
            periode,
            team_dept,
            team_id,
            team_name 
            ORDER BY
            sum( result ) DESC 
            ) AS contest ON contest.team_dept = std_ypm_teams.team_dept 
            AND std_ypm_teams.team_name = contest.team_name
            AND std_ypm_teams.team_id = contest.team_id
            LEFT JOIN ( SELECT periode, team_dept, team_name,team_id, sum(result) as result FROM std_ypm_results WHERE periode LIKE '%CONTEST_".$periode."%' AND asesor_id = '".Auth::user()->username."' GROUP BY periode, team_dept,team_id, team_name ) AS contest_asesor ON contest_asesor.team_dept = std_ypm_teams.team_dept 
            AND std_ypm_teams.team_name = contest_asesor.team_name 
            AND std_ypm_teams.team_id = contest_asesor.team_id
            WHERE
            std_ypm_teams.periode = '".$periode."' 
            ORDER BY
            contest desc");

        $teams = DB::connection('ympimis_2')->SELECT("SELECT
            std_ypm_teams.team_id,
            std_ypm_teams.team_dept,
            std_ypm_teams.team_name,
            std_ypm_teams.team_title,
            std_ypm_teams.file_pdf_q1,
            std_ypm_teams.file_pdf_q2,
            std_ypm_teams.file_pdf_q3,
            std_ypm_teams.file_pdf_contest,
            std_ypm_teams.std_approval,
            std_ypm_teams.std_name,
            CONCAT(
                            DATE_FORMAT( std_ypm_teams.std_approved_at, '%d-%b-%Y' ),
                            ' ',
                            DATE_FORMAT( std_ypm_teams.std_approved_at, '%H:%i:%s' )) AS std_approved_ats,
            std_ypm_teams.day,
            COALESCE ( contest_asesor.result, 0 ) AS contest_asesor
            FROM
            `std_ypm_teams`
            LEFT JOIN ( SELECT periode, team_dept, team_name,team_id, sum(result) as result FROM std_ypm_results WHERE periode LIKE '%CONTEST_".$periode."%' AND asesor_id = '".Auth::user()->username."' GROUP BY periode, team_dept,team_id, team_name ) AS contest_asesor ON contest_asesor.team_dept = std_ypm_teams.team_dept 
            AND std_ypm_teams.team_name = contest_asesor.team_name 
            AND std_ypm_teams.team_id = contest_asesor.team_id
            WHERE
            std_ypm_teams.periode = '".$periode."'
            order by contest_asesor desc");

        $ypm_all = DB::connection('ympimis_2')->table('std_ypm_results')->where('periode','like','%'.$periode.'%')->get();

        $judges = DB::connection('ympimis_2')->table('std_ypm_judges')->select('std_ypm_judges.*',DB::RAW("CONCAT(
                            DATE_FORMAT( std_ypm_judges.judges_approved_at, '%d-%b-%Y' ),
                            ' ',
                            DATE_FORMAT( std_ypm_judges.judges_approved_at, '%H:%i:%s' )) AS judges_approved_ats"))->where('periode',$periode)->get();
        $response = array(
            'status' => true,
            'periode' => $periode,
            'ypm' => $ypm,
            'teams' => $teams,
            'judges' => $judges,
            'ypm_all' => $ypm_all,
            'now' => date('Y-m-d H:i:s'),
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

public function updateYPMEvaluation(Request $request)
{
    try {
        $id = $request->get('id');
        $values = $request->get('values');

        $update = DB::connection('ympimis_2')->table('std_ypm_results')->where('id',$id)->update([
            'result' => $values,
            'updated_at' => date('Y-m-d H:i:s')
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

public function deleteYPMEvaluation(Request $request)
{
    try {
        $id = $request->get('id');

        $update = DB::connection('ympimis_2')->table('std_ypm_results')->where('id',$id)->delete();
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

public function inputYPMEvaluation(Request $request)
{
    try {
        $result = $request->get('result');
        $periode = $request->get('periode');
        $team_dept = $request->get('team_dept');
        $team_id = $request->get('team_id');
        $team_name = $request->get('team_name');
        $criteria = $request->get('criteria');
        $asesor_id = $request->get('asesor_id');
        $asesor_name = $request->get('asesor_name');

        $insert = DB::connection('ympimis_2')->table('std_ypm_results')->insert([
            'periode' => $periode,
            'team_dept' => $team_dept,
            'team_id' => $team_id,
            'team_name' => $team_name,
            'criteria' => $criteria,
            'asesor_id' => $asesor_id,
            'asesor_name' => $asesor_name,
            'result' => $result,
            'assessment_date' => date('Y-m-d'),
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

public function inputYPMEvaluationContest(Request $request)
{
    try {
        $asesor_id = $request->get('asesor_id');
        $asesor_name = $request->get('asesor_name');
        $team_dept = $request->get('team_dept');
        $team_id = $request->get('team_id');
        $team_name = $request->get('team_name');
        $title = $request->get('title');
        $criteria = $request->get('criteria');
        $periode = $request->get('periode');
        $result = $request->get('result');

        if ($result == '0') {
            $datas = DB::connection('ympimis_2')->table('std_ypm_results')->where('team_dept',$team_dept)->where('team_id',$team_id)->where('team_name',$team_name)->where('asesor_id',$asesor_id)->where('criteria',$criteria)->where('periode','CONTEST_'.$periode)->first();
            if ($datas) {
                $datas = DB::connection('ympimis_2')->table('std_ypm_results')->where('team_dept',$team_dept)->where('team_id',$team_id)->where('team_name',$team_name)->where('asesor_id',$asesor_id)->where('criteria',$criteria)->where('periode','CONTEST_'.$periode)->delete();
                $response = array(
                    'status' => true,
                );
                return Response::json($response);
            }
        }else{
            $datas = DB::connection('ympimis_2')->table('std_ypm_results')->where('team_dept',$team_dept)->where('team_id',$team_id)->where('team_name',$team_name)->where('asesor_id',$asesor_id)->where('criteria',$criteria)->where('periode','CONTEST_'.$periode)->first();
            if ($datas) {
                $datas = DB::connection('ympimis_2')->table('std_ypm_results')->where('team_dept',$team_dept)->where('team_id',$team_id)->where('team_name',$team_name)->where('asesor_id',$asesor_id)->where('criteria',$criteria)->where('periode','CONTEST_'.$periode)->delete();
            }
            $insert = DB::connection('ympimis_2')->table('std_ypm_results')->insert([
                'periode' => 'CONTEST_'.$periode,
                'team_dept' => $team_dept,
                'team_id' => $team_id,
                'team_name' => $team_name,
                'criteria' => $criteria,
                'asesor_id' => $asesor_id,
                'asesor_name' => $asesor_name,
                'result' => $result,
                'assessment_date' => date('Y-m-d'),
                'created_by' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
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

public function indexYPMPointCheck()
{
    return view('standardization.ypm.point_check')
    ->with('title', 'YPM Evaluation Point Check')
    ->with('title_jp', 'YPM評価チェック項目')
    ->with('page', 'YPM Evaluation Point Check')
    ->with('jpn', 'YPM評価チェック項目');
}

public function fetchYPMPointCheck(Request $request)
{
    try {
        $point_check = DB::connection('ympimis_2')->table('std_ypm_points')->get();
        $response = array(
            'status' => true,
            'point_check' => $point_check
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

public function inputYPMPointCheck(Request $request)
{
    try {
        $criteria = $request->get('criteria');
        $result_1 = $request->get('result_1');
        $result_2 = $request->get('result_2');
        $result_3 = $request->get('result_3');
        $result_4 = $request->get('result_4');

        $input = DB::connection('ympimis_2')->table('std_ypm_points')->insert([
            'criteria' => $criteria,
            'result_1' => $result_1,
            'result_2' => $result_2,
            'result_3' => $result_3,
            'result_4' => $result_4,
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

public function updateYPMPointCheck(Request $request)
{
    try {
        $id = $request->get('id');
        $criteria = $request->get('criteria');
        $result_1 = $request->get('result_1');
        $result_2 = $request->get('result_2');
        $result_3 = $request->get('result_3');
        $result_4 = $request->get('result_4');

        $update = DB::connection('ympimis_2')->table('std_ypm_points')->where('id',$id)->update([
            'criteria' => $criteria,
            'result_1' => $result_1,
            'result_2' => $result_2,
            'result_3' => $result_3,
            'result_4' => $result_4,
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

public function deleteYPMPointCheck(Request $request)
{
    try {
        $id = $request->get('id');

        $delete = DB::connection('ympimis_2')->table('std_ypm_points')->where('id',$id)->delete();
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

public function indexYPMMaster()
{
    $periode = DB::connection('ympimis_2')
    ->table('std_ypm_teams')
    ->select('periode')
    ->distinct()
    ->get();

    $emp = EmployeeSync::where('end_date',null)->get();
    return view('standardization.ypm.master')
    ->with('title', 'YPM Evaluation Master')
    ->with('title_jp', 'YPM評価マスター')
    ->with('page', 'YPM Evaulation Master')
    ->with('jpn', 'YPM評価マスター')
    ->with('periode', $periode)
    ->with('emp', $emp);
}

public function fetchYPMMaster(Request $request)
{
    try {
        $fiscal_year = WeeklyCalendar::select('fiscal_year')->distinct()->orderby('id','desc')->get();
        if ($request->get('periode') != '') {
            $periode = $request->get('periode');
        }else{
            $periodes = DB::connection('ympimis_2')
            ->select("SELECT DISTINCT
                ( periode )
                FROM
                std_ypm_teams
                ORDER BY
                created_at DESC
                LIMIT 1");
            if (count($periodes) > 0) {
                $periode = $periodes[0]->periode;
            }else{
                $periode = $fiscal_year[0]->fiscal_year;
            }
        }
        $teams = DB::connection('ympimis_2')
        ->table('std_ypm_teams')
        ->where('periode', $periode)
        ->get();

        $response = array(
            'status' => true,
            'teams' => $teams,
            'periode' => $periode,
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

public function updateYPMMaster(Request $request)
{
    try {
        $id = $request->get('id');
        $team_dept = $request->get('team_dept');
        $team_name = $request->get('team_name');
        $team_title = $request->get('team_title');
        $day = $request->get('day');

        $update = DB::connection('ympimis_2')->table('std_ypm_teams')->where('id', $id)->update([
            'team_dept' => $team_dept,
            'team_name' => $team_name,
            'team_title' => $team_title,
            'day' => $day,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $response = array(
            'status' => true,
            'message' => 'Update YPM Succeeded',
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

public function deleteYPMMaster(Request $request)
{
    try {

        $id = $request->get('id');
        $delete = DB::connection('ympimis_2')
        ->table('std_ypm_teams')
        ->where('id', $id)
        ->delete();
        $response = array(
            'status' => true,
            'message' => 'Team Succesfully Uploaded',
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

public function downloadYPMMaster()
{
    $file_path = public_path('data_file/TemplateYPM.xlsx');
    return response()->download($file_path);
}

public function uploadYPMMaster(Request $request)
{
    $filename = "";
    $file_destination = 'data_file/ypm';

    if (count($request->file('newAttachment')) > 0) {
        try {
            $file = $request->file('newAttachment');
            $filename = 'ypm_' . $request->get('periode') . '_' . date('YmdHisa') . '.' . $request->input('extension');
            $file->move($file_destination, $filename);

            $excel = 'data_file/ypm/' . $filename;
            $rows = Excel::load($excel, function ($reader) {
                $reader->noHeading();
                $reader->skipRows(1);

                $reader->each(function ($row) {
                });
            })->toObject();

            $delete = DB::connection('ympimis_2')
            ->table('std_ypm_teams')
            ->where('periode', $request->get('periode'))
            ->delete();

            for ($i = 0; $i < count($rows); $i++) {

                $code_generator = CodeGenerator::where('note', '=', 'ypm')->first();
                $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
                $serial_number = $code_generator->prefix.$number;
                $code_generator->index = $code_generator->index+1;

                $upload = DB::connection('ympimis_2')->table('std_ypm_teams')->insert(
                    [
                        'periode' => $request->get('periode'),
                        'team_dept' => $rows[$i][1],
                        'team_id' => $serial_number,
                        'team_name' => $rows[$i][2],
                        'team_title' => $rows[$i][3],
                        'day' => $rows[$i][4],
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );

                $code_generator->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Team Succesfully Uploaded',
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    } else {
        $response = array(
            'status' => false,
            'message' => 'Please select file to attach',
        );
        return Response::json($response);
    }
}

public function uploadYPMPdf(Request $request)
{
    $filename = "";
    $file_destination = 'data_file/ypm/pdf';

    if (count($request->file('newAttachment')) > 0) {
        try {
            $file = $request->file('newAttachment');
            $filename = 'ypm_' . $request->get('periode') . '_'. $request->get('cat') . '_' . $request->get('id') . '_' . date('YmdHisa') . '.' . $request->input('extension');
            $file->move($file_destination, $filename);
            $upload = DB::connection('ympimis_2')->table('std_ypm_teams')->where('id', $request->get('id'))->update(
                [
                    'file_pdf_'.$request->get('cat') => $filename,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );

            $response = array(
                'status' => true,
                'message' => 'PDF Succesfully Uploaded',
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    } else {
        $response = array(
            'status' => false,
            'message' => 'Please select file to attach',
        );
        return Response::json($response);
    }
}

public function indexYPMReport()
{
    $periodes = DB::connection('ympimis_2')
    ->select("SELECT DISTINCT
        ( periode )
        FROM
        std_ypm_teams
        ORDER BY
        periode DESC");

    return view('standardization.ypm.report')
    ->with('title', 'YPM Evaluation Report')
    ->with('title_jp', 'YPM評価報告')
    ->with('page', 'YPM Evaluation Report')
    ->with('jpn', 'YPM評価報告')
    ->with('periode', $periodes);
}

public function fetchYPMReport(Request $request)
{
    try {
        $periodes = DB::connection('ympimis_2')
        ->select("SELECT DISTINCT
            ( periode )
            FROM
            std_ypm_teams
            ORDER BY
            periode DESC
            LIMIT 1");
        if ($request->get('periode') == '') {
            $periode = $periodes[0]->periode;
        } else {
            $periode = $request->get('periode');
        }
        $report = DB::connection('ympimis_2')
        ->table('std_ypm_results')
        ->select('std_ypm_results.*','std_ypm_teams.team_title','std_ypm_teams.std_approval','std_ypm_teams.std_name','std_ypm_teams.hadiah',DB::RAW("CONCAT(
                            DATE_FORMAT( std_ypm_teams.std_approved_at, '%d-%b-%Y' ),
                            '<br>',
                            DATE_FORMAT( std_ypm_teams.std_approved_at, '%H:%i:%s' )) AS std_approved_ats"))
        ->where('std_ypm_results.periode','like', '%'.$periode.'%')
        ->where('std_ypm_teams.periode','like', '%'.$periode.'%')
        ->join('std_ypm_teams','std_ypm_teams.team_id','std_ypm_results.team_id')
        ->get();

        $point = DB::connection('ympimis_2')->table('std_ypm_points')->get();

        $judges = DB::connection('ympimis_2')->table('std_ypm_judges')->select('std_ypm_judges.*',DB::RAW("CONCAT(
                            DATE_FORMAT( std_ypm_judges.judges_approved_at, '%d-%b-%Y' ),
                            ' ',
                            DATE_FORMAT( std_ypm_judges.judges_approved_at, '%H:%i:%s' )) AS judges_approved_ats"))->where('periode',$periode)->get();

        $response = array(
            'status' => true,
            'report' => $report,
            'judges' => $judges,
            'point' => $point
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

    public function indexRiskAssessment()
    {
        return view('standardization.risk_assessment.index_menu')
        ->with('title', 'Risk Assessment Menu')
        ->with('title_jp', '品保')
        ->with('page', 'Risk Assessment Menu')
        ->with('jpn', '品保');
    }

    public function approvalYPM($periode,$remark,$employee_id)
    {
        try {
            if ($remark == 'std') {
                $std = DB::table('users')->where('username','PI0904007')->first();
                $emp = EmployeeSync::where('employee_id','PI0904007')->first();
                $update_teams = DB::connection('ympimis_2')->table('std_ypm_teams')->where('periode',$periode)->update([
                    'std_id' => strtoupper($emp->employee_id),
                    'std_name' => $emp->name,
                    'std_email' => $std->email,
                    'std_approval' => 'Approved',
                    'std_approved_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $teams_all = DB::connection('ympimis_2')->select("SELECT
                    std_ypm_results.team_id,
                    std_ypm_results.team_dept,
                    std_ypm_results.team_name,
                    std_ypm_teams.team_title,
                    std_ypm_teams.file_pdf_contest,
                    std_ypm_teams.hadiah,
                    results.nilai 
                FROM
                    std_ypm_results
                    JOIN std_ypm_teams ON std_ypm_teams.team_id = std_ypm_results.team_id
                    JOIN (
                    SELECT
                        std_ypm_results.team_id,
                        sum( result ) AS nilai 
                    FROM
                        std_ypm_results
                        JOIN std_ypm_teams ON std_ypm_teams.team_id = std_ypm_results.team_id 
                    WHERE
                        std_ypm_results.periode LIKE '%".$periode."%' 
                    GROUP BY
                        std_ypm_results.team_id 
                    ) AS results ON results.team_id = std_ypm_teams.team_id 
                WHERE
                    std_ypm_results.periode LIKE '%".$periode."%' 
                GROUP BY
                    std_ypm_results.team_id,
                    std_ypm_results.team_dept,
                    std_ypm_results.team_name,
                    std_ypm_teams.team_title,
                    std_ypm_teams.file_pdf_contest,
                    std_ypm_teams.hadiah,
                    results.nilai 
                ORDER BY
                    results.nilai DESC");

                $results = DB::connection('ympimis_2')->select("SELECT
                        std_ypm_results.team_id,
                        asesor_id,
                        sum( result ) AS nilai 
                    FROM
                        std_ypm_results
                        JOIN std_ypm_teams ON std_ypm_teams.team_id = std_ypm_results.team_id 
                    WHERE
                        std_ypm_results.periode LIKE '%".$periode."%' 
                    GROUP BY
                        std_ypm_results.team_id,
                        asesor_id");

                $judges_all = DB::connection('ympimis_2')->table('std_ypm_judges')->where('periode',$periode)->get();

                $next_judges = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->limit(1)->first();
                if ($next_judges) {
                    $update_next_judges = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->limit(1)->update([
                        'judges_priority' => '1',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                $judges = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->first();

                if ($judges) {
                    $mail_to = $judges->judges_email;
                    $data = array(
                        'results' => $results,
                        'statuses' => 'Belum',
                        'teams' => $teams_all,
                        'judges_all' => $judges_all,
                        'periode' => $periode,
                        'employee_id' => $judges->judges_id
                    );
                    Mail::to($mail_to)
                    ->bcc(['rani.nurdiyana.sari@music.yamaha.com','mokhamad.khamdan.khabibi@music.yamaha.com'])
                    ->send(new SendEmail($data, 'ypm_approval'));
                }

                $response = array(
                    'status' => true,
                );
                return Response::json($response);
            }
            if ($remark == 'judges') {
                $judgess = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->where('judges_id',$employee_id)->first();

                if (!$judgess) {
                    return view('standardization.ypm.approved')
                        ->with('title', 'YPM Contest Approval')
                        ->with('title_jp', 'YPMコンテストの承認')
                        ->with('message', 'YPM Has Been Approved YPM承認済み')
                        ->with('head', 'YPM Contest Approval')
                        ->with('page', 'YMPI Productivity Management Evaluation')
                        ->with('jpn', 'YMPI生産性管理評価');
                }else{
                    $std = DB::table('users')->where('username','PI0904007')->first();
                    $emp = EmployeeSync::where('employee_id','PI0904007')->first();

                    $teams_all = DB::connection('ympimis_2')->select("SELECT
                        std_ypm_results.team_id,
                        std_ypm_results.team_dept,
                        std_ypm_results.team_name,
                        std_ypm_teams.team_title,
                        std_ypm_teams.file_pdf_contest,
                        std_ypm_teams.hadiah,
                        results.nilai 
                    FROM
                        std_ypm_results
                        JOIN std_ypm_teams ON std_ypm_teams.team_id = std_ypm_results.team_id
                        JOIN (
                        SELECT
                            std_ypm_results.team_id,
                            sum( result ) AS nilai 
                        FROM
                            std_ypm_results
                            JOIN std_ypm_teams ON std_ypm_teams.team_id = std_ypm_results.team_id 
                        WHERE
                            std_ypm_results.periode LIKE '%".$periode."%' 
                        GROUP BY
                            std_ypm_results.team_id 
                        ) AS results ON results.team_id = std_ypm_teams.team_id 
                    WHERE
                        std_ypm_results.periode LIKE '%".$periode."%' 
                    GROUP BY
                        std_ypm_results.team_id,
                        std_ypm_results.team_dept,
                        std_ypm_results.team_name,
                        std_ypm_teams.team_title,
                        std_ypm_teams.file_pdf_contest,
                        std_ypm_teams.hadiah,
                        results.nilai 
                    ORDER BY
                        results.nilai DESC");

                    $results = DB::connection('ympimis_2')->select("SELECT
                            std_ypm_results.team_id,
                            asesor_id,
                            sum( result ) AS nilai 
                        FROM
                            std_ypm_results
                            JOIN std_ypm_teams ON std_ypm_teams.team_id = std_ypm_results.team_id 
                        WHERE
                            std_ypm_results.periode LIKE '%".$periode."%' 
                        GROUP BY
                            std_ypm_results.team_id,
                            asesor_id");

                    $judges_all = DB::connection('ympimis_2')->table('std_ypm_judges')->where('periode',$periode)->get();

                    $updatejudges = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->where('judges_id',$employee_id)->update([
                        'judges_priority' => null,
                        'judges_approval' => 'Approved',
                        'judges_approved_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $judges = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->first();

                    if ($judges) {
                        $mail_to = $judges->judges_email;
                        $data = array(
                            'results' => $results,
                            'statuses' => 'Belum',
                            'teams' => $teams_all,
                            'judges_all' => $judges_all,
                            'periode' => $periode,
                            'employee_id' => $judges->judges_id,
                        );

                        Mail::to($mail_to)
                        ->bcc(['rani.nurdiyana.sari@music.yamaha.com','mokhamad.khamdan.khabibi@music.yamaha.com'])
                        ->send(new SendEmail($data, 'ypm_approval'));

                        $next_judges = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->limit(1)->first();
                        if ($next_judges) {
                            $update_next_judges = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->limit(1)->update([
                                'judges_priority' => '1',
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                        }

                        return view('standardization.ypm.approved')
                        ->with('title', 'YPM Contest Approval')
                        ->with('title_jp', 'YPMコンテストの承認')
                        ->with('message', 'YPM Approved Successfully YPM承認完了')
                        ->with('head', 'YPM Contest Approval')
                        ->with('page', 'YMPI Productivity Management Evaluation')
                        ->with('jpn', 'YMPI生産性管理評価');
                    }else{
                        $data = array(
                            'results' => $results,
                            'statuses' => 'Final',
                            'teams' => $teams_all,
                            'judges_all' => $judges_all,
                            'periode' => $periode,
                            'employee_id' => 'PI1910002',
                        );

                        Mail::to(['rani.nurdiyana.sari@music.yamaha.com'])
                        ->bcc(['rani.nurdiyana.sari@music.yamaha.com','mokhamad.khamdan.khabibi@music.yamaha.com'])
                        ->send(new SendEmail($data, 'ypm_approval'));

                        $next_judges = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->limit(1)->first();
                        if ($next_judges) {
                            $update_next_judges = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->limit(1)->update([
                                'judges_priority' => null,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                        }

                        return view('standardization.ypm.approved')
                        ->with('title', 'YPM Contest Approval')
                        ->with('title_jp', 'YPMコンテストの承認')
                        ->with('message', 'YPM Approved Successfully YPM承認完了')
                        ->with('head', 'YPM Contest Approval')
                        ->with('page', 'YMPI Productivity Management Evaluation')
                        ->with('jpn', 'YMPI生産性管理評価');
                    }
                }
            }
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function rejectYPM($periode,$remark,$employee_id)
    {
        try {
            $judgess = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->where('judges_id',$employee_id)->first();

            if (!$judgess) {
                return view('standardization.ypm.reject')
                    ->with('title', 'YPM Contest Approval')
                    ->with('title_jp', 'YPMコンテストの承認')
                    ->with('message', 'YPM Has Been Rejected YPM却下済み')
                    ->with('head', 'YPM Contest Approval')
                    ->with('page', 'YMPI Productivity Management Evaluation')
                    ->with('jpn', 'YMPI生産性管理評価');
            }else{
                $std = DB::table('users')->where('username','PI0904007')->first();
                $emp = EmployeeSync::where('employee_id','PI0904007')->first();

                $teams_all = DB::connection('ympimis_2')->select("SELECT
                    std_ypm_results.team_id,
                    std_ypm_results.team_dept,
                    std_ypm_results.team_name,
                    std_ypm_teams.team_title,
                    std_ypm_teams.file_pdf_contest,
                    results.nilai 
                FROM
                    std_ypm_results
                    JOIN std_ypm_teams ON std_ypm_teams.team_id = std_ypm_results.team_id
                    JOIN (
                    SELECT
                        std_ypm_results.team_id,
                        sum( result ) AS nilai 
                    FROM
                        std_ypm_results
                        JOIN std_ypm_teams ON std_ypm_teams.team_id = std_ypm_results.team_id 
                    WHERE
                        std_ypm_results.periode LIKE '%".$periode."%' 
                    GROUP BY
                        std_ypm_results.team_id 
                    ) AS results ON results.team_id = std_ypm_teams.team_id 
                WHERE
                    std_ypm_results.periode LIKE '%".$periode."%' 
                GROUP BY
                    std_ypm_results.team_id,
                    std_ypm_results.team_dept,
                    std_ypm_results.team_name,
                    std_ypm_teams.team_title,
                    std_ypm_teams.file_pdf_contest,
                    results.nilai 
                ORDER BY
                    results.nilai DESC");

                $results = DB::connection('ympimis_2')->select("SELECT
                        std_ypm_results.team_id,
                        asesor_id,
                        sum( result ) AS nilai 
                    FROM
                        std_ypm_results
                        JOIN std_ypm_teams ON std_ypm_teams.team_id = std_ypm_results.team_id 
                    WHERE
                        std_ypm_results.periode LIKE '%".$periode."%' 
                    GROUP BY
                        std_ypm_results.team_id,
                        asesor_id");

                $judges_all = DB::connection('ympimis_2')->table('std_ypm_judges')->where('periode',$periode)->get();

                $judges = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->where('judges_id',$employee_id)->first();

                $updatejudges = DB::connection('ympimis_2')->table('std_ypm_judges')->where('judges_approval',null)->where('judges_id',$employee_id)->update([
                    'judges_approval' => 'Rejected',
                    'judges_approved_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $data = array(
                    'results' => $results,
                    'statuses' => 'Final',
                    'teams' => $teams_all,
                    'judges_all' => $judges_all,
                    'periode' => $periode,
                    'employee_id' => $judges->judges_name,
                );

                Mail::to(['rani.nurdiyana.sari@music.yamaha.com'])
                ->bcc(['ympi-mis-ML@music.yamaha.com'])
                ->send(new SendEmail($data, 'ypm_reject'));
                
                return view('standardization.ypm.reject')
                ->with('title', 'YPM Contest Approval')
                ->with('title_jp', 'YPMコンテストの承認')
                ->with('message', 'YPM Rejected Successfully YPM却下完了')
                ->with('head', 'YPM Contest Approval')
                ->with('page', 'YMPI Productivity Management Evaluation')
                ->with('jpn', 'YMPI生産性管理評価');
            }

        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function inputYPMHadiah(Request $request)
    {
        try {
            $team_id = $request->get('team_id');
            $hadiah = $request->get('hadiah');
            $periode = $request->get('periode');

            for ($i=0; $i < count($team_id); $i++) { 
                $teams = DB::connection('ympimis_2')->table('std_ypm_teams')->where('team_id',$team_id[$i])->where('periode',$periode)->update([
                    'hadiah' => $hadiah[$i]
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


}
