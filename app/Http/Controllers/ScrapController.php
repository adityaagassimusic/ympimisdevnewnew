<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\EmployeeSync;
use App\Http\Controllers\Controller;
use App\PenarikanScrap;
use App\SapCompletion;
use App\SapTransactions;
use App\ScrapList;
use App\ScrapLocation;
use App\ScrapLog;
use App\ScrapPenarikanLog;
use App\TransactionCompletion;
use App\User;
use DataTables;
use Excel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;

class ScrapController extends Controller
{
    private $__storage_location;
    public function __construct()
    {
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

        $this->reicive = [
            'MSCR',
            'WSCR',
            'MMJR',
            'OTHR',
            'WPRC',
            'MSTK',
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
            'WPCS',
        ];

        $this->category_reason = [
            'Material Jelek',
            'Material Salah',
        ];

        $this->category = [
            'PANTHOM',
            'NON PANTHOM',
        ];
    }

    public function IndexUploadScrapMirai()
    {

        return view('scrap.index_upload_scrap', array(
            'title' => 'Scrap Upload',
            'title_jp' => '',

        ))->with('page', 'Scrap');
    }

    public function FetchUploadScrapMirai()
    {
        $bulan = date('Y-m-01');
        $resumes = db::select('select * from sap_transactions where DATE_FORMAT(posting_date, "%Y-%m-%d") >= "' . $bulan . '"');

        $response = array(
            'status' => true,
            'message' => 'Upload Berhasil',
            'resumes' => $resumes,
        );
        return Response::json($response);
    }

    public function InputUploadScrapMirai(Request $request)
    {
        $filename = "";
        $file_destination = 'data_file/scrap';
        $bulan = date('Y-m-01');

        if (count($request->file('newAttachment')) > 0) {
            try {
                $file = $request->file('newAttachment');
                $filename = 'Scrap_Month_' . date('YmdHisa') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);

                $excel = 'data_file/scrap/' . $filename;
                $rows = Excel::load($excel, function ($reader) {
                    $reader->noHeading();
                    $reader->skipRows(1);
                })->toObject();

                $delete = SapTransactions::where(DB::RAW("DATE_FORMAT(posting_date,'%Y-%m-%d')"), '>=', $bulan)->forceDelete();

                for ($i = 0; $i < count($rows); $i++) {
                    $data = new SapTransactions(
                        [
                            'entry_date' => date('Y-m-d', strtotime($rows[$i][0])),
                            'posting_date' => date('Y-m-d', strtotime($rows[$i][1])),
                            'movement_type' => $rows[$i][2],
                            'material_number' => $rows[$i][3],
                            'quantity' => $rows[$i][4],
                            'storage_location' => $rows[$i][5],
                            'receive_location' => $rows[$i][6],
                            'reference' => $rows[$i][7],
                            'remark' => $rows[$i][8],
                            'created_by' => Auth::id(),
                            'updated_at' => date("Y-m-d H:i:s"),
                            'created_at' => date("Y-m-d H:i:s"),
                        ]
                    );
                    $data->save();
                }
                $response = array(
                    'status' => true,
                    'message' => 'Berhasil Di Upload',
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

    public function InputUploadDailyScrapMirai(Request $request)
    {
        $filename = "";
        $file_destination = 'data_file/scrap';
        $date = $request->get('date');

        if (count($request->file('newAttachment')) > 0) {
            try {
                $delete = SapTransactions::where(DB::RAW("DATE_FORMAT(entry_date,'%Y-%m-%d')"), '>=', $date)->forceDelete();
                $file = $request->file('newAttachment');
                $filename = 'Scrap_Daily_' . date('YmdHisa') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);

                $excel = 'data_file/scrap/' . $filename;
                $rows = Excel::load($excel, function ($reader) {
                    $reader->noHeading();
                    $reader->skipRows(1);
                })->toObject();

                for ($i = 0; $i < count($rows); $i++) {
                    $data = new SapTransactions(
                        [
                            'entry_date' => date('Y-m-d', strtotime($rows[$i][0])),
                            'posting_date' => date('Y-m-d', strtotime($rows[$i][1])),
                            'movement_type' => $rows[$i][2],
                            'material_number' => $rows[$i][3],
                            'quantity' => $rows[$i][4],
                            'storage_location' => $rows[$i][5],
                            'receive_location' => $rows[$i][6],
                            'reference' => $rows[$i][7],
                            'remark' => $rows[$i][8],
                            'created_by' => Auth::id(),
                            'updated_at' => date("Y-m-d H:i:s"),
                            'created_at' => date("Y-m-d H:i:s"),
                        ]
                    );
                    $data->save();
                }
                $response = array(
                    'status' => true,
                    'message' => 'Berhasil Di Upload',
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

    public function displayScrapWarehouse()
    {

        return view('scrap.display', array(
            'title' => 'Scrap & Return MMJR/OTHR',
            'title_jp' => 'MMJR/OTHRの廃却・返却',

        ))->with('page', 'Scrap');
    }

    public function MonitoringWip()
    {

        return view('scrap.monitoring', array(
            'title' => 'Scrap & Return MMJR/OTHR',
            'title_jp' => 'MMJR/OTHRの廃却・返却',
            'storage_locations' => $this->storage_location,

        ))->with('page', 'Scrap');
    }

    public function MonitoringScrapDisplay()
    {
        $storage_locations = $this->storage_location;

        return view('scrap.display_monitoring', array(
            'title' => 'Scrap & Return MMJR/OTHR',
            'title_jp' => 'MMJR/OTHRの廃却・返却',
            'storage_locations' => $storage_locations,

        ))->with('page', 'Scrap');
    }

    public function indexScrap()
    {
        $reason = db::select('SELECT reason, reason_name FROM scrap_reasons ORDER BY reason ASC');

        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)->first();

        $defect = db::select('select defect from scrap_defects order by  defect ASC');

        return view('scrap.index', array(
            'reason' => $reason,
            'title' => 'Scrap & Return MMJR/OTHR',
            'title_jp' => 'MMJR/OTHRの廃却・返却',
            'storage_locations' => $this->storage_location,
            'category_reason' => $this->category_reason,
            'reicive' => $this->reicive,
            'category' => $this->category,
            'emp_dept' => $emp_dept,
            'defect' => $defect
        ))->with('page', 'Scrap');
    }

    public function ListWip()
    {
        $reason = db::select('SELECT reason, reason_name FROM scrap_reasons ORDER BY reason ASC');

        return view('scrap.list_scrap_wip', array(
            'reason' => $reason,
            'title' => 'Scrap & Return MMJR/OTHR',
            'title_jp' => 'MMJR/OTHRの廃却・返却',
            'storage_locations' => $this->storage_location,
            'category_reason' => $this->category_reason,
            'reicive' => $this->reicive,
            'category' => $this->category,
        ))->with('page', 'Scrap');
    }

    public function indexScrapData()
    {
        $storage_locations = ScrapLocation::select('location', 'storage_location')->distinct()
        ->orderBy('location', 'asc')
        ->get();

        return view('scrap.list', array(
            'title' => 'Data Scrap Material',
            'title_jp' => 'スクラップ材料',
            'storage_locations' => $storage_locations,
        ))->with('page', 'Scrap');
    }

    public function indexScrapView()
    {

        return view('scrap.view', array(
            'title' => 'Scrap Material',
            'title_jp' => 'スクラップ材料',
            'storage_locations' => $this->storage_location,

        ))->with('page', 'Scrap');
    }

    public function indexWarehouse()
    {
        $emp_dept = User::where('username', Auth::user()->username)
        ->select('username', 'role_code')
        ->first();

        return view('scrap.warehouse', array(
            'title' => 'Scrap Material',
            'title_jp' => 'スクラップ材料',
            'emp_dept' => $emp_dept,

        ))->with('page', 'Scrap');
    }

    public function indexLogs()
    {
        $materials = db::table('scrap_materials')
        ->whereNull('deleted_at')
        ->select('material_number', 'material_description as description', 'issue_location')
        ->orderBy('issue_location', 'ASC')
        ->orderBy('material_number', 'ASC')
        ->get();

        return view('scrap.logs', array(
            'title' => 'Scrap Logs',
            'storage_locations' => $this->storage_location,
            'reicive' => $this->reicive,
            'category' => $this->category,
            'category_reason' => $this->category_reason,
            'materials' => $materials,
        ))->with('page', 'Scrap Logs');
    }

    public function fetchLogs(Request $request)
    {

        $datefrom = '';
        if ($request->get('datefrom') != null) {
            $datefrom = " AND DATE_FORMAT(non.updated_at, '%Y-%m-%d') >= '" . $request->get('datefrom') . "' ";
        }

        $dateto = '';
        if ($request->get('dateto') != null) {
            $dateto = " AND DATE_FORMAT(non.updated_at, '%Y-%m-%d') <= '" . $request->get('dateto') . "' ";
        }

        $issue = '';
        if ($request->get('issue') != null) {
            $issues = $request->get('issue');
            for ($i = 0; $i < count($issues); $i++) {
                $issue = $issue . "'" . $issues[$i] . "'";
                if ($i != (count($issues) - 1)) {
                    $issue = $issue . ',';
                }
            }
            $issue = " AND issue_location IN (" . $issue . ") ";
        }

        $receive = '';
        if ($request->get('receive') != null) {
            $receives = $request->get('receive');
            for ($i = 0; $i < count($receives); $i++) {
                $receive = $receive . "'" . $receives[$i] . "'";
                if ($i != (count($receives) - 1)) {
                    $receive = $receive . ',';
                }
            }
            $receive = " AND receive_location IN (" . $receive . ") ";
        }

        $material = '';
        if ($request->get('material') != null) {
            $materials = $request->get('material');
            for ($i = 0; $i < count($materials); $i++) {
                $material = $material . "'" . $materials[$i] . "'";
                if ($i != (count($materials) - 1)) {
                    $material = $material . ',';
                }
            }
            $material = " AND non.material_number IN (" . $material . ") ";
        }

        $remark = '';
        if ($request->get('remark') != null) {
            $remark = " AND non.remark = '" . $request->get('remark') . "' ";
        }

        $condition = $datefrom . $dateto . $issue . $receive . $material . $remark;

        $log = db::select("
           SELECT
           non.id,
           CONCAT(non.slip,'-SC') as slip,
           non.order_no,
           non.material_number,
           non.issue_location,
           non.category,
           IF(sts.material_number is null, '-', 'Silver') as jenis,
           non.receive_location,
           non.material_description,
           non.quantity,
           non.remark,
           non.reason,
           non.summary,
           IF(non.no_invoice is not null, non.no_invoice, '-') as no_invoice,
           non.slip_created AS printed_at,
           scrap_user.`name` AS printed_by,
           IF(non.remark = 'received', non.created_at, '-') AS received_at,
           IF(non.remark = 'received', receive_user.`name`, '-') AS received_by,
           IF(non.remark = 'canceled' || non.remark = 'deleted', non.deleted_at, '-') AS canceled_at,
           IF(non.remark = 'canceled' || non.remark = 'deleted', cancel_user.`name`, '-') AS canceled_by
           FROM
           (SELECT id, slip, order_no, material_number, material_description, issue_location, receive_location, quantity, remark, reason, summary, slip_created, scraped_by, created_at, created_by, no_invoice, category, canceled_by, deleted_at, updated_at, canceled_user, canceled_user_at FROM `scrap_logs`
            UNION ALL
            SELECT id, slip, order_no, material_number, material_description, issue_location, receive_location, quantity, remark, reason, summary, created_at as slip_created, created_by as scraped_by, created_at, created_by, no_invoice, category, 0 as canceled_by, deleted_at, updated_at, 0 as canceled_user, 0 as canceled_user_at FROM `scrap_lists` where remark is not null
            ) AS non
           LEFT JOIN (select stocktaking_silver_lists.material_number from stocktaking_silver_lists group by stocktaking_silver_lists.material_number) as sts on sts.material_number = non.material_number
           LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS scrap_user ON scrap_user.id = non.scraped_by
           LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS receive_user ON receive_user.id = non.created_by
           LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS cancel_user ON cancel_user.id = non.canceled_by
           WHERE non.id is not null " . $condition . "
           ORDER BY non.slip_created");

        return DataTables::of($log)
        ->addColumn('ppp', function($data){
            $count_sum = explode('/', $data->summary);
            $p = '';

            for ($i=0; $i < count($count_sum); $i++) { 
                $p .= $count_sum[$i]."<br>";
            }
            return $p;
        })
        ->addColumn('cancel', function ($data) {
            if (Auth::user()->role_code == "S-MIS" || Auth::user()->role_code == "SL-LOG" || Auth::user()->role_code == "S-PRD" || Auth::user()->role_code == "S-PCH" || Auth::user()->role_code == "S-QA" || Auth::user()->role_code == "C-PCH") {
                if ($data->remark == 'received') {
                    return '<button style="width: 50%; height: 100%;" onclick="CancelScrapUser(\'' . $data->id . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button>';
                }
                if (Auth::user()->role_code == "S-MIS" || Auth::user()->role_code == "S-PRD" || Auth::user()->role_code == "S-PCH" || Auth::user()->role_code == "S-QA" || Auth::user()->role_code == "C-PCH") {
                    if ($data->remark == 'pending') {
                        return '<button style="width: 50%; height: 100%;" onclick="cancelScrap(\'' . $data->id . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button>';
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }
            } else {
                return '-';
            }
        })
        ->addColumn('reprint', function ($data) {
            if (Auth::user()->role_code == "S-MIS" || Auth::user()->role_code == "SL-LOG" || Auth::user()->role_code == "S-QA" || Auth::user()->role_code == "S-PCH") {
                return '<button style="width: 50%; height: 100%;" class="btn btn-xs btn-info form-control" type="button" onclick="reprintScrap(\'' . $data->id . '\')"><span><i class="fa fa-print"></i></span></button>';
            } else {
                return '-';
            }
        })
        ->addColumn('test', function ($data) {
            if (Auth::user()->role_code == "S-MIS" || Auth::user()->role_code == "L-QA" || Auth::user()->role_code == "L-BPP" || Auth::user()->role_code == "L-WP"|| Auth::user()->role_code == "L-KPP") {
                if ($data->remark == 'ditarik' || $data->remark == 'pending') {
                    return '<button style="width: 50%; height: 100%;" onclick="penarikanScrap(' . $data->id . ')" class="btn btn-xs btn-success form-control"><span><i class="fa fa-hand-lizard-o"></i></span></button>';
                } else if ($data->no_invoice != null) {
                    return '<button style="width: 50%; height: 100%;" onclick="penarikanScrap(' . $data->id . ')" class="btn btn-xs btn-success form-control"><span><i class="fa fa-hand-lizard-o"></i></span></button>';
                } else {
                    return '-';
                }
            } else {
                return '-';
            }
        })
        ->rawColumns(['cancel' => 'cancel', 'test' => 'test', 'reprint' => 'reprint', 'ppp' => 'ppp'])
        ->make(true);
    }

    public function addPenarikanScrap(Request $request)
    {
        try {
            $name = Auth::user()->name;

            // $update = ScrapList::where('id', '=', $request->get('id_penarikan'))->first();
            $id = ScrapList::where('id', '=', $request->get('id_penarikan'))->first();
            // $id->update([
            //     'remark' => 'ditarik',
            // ]);
            $scrap_logs = '';
            if ($id != null) {
                $logs = db::table('scrap_logs')->insert([
                    'slip' => $id->slip,
                    'order_no' => $id->order_no,
                    'material_number' => $id->material_number,
                    'material_description' => $id->material_description,
                    'spt' => $id->spt,
                    'valcl' => $id->valcl,
                    'category' => $id->category,
                    'issue_location' => $id->issue_location,
                    'receive_location' => $id->receive_location,
                    'quantity' => $id->quantity,
                    'uom' => $id->uom,
                    'reason' => $id->reason,
                    'summary' => $id->summary,
                    'remark' => 'ditarik',
                    'created_by' => Auth::id(),
                    'scraped_by' => $id->created_by,
                    'slip_created' => $id->created_at,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'created_at' => date("Y-m-d H:i:s"),
                ]);

                $delete = ScrapList::where('id', '=', $request->get('id_penarikan'))->forceDelete();

                $scrap_logs = ScrapLog::where('slip', '=', $id->slip)
                ->select('id', 'slip', 'order_no', 'material_number', 'material_description', 'spt', 'valcl', 'category', 'issue_location', 'receive_location', 'quantity', 'uom', 'reason', 'summary', 'no_invoice', 'remark', 'created_at', 'scraped_by')
                ->first();
            } else {
                $upd = db::table('scrap_logs')->where('id', '=', $request->get('id_penarikan'))->update([
                    'remark' => 'ditarik',
                ]);

                $scrap_logs = ScrapLog::where('id', '=', $request->get('id_penarikan'))
                ->select('id', 'slip', 'order_no', 'material_number', 'material_description', 'spt', 'valcl', 'category', 'issue_location', 'receive_location', 'quantity', 'uom', 'reason', 'summary', 'no_invoice', 'remark', 'created_at', 'scraped_by')
                ->first();
            }

            // $penarikan = new PenarikanScrap([
            //     'order_no'         => $scrap_logs->order_no,
            //     'ke_lokasi'        => $request->get('ke_lokasi'),
            //     'penarikan_name'   => $name,
            //     'penarikan_at'     => date("Y-m-d H:i:s"),
            //     'penarikan_reason' => $request->get('penarikan_reason'),
            // ]);
            // $penarikan->save();

            $code_generator = CodeGenerator::where('note', '=', 'PENARIKAN SCRAP')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $request_id = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $scrap_penarikan = db::table('scrap_penarikan_logs')->insert([
                'slip_penarikan' => $request_id,
                'slip' => $scrap_logs->slip,
                'order_no' => $scrap_logs->order_no,
                'material_number' => $scrap_logs->material_number,
                'material_description' => $scrap_logs->material_description,
                'spt' => $scrap_logs->spt,
                'valcl' => $scrap_logs->valcl,
                'category' => $scrap_logs->category,
                'issue_location' => $scrap_logs->issue_location,
                'receive_location' => $scrap_logs->receive_location,
                'withdrawal_to' => $request->get('ke_lokasi'),
                'quantity' => $scrap_logs->quantity,
                'uom' => $scrap_logs->uom,
                'no_invoice' => $scrap_logs->no_invoice,
                'scrap_by' => $scrap_logs->scraped_by,
                'scrap_at' => $scrap_logs->scraped_at,
                'reason' => $request->get('penarikan_reason'),
                'created_by' => Auth::id(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $data_penarikan = db::select('select slip_penarikan, slip, order_no, material_number, material_description, spt, valcl, category, issue_location, receive_location, withdrawal_to, quantity, uom, no_invoice, scrap_by, reason, created_by, created_at from scrap_penarikan_logs where slip = "' . $scrap_logs->slip . '"');

            self::printReceivePenarikan($data_penarikan[0]->slip, $data_penarikan[0]->slip_penarikan, $data_penarikan[0]->material_number, $data_penarikan[0]->category, $data_penarikan[0]->material_description, $data_penarikan[0]->receive_location, $data_penarikan[0]->withdrawal_to, $data_penarikan[0]->quantity, $data_penarikan[0]->uom, $data_penarikan[0]->no_invoice, $name = Auth::user()->name, $data_penarikan[0]->created_at);

            self::printReceivePenarikan($data_penarikan[0]->slip, $data_penarikan[0]->slip_penarikan, $data_penarikan[0]->material_number, $data_penarikan[0]->category, $data_penarikan[0]->material_description, $data_penarikan[0]->receive_location, $data_penarikan[0]->withdrawal_to, $data_penarikan[0]->quantity, $data_penarikan[0]->uom, $data_penarikan[0]->no_invoice, $name = Auth::user()->name, $data_penarikan[0]->created_at);

            // $data_penarikan = PenarikanScrap::where('order_no', '=', $scrap_logs->order_no)
            //     ->select('order_no', 'ke_lokasi', 'penarikan_name', 'penarikan_at', 'penarikan_reason')
            //     ->first();

            // $data = [
            //     'scrap_logs'     => $scrap_logs,
            //     'data_penarikan' => $data_penarikan,
            // ];

            // Mail::to(['dwi.misnanto@music.yamaha.com'])
            //     ->bcc(['lukmannul.arif@music.yamaha.com'])
            //     ->send(new SendEmail($data, 'request_penarikan'));

        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
        $response = array(
            'status' => true,
            'message' => 'Slip Scrap berhasil di cancel',
        );
        return Response::json($response);
    }

    public function printReceivePenarikan($slip, $slip_penarikan, $material_number, $category, $material_description, $receive_location, $withdrawal_to, $quantity, $uom, $no_invoice, $name)
    {
        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)->first();

        if (Auth::user()->role_code == 'S-MIS' || Auth::user()->role_code == 'S') {
            $printer_name = 'MIS';
        } else {
            $printer_name = 'IQA Printer';
        }
        //     if ($from_loc == 'CL91') {
        //         $printer_name = 'FLO Printer 102';
        //     } elseif ($from_loc == 'SX91') {
        //         $printer_name = 'FLO Printer 103';
        //     } elseif ($from_loc == 'FL91') {
        //         $printer_name = 'KDO FL';
        //     } elseif ($from_loc == 'SX51' || $from_loc == 'CL51' || $from_loc == 'FL51' || $from_loc == 'VN51') {
        //         $printer_name = 'Stockroom-Printer';
        //     } elseif ($from_loc == 'SX21' || $from_loc == 'CL21' || $from_loc == 'FL21' || $from_loc == 'VN21') {
        //         $printer_name = 'Welding-Printer';
        //     } elseif ($from_loc == 'VN91' || $from_loc == 'VNA0' || $from_loc == 'VN11' || $from_loc == 'RC11' || $from_loc == 'PXMP') {
        //         $printer_name = 'Injection';
        //     } elseif ($from_loc == 'RC91') {
        //         $printer_name = 'FLO Printer RC';
        //     } elseif ($from_loc == 'CLA0' || $from_loc == 'SXA0' || $from_loc == 'FLA0' || $from_loc == 'ZPA0' || $from_loc == 'SXA2' || $from_loc == 'FLA2' || $from_loc == 'CLA2' || $from_loc == 'SA0R' || $from_loc == 'FA0R' || $from_loc == 'LA0R') {
        //         $printer_name = 'KDO ZPRO';
        //     } elseif ($from_loc == 'SXA1' || $from_loc == 'FLA1' || $from_loc == 'FA1R' || $from_loc == 'SA1R') {
        //         $printer_name = 'Body Process Printer';
        //     } elseif ($from_loc == 'CS91' || $from_loc == 'SXT9' || $from_loc == 'FLT9' || $from_loc == 'CLB9' || $from_loc == 'PNR9') {
        //         $printer_name = 'KDO CASE';
        //     } elseif ($from_loc == 'PN91') {
        //         $printer_name = 'FLO Printer 105';
        //     } elseif (($from_loc == 'MSTK' || $from_loc == 'WPCS') && $emp_dept->department == 'Quality Assurance Department') {
        //         $printer_name = 'IQA Printer';
        //     } elseif ($from_loc == 'MSTK' || $from_loc == 'OTHR' || $from_loc == 'WPPN' || $from_loc == 'WPCS' || $from_loc == 'WPRC' || $from_loc == '214' || $from_loc == 'MMJR') {
        //         $printer_name = 'FLO Printer LOG';
        //     } else {
        //         $printer_name = 'MIS';
        //     }
        // }

        // if ($material_number == 'NO SAP') {
        //     $printer_name = 'FLO Printer LOG';
        // }

        // if ($cat == 'received') {
        //     $printer_name = 'FLO Printer LOG';
        // }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text("SLIP SCRAP" . "\n");
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($slip . "\n");
        $printer->feed(1);
        $printer->text("SLIP PENARIKAN SCRAP" . "\n");
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($slip_penarikan . "\n");
        $printer->feed(1);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($material_number . ' - ' . $category . "\n");
        $printer->setTextSize(1, 1);
        $printer->text($material_description . "\n");
        $printer->setTextSize(2, 2);
        $printer->text($receive_location . " -> " . $withdrawal_to . "\n");
        $printer->text($quantity . " " . $uom . "\n");
        $printer->setTextSize(2, 2);
        $printer->text("No Invoice : " . $no_invoice . "\n");
        $printer->feed(2);
        $printer->setTextSize(1, 1);
        $printer->qrCode($slip_penarikan, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
        $printer->text($slip_penarikan . "\n");
        $printer->feed(1);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("------------------------------------");
        $printer->feed(1);
        $printer->text("|Delivered By:    |Confirmed By:   |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("------------------------------------");
        $printer->feed(2);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->textRaw("(" . date("d-M-Y H:i:s") . ")\n");
        $printer->text($name . "\n");
        $printer->feed(1);
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    public function accPenarikanScrap($id)
    {
        try {
            $update = ScrapLog::where('id', '=', $id)->update([
                'remark' => 'acc ditarik',
            ]);

            $scrap_logs = ScrapLog::where('id', '=', $id)
            ->select('id', 'slip', 'order_no', 'material_number', 'material_description', 'spt', 'valcl', 'category', 'issue_location', 'receive_location', 'quantity', 'uom', 'reason', 'summary', 'no_invoice', 'remark', 'created_at')
            ->first();
            $data_penarikan = PenarikanScrap::where('order_no', '=', $scrap_logs->order_no)
            ->select('order_no', 'ke_lokasi', 'penarikan_name', 'penarikan_at', 'penarikan_reason')
            ->first();

            return view('scrap.penarikan_done', array(
                'title' => 'Scrap & Return MMJR/OTHR',
                'title_jp' => '??',
                'scrap_logs' => $scrap_logs,
                'data_penarikan' => $data_penarikan,

            ))->with('page', 'Penarikan Scrap');
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function rejectPenarikanScrap($id)
    {
        try {
            $update = ScrapLog::where('id', '=', $id)->update([
                'remark' => 'data sudah di BAP',
            ]);

            $scrap_logs = ScrapLog::where('id', '=', $id)
            ->select('id', 'slip', 'order_no', 'material_number', 'material_description', 'spt', 'valcl', 'category', 'issue_location', 'receive_location', 'quantity', 'uom', 'reason', 'summary', 'no_invoice', 'remark', 'created_at')
            ->first();
            $data_penarikan = PenarikanScrap::where('order_no', '=', $scrap_logs->order_no)
            ->select('order_no', 'ke_lokasi', 'penarikan_name', 'penarikan_at', 'penarikan_reason')
            ->first();

            return view('scrap.penarikan_done', array(
                'title' => 'Scrap & Return MMJR/OTHR',
                'title_jp' => '??',
                'data_penarikan' => $data_penarikan,
                'scrap_logs' => $scrap_logs,

            ))->with('page', 'Penarikan Scrap');
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function printScrapPenarikan(Request $request)
    {
        try {
            $name = Auth::user()->name;
            $scrap_logs = ScrapLog::where('id', '=', $request->get('id'))->first();
            $scrap_penarikan = PenarikanScrap::where('order_no', '=', $scrap_logs->order_no)->first();

            $update = $scrap_logs->update([
                'remark' => 'done print slip penarikan',
            ]);

            $slip = $scrap_logs->slip;
            $orderno = $scrap_logs->order_no;
            $material_number = $scrap_logs->material_number;
            $cat = $scrap_logs->category;
            $remark = $scrap_logs->remark;
            $description = $scrap_logs->description;
            $from_loc = $scrap_logs->receive_location;
            $to_loc = $scrap_penarikan->ke_lokasi;
            $quantity = $scrap_logs->quantity;
            $uom = $scrap_logs->uom;
            $name = $name;
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
        $response = array(
            'status' => true,
            'message' => 'Slip Penarikan Di Print',
        );
        return Response::json($response);
    }

    public function cancelScrap(Request $request)
    {
        $auth_id = Auth::id();
        $scrap = ScrapList::where('id', '=', $request->get('id'))->first();
        $scrap_logs = ScrapLog::where('id', '=', $request->get('id'))->first();
        try {
            $scrap_log = new ScrapLog([
                'slip' => $scrap->slip,
                'order_no' => $scrap->order_no,
                'material_number' => $scrap->material_number,
                'material_description' => $scrap->material_description,
                'spt' => $scrap->spt,
                'valcl' => $scrap->valcl,
                'category' => $scrap->category,
                'issue_location' => $scrap->issue_location,
                'receive_location' => $scrap->receive_location,
                'quantity' => $scrap->quantity,
                'uom' => $scrap->uom,
                'reason' => $scrap->reason,
                'summary' => $scrap->summary,
                'scraped_by' => $scrap->created_by,
                'slip_created' => $scrap->created_at,
                'deleted_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'canceled_by' => Auth::id(),
                'remark' => 'canceled',
            ]);
            $scrap_log->save();
            $scrap->forceDelete();
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
        $response = array(
            'status' => true,
            'message' => 'Slip Scrap berhasil di cancel',
        );
        return Response::json($response);
    }

    public function deleteScrap(Request $request)
    {
        $auth_id = Auth::id();
        $scrap = ScrapList::where('id', '=', $request->get('id'))->first();
        try {
            $scrap_log = new ScrapLog([
                'slip' => $scrap->slip,
                'order_no' => $scrap->order_no,
                'material_number' => $scrap->material_number,
                'material_description' => $scrap->material_description,
                'spt' => $scrap->spt,
                'valcl' => $scrap->valcl,
                'category' => $scrap->category,
                'issue_location' => $scrap->issue_location,
                'receive_location' => $scrap->receive_location,
                'quantity' => $scrap->quantity,
                'uom' => $scrap->uom,
                'reason' => $scrap->reason,
                'summary' => $scrap->summary,
                'scraped_by' => $scrap->created_by,
                'slip_created' => $scrap->created_at,
                'created_at' => null,
                'deleted_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'canceled_by' => Auth::id(),
                'remark' => 'canceled',
            ]);
            $scrap_log->save();
            $scrap->forceDelete();
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
        $response = array(
            'status' => true,
            'message' => 'Slip Scrap berhasil di delete',
        );
        return Response::json($response);
    }

    public function fetchScrapDetail(Request $request)
    {
        $admin = Auth::user()->role_code;
        try {
            if (Auth::user()->role_code == "S-MIS" || Auth::user()->role_code == "SL-LOG" || Auth::user()->username == "pi9901011" || Auth::user()->role_code == "OP-QA" || Auth::user()->role_code == "S-PRD" || Auth::user()->role_code == "OP-LOG") {
                $date_from = $request->get('date_from');
                $date_to = $request->get('date_to');
                if ($date_from == "") {
                    if ($date_to == "") {
                        $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                        $last = "LAST_DAY(NOW())";
                    } else {
                        $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                        $last = "'" . $date_to . "'";
                    }
                } else {
                    if ($date_to == "") {
                        $first = "'" . $date_from . "'";
                        $last = "LAST_DAY(NOW())";
                    } else {
                        $first = "'" . $date_from . "'";
                        $last = "'" . $date_to . "'";
                    }
                }

                if (($date_to && $date_from) == null) {
                    $resumes = DB::SELECT("SELECT
                      id,
                      slip,
                      order_no,
                      material_description,
                      category,
                      issue_location,
                      receive_location,
                      quantity,
                      uom,
                      reason,
                      created_at,
                      remark
                      FROM
                      `scrap_logs`
                      WHERE
                      remark = 'received'
                      AND DATE( scrap_logs.created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )
                      ORDER BY
                      created_at DESC");
                } else {
                    $resumes = DB::SELECT("SELECT
                      id,
                      slip,
                      order_no,
                      material_description,
                      category,
                      issue_location,
                      receive_location,
                      quantity,
                      uom,
                      reason,
                      remark,
                      created_at
                      FROM
                      `scrap_logs`
                      WHERE
                      remark = 'received'
                      AND DATE( scrap_logs.created_at ) >= " . $first . "
                      AND DATE( scrap_logs.created_at ) <= " . $last . "
                      ORDER BY
                      created_at DESC");
                }
            }

            if (Auth::user()->role_code == "OP-QA" || Auth::user()->role_code == "S-QA" || Auth::user()->role_code == "L-QA") {
                $date_from = $request->get('date_from');
                $date_to = $request->get('date_to');
                if ($date_from == "") {
                    if ($date_to == "") {
                        $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                        $last = "LAST_DAY(NOW())";
                    } else {
                        $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                        $last = "'" . $date_to . "'";
                    }
                } else {
                    if ($date_to == "") {
                        $first = "'" . $date_from . "'";
                        $last = "LAST_DAY(NOW())";
                    } else {
                        $first = "'" . $date_from . "'";
                        $last = "'" . $date_to . "'";
                    }
                }

                if (($date_to && $date_from) == null) {
                    $resumes = DB::SELECT("SELECT
                      id,
                      slip,
                      order_no,
                      material_description,
                      category,
                      issue_location,
                      receive_location,
                      quantity,
                      uom,
                      reason,
                      created_at,
                      remark
                      FROM
                      `scrap_lists`
                      WHERE
                      remark = 'qa_check'
                      AND DATE( scrap_lists.created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )
                      ORDER BY
                      created_at DESC");
                } else {
                    $resumes = DB::SELECT("SELECT
                      id,
                      slip,
                      order_no,
                      material_description,
                      category,
                      issue_location,
                      receive_location,
                      quantity,
                      uom,
                      reason,
                      remark,
                      created_at
                      FROM
                      `scrap_lists`
                      WHERE
                      remark = 'qa_check'
                      AND DATE( scrap_lists.created_at ) >= " . $first . "
                      AND DATE( scrap_lists.created_at ) <= " . $last . "
                      ORDER BY
                      created_at DESC");
                }
            }

            $response = array(
                'status' => true,
                'resumes' => $resumes,
                'admin' => $admin,
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

    public function fetchScrapWarehouse(Request $request)
    {
        $dateto = $request->get('dateto');

        if ($dateto != "") {
            $resumes = db::select("
                SELECT
                *
                FROM
                scrap_lists
                WHERE
                scrap_lists.deleted_at IS NULL
                and scrap_lists.remark = '2'
                and DATE_FORMAT(created_at, '%Y-%m-%d') = '" . $dateto . "'
                ORDER BY created_at DESC
                ");
        } else {
            $resumes = db::select("
                SELECT
                *
                FROM
                scrap_lists
                WHERE
                scrap_lists.deleted_at IS NULL
                and scrap_lists.remark = '2'
                ORDER BY created_at DESC
                ");
        }
        $response = array(
            'status' => true,
            'resumes' => $resumes,
        );
        return Response::json($response);
    }

    public function scanScrapWarehouse(Request $request)
    {
        $auth_id = Auth::id();
        $scrap = ScrapList::where('slip', '=', $request->get('number'))->first();

        if (count($scrap) == 0) {
            $response = array(
                'status' => false,
                'message' => 'Slip Scrap Sudah Diterima Warehouse',
            );
            return Response::json($response);
        } else {
            if ($scrap->remark == 'received') {
                $response = array(
                    'status' => false,
                    'message' => 'Slip Scrap Sudah Diterima Warehouse',
                );
                return Response::json($response);
            } elseif ($scrap->remark == 'pending') {
                try {
                    $resumes_sap = db::select('select reference from sap_transactions where reference = "' . $scrap->order_no . '"');
                    if (count($resumes_sap) == 0) {
                        if ($scrap->category == 'FINISH') {
                            $mpdl = db::table('material_plant_data_lists')->where('material_number', '=', $scrap->material_number)->first();
                            if ($mpdl->storage_location == $scrap->issue_location) {
                                $tc = new TransactionCompletion([
                                    'serial_number' => $scrap->order_no,
                                    'material_number' => $scrap->material_number,
                                    'issue_plant' => '8190',
                                    'issue_location' => $scrap->issue_location,
                                    'quantity' => $scrap->quantity,
                                    'movement_type' => '101',
                                    'reference_number' => 'SCRAP',
                                    'created_by' => $scrap->created_by,
                                ]);
                                $tc->save();

                                // YMES COMPLETION NEW
                                $category = 'production_result_scrap';
                                $function = 'scanScrapWarehouse';
                                $action = 'production_result';
                                $result_date = date("Y-m-d H:i:s");
                                $slip_number = $scrap->order_no;
                                $serial_number = null;
                                $material_number = $scrap->material_number;
                                $material_description = $scrap->material_description;
                                $issue_location = $scrap->issue_location;
                                $mstation = 'W' . $mpdl->mrpc . 'S10';
                                $quantity = $scrap->quantity;
                                $remark = 'MIRAI';
                                $created_by = Auth::user()->username;
                                $created_by_name = Auth::user()->name;
                                $synced = null;
                                $synced_by = null;

                                app(YMESController::class)->production_result(
                                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                                // YMES END

                                //
                                // INSERT SAP TRX (MVT PR01)
                                if (($scrap->material_number != 'NO SAP') && ($scrap->issue_location != 'PNR9') && ($scrap->reason != 'B11') && ($scrap->reason != 'B13') && ($scrap->reason != 'B14')) {
                                    $insert_tabel = db::table('sap_transactions')->insert([
                                        'entry_date' => date("Y-m-d H:i:s"),
                                        'posting_date' => date("Y-m-d H:i:s"),
                                        'movement_type' => 'PR01',
                                        'material_number' => $scrap->material_number,
                                        'quantity' => $scrap->quantity,
                                        'storage_location' => $scrap->issue_location,
                                        'receive_location' => $scrap->receive_location,
                                        'reference' => $scrap->order_no,
                                        'remark' => $scrap->material_description,
                                        'created_by' => '11',
                                        'created_at' => date("Y-m-d H:i:s"),
                                        'updated_at' => date("Y-m-d H:i:s"),
                                    ]);
                                }
                                //
                            }
                        }

                        //
                        // INSERT SAP TRX (MVT XS03)
                        if (($scrap->material_number != 'NO SAP') && ($scrap->issue_location != 'PNR9') && ($scrap->reason != 'B11') && ($scrap->reason != 'B13') && ($scrap->reason != 'B14')) {
                            $insert_tabel = db::table('sap_transactions')->insert([
                                'entry_date' => date("Y-m-d H:i:s"),
                                'posting_date' => date("Y-m-d H:i:s"),
                                'movement_type' => 'XS03',
                                'material_number' => $scrap->material_number,
                                'quantity' => $scrap->quantity,
                                'storage_location' => $scrap->issue_location,
                                'receive_location' => $scrap->receive_location,
                                'reference' => $scrap->order_no,
                                'remark' => $scrap->material_description,
                                'created_by' => '11',
                                'created_at' => date("Y-m-d H:i:s"),
                                'updated_at' => date("Y-m-d H:i:s"),
                            ]);
                        }
                        //

                        $scrap_log = new ScrapLog([
                            'slip' => $scrap->slip,
                            'order_no' => $scrap->order_no,
                            'material_number' => $scrap->material_number,
                            'material_description' => $scrap->material_description,
                            'spt' => $scrap->spt,
                            'valcl' => $scrap->valcl,
                            'category' => $scrap->category,
                            'issue_location' => $scrap->issue_location,
                            'receive_location' => $scrap->receive_location,
                            'quantity' => $scrap->quantity,
                            'uom' => $scrap->uom,
                            'category_reason' => $scrap->category_reason,
                            'reason' => $scrap->reason,
                            'summary' => $scrap->summary,
                            'scraped_by' => $scrap->created_by,
                            'slip_created' => $scrap->created_at,
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s"),
                            'created_by' => Auth::id(),
                            'remark' => 'received',
                            'no_invoice' => $scrap->no_invoice,
                            'date_qa' => $scrap->created_by,
                            'name_qa' => $scrap->name_qa,
                        ]);
                        $scrap_log->save();
                        $scrap->forceDelete();

                        // BODY INOUT

                        $intransits = db::connection('ympimis_2')->table('kanban_inout_intransits')
                        ->where('tag', '=', $request->get('number'))
                        ->get();

                        if (count($intransits) > 0) {
                            foreach ($intransits as $intransit) {
                                db::connection('ympimis_2')->table('kanban_inout_logs')
                                ->insert([
                                    'tag' => $intransit->tag,
                                    'material_number' => $intransit->material_number,
                                    'material_description' => $intransit->material_description,
                                    'issue_location' => $intransit->issue_location,
                                    'receive_location' => $intransit->receive_location,
                                    'quantity' => $intransit->quantity,
                                    'remark' => 'WH-IN',
                                    'category' => 'SCRAP',
                                    'transaction_by' => Auth::user()->username,
                                    'transaction_by_name' => Auth::user()->name,
                                    'created_by' => Auth::user()->username,
                                    'created_by_name' => Auth::user()->name,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                            }

                            db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->where('tag', '=', $request->get('number'))
                            ->delete();
                        }

                        $response = array(
                            'status' => true,
                            'message' => 'Slip Scrap Berhasil Diterima Warehouse',
                        );
                        return Response::json($response);
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Slip Scrap Sudah Diterima Warehouse',
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
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Slip Scrap Tidak Ditemukan Di Sistem',
                );
                return Response::json($response);
            }
        }
    }

    public function CancelScrapUser(Request $request)
    {
        try {
            $auth_id = Auth::id();
            $scrap_logs = ScrapLog::where('id', $request->get('id'))->first();

            $update = ScrapLog::where('id', '=', $request->get('id'))->update([
                'remark' => 'canceled',
                'canceled_by' => Auth::id(),
                'deleted_at' => date("Y-m-d H:i:s"),
            ]);

            if ($scrap_logs->category == 'FINISH') {
                $mpdl = db::table('material_plant_data_lists')->where('material_number', '=', $scrap_logs->material_number)->first();
                if ($mpdl->storage_location == $scrap_logs->issue_location) {
                    $tc = new TransactionCompletion([
                        'serial_number' => $scrap_logs->order_no,
                        'material_number' => $scrap_logs->material_number,
                        'issue_plant' => '8190',
                        'issue_location' => $scrap_logs->issue_location,
                        'quantity' => $scrap_logs->quantity,
                        'movement_type' => '102',
                        'reference_number' => 'SCRAP',
                        'reference_file' => 'directly_executed_on_sap_because_there_was_a_transaction_error',
                        'created_by' => $scrap_logs->created_by,
                    ]);
                    $tc->save();

                    // YMES CANCEL COMPLETION NEW
                    $category = 'production_result_scrap';
                    $function = 'CancelScrapUser';
                    $action = 'production_result';
                    $result_date = date("Y-m-d H:i:s");
                    $slip_number = $scrap_logs->order_no;
                    $serial_number = null;
                    $material_number = $scrap_logs->material_number;
                    $material_description = $mpdl->material_description;
                    $issue_location = $scrap_logs->issue_location;
                    $mstation = 'W' . $mpdl->mrpc . 'S10';
                    $quantity = $scrap_logs->quantity * -1;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->production_result(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END

                    //
                    // INSERT SAP TRX (MVT PR02)
                    if (($scrap_logs->material_number != 'NO SAP') && ($scrap_logs->issue_location != 'PNR9') && ($scrap_logs->reason != 'B11') && ($scrap_logs->reason != 'B13') && ($scrap_logs->reason != 'B14')) {
                        $insert_tabel = db::table('sap_transactions')->insert([
                            'entry_date' => date("Y-m-d H:i:s"),
                            'posting_date' => date("Y-m-d H:i:s"),
                            'movement_type' => 'PR02',
                            'material_number' => $scrap_logs->material_number,
                            'quantity' => $scrap_logs->quantity,
                            'storage_location' => $scrap_logs->issue_location,
                            'receive_location' => $scrap_logs->receive_location,
                            'reference' => $scrap_logs->order_no,
                            'remark' => $scrap_logs->material_description,
                            'created_by' => '11',
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    }
                    //
                }
            }

            //
            // INSERT SAP TRX (MVT XS04)
            if (($scrap_logs->material_number != 'NO SAP') && ($scrap_logs->issue_location != 'PNR9') && ($scrap_logs->reason != 'B11') && ($scrap_logs->reason != 'B13') && ($scrap_logs->reason != 'B14')) {
                $insert_tabel = db::table('sap_transactions')->insert([
                    'entry_date' => date("Y-m-d H:i:s"),
                    'posting_date' => date("Y-m-d H:i:s"),
                    'movement_type' => 'XS04',
                    'material_number' => $scrap_logs->material_number,
                    'quantity' => $scrap_logs->quantity,
                    'storage_location' => $scrap_logs->issue_location,
                    'receive_location' => $scrap_logs->receive_location,
                    'reference' => $scrap_logs->order_no,
                    'remark' => $scrap_logs->material_description,
                    'created_by' => '11',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);
            }
            //

            $response = array(
                'status' => true,
                'message' => 'Slip Cancel',
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

    public function InputInvoiceQA(Request $request)
    {
        $scrap = ScrapList::where('slip', '=', $request->get('slip'))->first();

        try {
            $scrap->invoice_qa = $request->get('invoice');
            $scrap->save();

            $response = array(
                'status' => true,
                'message' => 'Input Invoice Berhasil',
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

    public function fetchScrap(Request $request)
    {
        $id = substr($request->get('id'), 2);
        $return = ScrapList::where('scrap_lists.id', '=', $id)
        ->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
        ->select('scrap_lists.id', 'scrap_lists.material_number', 'scrap_lists.material_description', 'scrap_lists.issue_location', 'scrap_lists.receive_location', 'scrap_lists.quantity', 'users.name', 'scrap_lists.created_at', 'scrap_lists.created_by')
        ->first();

        if ($scrap == null) {
            $response = array(
                'status' => false,
                'message' => "QRcode scrap tidak ditemukan.",
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'scrap' => $scrap,
        );
        return Response::json($response);
    }

    public function fetchScrapList(Request $request)
    {

        $pesan = "";

        $list_all = db::table('scrap_materials')
        ->whereNull('deleted_at')
        ->select('material_number', 'material_description as description', 'issue_location', 'spt', 'valcl', 'uom', 'remark')
        ->where('issue_location', $request->get('loc'))
        ->orderBy('material_number', 'ASC')
        ->get();

        $pesan = 'Lokasi Berhasil Dipilih';

        if (count($list_all) == 0) {
            $response = array(
                'status' => false,
                'message' => 'Lokasi terpilih tidak memiliki list material',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'lists' => $list_all,
            'message' => $pesan,
        );
        return Response::json($response);
    }

    public function fetchScrapResume(Request $request)
    {
        $admin = Auth::user()->role_code;

        $qa = Auth::user()->username;

        $resumes = ScrapList::where('issue_location', '=', $request->get('loc'))
        ->where('remark', '=', 'pending')
        ->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
        ->select('scrap_lists.id', 'scrap_lists.slip', 'scrap_lists.material_number', 'scrap_lists.material_description', 'scrap_lists.receive_location', 'scrap_lists.category', 'scrap_lists.quantity', 'scrap_lists.remark', 'users.name', DB::RAW('DATE_FORMAT(scrap_lists.created_at,"%d-%m-%Y %H:%i:%s") as tanggal'), 'scrap_lists.created_by', 'scrap_lists.order_no')
        ->orderBy('scrap_lists.created_at', 'desc')
        ->get();

        $response = array(
            'status' => true,
            'resumes' => $resumes,
            'admin' => $admin,
            'qa' => $qa,
        );
        return Response::json($response);
    }

    public function ResumeListWip(Request $request)
    {
        try {
            $loc = $request->get('loc');
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                    $last = "LAST_DAY(NOW())";
                } else {
                    $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                    $last = "'" . $date_to . "'";
                }
            } else {
                if ($date_to == "") {
                    $first = "'" . $date_from . "'";
                    $last = "LAST_DAY(NOW())";
                } else {
                    $first = "'" . $date_from . "'";
                    $last = "'" . $date_to . "'";
                }
            }

            if (($date_to && $date_from) == null) {
                $resumes = DB::SELECT("SELECT
                 slip,
                 material_description,
                 DATE( scrap_lists.created_at ) AS tanggal,
                 `name`
                 FROM
                 `scrap_lists`
                 LEFT JOIN users
                 ON scrap_lists.created_by = users.id
                 WHERE
                 issue_location = '" . $loc . "'
                 AND DATE( scrap_lists.created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )");

                $resumes1 = DB::SELECT("SELECT
                 slip,
                 material_description,
                 DATE( scrap_logs.created_at ) AS tanggal,
                 `name`,
                 remark
                 FROM
                 `scrap_logs`
                 LEFT JOIN users
                 ON scrap_logs.created_by = users.id
                 WHERE
                 issue_location = '" . $loc . "'
                 AND remark = 'received'
                 AND DATE( scrap_logs.created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )");
            } else {
                $resumes = DB::SELECT("SELECT
                 slip,
                 material_description,
                 DATE( scrap_lists.created_at ) AS tanggal,
                 `name`
                 FROM
                 `scrap_lists`
                 LEFT JOIN users
                 ON scrap_lists.created_by = users.id
                 WHERE
                 issue_location = '" . $loc . "'
                 AND DATE( scrap_lists.created_at ) >= " . $first . "
                 AND DATE( scrap_lists.created_at ) <= " . $last . "");

                $resumes1 = DB::SELECT("SELECT
                 slip,
                 material_description,
                 DATE( scrap_logs.created_at ) AS tanggal,
                 `name`,
                 remark
                 FROM
                 `scrap_logs`
                 LEFT JOIN users
                 ON scrap_logs.created_by = users.id
                 WHERE
                 issue_location = '" . $loc . "'
                 AND remark = 'received'
                 AND DATE( scrap_logs.created_at ) >= " . $first . "
                 AND DATE( scrap_logs.created_at ) <= " . $last . "");
            }

            $response = array(
                'status' => true,
                'resumes' => $resumes,
                'resumes1' => $resumes1,
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

    public function ResumeListMonth(Request $request)
    {
        try {
            $loc = $request->get('loc');
            $bulan = $request->get('bulan');

            $bulan_now = DATE('Y-m');

            if ($bulan != "") {
                $resumes = DB::SELECT("SELECT
                 sl.issue_location,
                 sl.material_number,
                 sl.material_description,
                 DATE( sl.created_at ) AS tanggal,
                 standard_price/1000 AS harga,
                 SUM(quantity) AS jumlah,
                 SUM(quantity)*(standard_price/1000) AS total
                 FROM
                 scrap_logs AS sl
                 LEFT JOIN material_plant_data_lists AS harga ON harga.material_number = sl.material_number
                 WHERE
                 issue_location = '" . $loc . "'
                 AND DATE_FORMAT( sl.created_at, '%Y-%m' ) = '" . $bulan . "'
                 AND remark = 'received'
                 GROUP BY
                 issue_location, sl.material_number, sl.material_description, standard_price ORDER BY sl.created_at ASC
                 ");
            } else {
                $resumes = DB::SELECT("SELECT
                 sl.issue_location,
                 sl.material_number,
                 sl.material_description,
                 DATE( sl.created_at ) AS tanggal,
                 standard_price/1000 AS harga,
                 SUM(quantity) AS jumlah,
                 SUM(quantity)*(standard_price/1000) AS total
                 FROM
                 scrap_logs AS sl
                 LEFT JOIN material_plant_data_lists AS harga ON harga.material_number = sl.material_number
                 WHERE
                 issue_location = '" . $loc . "'
                 AND DATE_FORMAT( sl.created_at, '%Y-%m' ) = '" . $bulan_now . "'
                 AND remark = 'received'
                 GROUP BY
                 issue_location, sl.material_number, sl.material_description, standard_price
                 ORDER BY sl.created_at ASC
                 ");
            }

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

    public function ResumeListWh(Request $request)
    {
        $today = date('Y-m-d');

        $resumes = ScrapLog::where('issue_location', '=', $request->get('loc'))
        ->where('remark', '=', 'received')
        ->where(DB::raw("DATE_FORMAT(scrap_logs.created_at, '%Y-%m-%d')"), $today)
        ->leftJoin('users', 'users.id', '=', 'scrap_logs.created_by')
        ->select('scrap_logs.id', 'scrap_logs.slip', 'scrap_logs.material_description', 'scrap_logs.issue_location', 'scrap_logs.receive_location', 'scrap_logs.category', 'scrap_logs.quantity', 'scrap_logs.remark', 'users.name', DB::RAW('DATE_FORMAT(scrap_logs.created_at,"%d-%m-%Y") as tanggal'), 'scrap_logs.created_by')
        ->orderBy('scrap_logs.created_at', 'desc')
        ->get();

        $response = array(
            'status' => true,
            'resumes' => $resumes,
        );
        return Response::json($response);
    }

    public function fetchScrapListAssy(Request $request)
    {

        if ($request->get('cat') == 'ASSY') {
            $lists = db::table('scrap_materials')
            ->whereNull('deleted_at')
            ->select('material_number', 'material_description as description', 'issue_location')
            ->where('issue_location', $request->get('loc'))
            ->where('spt', '=', '50')
            ->orderBy('material_number', 'ASC')
            ->get();
        } elseif ($request->get('cat') == 'SINGLE') {
            $lists = db::table('scrap_materials')
            ->whereNull('deleted_at')
            ->select('material_number', 'material_description as description', 'issue_location')
            ->where('issue_location', $request->get('loc'))
            ->where('spt', '!=', '50')
            ->orderBy('material_number', 'ASC')
            ->get();
        }

        if (count($lists) == 0) {
            $response = array(
                'status' => false,
                'message' => 'Lokasi terpilih tidak memiliki list material',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'lists' => $lists,
            'message' => 'Lokasi berhasil dipilih',
        );
        return Response::json($response);
    }

    public function printReceive($noslip, $orderno, $remark, $material_number, $description, $quantity, $uom, $spt, $from_loc, $to_loc, $invoice, $name, $cat)
    {
        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)->first();

        if (Auth::user()->role_code == 'S-MIS' || Auth::user()->role_code == 'S') {
            $printer_name = 'MIS';
        } else {
            if ($from_loc == 'CL91') {
                $printer_name = 'FLO Printer 102';
            } elseif ($from_loc == 'SX91') {
                $printer_name = 'FLO Printer 103';
            } elseif ($from_loc == 'FL91') {
                $printer_name = 'KDO FL';
            } elseif ($from_loc == 'SX51' || $from_loc == 'CL51' || $from_loc == 'FL51' || $from_loc == 'VN51') {
                $printer_name = 'Stockroom-Printer';
            } elseif ($from_loc == 'SX21' || $from_loc == 'CL21' || $from_loc == 'FL21' || $from_loc == 'VN21') {
                if (str_contains($description, 'neck') || str_contains($description, 'bell') || str_contains($description, 'body') || str_contains($description, 'bow') || str_contains($description, 'post') || str_contains($description, 'foot')) {
                    $printer_name = 'HTS';
                } else {
                    $printer_name = 'Welding-Printer';
                }
            } elseif ($from_loc == 'VN91' || $from_loc == 'VNA0' || $from_loc == 'VN11' || $from_loc == 'RC11') {
                $printer_name = 'Injection';
            } elseif ($from_loc == 'PXMP') {
                $printer_name = 'KDO MP';
            } elseif ($from_loc == 'RC91') {
                $printer_name = 'FLO Printer RC';
            } elseif ($from_loc == 'CLA0' || $from_loc == 'SXA0' || $from_loc == 'FLA0' || $from_loc == 'ZPA0' || $from_loc == 'SXA2' || $from_loc == 'FLA2' || $from_loc == 'CLA2' || $from_loc == 'SA0R' || $from_loc == 'FA0R' || $from_loc == 'LA0R') {
                $printer_name = 'KDO ZPRO';
            } elseif ($from_loc == 'SXA1' || $from_loc == 'FLA1' || $from_loc == 'FA1R' || $from_loc == 'SA1R') {
                $printer_name = 'Body Process Printer';
            } elseif ($from_loc == 'CS91') {
                $printer_name = 'KDO CASE';
            } elseif ($from_loc == 'PN91') {
                $printer_name = 'FLO Printer 105';
            } elseif (($from_loc == 'MSTK' || $from_loc == 'WPCS' || $from_loc == 'WPPN') && $emp_dept->department == 'Standardization Department') {
                $printer_name = 'IQA Printer';
            } elseif ($from_loc == 'MSTK' || $from_loc == 'OTHR' || $from_loc == 'WPPN' || $from_loc == 'WPCS' || $from_loc == 'WPRC' || $from_loc == '214' || $from_loc == 'MMJR') {
                $printer_name = 'FLO Printer LOG';
            } elseif ($from_loc == 'PNR9') {
                $printer_name = 'Printer-RPL';
            } elseif ($from_loc == 'SXT9' || $from_loc == 'FLT9' || $from_loc == 'CLB9') {
                $printer_name = 'Tanpo';
            } else {
                $printer_name = 'MIS';
            }
        }

        if ($material_number == 'NO SAP') {
            if ($from_loc == 'CLA0' || $from_loc == 'SXA0' || $from_loc == 'FLA0' || $from_loc == 'ZPA0' || $from_loc == 'SXA2' || $from_loc == 'FLA2' || $from_loc == 'CLA2' || $from_loc == 'SA0R' || $from_loc == 'FA0R' || $from_loc == 'LA0R') {
                $printer_name = 'KDO ZPRO';
            } else {
                $printer_name = 'FLO Printer LOG';
            }
        }

        if ($cat == 'received') {
            $printer_name = 'FLO Printer LOG';
        }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text("SCRAP SLIP" . "\n");
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($orderno . "\n");
        $printer->feed(1);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($material_number . ' - ' . $remark . "\n");
        $printer->setTextSize(1, 1);
        $printer->text($description . "\n");
        $printer->setTextSize(2, 2);
        $printer->text($from_loc . " -> " . $to_loc . "\n");
        $printer->text($quantity . " " . $uom . "\n");
        if ($emp_dept->employee_id == 'PI9910004' || $emp_dept->employee_id == 'PI0904005') {
            $printer->setTextSize(2, 2);
            $printer->text("No Invoice : " . $invoice . "\n");
        }
        $printer->feed(2);
        $printer->setTextSize(1, 1);
        $printer->qrCode($noslip, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
        $printer->text($noslip . "\n");
        $printer->feed(1);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("------------------------------------");
        $printer->feed(1);
        $printer->text("|Delivered By:    |Confirmed By:   |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("------------------------------------");
        $printer->feed(2);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->textRaw("(" . date("d-M-Y H:i:s") . ")\n");
        $printer->text($name . "\n");
        $printer->feed(1);
        if ($cat == 'received') {
            $printer->setTextSize(1, 1);
            $printer->text('(Reprint)' . "\n");
            $printer->text($emp_dept->name . "\n");
        }
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    public function printSAP($noslip, $orderno, $remark, $material_number, $description, $quantity, $reason, $uom, $spt, $from_loc, $to_loc, $invoice, $name, $cat)
    {
        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)->first();
        $code = db::select("SELECT DISTINCT
           reason_code
           FROM
           scrap_reasons
           LEFT JOIN scrap_lists ON scrap_lists.reason = scrap_reasons.reason
           WHERE
           scrap_reasons.remark = '" . $to_loc . "'
           AND scrap_reasons.reason = '" . $reason . "'");

        if (Auth::user()->role_code == 'S-MIS' || Auth::user()->role_code == 'S') {
            $printer_name = 'MIS';
        } else {
            if ($from_loc == 'CL91') {
                $printer_name = 'FLO Printer 102';
            } elseif ($from_loc == 'SX91') {
                $printer_name = 'FLO Printer 103';
            } elseif ($from_loc == 'FL91') {
                $printer_name = 'KDO FL';
            } elseif ($from_loc == 'SX51' || $from_loc == 'CL51' || $from_loc == 'FL51' || $from_loc == 'VN51') {
                $printer_name = 'Stockroom-Printer';
            } elseif ($from_loc == 'SX21' || $from_loc == 'CL21' || $from_loc == 'FL21' || $from_loc == 'VN21') {
                if (str_contains($description, 'neck') || str_contains($description, 'bell') || str_contains($description, 'body') || str_contains($description, 'bow') || str_contains($description, 'post') || str_contains($description, 'foot')) {
                    $printer_name = 'HTS';
                } else {
                    $printer_name = 'Welding-Printer';
                }
            } elseif ($from_loc == 'VN91' || $from_loc == 'VNA0' || $from_loc == 'VN11' || $from_loc == 'RC11') {
                $printer_name = 'Injection';
            } elseif ($from_loc == 'PXMP') {
                $printer_name = 'KDO MP';
            } elseif ($from_loc == 'RC91') {
                $printer_name = 'FLO Printer RC';
            } elseif ($from_loc == 'CLA0' || $from_loc == 'SXA0' || $from_loc == 'FLA0' || $from_loc == 'ZPA0' || $from_loc == 'SXA2' || $from_loc == 'FLA2' || $from_loc == 'CLA2' || $from_loc == 'SA0R' || $from_loc == 'FA0R' || $from_loc == 'LA0R') {
                $printer_name = 'KDO ZPRO';
            } elseif ($from_loc == 'SXA1' || $from_loc == 'FLA1' || $from_loc == 'FA1R' || $from_loc == 'SA1R') {
                $printer_name = 'Body Process Printer';
            } elseif ($from_loc == 'CS91') {
                $printer_name = 'KDO CASE';
            } elseif ($from_loc == 'PN91') {
                $printer_name = 'FLO Printer 105';
            } elseif (($from_loc == 'MSTK' || $from_loc == 'WPCS' || $from_loc == 'WPPN') && $emp_dept->department == 'Standardization Department') {
                $printer_name = 'IQA Printer';
            } elseif ($from_loc == 'MSTK' || $from_loc == 'OTHR' || $from_loc == 'WPPN' || $from_loc == 'WPCS' || $from_loc == 'WPRC' || $from_loc == '214' || $from_loc == 'MMJR') {
                $printer_name = 'FLO Printer LOG';
            } elseif ($from_loc == 'PNR9') {
                $printer_name = 'Printer-RPL';
            } elseif ($from_loc == 'SXT9' || $from_loc == 'FLT9' || $from_loc == 'CLB9') {
                $printer_name = 'Tanpo';
            } else {
                $printer_name = 'MIS';
            }
        }

        if ($material_number == 'NO SAP') {
            if ($from_loc == 'CLA0' || $from_loc == 'SXA0' || $from_loc == 'FLA0' || $from_loc == 'ZPA0' || $from_loc == 'SXA2' || $from_loc == 'FLA2' || $from_loc == 'CLA2' || $from_loc == 'SA0R' || $from_loc == 'FA0R' || $from_loc == 'LA0R') {
                $printer_name = 'KDO ZPRO';
            } else {
                $printer_name = 'FLO Printer LOG';
            }
        }

        if ($cat == 'received') {
            $printer_name = 'FLO Printer LOG';
        }

        $qty = $quantity;

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text("SCRAP SLIP (SAP)" . "\n");
        $printer->feed(1);
        $printer->initialize();
        $printer->setEmphasis(true);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(2, 2);
        $printer->text("ValCl 9030" . "\n");
        $printer->text($material_number . ' - ' . $remark . "\n");

        $printer->setTextSize(1, 1);
        $printer->text($description . "\n");
        $printer->setTextSize(2, 2);
        $printer->text($from_loc . " -> " . $to_loc . "\n");
        $printer->text($quantity . " " . $uom . "\n");
        if ($emp_dept->employee_id == 'PI9910004' || $emp_dept->employee_id == 'PI0904005') {
            $printer->setTextSize(2, 2);
            $printer->text("No Invoice : " . $invoice . "\n");
        }
        $printer->feed(1);

        $printer->setTextSize(1, 1);
        $printer->text("From: " . $from_loc . "\n");
        $printer->qrCode($from_loc, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
        $printer->feed(1);

        $printer->setTextSize(1, 1);
        $printer->text("To: " . $to_loc . "\n");
        $printer->qrCode($to_loc, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
        $printer->feed(1);

        $printer->setTextSize(1, 1);
        if ($reason == 'Material Kembali Ke WIP') {
            $code = 'RTN';
            $printer->text("Reason Code: " . $code . "\n");
            $printer->qrCode($code, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
            $printer->feed(1);
        } else {
            $printer->text("Reason Code: " . $code[0]->reason_code . "\n");
            $printer->qrCode($code[0]->reason_code, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
            $printer->feed(1);
        }

        $printer->setTextSize(1, 1);
        $printer->text("Reference Number: " . $orderno . "\n");
        $printer->qrCode($orderno, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
        $printer->feed(1);

        $printer->setTextSize(1, 1);
        $printer->text("GMC: " . $material_number . "\n");
        $printer->qrCode($material_number, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
        $printer->feed(1);

        $printer->setTextSize(1, 1);
        $printer->text("Qty: " . $quantity . "\n");
        $printer->qrCode($qty, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
        $printer->feed(1);

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("------------------------------------");
        $printer->feed(1);
        $printer->text("|Delivered By:    |Confirmed By:   |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("------------------------------------");
        $printer->feed(2);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->textRaw("(" . date("d-M-Y H:i:s") . ")\n");
        $printer->text($name . "\n");
        $printer->feed(1);
        if ($cat == 'received') {
            $printer->setTextSize(1, 1);
            $printer->text('(Reprint)' . "\n");
            $printer->text($emp_dept->name . "\n");
        }
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    public function printScanWh($category, $material, $description, $quantity, $issue, $slip, $receive_location, $name, $printer_name, $invoice)
    {
        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)->first();

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text("SCRAP SLIP" . "\n");
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("No Slip : " . $slip . "-SC\n");
        $printer->feed(1);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($category . "\n");
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($material . "\n");
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($description . "\n");
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($issue . " -> " . $receive_location . "\n");
        if ($emp_dept->employee_id == 'PI9910004' || $emp_dept->employee_id == 'PI0904005') {
            $printer->setTextSize(2, 2);
            $printer->text("No Invoice : " . $invoice . "\n");
        }
        $printer->text($quantity . " PC(s)" . "\n");
        $printer->feed(1);
        $printer->setEmphasis(true);
        $printer->feed(1);
        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1);
        $printer->qrCode($slip, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
        $printer->feed(1);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("------------------------------------");
        $printer->feed(1);
        $printer->text("|Delivered By:    |Confirmed By:   |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("------------------------------------");
        $printer->feed(2);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->textRaw("(" . date("d-M-Y H:i:s") . ")\n");
        $printer->text($name . "\n");
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    public function reprintScrap(Request $request)
    {
        try {
            if ($request->get('cat') == 'pending') {
                $scrap = ScrapList::where('scrap_lists.id', '=', $request->get('id'))
                ->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
                ->first();
            } else {
                $scrap = ScrapLog::where('scrap_logs.id', '=', $request->get('id'))
                ->leftJoin('users', 'users.id', '=', 'scrap_logs.scraped_by')
                ->first();
            }

            self::printReceive($scrap->slip, $scrap->order_no, $scrap->category, $scrap->material_number, $scrap->material_description, $scrap->quantity, $scrap->uom, $scrap->spt, $scrap->issue_location, $scrap->receive_location, $scrap->no_invoice, $scrap->name, $request->get('cat'));
            self::printSAP($scrap->slip, $scrap->order_no, $scrap->category, $scrap->material_number, $scrap->material_description, $scrap->quantity, $scrap->reason, $scrap->uom, $scrap->spt, $scrap->issue_location, $scrap->receive_location, $scrap->no_invoice, $scrap->name, $request->get('cat'));
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
        $response = array(
            'status' => true,
            'message' => 'Cetak ulang slip scrap berhasil',
        );
        return Response::json($response);
    }

    public function printScrap(Request $request)
    {
        $id = Auth::id();

        // $prefix_now     = date("y");
        // $code_generator = CodeGenerator::where('note', '=', 'scrap')->first();
        // if ($prefix_now != $code_generator->prefix) {
        //     $code_generator->prefix = $prefix_now;
        //     $code_generator->index  = '0';
        //     $code_generator->save();
        // }
        // $numbers               = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
        // $slip                  = $code_generator->prefix . $numbers;
        // $code_generator->index = $code_generator->index + 1;
        // $code_generator->save();

        // DB::beginTransaction();
        try {
            $defect = $request->get('sum');
            $summary = implode("/",$defect);

            $order_no = "";

            $spt = '50';
            if ($request->get('spt') == '50') {
                //PHANTOM
                $bom = db::table('bom_scraps')
                ->where('material_parent', $request->get('material'))
                ->get();

                if (count($bom) <= 0) {
                    $response = array(
                        'status' => false,
                        'message' => "Material non phanton dan bom tidak ditemukan",
                    );
                    return Response::json($response);
                }

                for ($i = 0; $i < count($bom); $i++) {
                    $prefix_now = date("y");
                    $code_generator = CodeGenerator::where('note', '=', 'scrap')->first();
                    if ($prefix_now != $code_generator->prefix) {
                        $code_generator->prefix = $prefix_now;
                        $code_generator->index = '0';
                        $code_generator->save();
                    }

                    $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                    $slip = $code_generator->prefix . $numbers;
                    $code_generator->index = $code_generator->index + 1;
                    $code_generator->save();

                    $order_no = "G" . $slip . "/" . $request->get('reason');

                    $quantity = round($bom[$i]->usage * $request->get('quantity'), 3);

                    $scrap = new ScrapList([
                        'slip' => $slip,
                        'order_no' => $order_no,
                        'material_number' => $bom[$i]->material_child,
                        'material_description' => $bom[$i]->child_description,
                        'issue_location' => $request->get('issue'),
                        'category' => $request->get('category'),
                        'receive_location' => $request->get('receive_location'),
                        'reason' => $request->get('reason'),
                        // 'summary' => $request->get('summary'),
                        'summary' => $summary,
                        'quantity' => $quantity,
                        'uom' => $bom[$i]->child_uom,
                        'spt' => null,
                        'valcl' => $bom[$i]->child_valcl,
                        'remark' => 'pending',
                        'category_reason' => $request->get('category_reason'),
                        'no_invoice' => $request->get('invoice'),
                        'created_by' => $id,
                    ]);
                    $scrap->save();

                    self::printReceive($slip, $order_no, $request->get('category'), $bom[$i]->material_child, $bom[$i]->child_description, $quantity, $bom[$i]->child_uom, $spt, $request->get('issue'), $request->get('receive_location'), $request->get('invoice'), Auth::user()->name, 'pending');
                    self::printSAP($slip, $order_no, $request->get('category'), $bom[$i]->material_child, $bom[$i]->child_description, $quantity, $request->get('reason'), $bom[$i]->child_uom, $spt, $request->get('issue'), $request->get('receive_location'), $request->get('invoice'), Auth::user()->name, 'pending');
                }
            } else {
                //NON PHANTOM
                $prefix_now = date("y");
                $code_generator = CodeGenerator::where('note', '=', 'scrap')->first();
                if ($prefix_now != $code_generator->prefix) {
                    $code_generator->prefix = $prefix_now;
                    $code_generator->index = '0';
                    $code_generator->save();
                }

                $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                $slip = $code_generator->prefix . $numbers;
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();

                $order_no = '';
                if ($request->get('reason') == 'Material Kembali Ke WIP') {
                    $order_no = 'RTN' . $slip;
                } else {
                    $order_no = $slip . "/" . $request->get('reason');
                }
                $spt = null;

                $scrap = new ScrapList([
                    'slip' => $slip,
                    'order_no' => $order_no,
                    'material_number' => $request->get('material'),
                    'material_description' => $request->get('description'),
                    'issue_location' => $request->get('issue'),
                    'category' => $request->get('category'),
                    'receive_location' => $request->get('receive_location'),
                    'reason' => $request->get('reason'),
                    // 'summary' => $request->get('summary'),
                    'summary' => $summary,
                    'quantity' => $request->get('quantity'),
                    'uom' => $request->get('uom'),
                    'spt' => $spt,
                    'valcl' => $request->get('valcl'),
                    'remark' => 'pending',
                    'category_reason' => $request->get('category_reason'),
                    'no_invoice' => $request->get('invoice'),
                    'created_by' => $id,
                ]);
                $scrap->save();

                self::printReceive($slip, $order_no, $request->get('category'), $request->get('material'), $request->get('description'), $request->get('quantity'), $request->get('uom'), $spt, $request->get('issue'), $request->get('receive_location'), $request->get('invoice'), Auth::user()->name, 'pending');
                self::printSAP($slip, $order_no, $request->get('category'), $request->get('material'), $request->get('description'), $request->get('quantity'), $request->get('reason'), $request->get('uom'), $spt, $request->get('issue'), $request->get('receive_location'), $request->get('invoice'), Auth::user()->name, 'pending');

            }

            // DB::commit();
            $response = array(
                'status' => true,
                'message' => "Slip SCRAP berhasil di cetak.",
            );
            return Response::json($response);

        } catch (\Exception$e) {
            // DB::rollback();

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function importCompletion(Request $request)
    {
        if ($request->hasFile('completion')) {
            try {
                $file = $request->file('completion');
                $file_name = 'import_cs_' . Auth::id() . '(' . date("y-m-d") . ')' . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('import/completion/'), $file_name);

                $excel = public_path('import/completion/') . $file_name;
                $rows = Excel::load($excel, function ($reader) {
                    $reader->noHeading();
                    $reader->skipRows(1);
                })->get();
                $rows = $rows->toArray();

                $month = $request->get('date_completion');
                $cc = $request->get('cc');
                $cost_center_name = explode(",", $cc);

                $existing = SapCompletion::leftJoin('storage_locations', 'storage_locations.storage_location', '=', 'sap_completions.storage_location')
                ->where(db::raw('DATE_FORMAT(sap_completions.posting_date, "%Y-%m")'), $month)
                ->whereIn('storage_locations.cost_center_name', $cost_center_name)
                ->delete();

                for ($i = 0; $i < count($rows); $i++) {
                    $entry_date = $rows[$i][0]->format('Y-m-d');
                    $posting_date = $rows[$i][3]->format('Y-m-d');
                    $movement_type = $rows[$i][4];
                    $material_number = $rows[$i][5];
                    $quantity = $rows[$i][8];
                    $storage_location = $rows[$i][12];
                    $reference = $rows[$i][15];

                    $log = new SapCompletion([
                        'entry_date' => $entry_date,
                        'posting_date' => $posting_date,
                        'movement_type' => $movement_type,
                        'material_number' => $material_number,
                        'quantity' => $quantity,
                        'storage_location' => $storage_location,
                        'reference' => $reference,
                        'created_by' => Auth::id(),
                    ]);
                    $log->save();

                }
                $response = array(
                    'status' => true,
                    'message' => 'Upload file success',
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
                'message' => 'Upload failed, File not found',
            );
            return Response::json($response);
        }
    }

    public function updateScrap(Request $request)
    {
        $auth_id = Auth::id();
        $assy = ScrapList::where('id', '=', $request->get('id'))->first();
        try {
            $scrap_log = new ScrapLog([
                'scrap_id' => $assy->id,
                'material_number' => $assy->material_number,
                'material_description' => $assy->material_description,
                'issue_location' => $assy->issue_location,
                'receive_location' => $assy->receive_location,
                'quantity' => $assy->quantity,
                'category' => $assy->category,
                'category_reason' => $assy->category_reason,
                'remark' => '1',
                'reason' => $assy->reason,
                'summary' => $assy->summary,
                'printed_by' => $assy->created_by,
                'printed_at' => $assy->created_at,
            ]);

            $scrap_log->save();

            $assy = ScrapList::find($request->get('id'));
            $assy->remark = '1';
            $assy->save();

            $scrap = ScrapList::where('scrap_lists.id', '=', $request->get('id'))
            ->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
            ->first();

            $response = array(
                'status' => true,
                'message' => 'Slip scrap berhasil update',
            );

            return Response::json($response);

        } catch (QueryException $e) {
            return back()->with('error', 'Error')->with('page', 'Category Error');
        }
    }

    public function indexScrapResume(Request $request)
    {

        $lists = db::table('scrap_materials')
        ->whereNull('deleted_at')
        ->select('material_number', 'material_description as description', 'issue_location')
        ->where('issue_location', $request->get('loc'))
        ->orderBy('material_number', 'DESC')
        ->get();

        $today = date('Y-m-d');

        $resumes = ScrapList::where('issue_location', '=', $request->get('loc'))
        ->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
        ->select('scrap_lists.id', 'scrap_lists.material_number', 'scrap_lists.slip', 'scrap_lists.material_description', 'scrap_lists.issue_location', 'scrap_lists.category', 'scrap_lists.reason', 'scrap_lists.quantity', 'scrap_lists.remark', 'users.name', DB::RAW('DATE_FORMAT(scrap_lists.created_at,"%d-%m-%Y") as date'), 'scrap_lists.created_by')
        ->orderBy('scrap_lists.created_at', 'desc')
        ->get();

        $response = array(
            'status' => true,
            'lists' => $lists,
            'resumes' => $resumes,
            'message' => 'Lokasi berhasil dipilih',
        );
        return Response::json($response);
    }

    public function fetchMonitoringScrap(Request $request)
    {
        $today = date("Y-m-d");
        $tahun = date('Y');
        $dateto = $request->get('dateto');
        $location = $request->get('loc');
        if ($dateto != "") {
            $data = db::select("
                SELECT
                a.issue_location,
                SUM( a.ListScrap ) as LScrap,
                SUM( a.Received ) as RScrap
                FROM
                (
                 SELECT
                 a.issue_location,
                 SUM( a.ListScrap ) as LScrap,
                 SUM( a.Received ) as RScrap
                 FROM
                 (
                  SELECT
                  issue_location,
                  sum( CASE WHEN `remark` = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
                  0 AS Received
                  FROM
                  scrap_lists
                  WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '" . $dateto . "'
                  GROUP BY
                  issue_location UNION ALL
                  SELECT
                  issue_location,
                  0 AS ListScrap,
                  sum( CASE WHEN `remark` = 'received' THEN 1 ELSE 0 END ) AS Received
                  FROM
                  scrap_logs
                  WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '" . $dateto . "'
                  GROUP BY
                  issue_location
                  ) a
                  GROUP BY
                  a.issue_location");
             } else {
                $data = db::select("
                  SELECT
                  a.issue_location,
                  SUM( a.ListScrap ) as LScrap,
                  SUM( a.Received ) as RScrap
                  FROM
                  (
                   SELECT
                   issue_location,
                   sum( CASE WHEN `remark` = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
                   0 AS Received
                   FROM
                   scrap_lists
                   where issue_location = '" . $location . "'
                   and DATE_FORMAT(created_at, '%Y-%m-%d') = '" . $today . "'
                   GROUP BY
                   issue_location UNION ALL
                   SELECT
                   issue_location,
                   0 AS ListScrap,
                   sum( CASE WHEN `remark` = 'received' THEN 1 ELSE 0 END ) AS Received
                   FROM
                   scrap_logs
                   where issue_location = '" . $location . "'
                   and DATE_FORMAT(created_at, '%Y-%m-%d') = '" . $today . "'
                   GROUP BY
                   issue_location
                   ) a
                   GROUP BY
                   a.issue_location
                   ");
              }

              $response = array(
                'status' => true,
                'datas' => $data,
                'tahun' => $tahun,
                'dateto' => $dateto,
                'location' => $location,
            );

              return Response::json($response);
          }

          public function fetchMonitoringScrapWarehouse(Request $request)
          {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');

            if ($date_from == "") {
                if ($date_to == "") {
                    $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                    $last = "LAST_DAY(NOW())";
                } else {
                    $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                    $last = "'" . $date_to . "'";
                }
            } else {
                if ($date_to == "") {
                    $first = "'" . $date_from . "'";
                    $last = "LAST_DAY(NOW())";
                } else {
                    $first = "'" . $date_from . "'";
                    $last = "'" . $date_to . "'";
                }
            }

            if (($date_to && $date_from) == null) {
                $data = db::select("
                   SELECT
                   a.issue_location,
                   SUM( a.ListScrap ) as LScrap,
                   SUM( a.Received ) as RScrap
                   FROM
                   (
                    SELECT
                    issue_location,
                    sum( CASE WHEN remark = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
                    0 AS Received
                    FROM
                    scrap_lists
                    WHERE DATE( created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )
                    GROUP BY
                    issue_location UNION ALL
                    SELECT
                    issue_location,
                    sum( CASE WHEN remark = 'received' THEN 1 ELSE 0 END ) AS Received,
                    0 AS ListScrap
                    FROM
                    scrap_logs
                    WHERE DATE( created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )
                    GROUP BY
                    issue_location
                    ) a
                   GROUP BY
                   a.issue_location");
            } else {
                $data = db::select("
                   SELECT
                   a.issue_location,
                   SUM( a.ListScrap ) as LScrap,
                   SUM( a.Received ) as RScrap
                   FROM
                   (
                    SELECT
                    issue_location,
                    sum( CASE WHEN remark = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
                    0 AS Received
                    FROM
                    scrap_lists
                    WHERE DATE( created_at ) >= " . $first . "
                    AND DATE( created_at ) <= " . $last . "
                    GROUP BY
                    issue_location UNION ALL
                    SELECT
                    issue_location,
                    sum( CASE WHEN remark = 'received' THEN 1 ELSE 0 END ) AS Received,
                    0 AS ListScrap
                    FROM
                    scrap_logs
                    WHERE DATE( created_at ) >= " . $first . "
                    AND DATE( created_at ) <= " . $last . "
                    GROUP BY
                    issue_location
                    ) a
                   GROUP BY
                   a.issue_location");
            }

            $response = array(
                'status' => true,
                'datas' => $data,
                'date_from' => $date_from,
                'date_to' => $date_to,
            );

            return Response::json($response);
        }

        public function fatchMonitoringDisplayScrap(Request $request)
        {
            $tahun = date('Y');
            $dateto = $request->get('dateto');

            $location = $request->get('loc');

            if ($dateto != "") {
                $data = db::select("
                    SELECT
                    a.issue_location,
                    SUM( a.ListScrap ) as LScrap,
                    SUM( a.Received ) as RScrap
                    FROM
                    (
                     SELECT
                     a.issue_location,
                     SUM( a.ListScrap ) as LScrap,
                     SUM( a.Received ) as RScrap
                     FROM
                     (
                      SELECT
                      issue_location,
                      sum( CASE WHEN `remark` = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
                      0 AS Received
                      FROM
                      scrap_lists
                      WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '" . $dateto . "'
                      GROUP BY
                      issue_location UNION ALL
                      SELECT
                      issue_location,
                      0 AS ListScrap,
                      sum( CASE WHEN `remark` = 'received' THEN 1 ELSE 0 END ) AS Received
                      FROM
                      scrap_logs
                      WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '" . $dateto . "'
                      GROUP BY
                      issue_location
                      ) a
                      GROUP BY
                      a.issue_location");
                 } else {
                    $data = db::select("
                      SELECT
                      a.issue_location,
                      SUM( a.ListScrap ) as LScrap,
                      SUM( a.Received ) as RScrap
                      FROM
                      (
                       SELECT
                       issue_location,
                       sum( CASE WHEN `remark` = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
                       0 AS Received
                       FROM
                       scrap_lists
                       GROUP BY
                       issue_location UNION ALL
                       SELECT
                       issue_location,
                       0 AS ListScrap,
                       sum( CASE WHEN `remark` = 'received' THEN 1 ELSE 0 END ) AS Received
                       FROM
                       scrap_logs
                       GROUP BY
                       issue_location
                       ) a
                      GROUP BY
                      a.issue_location
                      ");
                }

                $response = array(
                    'status' => true,
                    'datas' => $data,
                    'tahun' => $tahun,
                    'dateto' => $dateto,
                );

                return Response::json($response);
            }

            public function indexScrapRecord()
            {
                $materials = db::table('scrap_materials')
                ->distinct('material_number')
                ->whereNull('deleted_at')
                ->select('material_number', 'material_description as description', 'issue_location')
                ->orderBy('issue_location', 'ASC')
                ->orderBy('material_number', 'ASC')
                ->get();

                $materials_log = db::table('scrap_logs')
        // ->distinct('material_number')
                ->select('material_number', 'material_description as description', 'issue_location')
                ->orderBy('material_number', 'asc')
                ->get();

                $dept = db::select("SELECT department_name from departments where department_shortname not in ('JPN')");
                $user = db::select("SELECT employee_id, name, position FROM employee_syncs where end_date is null");

                return view('scrap.scrap_record', array(
                    'title' => 'Scrap Logs',
                    'title_jp' => 'リターンログ',
                    'storage_locations' => $this->storage_location,
                    'reicives' => $this->reicive,
                    'categorys' => $this->category,
                    'category_reasons' => $this->category_reason,
                    'materials' => $materials,
                    'dept' => $dept,
                    'user' => $user,
                    'materials_log' => $materials_log,
                ))->with('page', 'Scrap Logs');
            }

            public function fetchRecord(Request $request)
            {
                try {
                    $id_user = Auth::id();
                    $date_from = $request->get('date_from');
                    $date_to = $request->get('date_to');
                    $now = date('Y-m-d');
                    $issue = $request->get('issue');
                    $reicive = $request->get('reicive');
                    $category = $request->get('category');
                    $category_reason = $request->get('category_reason');

                    if ($date_from == '') {
                        if ($date_to == '') {
                            $whereDate = 'DATE(created_at) BETWEEN CONCAT(DATE_FORMAT("' . $now . '" - INTERVAL 30 DAY,"%Y-%m-%d")) AND "' . $now . '"';
                        } else {
                            $whereDate = 'DATE(created_at) BETWEEN CONCAT(DATE_FORMAT("' . $now . '" - INTERVAL 30 DAY,"%Y-%m-%d")) AND "' . $date_to . '"';
                        }
                    } else {
                        if ($date_to == '') {
                            $whereDate = 'DATE(created_at) BETWEEN "' . $date_from . '" AND DATE(NOW())';
                        } else {
                            $whereDate = 'DATE(created_at) BETWEEN "' . $date_from . '" AND "' . $date_to . '"';
                        }
                    }

                    $whereIssue = "";
                    if ($request->get('issue') == "") {
                        $whereIssue = "";
                    } else {
                        $whereIssue = "AND issue_location = '" . $request->get('issue') . "'";
                    }

                    $whereReicive = "";
                    if ($request->get('reicive') == "") {
                        $whereReicive = "";
                    } else {
                        $whereReicive = "AND receive_location = '" . $request->get('reicive') . "'";
                    }

                    $whereCategory = "";
                    if ($request->get('category') == "") {
                        $whereCategory = "";
                    } else {
                        $whereCategory = "AND category = '" . $request->get('category') . "'";
                    }

                    $whereCategoryReason = "";
                    if ($request->get('category_reason') == "") {
                        $whereCategoryReason = "";
                    } else {
                        $whereCategoryReason = "AND category_reason = '" . $request->get('category_reason') . "'";
                    }

                    $profession = DB::SELECT("SELECT *
                      FROM
                      `scrap_logs`
                      WHERE
                      " . $whereDate . " " . $whereIssue . " " . $whereReicive . " " . $whereCategory . " " . $whereCategoryReason . "
                      ORDER BY
                      created_at DESC");

                    $response = array(
                        'status' => true,
                        'profession' => $profession,
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

            public function createScrap()
            {
                $title = "";
                $title_jp = "";

                $reason = db::select('SELECT reason, reason_name FROM scrap_reasons ORDER BY reason ASC');

                return view('scrap.create', array(
                    'reason' => $reason,
                    'title' => 'Scrap Material',
                    'title_jp' => 'スクラップ材料',
                    'storage_locations' => $this->storage_location,
                    'category_reason' => $this->category_reason,
                    'reicive' => $this->reicive,
                    'category' => $this->category,
                ))->with('page', 'Scrap');
            }

            public function PenarikanScrap()
            {
                $reason = db::select('SELECT reason, reason_name FROM scrap_reasons ORDER BY reason ASC');
                $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)->first();

                return view('scrap.penarikan_scrap', array(
                    'reason' => $reason,
                    'title' => 'Penarikan Scrap',
                    'title_jp' => 'スクラップの引き出し',
                    'storage_locations' => $this->storage_location,
                    'category_reason' => $this->category_reason,
                    'for_loc' => $this->reicive,
                    'category' => $this->category,
                    'emp_dept' => $emp_dept,
                ))->with('page', 'Scrap');
            }

            public function ConfirmPenarikanScrap()
            {
                $emp_dept = User::where('username', Auth::user()->username)
                ->select('username', 'role_code')
                ->first();

                return view('scrap.penarikan_confirmation', array(
                    'title' => 'Scrap Material',
                    'title_jp' => 'スクラップ材料',
                    'emp_dept' => $emp_dept,

                ))->with('page', 'Scrap');
            }

            public function PenarikanScrapLog()
            {
                $materials = db::table('scrap_materials')
                ->whereNull('deleted_at')
                ->select('material_number', 'material_description as description', 'issue_location')
                ->orderBy('issue_location', 'ASC')
                ->orderBy('material_number', 'ASC')
                ->get();

                return view('scrap.penarikan_logs', array(
                    'title' => 'Penarikan',
                    'title_jp' => 'リターンログ',
                    'storage_locations' => $this->storage_location,
                    'reicives' => $this->reicive,
                    'categorys' => $this->category,
                    'category_reasons' => $this->category_reason,
                    'materials' => $materials,
                ))->with('page', 'Scrap Penarikan Logs');
            }

            public function fetchPenarikanScrap(Request $request)
            {

                $pesan = "";

                $scrap_logs = db::table('scrap_logs')
                ->whereNull('deleted_at')
                ->select('slip', 'order_no', 'material_number', 'material_description as description', 'category', 'quantity', 'reason', 'receive_location', 'spt', 'valcl', 'uom', 'remark', DB::RAW('DATE_FORMAT(scrap_logs.created_at,"%d-%m-%Y") as tanggal'))
                ->where('receive_location', $request->get('loc'))
                ->where('remark', '=', 'received')
                ->orderBy('material_number', 'ASC')
                ->get();

                $pesan = 'Lokasi Berhasil Dipilih';

                if (count($scrap_logs) == 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Lokasi terpilih tidak memiliki list material',
                    );
                    return Response::json($response);
                }
                $response = array(
                    'status' => true,
                    'lists' => $scrap_logs,
                    'message' => $pesan,
                );
                return Response::json($response);
            }

            public function listPenarikanScrap(Request $request)
            {
                $admin = Auth::user()->role_code;
                $list_penarikan = db::table('scrap_logs')
                ->whereNull('deleted_at')
                ->select('material_number', 'material_description as description', 'issue_location', 'spt', 'valcl', 'uom', 'remark')
                ->where('issue_location', $request->get('loc'))
                ->where('remark', '=', 'received')
                ->orderBy('material_number', 'ASC')
                ->get();

                $response = array(
                    'status' => true,
                    'resumes' => $list_penarikan,
                    'admin' => $admin,
                );
                return Response::json($response);
            }

            public function printPenarikanLong($slip, $orderno, $material_number, $cat, $description, $from_loc, $to_loc, $quantity, $uom, $name, $remark)
            {
                if (Auth::user()->role_code == 'S-MIS' || Auth::user()->role_code == 'S') {
                    $printer_name = 'MIS';
                } else {
                    if ($to_loc == 'CL91') {
                        $printer_name = 'FLO Printer 102';
                    } elseif ($to_loc == 'SX91') {
                        $printer_name = 'FLO Printer 103';
                    } elseif ($to_loc == 'FL91') {
                        $printer_name = 'FLO Printer 101';
                    } elseif ($to_loc == 'SX51' || $to_loc == 'CL51' || $to_loc == 'FL51' || $to_loc == 'VN51') {
                        $printer_name = 'Stockroom-Printer';
                    } elseif ($to_loc == 'SX21' || $to_loc == 'CL21' || $to_loc == 'FL21' || $to_loc == 'VN21') {
                        $printer_name = 'Welding-Printer';
                    } elseif ($to_loc == 'VN91' || $to_loc == 'VNA0' || $to_loc == 'VN11' || $to_loc == 'RC11') {
                        $printer_name = 'Injection';
                    } elseif ($to_loc == 'RC91') {
                        $printer_name = 'FLO Printer RC';
                    } elseif ($to_loc == 'CLA0' || $to_loc == 'SXA0' || $to_loc == 'FLA0' || $to_loc == 'ZPA0' || $to_loc == 'SXA2' || $to_loc == 'FLA2' || $to_loc == 'CLA2' || $to_loc == 'SA0R' || $to_loc == 'FA0R' || $to_loc == 'LA0R') {
                        $printer_name = 'KDO ZPRO';
                    } elseif ($to_loc == 'SXA1' || $to_loc == 'FLA1' || $to_loc == 'FA1R' || $to_loc == 'SA1R') {
                        $printer_name = 'Body Process Printer';
                    } elseif ($to_loc == 'CS91' || $to_loc == 'SXT9' || $to_loc == 'FLT9' || $to_loc == 'CLB9' || $to_loc == 'PNR9') {
                        $printer_name = 'KDO CASE';
                    } elseif ($to_loc == 'PN91') {
                        $printer_name = 'FLO Printer 105';
                    } elseif (($to_loc == 'MSTK' || $to_loc == 'WPCS') && $emp_dept->department == 'Quality Assurance Department') {
                        $printer_name = 'IQA Printer';
                    } elseif ($to_loc == 'MSTK' || $to_loc == 'OTHR') {
                        $printer_name = 'FLO Printer LOG';
                    } else {
                        $printer_name = 'MIS';
                    }
                }

                if ($material_number == 'NO SAP') {
                    $printer_name = 'FLO Printer LOG';
                }

                if ($cat == 'received') {
                    $printer_name = 'FLO Printer LOG';
                }

                $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)->first();

                $connector = new WindowsPrintConnector($printer_name);
                $printer = new Printer($connector);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setEmphasis(true);
                $printer->setReverseColors(true);
                $printer->setTextSize(2, 2);
                $printer->text("PENARIKAN SCRAP" . "\n");
                $printer->initialize();
                $printer->setEmphasis(true);
                $printer->setTextSize(2, 2);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text($orderno . "\n");
                $printer->setTextSize(1, 1);
                $printer->qrCode($orderno, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
                $printer->feed(1);
                $printer->initialize();
                $printer->setEmphasis(true);
                $printer->setTextSize(2, 2);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text($material_number . ' - ' . $cat . "\n");
                $printer->setTextSize(1, 1);
                $printer->text($description . "\n");
                $printer->setTextSize(2, 2);
                $printer->text($from_loc . " -> " . $to_loc . "\n");
                $printer->text($quantity . " " . $uom . "\n");
                if ($emp_dept->employee_id == 'PI9910004' || $emp_dept->employee_id == 'PI0904005') {
                    $printer->setTextSize(2, 2);
                    $printer->text("No Invoice : " . $invoice . "\n");
                }
                $printer->feed(2);
                $printer->setTextSize(1, 1);
                $printer->text("From: " . $from_loc . "\n");
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->qrCode($from_loc, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
                $printer->feed(2);
                $printer->text("GMC: " . $material_number . "\n");
                $printer->qrCode($material_number, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
                $printer->feed(2);
                $printer->text("Qty: " . $quantity . "\n");
                $printer->qrCode($quantity, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
                $printer->feed(1);
                $printer->initialize();
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("------------------------------------");
                $printer->feed(1);
                $printer->text("|Delivered By:    |Confirmed By:   |");
                $printer->feed(1);
                $printer->text("|                 |                |");
                $printer->feed(1);
                $printer->text("|                 |                |");
                $printer->feed(1);
                $printer->text("|                 |                |");
                $printer->feed(1);
                $printer->text("------------------------------------");
                $printer->feed(2);
                $printer->initialize();
                $printer->setEmphasis(true);
                $printer->setTextSize(1, 1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->textRaw("(" . date("d-M-Y H:i:s") . ")\n");
                $printer->text($name . "\n");
                $printer->feed(1);
                if ($remark == 'received') {
                    $printer->setTextSize(1, 1);
                    $printer->text('(Reprint)' . "\n");
                    $printer->text($emp_dept->name . "\n");
                }
                $printer->feed(1);
                $printer->cut();
                $printer->close();
            }

            public function printPenarikanShort($slip, $orderno, $material_number, $cat, $description, $from_loc, $to_loc, $quantity, $uom, $name, $remark)
            {
                $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)->first();

                if (Auth::user()->role_code == 'S-MIS' || Auth::user()->role_code == 'S') {
                    $printer_name = 'MIS';
                } else {
                    if ($to_loc == 'CL91') {
                        $printer_name = 'FLO Printer 102';
                    } elseif ($to_loc == 'SX91') {
                        $printer_name = 'FLO Printer 103';
                    } elseif ($to_loc == 'FL91') {
                        $printer_name = 'FLO Printer 101';
                    } elseif ($to_loc == 'SX51' || $to_loc == 'CL51' || $to_loc == 'FL51' || $to_loc == 'VN51') {
                        $printer_name = 'Stockroom-Printer';
                    } elseif ($to_loc == 'SX21' || $to_loc == 'CL21' || $to_loc == 'FL21' || $to_loc == 'VN21') {
                        $printer_name = 'Welding-Printer';
                    } elseif ($to_loc == 'VN91' || $to_loc == 'VNA0' || $to_loc == 'VN11' || $to_loc == 'RC11') {
                        $printer_name = 'Injection';
                    } elseif ($to_loc == 'RC91') {
                        $printer_name = 'FLO Printer RC';
                    } elseif ($to_loc == 'CLA0' || $to_loc == 'SXA0' || $to_loc == 'FLA0' || $to_loc == 'ZPA0' || $to_loc == 'SXA2' || $to_loc == 'FLA2' || $to_loc == 'CLA2' || $to_loc == 'SA0R' || $to_loc == 'FA0R' || $to_loc == 'LA0R') {
                        $printer_name = 'KDO ZPRO';
                    } elseif ($to_loc == 'SXA1' || $to_loc == 'FLA1' || $to_loc == 'FA1R' || $to_loc == 'SA1R') {
                        $printer_name = 'Body Process Printer';
                    } elseif ($to_loc == 'CS91' || $to_loc == 'SXT9' || $to_loc == 'FLT9' || $to_loc == 'CLB9' || $to_loc == 'PNR9') {
                        $printer_name = 'KDO CASE';
                    } elseif ($to_loc == 'PN91') {
                        $printer_name = 'FLO Printer 105';
                    } elseif (($to_loc == 'MSTK' || $to_loc == 'WPCS') && $emp_dept->department == 'Quality Assurance Department') {
                        $printer_name = 'IQA Printer';
                    } elseif ($to_loc == 'MSTK' || $to_loc == 'OTHR') {
                        $printer_name = 'FLO Printer LOG';
                    } else {
                        $printer_name = 'MIS';
                    }
                }

                if ($material_number == 'NO SAP') {
                    $printer_name = 'FLO Printer LOG';
                }

                if ($cat == 'received') {
                    $printer_name = 'FLO Printer LOG';
                }

                $connector = new WindowsPrintConnector($printer_name);
                $printer = new Printer($connector);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setEmphasis(true);
                $printer->setReverseColors(true);
                $printer->setTextSize(2, 2);
                $printer->text("PENARIKAN SCRAP" . "\n");
                $printer->initialize();
                $printer->setEmphasis(true);
                $printer->setTextSize(2, 2);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text($orderno . "\n");
                $printer->feed(1);
                $printer->initialize();
                $printer->setEmphasis(true);
                $printer->setTextSize(2, 2);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text($material_number . ' - ' . $cat . "\n");
                $printer->setTextSize(1, 1);
                $printer->text($description . "\n");
                $printer->setTextSize(2, 2);
                $printer->text($from_loc . " -> " . $to_loc . "\n");
                $printer->text($quantity . " " . $uom . "\n");
                if ($emp_dept->employee_id == 'PI9910004' || $emp_dept->employee_id == 'PI0904005') {
                    $printer->setTextSize(2, 2);
                    $printer->text("No Invoice : " . $invoice . "\n");
                }
                $printer->feed(2);
                $printer->setTextSize(1, 1);
                $printer->qrCode($slip, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
                $printer->text($slip . "\n");
                $printer->feed(1);
                $printer->initialize();
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("------------------------------------");
                $printer->feed(1);
                $printer->text("|Delivered By:    |Confirmed By:   |");
                $printer->feed(1);
                $printer->text("|                 |                |");
                $printer->feed(1);
                $printer->text("|                 |                |");
                $printer->feed(1);
                $printer->text("|                 |                |");
                $printer->feed(1);
                $printer->text("------------------------------------");
                $printer->feed(2);
                $printer->initialize();
                $printer->setEmphasis(true);
                $printer->setTextSize(1, 1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->textRaw("(" . date("d-M-Y H:i:s") . ")\n");
                $printer->text($name . "\n");
                $printer->feed(1);
                if ($cat == 'received') {
                    $printer->setTextSize(1, 1);
                    $printer->text('(Reprint)' . "\n");
                    $printer->text($emp_dept->name . "\n");
                }
                $printer->feed(1);
                $printer->cut();
                $printer->close();
            }

            public function ReprintPenarikanScrap(Request $request)
            {
                try {
                    $sp = ScrapPenarikanLog::where('id', '=', $request->get('id'))->first();
                    $slip = $sp->slip;
                    $orderno = $sp->order_no;
                    $material_number = $sp->material_number;
                    $cat = $sp->category;
                    $remark = $sp->remark;
                    $description = $sp->description;
                    $from_loc = $sp->receive_location;
                    $to_loc = $sp->withdrawal_to;
                    $quantity = $sp->amount;
                    $uom = $sp->uom;
                    $name = Auth::user()->name;

                } catch (\Exception$e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
                $response = array(
                    'status' => true,
                    'message' => 'Cetak ulang slip scrap berhasil',
                );
                return Response::json($response);
            }

            public function fetchPenarikanScrapList(Request $request)
            {
                $admin = Auth::user()->role_code;

                $resumes = ScrapPenarikanLog::where('receive_location', '=', $request->get('loc'))
                ->where('remark', '=', 'withdraw')
                ->leftJoin('users', 'users.id', '=', 'scrap_penarikan_logs.created_by')
                ->select('scrap_penarikan_logs.id', 'scrap_penarikan_logs.slip', 'scrap_penarikan_logs.material_number', 'scrap_penarikan_logs.material_description', 'scrap_penarikan_logs.receive_location', 'scrap_penarikan_logs.category', 'scrap_penarikan_logs.quantity', 'scrap_penarikan_logs.withdrawal_to', 'scrap_penarikan_logs.amount', 'scrap_penarikan_logs.remark', 'users.name', DB::RAW('DATE_FORMAT(scrap_penarikan_logs.created_at,"%d-%m-%Y") as tanggal'), 'scrap_penarikan_logs.created_by', 'scrap_penarikan_logs.order_no')
                ->orderBy('scrap_penarikan_logs.created_at', 'desc')
                ->get();

                $response = array(
                    'status' => true,
                    'resumes' => $resumes,
                    'admin' => $admin,
                );
                return Response::json($response);
            }

            public function deletePenarikanScrap(Request $request)
            {
                try {
                    $auth_id = Auth::id();
                    $scrap = ScrapPenarikanLog::where('slip', '=', $request->get('slip'))->first();
                    $scrap->forceDelete();
                } catch (\Exception$e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
                $response = array(
                    'status' => true,
                    'message' => 'Slip Scrap berhasil di delete',
                );
                return Response::json($response);
            }

            public function ScanPenarikanScrap(Request $request)
            {
                $sp = ScrapPenarikanLog::where('slip', '=', $request->get('number'))->first();

                if ($sp->remark == 'confirmed') {
                    $response = array(
                        'status' => false,
                        'message' => 'Wes di confirm',
                    );
                    return Response::json($response);
                }
                if ($sp->remark == 'withdraw') {
                    try {
                        $upd = ScrapPenarikanLog::where('slip', '=', $request->get('number'))->update([
                            'remark' => 'confirmed',
                            'confirmed_by' => Auth::id(),
                        ]);

                        $response = array(
                            'status' => true,
                            'message' => 'OK confirmed',
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
                        'message' => 'Salah Bosku',
                    );
                    return Response::json($response);
                }
            }

            public function FetchScanPenarikanScrap(Request $request)
            {
                $admin = Auth::user()->role_code;

                try {
                    if (Auth::user()->role_code == "S-MIS" || Auth::user()->role_code == "SL-LOG" || Auth::user()->username == "PI9901011") {
                        $date_from = $request->get('date_from');
                        $date_to = $request->get('date_to');
                        if ($date_from == "") {
                            if ($date_to == "") {
                                $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                                $last = "LAST_DAY(NOW())";
                            } else {
                                $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                                $last = "'" . $date_to . "'";
                            }
                        } else {
                            if ($date_to == "") {
                                $first = "'" . $date_from . "'";
                                $last = "LAST_DAY(NOW())";
                            } else {
                                $first = "'" . $date_from . "'";
                                $last = "'" . $date_to . "'";
                            }
                        }

                        if (($date_to && $date_from) == null) {
                            $resumes = DB::SELECT("SELECT
                                id,
                                slip,
                                order_no,
                                material_description,
                                category,
                                issue_location,
                                receive_location,
                                quantity,
                                uom,
                                reason,
                                created_at,
                                remark
                                FROM
                                scrap_penarikan_logs
                                WHERE
                                remark = 'confirmed'
                                AND DATE( scrap_penarikan_logs.created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )
                                ORDER BY
                                created_at DESC");
                        } else {
                            $resumes = DB::SELECT("SELECT
                                id,
                                slip,
                                order_no,
                                material_description,
                                category,
                                issue_location,
                                receive_location,
                                quantity,
                                uom,
                                reason,
                                created_at,
                                remark
                                FROM
                                scrap_penarikan_logs
                                WHERE
                                remark = 'confirmed'
                                AND DATE( scrap_penarikan_logs.created_at ) >= " . $first . "
                                AND DATE( scrap_penarikan_logs.created_at ) <= " . $last . "
                                ORDER BY
                                created_at DESC");
                        }
                    }
                    $response = array(
                        'status' => true,
                        'resumes' => $resumes,
                        'admin' => $admin,
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

            public function FetchPenarikanScrapLogs(Request $request)
            {
                $date = '';
                if (strlen($request->get('datefrom')) > 0) {
                    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
                    $date = "date(pn.created_at) >= '" . $datefrom . "' ";
                    if (strlen($request->get('dateto')) > 0) {
                        $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                        $date = $date . "AND date(pn.created_at) <= '" . $dateto . "'";
                    }
                }

        // $issue = '';
        // if ($request->get('issue') != null) {
        //     $issues = $request->get('issue');
        //     for ($i = 0; $i < count($issues); $i++) {
        //         $issue = $issue . "'" . $issues[$i] . "'";
        //         if ($i != (count($issues) - 1)) {
        //             $issue = $issue . ',';
        //         }
        //     }
        //     $issue = " AND issue_location IN (" . $issue . ") ";
        // }

        // $receive = '';
        // if ($request->get('receive') != null) {
        //     $receives = $request->get('receive');
        //     for ($i = 0; $i < count($receives); $i++) {
        //         $receive = $receive . "'" . $receives[$i] . "'";
        //         if ($i != (count($receives) - 1)) {
        //             $receive = $receive . ',';
        //         }
        //     }
        //     $receive = " AND receive_location IN (" . $receive . ") ";
        // }

        // $material = '';
        // if ($request->get('material') != null) {
        //     $materials = $request->get('material');
        //     for ($i = 0; $i < count($materials); $i++) {
        //         $material = $material . "'" . $materials[$i] . "'";
        //         if ($i != (count($materials) - 1)) {
        //             $material = $material . ',';
        //         }
        //     }
        //     $material = " AND material_number IN (" . $material . ") ";
        // }

        // $remark = '';
        // if ($request->get('remark') != null) {
        //     $remark = " AND scrap_penarikan_logs.remark = '" . $request->get('remark') . "' ";
        // }

        // $condition = $date . $issue . $receive . $material . $remark;
        // dd($date);

        // $log = db::select("
        //     SELECT
        //     scrap_penarikan_logs.id,
        //     CONCAT( slip, '-SC' ) AS slip,
        //     order_no,
        //     material_number,
        //     material_description,
        //     spt,
        //     valcl,
        //     category,
        //     issue_location,
        //     withdrawal_to,
        //     receive_location,
        //     quantity,
        //     amount,
        //     sisa_stock,
        //     uom,
        //     reason,
        //     summary,
        //     no_invoice,
        //     remark,
        //     created_at,
        //     deleted_at,
        //     updated_at,
        //     a.`name` as created,
        //     b.`name` as confirm,
        //     c.`name` as scrap
        //     FROM
        //     scrap_penarikan_logs
        //     LEFT JOIN (SELECT id, CONCAT(SPLIT_STRING ( `name`, ' ', 1 ), ' ',SPLIT_STRING ( `name`, ' ', 2 )) AS `name` FROM users) AS a ON a.id = scrap_penarikan_logs.created_by
        //     LEFT JOIN (SELECT id, CONCAT(SPLIT_STRING ( `name`, ' ', 1 ), ' ',SPLIT_STRING ( `name`, ' ', 2 )) AS `name` FROM users) AS b ON b.id = scrap_penarikan_logs.confirmed_by
        //     LEFT JOIN (SELECT id, CONCAT(SPLIT_STRING ( `name`, ' ', 1 ), ' ',SPLIT_STRING ( `name`, ' ', 2 )) AS `name` FROM users) AS c ON c.id = scrap_penarikan_logs.scrap_by
        //     WHERE scrap_penarikan_logs.remark is not null " . $condition . "
        //     ORDER BY scrap_penarikan_logs.created_at");
                $data_penarikan = '';
                if ($date == null) {
                    $response = array(
                        'status' => false,
                        'message' => 'Pilih Tanggal Terlebih Dahulu',
                    );
                    return Response::json($response);
                } else {
                    $data_penarikan = db::select('select slip_penarikan, slip, material_number, material_description, quantity, uom, receive_location, withdrawal_to, no_invoice, reason, a.`name` as scrap_by, u.`name` as created_by from scrap_penarikan_logs as pn
                        left join users as a on a.id = pn.scrap_by
                        left join users as u on u.id = pn.created_by where ' . $date . '');
                }

        // return DataTables::of($log)
        //     ->addColumn('cancel', function ($data) {
        //         if (Auth::user()->role_code == "S-MIS" || Auth::user()->role_code == "S-PRD") {
        //             if ($data->remark == 'withdraw') {
        //                 return '<button style="width: 50%; height: 100%;" onclick="BatalPenarikanScrap(\'' . $data->id . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button>';
        //             } elseif ($data->remark == 'confirmed') {
        //                 return '<button style="width: 50%; height: 100%;" onclick="BatalPenarikanScrap(\'' . $data->id . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button>';
        //             } elseif ($data->remark == 'canceled') {
        //                 return '<button style="width: 50%; height: 100%;" class="btn btn-xs btn-success form-control"><span><i class="fa fa-check"></i></span></button>';
        //             } else {
        //                 return '-';
        //             }
        //         } else {
        //             return '-';
        //         }
        //     })
        //     ->rawColumns(['cancel' => 'cancel'])
        //     ->make(true);

                $response = array(
                    'status' => true,
                    'data_penarikan' => $data_penarikan,
                );
                return Response::json($response);
            }

            public function BatalPenarikanScrap(Request $request)
            {
                $auth_id = Auth::id();
                $sp = ScrapPenarikanLog::where('id', '=', $request->get('id'))->first();
                try {
                    if ($sp->remark == 'withdraw') {
                        $sp->forceDelete();
                    } elseif ($sp->remark == 'confirmed') {
                        $upd = ScrapPenarikanLog::where('id', '=', $request->get('id'))->update([
                            'remark' => 'deleted',
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
                    'message' => 'Berhasil Bos',
                );
                return Response::json($response);
            }

            public function SelectScrapReason(Request $request)
            {
                try {
                    $reason = db::select("select reason, reason_name, remark from scrap_reasons where remark = '" . $request->get('receive_location') . "' order by reason ASC");
                    $response = array(
                        'status' => true,
                        'message' => 'Success',
                        'reason' => $reason,
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

            public function SelectScrapType(Request $request)
            {
                try {
                    $type = $request->get('type_material');
                    $loc = '';

                    if ($type == 'Normal') {
                        $loc = $this->reicive;
                    } else {
                        $loc = ['WSCR'];
                    }

                    $response = array(
                        'status' => true,
                        'message' => 'Success',
                        'loc' => $loc,
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

            public function ExcelReportScrap(Request $request){
                // $datefrom = $request->get('datefrom');
                // $dateto = $request->get('dateto');
                // $issue_location = $request->get('issue');
                // $receive_location = $request->get('receive');
                // $gmc = $request->get('material');
                // $remark = $request->get('remark');
                $stattsss = '';
                if ($request->get('publish') != null) {
                  $stattsss = 'no_merge';
              }else{
                  $stattsss = 'merge';
              }

                $datefrom = '';
                if ($request->get('datefrom') != null) {
                    $datefrom = " AND DATE_FORMAT(non.updated_at, '%Y-%m-%d') >= '" . $request->get('datefrom') . "' ";
                }

                $dateto = '';
                if ($request->get('dateto') != null) {
                    $dateto = " AND DATE_FORMAT(non.updated_at, '%Y-%m-%d') <= '" . $request->get('dateto') . "' ";
                }

                $issue = '';
                if ($request->get('issue') != null) {
                    $issues = $request->get('issue');
                    for ($i = 0; $i < count($issues); $i++) {
                        $issue = $issue . "'" . $issues[$i] . "'";
                        if ($i != (count($issues) - 1)) {
                            $issue = $issue . ',';
                        }
                    }
                    $issue = " AND issue_location IN (" . $issue . ") ";
                }

                $receive = '';
                if ($request->get('receive') != null) {
                    $receives = $request->get('receive');
                    for ($i = 0; $i < count($receives); $i++) {
                        $receive = $receive . "'" . $receives[$i] . "'";
                        if ($i != (count($receives) - 1)) {
                            $receive = $receive . ',';
                        }
                    }
                    $receive = " AND receive_location IN (" . $receive . ") ";
                }

                $material = '';
                if ($request->get('material') != null) {
                    $materials = $request->get('material');
                    for ($i = 0; $i < count($materials); $i++) {
                        $material = $material . "'" . $materials[$i] . "'";
                        if ($i != (count($materials) - 1)) {
                            $material = $material . ',';
                        }
                    }
                    $material = " AND non.material_number IN (" . $material . ") ";
                }

                $remark = '';
                if ($request->get('remark') != null) {
                    $remark = " AND non.remark = '" . $request->get('remark') . "' ";
                }

                $condition = $datefrom . $dateto . $issue . $receive . $material . $remark;

                $datas = db::select("
                   SELECT
                   non.id,
                   CONCAT(non.slip,'-SC') as slip,
                   non.order_no,
                   non.material_number,
                   non.issue_location,
                   non.category,
                   IF(sts.material_number is null, '-', 'Silver') as jenis,
                   non.receive_location,
                   non.material_description,
                   non.quantity,
                   non.remark,
                   non.reason,
                   non.summary,
                   IF(non.no_invoice is not null, non.no_invoice, '-') as no_invoice,
                   non.slip_created AS printed_at,
                   scrap_user.`name` AS printed_by,
                   IF(non.remark = 'received', non.created_at, '-') AS received_at,
                   IF(non.remark = 'received', receive_user.`name`, '-') AS received_by,
                   IF(non.remark = 'canceled' || non.remark = 'deleted', non.deleted_at, '-') AS canceled_at,
                   IF(non.remark = 'canceled' || non.remark = 'deleted', cancel_user.`name`, '-') AS canceled_by
                   FROM
                   (SELECT id, slip, order_no, material_number, material_description, issue_location, receive_location, quantity, remark, reason, summary, slip_created, scraped_by, created_at, created_by, no_invoice, category, canceled_by, deleted_at, updated_at, canceled_user, canceled_user_at FROM `scrap_logs`
                    UNION ALL
                    SELECT id, slip, order_no, material_number, material_description, issue_location, receive_location, quantity, remark, reason, summary, created_at as slip_created, created_by as scraped_by, created_at, created_by, no_invoice, category, 0 as canceled_by, deleted_at, updated_at, 0 as canceled_user, 0 as canceled_user_at FROM `scrap_lists` where remark is not null
                    ) AS non
                   LEFT JOIN (select stocktaking_silver_lists.material_number from stocktaking_silver_lists group by stocktaking_silver_lists.material_number) as sts on sts.material_number = non.material_number
                   LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS scrap_user ON scrap_user.id = non.scraped_by
                   LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS receive_user ON receive_user.id = non.created_by
                   LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS cancel_user ON cancel_user.id = non.canceled_by
                   WHERE non.id is not null " . $condition . "
                   ORDER BY non.slip_created");

                $data = array(
                  'datas' => $datas
              );

                if ($stattsss == 'no_merge') {
                  ob_clean();
                  Excel::create('Scrap Report Without Merge', function($excel) use ($data){
                    $excel->sheet('Scrap Record', function($sheet) use ($data) {
                      return $sheet->loadView('scrap.excel_report_without_merge', $data);
                  });
                })->export('xlsx');
              }else{
                  ob_clean();
                  Excel::create('Scrap Report With Merge', function($excel) use ($data){
                    $excel->sheet('Scrap Record', function($sheet) use ($data) {
                      return $sheet->loadView('scrap.excel_report_check', $data);
                  });
                })->export('xlsx');
              }
              return redirect()->route('report_scrap_index')->with('status','Success Export Data');
            }
        }
