<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Response;

class LicenseController extends Controller
{

    private $software_category;
    private $equipment_category;
    private $operator_category;
    private $ta_category;

    public function __construct()
    {

        $this->middleware('auth');
        $this->equipment_category = [
            'ALARM KEBAKARAN',
            'BEJANA TEKANAN',
            'BOILER',
            'CRANE',
            'FORKLIFT',
            'HYDRANT',
            'LIFT',
            'LISTRIK',
            'PETIR',
        ];
        $this->operator_category = [
            'AHLI & PETUGAS K3 KIMIA',
            'JURU IKAT (RIGGER)',
            'JURU LAS',
            'LISTRIK',
            'OPERATOR BOILER',
            'OPERATOR CRANE',
            'OPERATOR FORKLIFT',
            'PETUGAS AK3 LISTRIK',
            'PETUGAS AK3 UMUM',
            'PETUGAS P3K',
            'PETUGAS PMK',
            'PETUGAS PROTEKSI RADIASI',
        ];
        $this->material_category = [
            'CHEMICAL BERBAHAYA',
        ];
        $this->software_category = [
            'Application',
            'Operating System',
        ];
        $this->ta_category = [
            'Abrasive',
            'Accessories',
            'Adhesive',
            'Chemical',
            'Cloth',
            'Gas',
            'General Dist.',
            'Injection',
            'Metals',
            'Oil',
            'Other',
            'Packaging',
            'Resin',
            'Safety',
            'Tools',
        ];
        $this->document_category = [
            'DOKUMEN PERUSAHAAN',
        ];
    }

    public function indexLicense($id)
    {
        if ($id == 'software') {
            $title = "Software License Control";
            $title_jp = "";
            $category = "Software License";
            $prefix = "IT";
            $license_category = $this->software_category;
        }
        if ($id == 'equipment') {
            $title = "Equipment License Control";
            $title_jp = "";
            $category = "Equipment Certificate";
            $prefix = "EQ";
            $license_category = $this->equipment_category;
        }
        if ($id == 'operator') {
            $title = "Operator License Control";
            $title_jp = "";
            $category = "Operator License";
            $prefix = "OP";
            $license_category = $this->operator_category;
        }
        if ($id == 'raw_material') {
            $title = "Material License Control";
            $title_jp = "";
            $category = "Material License";
            $prefix = "RM";
            $license_category = $this->material_category;
        }
        if ($id == 'document') {
            $title = "Document License Control";
            $title_jp = "";
            $category = "Document License";
            $prefix = "DC";
            $license_category = $this->document_category;
        }
        $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
        $employees = db::table('employee_syncs')->whereNull('end_date')->get();

        return view('licenses.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'category' => $category,
            'prefix' => $prefix,
            'license_categories' => $license_category,
            'departments' => $departments,
            'employees' => $employees,
        ))->with('page', 'License');
    }

    public function mailCMS()
    {
        $data = array();

        $mail_cc = ['budhi.apriyanto@music.yamaha.com', 'ympi-mis-ML@music.yamaha.com', 'yusli.erwandi@music.yamaha.com', 'mei.rahayu@music.yamaha.com', 'youichi.oyama@music.yamaha.com'];

        $mtas = db::connection('ympimis_2')->table('mtas')->whereNull('deleted_at')
            ->where('pgr', '=', 'G08')
            ->where('status_vendor', '=', 'Active')
            ->get();

        $buyers = array();

        foreach ($mtas as $mta) {
            if (!in_array($mta->pic_id, $buyers)) {
                array_push($buyers, $mta->pic_id);
            }
        }

        foreach ($buyers as $buyer) {
            $mail_to = array();
            $mail_bcc = array();
            $user = db::table('users')->where('username', '=', $buyer)->first();
            array_push($mail_to, $user->email);

            foreach ($mtas as $mta) {
                if ($mta->pic_id == $buyer) {
                    $vendor_mails = db::table('vendor_mails')->where('vendor_code', '=', $mta->vendor_code)->get();
                    foreach ($vendor_mails as $vendor_mail) {
                        array_push($mail_bcc, $vendor_mail->email);

                        $logs = db::connection('ympimis_2')->table('mta_cms_logs')
                            ->insert([
                                'vendor_code' => $vendor_mail->vendor_code,
                                'vendor_email' => $vendor_mail->email,
                                'buyer_email' => $user->email,
                                'created_by' => 1,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                }
            }

            Mail::to($mail_to)
                ->cc($mail_cc)
                ->bcc($mail_bcc)
                ->send(new SendEmail($data, 'trade_agreement_cms'));
        }
    }

    public function mailLicense()
    {
        $licenses = db::connection('ympimis_2')->select("SELECT
            id,
            license_id,
            category,
            license_category,
            department,
            employee_id,
            employee_name,
            compliance,
            license_number,
            location,
            frequency,
            valid_from,
            valid_to,
            TIMESTAMPDIFF( DAY, now(), valid_to ) AS diff,
            reminder,
            status,
            remark,
            creator_id,
            creator_name,
            created_by,
            deleted_at,
            created_at,
            updated_at
            FROM
            licenses
            WHERE
            deleted_at IS NULL
            AND status <> 'Discontinue'
            AND TIMESTAMPDIFF( DAY, now(), valid_to ) <= reminder");

        $operators = array();
        $equipments = array();
        $softwares = array();
        $materials = array();
        $documents = array();

        foreach ($licenses as $license) {
            if ($license->category == 'Equipment Certificate') {
                array_push($equipments,
                    [
                        'id' => $license->id,
                        'license_id' => $license->license_id,
                        'category' => $license->category,
                        'license_category' => $license->license_category,
                        'department' => $license->department,
                        'employee_id' => $license->employee_id,
                        'employee_name' => $license->employee_name,
                        'compliance' => $license->compliance,
                        'license_number' => $license->license_number,
                        'location' => $license->location,
                        'frequency' => $license->frequency,
                        'valid_from' => $license->valid_from,
                        'valid_to' => $license->valid_to,
                        'diff' => $license->diff,
                        'reminder' => $license->reminder,
                        'status' => $license->status,
                        'remark' => $license->remark,
                        'creator_id' => $license->creator_id,
                        'creator_name' => $license->creator_name,
                        'created_by' => $license->created_by,
                        'deleted_at' => $license->deleted_at,
                        'created_at' => $license->created_at,
                        'updated_at' => $license->updated_at,
                    ]);
            }
            if ($license->category == 'Software License') {
                array_push($softwares,
                    [
                        'id' => $license->id,
                        'license_id' => $license->license_id,
                        'category' => $license->category,
                        'license_category' => $license->license_category,
                        'department' => $license->department,
                        'employee_id' => $license->employee_id,
                        'employee_name' => $license->employee_name,
                        'compliance' => $license->compliance,
                        'license_number' => $license->license_number,
                        'location' => $license->location,
                        'frequency' => $license->frequency,
                        'valid_from' => $license->valid_from,
                        'valid_to' => $license->valid_to,
                        'diff' => $license->diff,
                        'reminder' => $license->reminder,
                        'status' => $license->status,
                        'remark' => $license->remark,
                        'creator_id' => $license->creator_id,
                        'creator_name' => $license->creator_name,
                        'created_by' => $license->created_by,
                        'deleted_at' => $license->deleted_at,
                        'created_at' => $license->created_at,
                        'updated_at' => $license->updated_at,
                    ]);
            }
            if ($license->category == 'Operator License') {
                array_push($operators,
                    [
                        'id' => $license->id,
                        'license_id' => $license->license_id,
                        'category' => $license->category,
                        'license_category' => $license->license_category,
                        'department' => $license->department,
                        'employee_id' => $license->employee_id,
                        'employee_name' => $license->employee_name,
                        'compliance' => $license->compliance,
                        'license_number' => $license->license_number,
                        'location' => $license->location,
                        'frequency' => $license->frequency,
                        'valid_from' => $license->valid_from,
                        'valid_to' => $license->valid_to,
                        'diff' => $license->diff,
                        'reminder' => $license->reminder,
                        'status' => $license->status,
                        'remark' => $license->remark,
                        'creator_id' => $license->creator_id,
                        'creator_name' => $license->creator_name,
                        'created_by' => $license->created_by,
                        'deleted_at' => $license->deleted_at,
                        'created_at' => $license->created_at,
                        'updated_at' => $license->updated_at,
                    ]);
            }
            if ($license->category == 'Material License') {
                array_push($materials,
                    [
                        'id' => $license->id,
                        'license_id' => $license->license_id,
                        'category' => $license->category,
                        'license_category' => $license->license_category,
                        'department' => $license->department,
                        'employee_id' => $license->employee_id,
                        'employee_name' => $license->employee_name,
                        'compliance' => $license->compliance,
                        'license_number' => $license->license_number,
                        'location' => $license->location,
                        'frequency' => $license->frequency,
                        'valid_from' => $license->valid_from,
                        'valid_to' => $license->valid_to,
                        'diff' => $license->diff,
                        'reminder' => $license->reminder,
                        'status' => $license->status,
                        'remark' => $license->remark,
                        'creator_id' => $license->creator_id,
                        'creator_name' => $license->creator_name,
                        'created_by' => $license->created_by,
                        'deleted_at' => $license->deleted_at,
                        'created_at' => $license->created_at,
                        'updated_at' => $license->updated_at,
                    ]);
            }
        }
        if ($license->category == 'Document License') {
            array_push($documents,
                [
                    'id' => $license->id,
                    'license_id' => $license->license_id,
                    'category' => $license->category,
                    'license_category' => $license->license_category,
                    'department' => $license->department,
                    'employee_id' => $license->employee_id,
                    'employee_name' => $license->employee_name,
                    'compliance' => $license->compliance,
                    'license_number' => $license->license_number,
                    'location' => $license->location,
                    'frequency' => $license->frequency,
                    'valid_from' => $license->valid_from,
                    'valid_to' => $license->valid_to,
                    'diff' => $license->diff,
                    'reminder' => $license->reminder,
                    'status' => $license->status,
                    'remark' => $license->remark,
                    'creator_id' => $license->creator_id,
                    'creator_name' => $license->creator_name,
                    'created_by' => $license->created_by,
                    'deleted_at' => $license->deleted_at,
                    'created_at' => $license->created_at,
                    'updated_at' => $license->updated_at,
                ]);
        }

        if (count($equipments) > 0) {
            $data = [
                'licenses' => $equipments,
                'title' => 'Renewal Equipment Certificate Reminder',
                'link' => 'equipment',
            ];

            Mail::to(['widura@music.yamaha.com', 'syafrizal.carnov.purwanto@music.yamaha.com'])
                ->cc(['yayuk.wahyuni@music.yamaha.com'])
                ->bcc('ympi-mis-ML@music.yamaha.com')
                ->send(new SendEmail($data, 'license_reminder'));
        }
        if (count($softwares) > 0) {
            $data = [
                'licenses' => $softwares,
                'title' => 'Renewal Software License Reminder',
                'link' => 'software',
            ];

            Mail::to(['ympi-mis-ml@music.yamaha.com'])
                ->cc([])
                ->send(new SendEmail($data, 'license_reminder'));
        }
        if (count($operators) > 0) {
            $data = [
                'licenses' => $operators,
                'title' => 'Renewal Operator License Reminder',
                'link' => 'operator',
            ];

            Mail::to(['widura@music.yamaha.com', 'syafrizal.carnov.purwanto@music.yamaha.com'])
                ->cc(['yayuk.wahyuni@music.yamaha.com'])
                ->bcc('ympi-mis-ML@music.yamaha.com')
                ->send(new SendEmail($data, 'license_reminder'));
        }
        if (count($materials) > 0) {
            $data = [
                'licenses' => $materials,
                'title' => 'Renewal Material License Reminder',
                'link' => 'raw_material',
            ];

            Mail::to(['hanin.hamidi@music.yamaha.com'])
                ->cc(['yusli.erwandi@music.yamaha.com', 'silvy.firliani@music.yamaha.com'])
                ->bcc('ympi-mis-ML@music.yamaha.com')
                ->send(new SendEmail($data, 'license_reminder'));
        }
        if (count($documents) > 0) {
            $data = [
                'licenses' => $documents,
                'title' => 'Renewal Document License Reminder',
                'link' => 'document',
            ];

            Mail::to(['eko.junaedi@music.yamaha.com'])
                ->bcc('ympi-mis-ML@music.yamaha.com')
                ->send(new SendEmail($data, 'license_reminder'));
        }

    }

    public function inputLicense(Request $request)
    {

        try {
            $code_generator = CodeGenerator::where('note', '=', 'license_id')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $license_id = $request->input('prefix') . $number;

            if ($request->input('code') == 'Updated') {
                $license_id = $request->input('licenseId');
            }

            $employee = explode('-', $request->input('user'));

            $employee_id = "";
            $employee_name = "";

            if (count($employee) > 1) {
                $employee_id = $employee[0];
                $employee_name = $employee[1];
            }

            if (count($request->file('attachment')) > 0) {
                $filename = "";
                $file_destination = 'files/licenses';
                $file = $request->file('attachment');
                $filename = $license_id . date('YmdHis') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);

                $licenses_attachment = db::connection('ympimis_2')->table('license_attachments')
                    ->insert([
                        'license_id' => $license_id,
                        'file_name' => $filename,
                        'valid_from' => $request->input('validFrom'),
                        'valid_to' => $request->input('validTo'),
                        'creator_id' => Auth::user()->username,
                        'creator_name' => Auth::user()->name,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            if ($request->input('code') == 'Created') {
                $status = $request->input('status');
                $lincese = db::connection('ympimis_2')->table('licenses')
                    ->insert([
                        'license_id' => $license_id,
                        'category' => $request->input('category'),
                        'license_category' => $request->input('licenseCategory'),
                        'department' => $request->input('department'),
                        'employee_id' => $employee_id,
                        'employee_name' => $employee_name,
                        'compliance' => $request->input('compliance'),
                        'license_number' => $request->input('license'),
                        'location' => $request->input('location'),
                        'frequency' => $request->input('frequency'),
                        'valid_from' => $request->input('validFrom'),
                        'valid_to' => $request->input('validTo'),
                        'reminder' => $request->input('reminder'),
                        'status' => $status,
                        'remark' => $request->input('remark'),
                        'secrecy' => $request->input('secrecy'),
                        'creator_id' => Auth::user()->username,
                        'creator_name' => Auth::user()->name,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            if ($request->input('code') == 'Updated') {
                $status = 'Active';
                if (date('Y-m-d', strtotime($request->input('validTo'))) <= date('Y-m-d')) {
                    $status = 'Expired';
                }
                $lincese = db::connection('ympimis_2')->table('licenses')
                    ->where('license_id', '=', $license_id)
                    ->update([
                        'category' => $request->input('category'),
                        'license_category' => $request->input('licenseCategory'),
                        'department' => $request->input('department'),
                        'employee_id' => $employee_id,
                        'employee_name' => $employee_name,
                        'compliance' => $request->input('compliance'),
                        'license_number' => $request->input('license'),
                        'location' => $request->input('location'),
                        'frequency' => $request->input('frequency'),
                        'valid_from' => $request->input('validFrom'),
                        'valid_to' => $request->input('validTo'),
                        'reminder' => $request->input('reminder'),
                        'status' => $status,
                        'remark' => $request->input('remark'),
                        'secrecy' => $request->input('secrecy'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $lincese_log = db::connection('ympimis_2')->table('license_logs')
                ->insert([
                    'license_id' => $license_id,
                    'category' => $request->input('category'),
                    'license_category' => $request->input('licenseCategory'),
                    'department' => $request->input('department'),
                    'employee_id' => $employee_id,
                    'employee_name' => $employee_name,
                    'compliance' => $request->input('compliance'),
                    'license_number' => $request->input('license'),
                    'location' => $request->input('location'),
                    'frequency' => $request->input('frequency'),
                    'valid_from' => $request->input('validFrom'),
                    'valid_to' => $request->input('validTo'),
                    'reminder' => $request->input('reminder'),
                    'status' => $status,
                    'remark' => $request->input('remark'),
                    'secrecy' => $request->input('secrecy'),
                    'log' => $request->input('code'),
                    'creator_id' => Auth::user()->username,
                    'creator_name' => Auth::user()->name,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if ($request->input('code') == 'Created') {
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();
            }

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

    public function fetchLicense(Request $request)
    {
        $licenses = db::connection('ympimis_2')->select("SELECT
            id,
            license_id,
            category,
            license_category,
            department,
            employee_id,
            employee_name,
            compliance,
            license_number,
            location,
            frequency,
            IFNULL(valid_from, '') AS valid_from,
            IFNULL(valid_to, '') AS valid_to,
            IFNULL(TIMESTAMPDIFF( DAY, now(), valid_to ), '') AS diff,
            IFNULL(DATE_FORMAT(DATE_SUB(valid_to, INTERVAL reminder DAY), '%Y-%m-01'), '') as month_reminder,
            reminder,
            status,
            remark,
            secrecy,
            creator_id,
            creator_name,
            created_by,
            deleted_at,
            created_at,
            updated_at
            FROM
            licenses
            WHERE
            deleted_at IS NULL
            AND category = '" . $request->get('category') . "'");

        $license_logs = db::connection('ympimis_2')->table('license_logs')
            ->where('category', '=', $request->get('category'))
            ->whereNull('deleted_at')
            ->get();

        $license_attachments = db::connection('ympimis_2')->table('license_attachments')
            ->whereNull('deleted_at')
            ->get();

        $response = array(
            'status' => true,
            'licenses' => $licenses,
            'license_logs' => $license_logs,
            'license_attachments' => $license_attachments,
        );
        return Response::json($response);
    }

    public function mailTradeAgreement()
    {

        $trade_agreements = db::connection('ympimis_2')
            ->select("SELECT
            mtas.id,
            mtas.vendor_code,
            mtas.vendor_name,
            mtas.email,
            mtas.location,
            mtas.annual_purchase,
            mtas.category,
            mtas.status_vendor,
            mtas.pgr,
            mtas.pgr_name,
            IFNULL(mtas.pic_id, '') as pic_id,
            IFNULL(mtas.pic_name, '') as pic_name,
            mtas.currency,
            mtas.payment_term,
            mtas.old_version,
            IFNULL(mtas.sent_at, '') as sent_at,
            IFNULL(mtas.sign_at, '') as sign_at,
            IFNULL(mtas.pre_ycj_at, '') as pre_ycj_at,
            IFNULL(mtas.app_ycj_at, '') as app_ycj_at,
            IFNULL(mtas.app_dir_at, '') as app_dir_at,
            mtas.progress,
            mtas.status,
            mtas.amendment,
            mtas.version,
            mtas.remark,
            IFNULL(mtas.att_id, '') as att_id,
            IFNULL(mtas.att_en, '') as att_en,
            mtas.created_by,
            mtas.deleted_at,
            mtas.created_at,
            mtas.updated_at,
            TIMESTAMPDIFF( DAY, sent_at, date( now()) ) AS count_sent_at
            FROM
            mtas
            WHERE status_vendor = 'Active'
            AND deleted_at IS NULL
            AND status = 'Open'
            ORDER BY vendor_name ASC");

        $data = [
            'trade_agreements' => $trade_agreements,
        ];

        $employees = db::select("SELECT
            u.email
            FROM
            employee_syncs AS es
            LEFT JOIN users AS u ON u.username = es.employee_id
            WHERE
            ( es.department = 'Procurement Department' OR es.department = 'Purchasing Control Department' )
            AND end_date IS NULL");

        $mail_to = array();

        foreach ($employees as $employee) {
            array_push($mail_to, $employee->email);
        }

        Mail::to($mail_to)
            ->cc(['budhi.apriyanto@music.yamaha.com', 'youichi.oyama@music.yamaha.com'])
            ->bcc('ympi-mis-ML@music.yamaha.com')
            ->send(new SendEmail($data, 'trade_agreement'));

// return view('licenses.trade_agreement_mail', array(
//     'data' => $data
// ))->with('page', 'Trade Agreeement');
    }

    public function indexTradeAgreementMonitoring()
    {
        $title = "Master Transaction Agreement (MTA)";
        $title_jp = "取引基本契約書";

        $employees = db::select("SELECT
            *
            FROM
            employee_syncs AS es
            WHERE
            ( es.department = 'Procurement Department' OR es.department = 'Purchasing Control Department' )
            AND end_date IS NULL");

        return view('licenses.trade_agreement', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'categories' => $this->ta_category,
        ))->with('page', 'Trade Agreeement');
    }

    public function indexTradeAgreementList()
    {
        $title = "Supplier List";
        $title_jp = "";

        return view('licenses.trade_agreement_list', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Trade Agreeement');
    }

    public function indexMonitoringCMS()
    {
        $title = "CMS Response Monitoring";
        $title_jp = "";

        return view('licenses.monitoring_cms', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Trade Agreeement');
    }

    public function indexTradeAgreement()
    {
        $title = "Master Transaction Agreement (MTA)";
        $title_jp = "取引基本契約書";

        return view('licenses.trade_agreement_index', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Trade Agreeement');
    }

    public function fetchMonitoringCMS(Request $request)
    {
        $logs = db::connection("ympimis_2")->select("SELECT
            mta_cms_logs.vendor_code,
            mtas.vendor_name,
            mtas.pic_id,
            mtas.pic_name,
            concat(
                date_format( mta_cms_logs.created_at, '%Y' ),
                ' Q',
                QUARTER ( mta_cms_logs.created_at )) AS q
            FROM
            mta_cms_logs
            LEFT JOIN mtas ON mtas.vendor_code = mta_cms_logs.vendor_code
            GROUP BY
            mta_cms_logs.vendor_code,
            concat(
                'Q',
                QUARTER ( mta_cms_logs.created_at )),
            mtas.vendor_name,
            mtas.pic_id,
            mtas.pic_name,
            mta_cms_logs.created_at
            ORDER BY mta_cms_logs.created_at ASC");

        $answers = db::connection("mobile")->select("SELECT
            tanggal,
            name as vendor_code,
            company as vendor_name,
            answer,
            file,
            created_at,
            concat(
                date_format( created_at, '%Y' ),
                ' Q',
                QUARTER ( created_at )) AS q
            FROM
            cms_vendors
            ");

        $charts = array();
        $responses = array();

        foreach ($logs as $log) {
            $response = "";
            $created_at = "";

            foreach ($answers as $answer) {
                if ($answer->vendor_code == $log->vendor_code && $answer->q == $log->q) {
                    $response = $answer->answer;
                    $created_at = $answer->created_at;

                }
            }

            array_push($charts, [
                'vendor_code' => $log->vendor_code,
                'q' => $log->q,
                'answer' => $response,
            ]);

            array_push($responses, [
                'q' => $log->q,
                'vendor_code' => $log->vendor_code,
                'vendor_name' => $log->vendor_name,
                'pic_id' => $log->pic_id,
                'pic_name' => $log->pic_name,
                'answer' => $response,
                'created_at' => $created_at,
            ]);

        }

        $response = array(
            'status' => true,
            'responses' => $responses,
            'logs' => $logs,
            'charts' => $charts,
        );
        return Response::json($response);
    }

    public function editTradeAgreement(Request $request)
    {
        try {

            $sent_at_ev = null;
            $sign_at_ev = null;
            $pre_ycj_at_ev = null;
            $app_ycj_at_ev = null;
            $app_dir_at_ev = null;

            $att_id = null;
            $att_en = null;
            $amendment_id = null;
            $amendment_en = null;

            if (count($request->file('sent_at_ev')) > 0) {
                $file_destination_sent_at = 'trade_agreements/sent_at';
                $file_sent_at = $request->file('sent_at_ev');
                $filename_sent_at = $request->input('vendor_code') . '_sent_at.' . $request->input('sent_at_ext');
                $file_sent_at->move($file_destination_sent_at, $filename_sent_at);
                $sent_at_ev = $filename_sent_at;

                $update_sent_at = db::connection('ympimis_2')
                    ->table('mtas')
                    ->where('vendor_code', '=', $request->input('vendor_code'))
                    ->update([
                        'sent_at_ev' => $sent_at_ev,
                    ]);
            }

            if (count($request->file('sign_at_ev')) > 0) {
                $file_destination_sign_at = 'trade_agreements/sign_at';
                $file_sign_at = $request->file('sign_at_ev');
                $filename_sign_at = $request->input('vendor_code') . '_sign_at.' . $request->input('sign_at_ext');
                $file_sign_at->move($file_destination_sign_at, $filename_sign_at);
                $sign_at_ev = $filename_sign_at;

                $update_sign_at = db::connection('ympimis_2')
                    ->table('mtas')
                    ->where('vendor_code', '=', $request->input('vendor_code'))
                    ->update([
                        'sign_at_ev' => $sign_at_ev,
                    ]);
            }

            if (count($request->file('pre_ycj_at_ev')) > 0) {
                $file_destination_pre_ycj_at = 'trade_agreements/pre_ycj_at';
                $file_pre_ycj_at = $request->file('pre_ycj_at_ev');
                $filename_pre_ycj_at = $request->input('vendor_code') . '_pre_ycj_at.' . $request->input('pre_ycj_at_ext');
                $file_pre_ycj_at->move($file_destination_pre_ycj_at, $filename_pre_ycj_at);
                $pre_ycj_at_ev = $filename_pre_ycj_at;

                $update_pre_ycj_at = db::connection('ympimis_2')
                    ->table('mtas')
                    ->where('vendor_code', '=', $request->input('vendor_code'))
                    ->update([
                        'pre_ycj_at_ev' => $pre_ycj_at_ev,
                    ]);
            }

            if (count($request->file('app_ycj_at_ev')) > 0) {
                $file_destination_app_ycj_at = 'trade_agreements/app_ycj_at';
                $file_app_ycj_at = $request->file('app_ycj_at_ev');
                $filename_app_ycj_at = $request->input('vendor_code') . '_app_ycj_at.' . $request->input('app_ycj_at_ext');
                $file_app_ycj_at->move($file_destination_app_ycj_at, $filename_app_ycj_at);
                $app_ycj_at_ev = $filename_app_ycj_at;

                $update_app_ycj_at = db::connection('ympimis_2')
                    ->table('mtas')
                    ->where('vendor_code', '=', $request->input('vendor_code'))
                    ->update([
                        'app_ycj_at_ev' => $app_ycj_at_ev,
                    ]);
            }

            if (count($request->file('app_dir_at_ev')) > 0) {
                $file_destination_app_dir_at = 'trade_agreements/app_dir_at';
                $file_app_dir_at = $request->file('app_dir_at_ev');
                $filename_app_dir_at = $request->input('vendor_code') . '_app_dir_at.' . $request->input('app_dir_at_ext');
                $file_app_dir_at->move($file_destination_app_dir_at, $filename_app_dir_at);
                $app_dir_at_ev = $filename_app_dir_at;

                $update_app_dir_at = db::connection('ympimis_2')
                    ->table('mtas')
                    ->where('vendor_code', '=', $request->input('vendor_code'))
                    ->update([
                        'app_dir_at_ev' => $app_dir_at_ev,
                    ]);
            }

//FILE & AMENDMENT
            if (count($request->file('att_id')) > 0) {
                $file_destination_att_id = 'trade_agreements';
                $file_att_id = $request->file('att_id');
                $filename_att_id = $request->input('vendor_code') . '_id.' . $request->input('att_id_ext');
                $file_att_id->move($file_destination_att_id, $filename_att_id);
                $att_id = $filename_att_id;

                $update_att_id = db::connection('ympimis_2')
                    ->table('mtas')
                    ->where('vendor_code', '=', $request->input('vendor_code'))
                    ->update([
                        'att_id' => $att_id,
                    ]);
            }
            if (count($request->file('att_en')) > 0) {
                $file_destination_att_en = 'trade_agreements';
                $file_att_en = $request->file('att_en');
                $filename_att_en = $request->input('vendor_code') . '_en.' . $request->input('att_en_ext');
                $file_att_en->move($file_destination_att_en, $filename_att_en);
                $att_en = $filename_att_en;

                $update_att_en = db::connection('ympimis_2')
                    ->table('mtas')
                    ->where('vendor_code', '=', $request->input('vendor_code'))
                    ->update([
                        'att_en' => $att_en,
                    ]);
            }

            if (count($request->file('amendment_id')) > 0) {
                $file_destination_amendment_id = 'trade_agreements';
                $file_amendment_id = $request->file('amendment_id');
                $filename_amendment_id = $request->input('vendor_code') . '_am_id.' . $request->input('amendment_id_ext');
                $file_amendment_id->move($file_destination_amendment_id, $filename_amendment_id);
                $amendment_id = $filename_amendment_id;

                $update_amendment_id = db::connection('ympimis_2')
                    ->table('mtas')
                    ->where('vendor_code', '=', $request->input('vendor_code'))
                    ->update([
                        'amendment_id' => $amendment_id,
                    ]);
            }
            if (count($request->file('amendment_en')) > 0) {
                $file_destination_amendment_en = 'trade_agreements';
                $file_amendment_en = $request->file('amendment_en');
                $filename_amendment_en = $request->input('vendor_code') . '_am_en.' . $request->input('amendment_en_ext');
                $file_amendment_en->move($file_destination_amendment_en, $filename_amendment_en);
                $amendment_en = $filename_amendment_en;

                $update_amendment_en = db::connection('ympimis_2')
                    ->table('mtas')
                    ->where('vendor_code', '=', $request->input('vendor_code'))
                    ->update([
                        'amendment_en' => $amendment_en,
                    ]);
            }

            $trade_agreement = db::connection('ympimis_2')
                ->table('mtas')
                ->where('vendor_code', '=', $request->input('vendor_code'))
                ->update([
                    'vendor_code' => $request->input('vendor_code'),
                    'vendor_name' => $request->input('vendor_name'),
// 'email' => $request->input('email'),
                    'location' => $request->input('location'),
                    'location_group' => $request->input('location_group'),
                    'annual_purchase' => $request->input('annual_purchase'),
                    'category' => $request->input('category'),
                    'status_vendor' => $request->input('status_vendor'),
                    'pgr' => $request->input('pgr'),
                    'pgr_name' => $request->input('pgr_name'),
                    'pic_id' => explode('_', $request->input('pic'))[0],
                    'pic_name' => explode('_', $request->input('pic'))[1],
                    'currency' => $request->input('currency'),
                    'payment_term' => $request->input('payment_term'),
                    'old_version' => $request->input('old_version'),
                    'remark' => $request->input('remark'),
                    'sent_at' => $request->input('sent_at'),
                    'sign_at' => $request->input('sign_at'),
                    'pre_ycj_at' => $request->input('pre_ycj_at'),
                    'app_ycj_at' => $request->input('app_ycj_at'),
                    'app_dir_at' => $request->input('app_dir_at'),
                    'progress' => $request->input('progress'),
                    'status' => $request->input('status'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Data has been updated.',
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

    public function createTradeAgreement(Request $request)
    {
        try {

// $file_destination = 'trade_agreements/sent_at';
// $file = $request->file('evidence');
// $filename = $request->input('vendor_code').'_sent_at.'.$request->input('extension');
// $file->move($file_destination, $filename);

            $pre_ycj_at = null;
            $app_ycj_at = null;

            if ($request->input('pgr_name') == 'Indirect Material') {
                $pre_ycj_at = 'No Need';
                $app_ycj_at = 'No Need';
            }

            $trade_agreement = db::connection('ympimis_2')
                ->table('mtas')
                ->insert([
                    'vendor_code' => $request->input('vendor_code'),
                    'vendor_name' => $request->input('vendor_name'),
                    'vendor_name_sap' => $request->input('vendor_name'),
// 'email' => $request->input('email'),
                    'location' => $request->input('location'),
                    'location_group' => $request->input('location_group'),
                    'annual_purchase' => $request->input('annual_purchase'),
                    'category' => $request->input('category'),
                    'status_vendor' => $request->input('status_vendor'),
                    'pgr' => $request->input('pgr'),
                    'pgr_name' => $request->input('pgr_name'),
                    'pic_id' => explode('_', $request->input('pic'))[0],
                    'pic_name' => explode('_', $request->input('pic'))[1],
                    'currency' => $request->input('currency'),
                    'payment_term' => $request->input('payment_term'),
                    'remark' => $request->input('remark'),
                    'old_version' => 0,
// 'sent_at' => $request->input('sent_at'),
// 'sent_at_ev' => $filename,
                    'pre_ycj_at' => $pre_ycj_at,
                    'app_ycj_at' => $app_ycj_at,
                    'progress' => 'Not Sent',
                    'status' => 'Open',
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Data has been saved.\n Email notification about CMS has been sent.',
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

    public function updateTradeAgreementProgress(Request $request)
    {
        try {
            $file_destination = 'trade_agreements/' . $request->input('category');
            $file = $request->file('evidence');
            $filename = $request->input('vendor_code') . '_' . $request->input('category') . '.' . $request->input('extension');
            $file->move($file_destination, $filename);

            $progress = "";
            $status = "Open";

            if ($request->input('category') == 'sent_at') {
                $progress == 'Under Check';
                $status = "Open";
            }
            if ($request->input('category') == 'sign_at') {
                $progress == 'Signed by Supplier';
                $status = "Open";
            }
            if ($request->input('category') == 'pre_ycj_at') {
                $progress == 'YCJ Approval';
                $status = "Open";
            }
            if ($request->input('category') == 'app_ycj_at') {
                $progress == 'YCJ Approval';
                $status = "Open";
            }
            if ($request->input('category') == 'app_dir_at') {
                $progress == 'Signed';
                $status = "Closed";
            }

            $trade_agreement = db::connection('ympimis_2')->table('mtas')
                ->where('vendor_code', '=', $request->input('vendor_code'))
                ->update([
                    $request->input('category') => $request->input('due'),
                    $request->input('category') . '_ev' => $filename,
                    'progress' => $progress,
                    'status' => $status,
                ]);

            $response = array(
                'status' => true,
                'file_destination' => $file_destination,
                'filename' => $file,
                'message' => 'Agreement Progress Updated.',
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

    public function fetchTradeAgreement(Request $request)
    {

        $trade_agreements = db::connection('ympimis_2')
            ->select("SELECT
            mtas.id,
            mtas.vendor_code,
            mtas.vendor_name,
            mtas.vendor_name_sap,
            mtas.email,
            mtas.location,
            mtas.location_group,
            mtas.annual_purchase,
            mtas.category,
            mtas.status_vendor,
            mtas.pgr,
            mtas.pgr_name,
            IFNULL(mtas.pic_id, '') as pic_id,
            IFNULL(mtas.pic_name, '') as pic_name,
            mtas.currency,
            mtas.payment_term,
            mtas.old_version,
            IFNULL(mtas.sent_at, '') as sent_at,
            IFNULL(mtas.sign_at, '') as sign_at,
            IFNULL(mtas.pre_ycj_at, '') as pre_ycj_at,
            IFNULL(mtas.app_ycj_at, '') as app_ycj_at,
            IFNULL(mtas.app_dir_at, '') as app_dir_at,
            mtas.progress,
            mtas.status,
            mtas.amendment,
            mtas.version,
            mtas.remark,
            IFNULL(mtas.att_id, '') as att_id,
            IFNULL(mtas.att_en, '') as att_en,
            IFNULL(mtas.amendment_id, '') as amendment_id,
            IFNULL(mtas.amendment_en, '') as amendment_en,
            mtas.created_by,
            mtas.deleted_at,
            mtas.created_at,
            mtas.updated_at,
            IFNULL(mtas.sent_at_ev, '') as sent_at_ev,
            IFNULL(mtas.sign_at_ev, '') as sign_at_ev,
            IFNULL(mtas.pre_ycj_at_ev, '') as pre_ycj_at_ev,
            IFNULL(mtas.app_ycj_at_ev, '') as app_ycj_at_ev,
            IFNULL(mtas.app_dir_at_ev, '') as app_dir_at_ev,
            TIMESTAMPDIFF( DAY, sent_at, date( now()) ) AS count_sent_at
            FROM
            mtas
            WHERE mtas.deleted_at IS NULL
            ORDER BY
            mtas.id ASC");

        $response = array(
            'status' => true,
            'trade_agreements' => $trade_agreements,
        );
        return Response::json($response);
    }

    public function fetchTradeAgreementList(Request $request)
    {

        $trade_agreements = db::connection('ympimis_2')
            ->select("SELECT
            mtas.id,
            mtas.vendor_code,
            mtas.vendor_name,
            mtas.email,
            mtas.location,
            mtas.location_group,
            mtas.annual_purchase,
            mtas.category,
            mtas.status_vendor,
            mtas.pgr,
            mtas.pgr_name,
            IFNULL(mtas.pic_id, '') as pic_id,
            IFNULL(mtas.pic_name, '') as pic_name,
            mtas.currency,
            mtas.payment_term,
            mtas.old_version,
            IFNULL(mtas.sent_at, '') as sent_at,
            IFNULL(mtas.sign_at, '') as sign_at,
            IFNULL(mtas.pre_ycj_at, '') as pre_ycj_at,
            IFNULL(mtas.app_ycj_at, '') as app_ycj_at,
            IFNULL(mtas.app_dir_at, '') as app_dir_at,
            mtas.progress,
            mtas.status,
            mtas.amendment,
            mtas.version,
            mtas.remark,
            IFNULL(mtas.att_id, '') as att_id,
            IFNULL(mtas.att_en, '') as att_en,
            IFNULL(mtas.amendment_id, '') as amendment_id,
            IFNULL(mtas.amendment_en, '') as amendment_en,
            mtas.created_by,
            mtas.deleted_at,
            mtas.created_at,
            mtas.updated_at,
            IFNULL(mtas.sent_at_ev, '') as sent_at_ev,
            IFNULL(mtas.sign_at_ev, '') as sign_at_ev,
            IFNULL(mtas.pre_ycj_at_ev, '') as pre_ycj_at_ev,
            IFNULL(mtas.app_ycj_at_ev, '') as app_ycj_at_ev,
            IFNULL(mtas.app_dir_at_ev, '') as app_dir_at_ev,
            TIMESTAMPDIFF( DAY, sent_at, date( now()) ) AS count_sent_at
            FROM
            WHERE mtas.deleted_at IS NULL
            mta_supplier_lists as mtas");

        $response = array(
            'status' => true,
            'trade_agreements' => $trade_agreements,
        );
        return Response::json($response);
    }

}
