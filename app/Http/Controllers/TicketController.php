<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\EmployeeSync;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\Ticket;
use App\TicketApprover;
use App\TicketAttachment;
use App\TicketCostdown;
use App\TicketEquipment;
use App\TicketTimeline;
use App\User;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Response;

class TicketController extends Controller
{
    private $category;
    private $priority;
    private $difficulty;
    private $status;
    private $costdown;

    public function __construct()
    {
        $this->middleware('auth');
        $this->category = [
            'Perbaikan Hardware/Software',
            'Perbaikan Jaringan',
            'Pengadaan Hardware/Software',
            'Pemasangan Hardware/Software',
            'Pembuatan Aplikasi Baru',
            'Pengembangan Aplikasi Lama',
            'Perbaikan Aplikasi Error/Bug',
        ];

        $this->category_new = [
            '(Software) Buat Aplikasi',
            '(Software) Perbaiki Aplikasi',
            '(Hardware) Pemasangan',
            '(Hardware) Perbaikan',
            '(Hardware) Pengadaan',
        ];
        $this->priority_new = [
            'Very High',
            'High',
            'Normal',
        ];
        $this->priority = [
            'Very High',
            'High',
            'Normal',
            'Low',
        ];
        $this->difficulty = [
            'S (>3 Months)',
            'A (<3 Months)',
            'B (<1 Month)',
            'C (<1 Week)',
        ];
        $this->status = [
            'Approval',
            'Waiting',
            'InProgress',
            'OnHold',
            'Finished',
        ];
        $this->costdown = [
            'Manpower',
            'Overtime',
            'Efficiency',
            'Material',
            'Safety',
            '5S',
            'Quality',
            'Delivery',
        ];
        $this->timeline_category = [
            'Meeting',
            'Installation',
            'Repair',
            'Analisa Sistem',
            'Desain Sistem',
            'Programming',
            'Pre-Implementasi',
            'Training',
            'Trial',
            'Go Live',
        ];
        $this->form_approver = [
            ['form_name' => 'FORM PERMINTAAN IJIN MEMBAWA KOMPUTER KELUAR PERUSAHAAN', 'approver' => 'Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN IJIN MEMBAWA KOMPUTER KELUAR PERUSAHAAN', 'approver' => 'Deputy General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN IJIN MEMBAWA KOMPUTER KELUAR PERUSAHAAN', 'approver' => 'General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN IJIN MEMBAWA KOMPUTER KELUAR PERUSAHAAN', 'approver' => 'Chief MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN IJIN MEMBAWA KOMPUTER KELUAR PERUSAHAAN', 'approver' => 'Manager MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN IJIN MEMBAWA KOMPUTER KELUAR PERUSAHAAN', 'approver' => 'Director', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN IJIN MEMBAWA KOMPUTER KELUAR PERUSAHAAN', 'approver' => 'MIS', 'remark' => 'Confirmed By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN IJIN MEMBAWA KOMPUTER KELUAR PERUSAHAAN', 'approver' => 'Security', 'remark' => 'Confirmed By', 'pic' => 'all'],

            ['form_name' => 'EMAIL ADDRESS REQUEST SHEET', 'approver' => 'Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'EMAIL ADDRESS REQUEST SHEET', 'approver' => 'Deputy General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'EMAIL ADDRESS REQUEST SHEET', 'approver' => 'General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'EMAIL ADDRESS REQUEST SHEET', 'approver' => 'Chief MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'EMAIL ADDRESS REQUEST SHEET', 'approver' => 'Manager MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'EMAIL ADDRESS REQUEST SHEET', 'approver' => 'Director', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'EMAIL ADDRESS REQUEST SHEET', 'approver' => 'MIS', 'remark' => 'Confirmed By', 'pic' => 'all'],

            ['form_name' => 'INTERNET ACCESS REQUEST SHEET', 'approver' => 'Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'INTERNET ACCESS REQUEST SHEET', 'approver' => 'Deputy General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'INTERNET ACCESS REQUEST SHEET', 'approver' => 'General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'INTERNET ACCESS REQUEST SHEET', 'approver' => 'Chief MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'INTERNET ACCESS REQUEST SHEET', 'approver' => 'Manager MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'INTERNET ACCESS REQUEST SHEET', 'approver' => 'Director', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'INTERNET ACCESS REQUEST SHEET', 'approver' => 'MIS', 'remark' => 'Confirmed By', 'pic' => 'all'],

            ['form_name' => 'FORM NEW MENU HRQ OR YMPICOID', 'approver' => 'Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM NEW MENU HRQ OR YMPICOID', 'approver' => 'Deputy General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM NEW MENU HRQ OR YMPICOID', 'approver' => 'General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM NEW MENU HRQ OR YMPICOID', 'approver' => 'Chief MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM NEW MENU HRQ OR YMPICOID', 'approver' => 'Manager MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM NEW MENU HRQ OR YMPICOID', 'approver' => 'Director', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM NEW MENU HRQ OR YMPICOID', 'approver' => 'MIS', 'remark' => 'Confirmed By', 'pic' => 'all'],

            ['form_name' => 'USB PORT ACCESS REQUEST SHEET', 'approver' => 'Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'USB PORT ACCESS REQUEST SHEET', 'approver' => 'Deputy General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'USB PORT ACCESS REQUEST SHEET', 'approver' => 'General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'USB PORT ACCESS REQUEST SHEET', 'approver' => 'Chief MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'USB PORT ACCESS REQUEST SHEET', 'approver' => 'Manager MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'USB PORT ACCESS REQUEST SHEET', 'approver' => 'Director', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'USB PORT ACCESS REQUEST SHEET', 'approver' => 'MIS', 'remark' => 'Confirmed By', 'pic' => 'all'],

            ['form_name' => 'FORM PERMINTAAN PERANGKAT IT', 'approver' => 'Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN PERANGKAT IT', 'approver' => 'Deputy General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN PERANGKAT IT', 'approver' => 'General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN PERANGKAT IT', 'approver' => 'Chief MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN PERANGKAT IT', 'approver' => 'Manager MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN PERANGKAT IT', 'approver' => 'MIS', 'remark' => 'Confirmed By', 'pic' => 'all'],

            ['form_name' => 'SAP ROLE REQUEST SHEET', 'approver' => 'Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'SAP ROLE REQUEST SHEET', 'approver' => 'Deputy General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'SAP ROLE REQUEST SHEET', 'approver' => 'General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'SAP ROLE REQUEST SHEET', 'approver' => 'Chief MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'SAP ROLE REQUEST SHEET', 'approver' => 'Manager MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'SAP ROLE REQUEST SHEET', 'approver' => 'Director', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'SAP ROLE REQUEST SHEET', 'approver' => 'MIS', 'remark' => 'Confirmed By', 'pic' => 'all'],

            ['form_name' => 'VPN ACCESS REQUEST SHEET', 'approver' => 'Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'VPN ACCESS REQUEST SHEET', 'approver' => 'Deputy General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'VPN ACCESS REQUEST SHEET', 'approver' => 'General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'VPN ACCESS REQUEST SHEET', 'approver' => 'Chief MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'VPN ACCESS REQUEST SHEET', 'approver' => 'Manager MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'VPN ACCESS REQUEST SHEET', 'approver' => 'Director', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'VPN ACCESS REQUEST SHEET', 'approver' => 'MIS', 'remark' => 'Confirmed By', 'pic' => 'all'],

            ['form_name' => 'MIRAI USER ROLE REQUEST SHEET', 'approver' => 'Manager', 'remark' => 'Approved By', 'pic' => 'all', 'pic' => 'all'],
            ['form_name' => 'MIRAI USER ROLE REQUEST SHEET', 'approver' => 'Deputy General Manager', 'remark' => 'Approved By', 'pic' => 'all', 'pic' => 'all'],
            ['form_name' => 'MIRAI USER ROLE REQUEST SHEET', 'approver' => 'General Manager', 'remark' => 'Approved By', 'pic' => 'all', 'pic' => 'all'],
            ['form_name' => 'MIRAI USER ROLE REQUEST SHEET', 'approver' => 'Chief MIS', 'remark' => 'Approved By', 'pic' => 'all', 'pic' => 'all'],
            ['form_name' => 'MIRAI USER ROLE REQUEST SHEET', 'approver' => 'Manager MIS', 'remark' => 'Approved By', 'pic' => 'all', 'pic' => 'all'],
            ['form_name' => 'MIRAI USER ROLE REQUEST SHEET', 'approver' => 'MIS', 'remark' => 'Confirmed By', 'pic' => 'all', 'pic' => 'all'],

            ['form_name' => 'YMPICOID USER ROLE REQUEST SHEET', 'approver' => 'Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'YMPICOID USER ROLE REQUEST SHEET', 'approver' => 'Deputy General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'YMPICOID USER ROLE REQUEST SHEET', 'approver' => 'General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'YMPICOID USER ROLE REQUEST SHEET', 'approver' => 'Chief MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'YMPICOID USER ROLE REQUEST SHEET', 'approver' => 'Manager MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'YMPICOID USER ROLE REQUEST SHEET', 'approver' => 'MIS', 'remark' => 'Confirmed By', 'pic' => 'all'],

            ['form_name' => 'FORM PERMINTAAN AKSES CCTV BEA CUKAI', 'approver' => 'Manager', 'remark' => 'Confirmed By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN AKSES CCTV BEA CUKAI', 'approver' => 'Manager MIS', 'remark' => 'Confirmed By', 'pic' => 'all'],
            ['form_name' => 'FORM PERMINTAAN AKSES CCTV BEA CUKAI', 'approver' => 'MIS', 'remark' => 'Knowing By', 'pic' => 'all'],

            ['form_name' => 'NEW APPLICATION INSTALLATION REQUEST FORM', 'approver' => 'Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'NEW APPLICATION INSTALLATION REQUEST FORM', 'approver' => 'Deputy General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'NEW APPLICATION INSTALLATION REQUEST FORM', 'approver' => 'General Manager', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'NEW APPLICATION INSTALLATION REQUEST FORM', 'approver' => 'Chief MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'NEW APPLICATION INSTALLATION REQUEST FORM', 'approver' => 'Manager MIS', 'remark' => 'Approved By', 'pic' => 'all'],
            ['form_name' => 'NEW APPLICATION INSTALLATION REQUEST FORM', 'approver' => 'MIS', 'remark' => 'Confirmed By', 'pic' => 'all'],
        ];
        $this->form_descriptions = [
            ['form_name' => 'FORM PERMINTAAN IJIN MEMBAWA KOMPUTER KELUAR PERUSAHAAN', 'description' => '<p>Dengan ini mengajukan permintaan untuk membawa komputer keluar perusahaan. <br/>Adapun komputer tersebut saya perlukan guna kepentingan pekerjaan dan segala tanggung jawab terhadap kerusakan dan kehilangan adalah tanggung jawab saya.
            <br/>
            <br/>
            Tanggal Mulai :
            <br/>
            Tanggal Sampai :
            <br/>
            Jenis Komputer : () Komputer Business Trip () Komputer User
            <br/>
            Nama Komputer :
            <br/>
            <br/>
			## Contoh Pengisian
            <br/>
			## Tanggal Mulai : 20 Juni 2022
            <br/>
			## Tanggal Sampai : 23 Juni 2022
            <br/>
			## Jenis Komputer : () Komputer Business Trip (X) Komputer User
            <br/>
			## Nama Komputer : ympimis-02
            </p>', ],

            ['form_name' => 'FORM PERMINTAAN PERANGKAT IT', 'description' => '<p>Dengan ini mengajukan permintaan perangkat IT. <br/>Adapun perangkat tersebut saya perlukan guna kepentingan pekerjaan dan segala tanggung jawab terhadap kerusakan dan kehilangan adalah tanggung jawab saya.
            <br/>
            <br/>
            Keperluan :
            <br/>
            Jenis Perangkat : () Laptop () Komputer () Tablet
            <br/>
            <br/>
            ## Contoh Pengisian
            <br/>
            ## Keperluan : Patrol Genba
            <br/>
            ## Jenis Perangkat : () Laptop () Komputer (X) Tablet
            </p>', ],

            ['form_name' => 'EMAIL ADDRESS REQUEST SHEET', 'description' => '<p>Request an Email address with company’s domain from YCJ.<br/>I will assure and responsible to keep and use the e-mail address proportionally.
            <br/>
            <br/>
            New Email Address : (Filled by MIS Member)
            </p>', ],

            ['form_name' => 'INTERNET ACCESS REQUEST SHEET', 'description' => '<p>
            Reason : ...</p>
            <p>Request an Internet Access with company&rsquo;s domain from YCJ.<br>
            I will assure and responsible to keep and use the Internet proportionally.</p>
            <br/>
            <br/>
			## Contoh Pengisian
            <br/>
			## Reason : Mencari panduan untuk mengembangkan software', ],

            ['form_name' => 'SAP ROLE REQUEST SHEET', 'description' => '<p>
            Module : () SD () PPMM () FICO</p>
            <p>Request an SAP Role.<br>
            I will assure and responsible to keep and use the SAP Role proportionally.</p>
            <br/>
            <br/>
			## Contoh Pengisian
            <br/>
			## Module : () SD (X) PPMM () FICO', ],

            ['form_name' => 'VPN ACCESS REQUEST SHEET', 'description' => '<p>
            Reason : ...</p>
            <p>Request an VPN Access.<br>
            I will assure and responsible to keep and use the VPN Access proportionally.</p>
            <br/>
            <br/>
			## Contoh Pengisian
            <br/>
			## Reason : Mengakses folder server untuk mengolah data efisiensi.', ],

            ['form_name' => 'USB PORT ACCESS REQUEST SHEET', 'description' => '<p>
            Reason : ...</p>
            <p>Computer Name : ...</p>
            <p>Request an PORT USB ACCESS.<br>
            I will assure and responsible to keep and use the PORT USB Access proportionally.</p>

            <br/>
            <br/>
			## Contoh Pengisian
            <br/>
			## Reason : Transfer Data dari Memori Kamera Ke Komputer
            <br/>
			## Computer Name : ympimis-02', ],

            ['form_name' => 'MIRAI USER ROLE REQUEST SHEET', 'description' => '<p>
            Role : ...<br/>
            Reason : ...</p>
            <p>Request an MIRAI Role Access<br>
            I will assure and responsible to keep and use the role properly.</p>
            <br/>
            <br/>
			## Contoh Pengisian
            <br/>
			## Role : Purchasing Admin
            <br/>
			## Reason : Keperluan Request Pembelian di MIRAI', ],

            ['form_name' => 'YMPICOID USER ROLE REQUEST SHEET', 'description' => '<p>
            Role : ...<br/>
            Reason : ...</p>
            <p>Request an ympi.co.id Role Access<br>
            I will assure and responsible to keep and use the role properly.</p>
            <br/>
            <br/>
			## Contoh Pengisian
            <br/>
			## Role : Admin
            <br/>
			## Reason : Untuk meresset password karyawan yang lupa password.', ],

            ['form_name' => 'FORM PERMINTAAN AKSES CCTV BEA CUKAI', 'description' => '<p>
            I request for Customs CCTV access and monitoring for work purposes.<br><br>
            </p>', ],

            ['form_name' => 'FORM NEW MENU HRQ OR YMPICOID', 'description' => '<p>
            Ask for permission to add new menu as below:<br><br>

            New Menu: ...<br>
            Purpose: ...</p>

            New menu will be added to () HRQ () YMPI.CO.ID<br><br>
            MIS Department will assure and responsible to keep HRQ/YMPICOID run according to YMPI`s IT Policy.
            <br/>
            <br/>
			## Contoh Pengisian
            <br/>
			## New Menu: Survey Ukuran Seragam<br>
			## Purpose: Untuk mengetahui permintaan ukuran seragam karyawan terbaru<br>

			## New menu will be added to () HRQ (X) YMPI.CO.ID', ],

            ['form_name' => 'NEW APPLICATION INSTALLATION REQUEST FORM', 'description' => '<p>
            Application Name : ...</p>
            <p>Reason : ...</p>
            <p>Computer Name : ...</p>
            <p>Request installation of new application.<br>
            I will assure and responsible to keep and use the application properly.</p>
            <br/>
            <br/>
			## Contoh Pengisian
            <br/>
            ## Application Name : Adobe Premiere Pro <br/>
			## Reason : Editing Video IK sebagai dokumentasi IK Digital <br/>
			## Computer Name : ympimis-02 <br/>', ],
        ];
    }

    public function uploadGuideline(Request $request)
    {
        try {
            $file_destination = 'files/manual';
            $filename = null;

            if ($request->file('attachment')) {
                $file = $request->file('attachment');
                $filename = $request->input('ticket_id') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);
            }

            $update_ticket = db::table('tickets')
                ->where('ticket_id', '=', $request->input('ticket_id'))
                ->update([
                    'guideline_file' => $filename,
                ]);

            $response = array(
                'status' => true,
                'message' => 'Guideline has been uploaded.',
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

    public function fetchTicketLog(Request $request)
    {
        $tickets = db::select("SELECT
         *,
         IF(estimated_due_date_to is null, due_date_to, estimated_due_date_to) as finished_date
         FROM
         tickets
         WHERE
         STATUS <> 'Rejected'
         and remark not in ('hardware', 'software')
         ORDER BY
         finished_date DESC");

        $response = array(
            'status' => true,
            'tickets' => $tickets,
        );
        return Response::json($response);
    }

    public function indexTicketLog()
    {
        $title = "System Update Logs";
        $title_jp = "";

        return view('about_mis.ticket.log', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'MIS Log')->with('head', 'MIS Log');
    }

    public function indexStocktakingAccount()
    {
        $title = "Accounts and Roles Stocktaking";
        $title_jp = "アカウントと業務内容の棚卸";

        $periods = db::connection('ympimis_2')->select("SELECT DISTINCT
         DATE_FORMAT( period, '%Y-%m' ) AS period_date,
         DATE_FORMAT( period, '%Y %M' ) AS period
         FROM
         mis_stocktaking_accounts
         ORDER BY
         period DESC");

        return view('about_mis.stocktaking.monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'periods' => $periods,
        ))->with('page', 'Accounts and Roles Stocktaking')->with('head', 'Accounts and Roles Stocktaking');
    }

    public function fetchStocktakingAccount(Request $request)
    {
        try {
            $accounts = db::connection('ympimis_2')->select("SELECT
                mis_stocktaking_accounts.*,
                DATE_FORMAT( mis_stocktaking_accounts.period, '%Y-%m' ) AS mon
                FROM
                mis_stocktaking_accounts
                WHERE DATE_FORMAT( mis_stocktaking_accounts.period, '%Y-%m' ) = '" . $request->get('period') . "'");

            $groups = array();
            foreach ($accounts as $account) {
                $key = $account->mon . $account->category;
                $user_total = 0;
                $user_active = 0;
                $user_inactive = 0;
                $employee_total = 0;
                $employee_active = 0;
                $employee_inactive = 0;
                $unmatch = 0;
                $handled = 0;

                if ($account->username != "") {
                    $user_total = 1;
                    if ($account->user_status == 'Active') {
                        $user_active = 1;
                    }
                    if ($account->user_status == 'Inactive') {
                        $user_inactive = 1;
                    }
                }

                if ($account->employee_id != "") {
                    $employee_total = 1;
                    if ($account->employee_status == 'Active') {
                        $employee_active = 1;
                    }
                    if ($account->employee_status == 'Inactive') {
                        $employee_inactive = 1;
                    }
                }

                if ($account->status == 'Unmatch') {
                    $unmatch = 1;
                }
                if ($account->adjusted_by != "" || $account->adjusted_by != null) {
                    $handled = 1;
                }

                if (!array_key_exists($key, $groups)) {

                    $groups[$key] = array(
                        'period' => $account->mon,
                        'category' => $account->category,
                        'user_total' => $user_total,
                        'user_active' => $user_active,
                        'user_inactive' => $user_inactive,
                        'employee_total' => $employee_total,
                        'employee_active' => $employee_active,
                        'employee_inactive' => $employee_inactive,
                        'unmatch' => $unmatch,
                        'handled' => $handled,
                    );
                } else {
                    $groups[$key]['user_total'] = $groups[$key]['user_total'] + $user_total;
                    $groups[$key]['user_active'] = $groups[$key]['user_active'] + $user_active;
                    $groups[$key]['user_inactive'] = $groups[$key]['user_inactive'] + $user_inactive;
                    $groups[$key]['employee_total'] = $groups[$key]['employee_total'] + $employee_total;
                    $groups[$key]['employee_active'] = $groups[$key]['employee_active'] + $employee_active;
                    $groups[$key]['employee_inactive'] = $groups[$key]['employee_inactive'] + $employee_inactive;
                    $groups[$key]['unmatch'] = $groups[$key]['unmatch'] + $unmatch;
                    $groups[$key]['handled'] = $groups[$key]['handled'] + $handled;
                }
            }

            $permissions = db::select("SELECT
                GROUP_CONCAT( DISTINCT p.role_code ) AS role_code,
                r.position,
                n.navigation_name
                FROM
                permissions AS p
                LEFT JOIN roles AS r ON r.role_code = p.role_code
                LEFT JOIN navigations AS n ON n.navigation_code = p.navigation_code
                WHERE
                n.remark = 1
                AND r.remark = 1
                GROUP BY
                r.position,
                n.navigation_name
                ORDER BY
                p.role_code ASC,
                n.navigation_name ASC");

            $permission_lists = db::select("SELECT
                p.role_code,
                r.position,
                n.navigation_code,
                n.navigation_name
                FROM
                permissions AS p
                LEFT JOIN roles AS r ON r.role_code = p.role_code
                LEFT JOIN navigations AS n ON n.navigation_code = p.navigation_code
                WHERE
                n.remark = 1
                AND r.remark = 1
                ORDER BY
                p.role_code ASC,
                n.navigation_name ASC");

            $navigations = db::select("SELECT DISTINCT
                navigation_name
                FROM
                navigations
                WHERE
                remark = 1");

            $navigation_lists = db::select("SELECT
                navigation_code,
                navigation_name
                FROM
                navigations
                WHERE
                remark = 1");

            $roles = ['Japanese',
                'Director',
                'General Manager',
                'Deputy General Manager',
                'Manager',
                'Coordinator',
                'Staff',
                'Leader',
                'Sub Leader',
                'Operator',
                'Canteen Staff',
                'Clinic Staff',
                'YEMI Member',
                'Buyer'];

            $role_lists = db::select("SELECT
                role_code,
                position
                FROM
                roles
                WHERE
                remark = 1");

            $role_resumes = db::select("SELECT
                DATE_FORMAT( created_at, '%Y-%m' ) AS period,
                count(
                IF
                ( STATUS = 'Added', 1, NULL )) AS added,
                count(
                IF
                ( STATUS = 'Removed', 1, NULL )) AS removed
                FROM
                permission_logs
                GROUP BY
                DATE_FORMAT(
                created_at,
                '%Y-%m')
                ORDER BY
                created_at DESC");

            $account_resumes = db::connection('ympimis_2')->select("SELECT
                DATE_FORMAT( period, '%Y-%m' ) AS period,
                COUNT(
                IF
                ( action = 'Adjusted', 1, NULL )) AS adjusted,
                COUNT(
                IF
                ( action = 'Allowed', 1, NULL )) AS allowed,
                COUNT(
                IF
                ( action = 'Deactivated', 1, NULL )) AS deactivated
                FROM
                mis_stocktaking_accounts
                GROUP BY
                DATE_FORMAT(
                period,
                '%Y-%m'
                )
                ORDER BY
                period DESC");

            $response = array(
                'status' => true,
                'accounts' => $accounts,
                'groups' => $groups,
                'permissions' => $permissions,
                'permission_lists' => $permission_lists,
                'navigations' => $navigations,
                'navigation_lists' => $navigation_lists,
                'roles' => $roles,
                'role_lists' => $role_lists,
                'role_resumes' => $role_resumes,
                'account_resumes' => $account_resumes,
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

    public function updatePermission(Request $request)
    {
        try {

            $current_permissions = db::select("SELECT
                p.role_code,
                r.position,
                n.navigation_code,
                n.navigation_name
                FROM
                permissions AS p
                LEFT JOIN roles AS r ON r.role_code = p.role_code
                LEFT JOIN navigations AS n ON n.navigation_code = p.navigation_code
                WHERE
                n.remark = 1
                AND r.remark = 1
                AND n.navigation_name = '" . $request->get('navigation_name') . "'
                AND r.position = '" . $request->get('position') . "'
                ORDER BY
                p.role_code ASC,
                n.navigation_name ASC");

            $permissions = array();

            foreach ($current_permissions as $row) {
                if (!in_array($row->navigation_code . '+' . $row->role_code, $request->get('permission'))) {

                    db::table('permission_logs')->insert([
                        'role_code' => $row->role_code,
                        'navigation_code' => $row->navigation_code,
                        'status' => 'Removed',
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    db::table('permissions')->where('role_code', '=', $row->role_code)
                        ->where('navigation_code', '=', $row->navigation_code)
                        ->delete();
                }
                array_push($permissions, $row->navigation_code . '+' . $row->role_code);
            }

            foreach ($request->get('permission') as $row) {
                if (!in_array($row, $permissions)) {

                    $permission = explode('+', $row);

                    db::table('permissions')->insert([
                        'role_code' => $permission[1],
                        'navigation_code' => $permission[0],
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    db::table('permission_logs')->insert([
                        'role_code' => $permission[1],
                        'navigation_code' => $permission[0],
                        'status' => 'Added',
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Permissions has been updated.',
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

    public function updateStocktakingAccount(Request $request)
    {
        try {
            if (count($request->get('update_actions')) == 0 && count($request->get('update_actions')) == 0) {
                $response = array(
                    'status' => false,
                    'message' => 'There is no data to be updated',
                );
                return Response::json($response);
            }

            if (count($request->get('update_actions')) > 0) {
                foreach ($request->get('update_actions') as $row) {
                    $update_action = db::connection('ympimis_2')->table('mis_stocktaking_accounts')
                        ->where('id', '=', $row['id'])
                        ->update([
                            'action' => $row['action'],
                            'adjusted_by' => strtoupper(Auth::user()->username),
                            'adjusted_by_name' => Auth::user()->name,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
            }

            if (count($request->get('update_remarks')) > 0) {
                foreach ($request->get('update_remarks') as $row) {
                    $update_action = db::connection('ympimis_2')->table('mis_stocktaking_accounts')
                        ->where('id', '=', $row['id'])
                        ->update([
                            'remark' => $row['remark'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Data has been updated',
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

    public function generateStocktakingAccount(Request $request)
    {
        try {
            $count_check = DB::connection('ympimis_2')->table('mis_stocktaking_accounts')
                ->where('period', '>=', date('Y-m-01'))
                ->where('period', '<=', date('Y-m-t'))
                ->whereNotNull('adjusted_by')
                ->count('id');

            if ($count_check > 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Can`t generate, there is unmatch data that has been handled.',
                );
                return Response::json($response);
            }

            $employees = db::select("SELECT
                es.employee_id,
                es.NAME AS employee_name,
                IF
                (
                es.position = 'Foreman'
                OR es.position = 'Chief'
                OR es.position = 'Coordinator',
                'Coordinator',
                IF
                (
                es.position LIKE '%Staff%',
                'Staff',
                IF
                (
                es.position LIKE '%Outsource%',
                'Outsource',
                IF
                ( es.position LIKE '%Operator%', 'Operator', IF ( es.grade_code = 'J0-', 'Japanese', es.position ) )
                ))
                ) AS position,
                IF
                ( es.position LIKE '%Outsource%', NULL, d.alias ) AS department,
                es.section,
                es.end_date
                FROM
                employee_syncs AS es
                LEFT JOIN departments AS d ON d.department_name = es.department");

            $mirai_users = db::select("SELECT
                u.username,
                u.role_code,
                r.role_name,
                r.position AS role_position,
                r.department AS role_department,
                u.deleted_at AS end_date
                FROM
                users AS u
                LEFT JOIN roles AS r ON r.role_code = u.role_code");

            $ympicoid_users = db::select("SELECT
                u.username,
                u.role_code,
                r.role_name,
                u.deleted_at AS end_date
                FROM
                miraimobile.users AS u
                LEFT JOIN miraimobile.roles AS r ON r.role_code = u.role_code");

            $github_users = db::connection('ympimis_2')->select("SELECT
                username,
                position AS role_code,
                position AS role_name,
                deleted_at AS end_date
                FROM
                github_users");

            $results = array();

            for ($i = 0; $i < count($employees); $i++) {
                $row = array();

                $period = date('Y-m-d');
                $category = 'MIRAI';
                $username = '';
                $role_code = '';
                $role_name = '';
                $role_position = '';
                $role_department = '';
                $user_status = 'Inactive';
                $employee_id = $employees[$i]->employee_id;
                $employee_name = $employees[$i]->employee_name;
                $employee_position = $employees[$i]->position;
                $employee_department = $employees[$i]->department;
                $employee_status = 'Active';
                if ($employees[$i]->end_date) {
                    $employee_status = 'Inactive';
                }
                $status = '';
                $created_by = strtoupper(Auth::user()->username);
                $created_by_name = Auth::user()->name;
                $created_at = date('Y-m-d H:i:s');
                $updated_at = date('Y-m-d H:i:s');

                for ($j = 0; $j < count($mirai_users); $j++) {
                    if (strtoupper($mirai_users[$j]->username) == strtoupper($employees[$i]->employee_id)) {
                        $username = $mirai_users[$j]->username;
                        $role_code = $mirai_users[$j]->role_code;
                        $role_name = $mirai_users[$j]->role_name;
                        $role_position = $mirai_users[$j]->role_position;
                        $role_department = $mirai_users[$j]->role_department;
                        $user_status = 'Active';
                        if ($mirai_users[$j]->end_date) {
                            $user_status = 'Inactive';
                        }
                        break;
                    }
                }

                if ($employee_status == 'Inactive' && $user_status == 'Inactive') {
                    $status = 'Match';
                } else {
                    if ($employee_position == "Manager" && $employee_position == $role_position) {
                        $status = 'Match';
                    } else if ($employee_position == "Japanese" && $employee_position == $role_position) {
                        $status = 'Match';
                    } else if ($role_position == $employee_position && $role_department == $employee_department) {
                        $status = 'Match';
                    } else if ($role_position == 'Operator/Outsource' && $employee_position == 'Outsource') {
                        $status = 'Match';
                    } else if ($role_position == 'Operator/Outsource' && $role_department == '' && $employee_position == 'Operator') {
                        $status = 'Match';
                    } else {
                        $status = 'Unmatch';
                    }
                }

                // array_push($results, [
                //     'period' => $period,
                //     'category' => $category,
                //     'username' => $username,
                //     'role_code' => $role_code,
                //     'role_name' => $role_name,
                //     'role_position' => $role_position,
                //     'role_department' => $role_department,
                //     'user_status' => $user_status,
                //     'employee_id' => $employee_id,
                //     'employee_name' => $employee_name,
                //     'employee_position' => $employee_position,
                //     'employee_department' => $employee_department,
                //     'employee_status' => $employee_status,
                //     'status' => $status,
                //     'created_by' => $created_by,
                //     'created_by_name' => $created_by_name,
                //     'created_at' => $created_at,
                //     'updated_at' => $updated_at,
                // ]);

                $row['period'] = $period;
                $row['category'] = $category;
                $row['username'] = $username;
                $row['role_code'] = $role_code;
                $row['role_name'] = $role_name;
                $row['role_position'] = $role_position;
                $row['role_department'] = $role_department;
                $row['user_status'] = $user_status;
                $row['employee_id'] = $employee_id;
                $row['employee_name'] = $employee_name;
                $row['employee_position'] = $employee_position;
                $row['employee_department'] = $employee_department;
                $row['employee_status'] = $employee_status;
                $row['status'] = $status;
                $row['created_by'] = $created_by;
                $row['created_by_name'] = $created_by_name;
                $row['created_at'] = $created_at;
                $row['updated_at'] = $updated_at;

                $results[] = $row;
            }

            for ($i = 0; $i < count($employees); $i++) {
                $row = array();

                $period = date('Y-m-d');
                $category = 'YMPICOID';
                $username = '';
                $role_code = '';
                $role_name = '';
                $role_position = '';
                $role_department = '';
                $user_status = 'Inactive';
                $employee_id = $employees[$i]->employee_id;
                $employee_name = $employees[$i]->employee_name;
                $employee_position = '';
                $employee_department = '';
                $employee_status = 'Active';
                if ($employees[$i]->end_date) {
                    $employee_status = 'Inactive';
                }
                $status = '';
                $created_by = strtoupper(Auth::user()->username);
                $created_by_name = Auth::user()->name;
                $created_at = date('Y-m-d H:i:s');
                $updated_at = date('Y-m-d H:i:s');

                for ($j = 0; $j < count($ympicoid_users); $j++) {
                    if (strtoupper($ympicoid_users[$j]->username) == strtoupper($employees[$i]->employee_id)) {
                        $username = $ympicoid_users[$j]->username;
                        $role_code = "";
                        $role_name = "";
                        $role_position = "";
                        $role_department = "";
                        $user_status = 'Active';
                        if ($ympicoid_users[$j]->end_date) {
                            $user_status = 'Inactive';
                        }
                        break;
                    }
                }

                if ($employee_status == $user_status) {
                    $status = 'Match';
                } else {
                    $status = 'Unmatch';
                }

                $row['period'] = $period;
                $row['category'] = $category;
                $row['username'] = $username;
                $row['role_code'] = $role_code;
                $row['role_name'] = $role_name;
                $row['role_position'] = $role_position;
                $row['role_department'] = $role_department;
                $row['user_status'] = $user_status;
                $row['employee_id'] = $employee_id;
                $row['employee_name'] = $employee_name;
                $row['employee_position'] = $employee_position;
                $row['employee_department'] = $employee_department;
                $row['employee_status'] = $employee_status;
                $row['status'] = $status;
                $row['created_by'] = $created_by;
                $row['created_by_name'] = $created_by_name;
                $row['created_at'] = $created_at;
                $row['updated_at'] = $updated_at;

                $results[] = $row;
            }

            for ($i = 0; $i < count($employees); $i++) {
                $row = array();

                if ($employees[$i]->department == 'Management Information System Department' && $employees[$i]->section == 'Software Section') {
                    $period = date('Y-m-d');
                    $category = 'GITHUB';
                    $username = '';
                    $role_code = '';
                    $role_name = '';
                    $role_position = '';
                    $role_department = '';
                    $user_status = 'Inactive';
                    $employee_id = $employees[$i]->employee_id;
                    $employee_name = $employees[$i]->employee_name;
                    $employee_position = '';
                    $employee_department = '';
                    $employee_status = 'Active';
                    if ($employees[$i]->end_date) {
                        $employee_status = 'Inactive';
                    }
                    $status = '';
                    $created_by = strtoupper(Auth::user()->username);
                    $created_by_name = Auth::user()->name;
                    $created_at = date('Y-m-d H:i:s');
                    $updated_at = date('Y-m-d H:i:s');

                    for ($j = 0; $j < count($github_users); $j++) {
                        if (strtoupper($github_users[$j]->username) == strtoupper($employees[$i]->employee_id)) {
                            $username = $github_users[$j]->username;
                            $role_code = "";
                            $role_name = "";
                            $role_position = "";
                            $role_department = "";
                            $user_status = 'Active';
                            if ($github_users[$j]->end_date) {
                                $user_status = 'Inactive';
                            }
                            break;
                        }
                    }

                    if ($employee_status == $user_status) {
                        $status = 'Match';
                    } else {
                        $status = 'Unmatch';
                    }

                    $row['period'] = $period;
                    $row['category'] = $category;
                    $row['username'] = $username;
                    $row['role_code'] = $role_code;
                    $row['role_name'] = $role_name;
                    $row['role_position'] = $role_position;
                    $row['role_department'] = $role_department;
                    $row['user_status'] = $user_status;
                    $row['employee_id'] = $employee_id;
                    $row['employee_name'] = $employee_name;
                    $row['employee_position'] = $employee_position;
                    $row['employee_department'] = $employee_department;
                    $row['employee_status'] = $employee_status;
                    $row['status'] = $status;
                    $row['created_by'] = $created_by;
                    $row['created_by_name'] = $created_by_name;
                    $row['created_at'] = $created_at;
                    $row['updated_at'] = $updated_at;

                    $results[] = $row;
                }
            }

            DB::connection('ympimis_2')->table('mis_stocktaking_accounts')->where('period', '>=', date('Y-m-01'))->where('period', '<=', date('Y-m-t'))->delete();
            foreach (array_chunk($results, 1000) as $t) {
                DB::connection('ympimis_2')->table('mis_stocktaking_accounts')->insert($t);
            }

            $response = array(
                'status' => true,
                'message' => 'Generated',
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

    public function getNotifForm()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(strtoupper(Auth::user()->username));
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $notif = 0;

            $tanggungan = db::select("
                SELECT DISTINCT
                ticket_forms.form_id
                FROM
                ticket_forms
                JOIN ticket_form_approvers ON ticket_forms.form_id = ticket_form_approvers.form_id
                WHERE
                ticket_form_approvers.`status` = 'Waiting'
                and ticket_forms.deleted_at is null
                and ticket_forms.`status` != 'Rejected'
                ");

            $ticket = [];
            foreach ($tanggungan as $tag) {
                array_push($ticket, $tag->form_id);
            }

            $jumlah_tanggungan = 0;

            for ($i = 0; $i < count($ticket); $i++) {
                $tanggungan_user = db::select("
                   SELECT
                   ( SELECT approver_id FROM ticket_form_approvers a WHERE a.id = ( ticket_form_approvers.id ) ) next
                   FROM
                   ticket_form_approvers
                   WHERE
                   `status` = 'Waiting'
                   AND form_id = '" . $ticket[$i] . "'
                   ORDER BY
                   id ASC
                   LIMIT 1
                   ");

                if (count($tanggungan_user) > 0 && $tanggungan_user[0]->next == $user) {
                    $jumlah_tanggungan += 1;
                }
            }

            $notif = $jumlah_tanggungan;

            return $notif;
        }
    }

    public function indexForm()
    {

        $title = "Form Request MIS";
        $title_jp = "";

        $employee_syncs = db::select("SELECT
         es.employee_id,
         es.name,
         es.department,
         d.department_shortname,
         es.section,
         es.group,
         es.sub_group
         FROM
         employee_syncs AS es
         LEFT JOIN departments AS d ON d.department_name = es.department
         WHERE
         (
         end_date IS NULL
         OR end_date >= date(
         now()))
         AND es.department IS NOT NULL
         ORDER BY
         es.hire_date ASC");

        $approvers = db::select("SELECT
         *
         FROM
         approvers");

        return view('about_mis.form.monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_syncs' => $employee_syncs,
            'form_approvers' => $this->form_approver,
            'form_descriptions' => $this->form_descriptions,
            'approvers' => $approvers,
        ))->with('page', 'MIS Form')->with('head', 'MIS Form');
    }

    public function fetchFormSecurity(Request $request)
    {
        $ticket_forms = db::select("SELECT
         *
         FROM
         ticket_forms AS tf
         WHERE
         tf.form_id IN (
         SELECT
         tfa.form_id
         FROM
         ticket_form_approvers AS tfa
         LEFT JOIN ( SELECT form_id, count( id ) AS cnt FROM ticket_form_approvers WHERE approved_at IS NULL GROUP BY form_id ) AS tfc ON tfc.form_id = tfa.form_id
         WHERE
         tfa.approver_id = 'Security Member'
         AND tfc.cnt = 1)");

        $response = array(
            'status' => true,
            'ticket_forms' => $ticket_forms,
        );
        return Response::json($response);
    }

    public function fetchForm(Request $request)
    {
        try {
            $ticket_forms = db::table('ticket_forms')->whereNull('deleted_at')->orderBy('form_id', 'DESC')->get();
            $ticket_form_approvers = db::table('ticket_form_approvers')->whereNull('deleted_at')->orderBy('form_id', 'DESC')->get();

            $response = array(
                'status' => true,
                'ticket_forms' => $ticket_forms,
                'ticket_form_approvers' => $ticket_form_approvers,
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

    public function inputForm(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', 'mis_form')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);

            $form_id = $code_generator->prefix . $number;
            $form_name = $request->input('form_name');
            $form_description = explode('##', $request->input('form_description'))[0];
            $employee_id = $request->input('employee_id');
            $employee_name = $request->input('employee_name');
            $department_shortname = $request->input('department_shortname');
            $department = $request->input('department');
            $status = 'Requested';
            $approvers = json_decode($request->input('create_approver'));

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $insert_form = db::table('ticket_forms')->insert([
                'form_id' => $form_id,
                'form_name' => $form_name,
                'form_description' => $form_description,
                'employee_id' => $employee_id,
                'employee_name' => $employee_name,
                'department_shortname' => $department_shortname,
                'department' => $department,
                'status' => $status,
                'created_by' => strtoupper(Auth::user()->username),
                'created_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // $insert_form_approver = db::table('ticket_form_approvers')->insert([
            //     'form_id' => $form_id,
            //     'approver_id' => strtoupper(Auth::user()->username),
            //     'approver_name' => Auth::user()->name,
            //     'approver_email' => Auth::user()->email,
            //     'status' => 'Approved',
            //     'approved_at' => date('Y-m-d H:i:s'),
            //     'position' => 'Applicant',
            //     'remark' => 'Submitted By',
            //     'created_at' => date('Y-m-d H:i:s'),
            //     'updated_at' => date('Y-m-d H:i:s')
            // ]);

            for ($i = 0; $i < count($approvers); $i++) {
                $approver_id = $approvers[$i]->approver_id;
                $approver_name = $approvers[$i]->approver_name;
                $approver_email = $approvers[$i]->approver_email;
                $position = $approvers[$i]->position;
                $remark = $approvers[$i]->remark;
                $insert_form_approver = db::table('ticket_form_approvers')->insert([
                    'form_id' => $form_id,
                    'approver_id' => $approver_id,
                    'approver_name' => $approver_name,
                    'approver_email' => $approver_email,
                    'status' => 'Waiting',
                    'position' => $position,
                    'remark' => $remark,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            self::formSendEmail($form_id, 'Approved');

            $response = array(
                'status' => true,
                'message' => 'Permohonan berhasil dilakukan.',
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

    public function approvalForm(Request $request)
    {

        $form_id = $request->get('form_id');
        $approver_id = $request->get('approver_id');
        $status = $request->get('status');

        $form_approver = db::table('ticket_form_approvers')->where('form_id', '=', $form_id)
            ->where('approver_id', '=', $approver_id)
            ->whereNull('approved_at')
            ->first();

        $form = db::table('ticket_forms')->where('form_id', '=', $form_id)->first();

        $form_approvers = db::table('ticket_form_approvers')->where('form_id', '=', $form_id)
            ->get();

        if (Auth::user()->role_code == 'S-MIS' || strtoupper(Auth::user()->username) == $approver_id) {

            if (!$form_approver) {
                return view('about_mis.form.notification', array(
                    'title' => 'MIS Form Approval',
                    'title_jp' => '',
                    'form' => $form,
                    'status' => false,
                    'message' => 'You have already "CONFIRMED" this approval.',
                ))->with('page', 'MIS Form');
            }

            for ($i = 0; $i < count($form_approvers); $i++) {
                if ($form_approvers[$i]->approver_id == $form_approver->approver_id) {
                    if ($i > 0) {
                        if ($form_approvers[$i - 1]->status != 'Approved') {
                            return view('about_mis.form.notification', array(
                                'title' => 'MIS Form Approval',
                                'title_jp' => '',
                                'form' => $form,
                                'status' => false,
                                'message' => 'Previous approver have not or do not approve.',
                            ))->with('page', 'MIS Ticket')->with('head', 'Ticket Confirmation');
                        }
                    }
                    break;
                }
            }

            if ($request->get('status') == null) {
                $data = [
                    'form' => $form,
                    'form_approvers' => $form_approvers,
                    'approver' => $form_approver,
                ];

                return view('about_mis.form.mail_approval', array(
                    'data' => $data));
            }

            if ($form_approver) {
                $update_form_approver = db::table('ticket_form_approvers')->where('form_id', '=', $form_id)
                    ->where('approver_id', '=', $approver_id)
                    ->update([
                        'status' => $status,
                        'approved_at' => date('Y-m-d H:i:s'),
                    ]);

                self::formSendEmail($form_id, $status);

                if ($status == 'Approved') {
                    return view('about_mis.form.notification', array(
                        'title' => 'MIS Form Approval',
                        'title_jp' => '',
                        'form' => $form,
                        'status' => true,
                        'message' => 'You have successfully "APPROVED" this approval.',
                    ))->with('page', 'MIS Form');
                }
                if ($status == 'Approved') {
                    return view('about_mis.form.notification', array(
                        'title' => 'MIS Form Approval',
                        'title_jp' => '',
                        'form' => $form,
                        'status' => false,
                        'message' => 'You have successfully "REJECTED" this approval.',
                    ))->with('page', 'MIS Form');
                }
            }
        } else {
            return view('about_mis.form.notification', array(
                'title' => 'MIS Form Approval',
                'title_jp' => '',
                'form' => $form,
                'status' => false,
                'message' => 'You do not have "PERMISSION" to confirm.',
            ))->with('page', 'MIS Form');
        }
    }

    public function formSendEmail($form_id, $status)
    {
        if ($status == 'Approved') {
            $approver = db::table('ticket_form_approvers')->where('form_id', '=', $form_id)
                ->whereNull('approved_at')
                ->where('remark', '=', 'Approved By')
                ->first();

            if ($approver) {
                if ($approver->remark == 'Approved By') {
                    $update_form = db::table('ticket_forms')->where('form_id', '=', $form_id)
                        ->update([
                            'status' => 'Partially Approved',
                        ]);
                    $form = db::table('ticket_forms')->where('form_id', '=', $form_id)
                        ->first();
                    $form_approvers = db::table('ticket_form_approvers')->where('form_id', '=', $form_id)
                        ->get();

                    $data = [
                        'form' => $form,
                        'form_approvers' => $form_approvers,
                        'approver' => $approver,
                    ];

                    Mail::to($approver->approver_email)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->queue(new SendEmail($data, 'mis_form_approval'));
                }
            } else {
                $update_form = db::table('ticket_forms')->where('form_id', '=', $form_id)
                    ->update([
                        'status' => 'Fully Approved',
                    ]);

                $form = db::table('ticket_forms')->where('form_id', '=', $form_id)
                    ->first();
                $form_approvers = db::table('ticket_form_approvers')->where('form_id', '=', $form_id)
                    ->get();
                $user = db::table('users')->where('username', '=', $form->created_by)->first();

                $data = [
                    'form' => $form,
                    'form_approvers' => $form_approvers,
                    'status' => 'Fully Approved',
                ];

                Mail::to($user->email)
                    ->cc(['ympi-mis-ML@music.yamaha.com'])
                    ->queue(new SendEmail($data, 'mis_form_notification'));
            }
        }
        if ($status == 'Rejected') {
            $update_form = db::table('ticket_forms')->where('form_id', '=', $form_id)
                ->update([
                    'status' => 'Rejected',
                ]);

            $form = db::table('ticket_forms')->where('form_id', '=', $form_id)
                ->first();
            $form_approvers = db::table('ticket_form_approvers')->where('form_id', '=', $form_id)
                ->get();
            $user = db::table('users')->where('username', '=', $form->created_by)->first();

            $data = [
                'form' => $form,
                'form_approvers' => $form_approvers,
                'status' => 'Rejected',
            ];

            Mail::to($user->email)
                ->cc(['ympi-mis-ML@music.yamaha.com'])
                ->queue(new SendEmail($data, 'mis_form_notification'));
        }
    }

    public function indexTicketResume()
    {
        $title = "Resume MIS Project";
        $title_jp = "";

        $weekly_calendars = db::table('weekly_calendars')->orderBy('week_date', 'DESC')
            ->select('fiscal_year')
            ->distinct()
            ->get();

        $now = db::table('weekly_calendars')->orderBy('week_date', 'DESC')
            ->where('week_date', '=', date('Y-m-d'))
            ->select('fiscal_year')
            ->first();

        return view('about_mis.ticket.resume', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'now' => $now,
            'weekly_calendars' => $weekly_calendars,
        ))->with('page', 'MIS Ticket')->with('head', 'Ticket');
    }

    public function confirmForm(Request $request)
    {
        try {

            $form_approvers = db::table('ticket_form_approvers')->where('form_id', '=', $request->input('form_id'))
                ->get();

            for ($i = 0; $i < count($form_approvers); $i++) {
                if ($form_approvers[$i]->approver_id == $request->input('approver')) {
                    if ($i > 0) {
                        if ($form_approvers[$i - 1]->status != 'Approved') {
                            $response = array(
                                'status' => false,
                                'message' => 'Belum disetujui oleh pihak bersangkutan.',
                            );
                            return Response::json($response);
                        }
                    }
                    break;
                }
            }

            if ($request->input('approver') == 'MIS Member') {
                $update_form = db::table('ticket_forms')->where('form_id', '=', $request->input('form_id'))
                    ->update([
                        'form_description' => $request->input('form_description'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                $update_form_approver = db::table('ticket_form_approvers')->where('form_id', '=', $request->input('form_id'))
                    ->where('approver_id', '=', $request->input('approver'))
                    ->update([
                        'approver_id' => strtoupper(Auth::user()->username),
                        'approver_name' => Auth::user()->name,
                        'approver_email' => Auth::user()->email,
                        'status' => 'Approved',
                        'approved_at' => date('Y-m-d H:i:s'),
                    ]);
            }
            if ($request->input('approver') == 'Security Member') {
                $update_form_approver = db::table('ticket_form_approvers')->where('form_id', '=', $request->input('form_id'))
                    ->where('approver_id', '=', $request->input('approver'))
                    ->update([
                        'approver_id' => $request->input('security_id'),
                        'approver_name' => $request->input('security_name'),
                        'approver_email' => '-',
                        'status' => 'Approved',
                        'approved_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $approver = db::table('ticket_form_approvers')->where('form_id', '=', $request->input('form_id'))
                ->whereNull('approved_at')
                ->first();

            if (!$approver) {
                $update_form = db::table('ticket_forms')->where('form_id', '=', $request->input('form_id'))
                    ->update([
                        'status' => 'Finished',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $response = array(
                'status' => true,
                'form_id' => $request->input('form_id'),
                'message' => 'MIS Form berhasil dikonfirmasi',
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

    public function fetchTicketResume(Request $request)
    {
        if ($request->get('fiscal_year') == 'all') {
            $fiscal_year = "";
        } else {
            $fiscal_year = "AND wc.fiscal_year = '" . $request->get('fiscal_year') . "'";
        }

        $costdowns = db::select("SELECT
         wc.fiscal_year,
         t.estimated_due_date_to,
         DATE_FORMAT(t.estimated_due_date_to, '%Y-%m') AS mon,
         t.ticket_id,
         t.project_name,
         t.department,
         tc.category,
         sum( tc.cost_amount ) AS amount
         FROM
         tickets AS t
         LEFT JOIN ticket_costdowns AS tc ON tc.ticket_id = t.ticket_id
         LEFT JOIN weekly_calendars AS wc ON wc.week_date = t.estimated_due_date_to
         WHERE
         t.estimated_due_date_to IS NOT NULL
         " . $fiscal_year . "
         GROUP BY
         wc.fiscal_year,
         t.ticket_id,
         t.project_name,
         t.department,
         t.estimated_due_date_to,
         tc.category,
         DATE_FORMAT(t.estimated_due_date_to, '%Y-%m')
         ORDER BY
         t.estimated_due_date_to DESC");

        $spents = db::select("SELECT
         wc.fiscal_year,
         t.estimated_due_date_to,
         DATE_FORMAT( t.estimated_due_date_to, '%Y-%m' ) AS mon,
         t.ticket_id,
         t.project_name,
         t.department,
         sum( te.quantity * te.item_price ) AS amount
         FROM
         tickets AS t
         LEFT JOIN ticket_equipments AS te ON te.ticket_id = t.ticket_id
         LEFT JOIN weekly_calendars AS wc ON wc.week_date = t.estimated_due_date_to
         WHERE
         t.estimated_due_date_to IS NOT NULL
         " . $fiscal_year . "
         GROUP BY
         wc.fiscal_year,
         t.estimated_due_date_to,
         DATE_FORMAT( t.estimated_due_date_to, '%Y-%m' ),
         t.ticket_id,
         t.project_name,
         t.department,
         t.ticket_id
         ORDER BY
         t.estimated_due_date_to DESC");

        $man_times = db::select("SELECT
         wc.fiscal_year,
         t.estimated_due_date_to,
         DATE_FORMAT( t.estimated_due_date_to, '%Y-%m' ) AS mon,
         t.ticket_id,
         IFNULL(tp.pic_shortname, '-') as pic_shortname,
         IFNULL(GROUP_CONCAT( DISTINCT tt.pic_shortname ), '-') AS pic,
         t.estimated_due_date_to,
         t.estimated_due_date_from,
         t.project_name,
         t.department,
         d.department_shortname,
         t.progress,
         sum(
         time_to_sec( IFNULL( tt.duration, 0 ) ))/ 60 AS duration
         FROM
         tickets AS t
         LEFT JOIN (
         SELECT
         a.ticket_id,
         a.duration,
         b.pic_shortname
         FROM
         ticket_timelines AS a
         LEFT JOIN ticket_pics AS b ON a.pic_id = b.pic_id
         ) AS tt ON tt.ticket_id = t.ticket_id
         LEFT JOIN weekly_calendars AS wc ON wc.week_date = t.estimated_due_date_to
         LEFT JOIN ticket_pics AS tp ON tp.pic_id = t.pic_id
         LEFT JOIN departments AS d ON t.department = d.department_name
         WHERE
         t.estimated_due_date_to IS NOT NULL
         " . $fiscal_year . "
         GROUP BY
         wc.fiscal_year,
         t.estimated_due_date_to,
         DATE_FORMAT( t.estimated_due_date_to, '%Y-%m' ),
         t.ticket_id,
         t.project_name,
         t.department,
         tp.pic_shortname,
         t.estimated_due_date_to,
         t.estimated_due_date_from,
         d.department_shortname,
         t.progress,
         t.ticket_id
         ORDER BY
         t.estimated_due_date_to DESC");

        $response = array(
            'status' => true,
            'costdowns' => $costdowns,
            'spents' => $spents,
            'man_times' => $man_times,
        );
        return Response::json($response);
    }

    public function indexTicketMonitoring($id)
    {
        if ($id == 'mis') {
            $title = "Monitoring MIS Ticket";
            $title_jp = "";
            $departments = db::table('departments')
                ->where('department_shortname', '!=', 'JPN')
                ->orderBy('department_name', 'ASC')
                ->get();

            return view('about_mis.ticket.monitoring', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'departments' => $departments,
                'categories' => $this->category,
                'priorities' => $this->priority_new,
                'costdowns' => $this->costdown,
            ))->with('page', 'MIS Ticket')->with('head', 'Ticket');
        }

        if ($id == 'borrow') {
            $title = "Peminjaman Laptop";
            $title_jp = "";

            return view('about_mis.ticket.borrow', array(
                'title' => $title,
                'title_jp' => $title_jp,
            ))->with('page', 'MIS Ticket')->with('head', 'Ticket');
        }
    }

    public function indexTicketMonitoringNew()
    {
        $title = "Monitoring MIS Ticket";
        $title_jp = "";
        $departments = db::table('departments')
            ->where('department_shortname', '!=', 'JPN')
            ->orderBy('department_name', 'ASC')
            ->get();

        return view('about_mis.ticket.monitoring_new', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'departments' => $departments,
            'categories' => $this->category_new,
            'priorities' => $this->priority_new,
            'costdowns' => $this->costdown,
        ))->with('page', 'MIS Ticket')->with('head', 'Ticket');
    }

    public function fetchDetailTicket(Request $request)
    {
        try {
            $month = $request->get('month');
            $category = $request->get('category');
            $ket = $request->get('ket');

            if ($ket == 'Priority') {
                $data = db::select('SELECT ticket_id, department, case_title, `status`, priority FROM tickets where priority = "' . $category . '" and DATE_FORMAT(created_at, "%Y-%m") = "' . $month . '" and `status` = "Waiting"');
            } else {
                $data = db::select('SELECT ticket_id, department, department_shortname, case_title, `status`, priority FROM tickets LEFT JOIN departments on departments.department_name = tickets.department where `status` = "' . $category . '" and department_shortname = "' . $month . '"');
            }

            $response = array(
                'status' => true,
                'data' => $data,
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

    public function fetchDetailTicketCategory(Request $request)
    {
        try {
            $category = $request->get('category');

            $dept = '';

            if ($request->get('department') != null) {
                $dept = 'and department = "' . $request->get('department') . '"';
            } else {
                $dept = '';
            }

            $data = db::select('SELECT ticket_id, department, department_shortname, case_title, `status`, pic_name FROM tickets LEFT JOIN departments on departments.department_name = tickets.department where `status` != "Rejected" and `group` = "' . $category . '" ' . $dept);

            $response = array(
                'status' => true,
                'data' => $data,
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

    public function fetchDetailTicketPic(Request $request)
    {
        try {
            $category = $request->get('category');

            $cat = '';

            if ($category == "Ticket Progress") {
                $cat = 'and status = "InProgress"';
            } else if ($category == "Ticket Finish") {
                $cat = 'and status = "Finished"';
            } else {
                $cat = 'and status is not null';
            }

            $pic = '';

            if ($request->get('pic') != null) {
                $pic = 'and pic_name = "' . $request->get('pic') . '"';
            } else {
                $pic = '';
            }

            $data = db::select('SELECT ticket_id, department, department_shortname, case_title, `status`, pic_name FROM tickets LEFT JOIN departments on departments.department_name = tickets.department where `status` != "Rejected" ' . $cat . ' ' . $pic);

            $response = array(
                'status' => true,
                'data' => $data,
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

    public function fetchDetailTicketPerolehan(Request $request)
    {
        try {
            $category = $request->get('category');
            $fy = $request->get('fy');

            $data = db::select('SELECT
            ticket_id,
            department,
            department_shortname,
            case_title,
            `status`,
            pic_name,
            due_date_to,
            fiscal_year
        FROM
            tickets
            LEFT JOIN departments ON departments.department_name = tickets.department 
            LEFT JOIN weekly_calendars ON tickets.due_date_to = weekly_calendars.week_date 
        WHERE
            `status` = "Finished" 
            AND fiscal_year = "'.$fy.'" 
            AND pic_id != "PI1412008"
            AND monthname( tickets.due_date_to ) = "'.$category.'"');

            $response = array(
                'status' => true,
                'data' => $data,
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

    public function fetchTicketMonitoring(Request $request)
    {

        $tickets = Ticket::
            orderByRaw("FIELD(priority , 'Very High', 'High', 'Normal') ASC")
            ->orderBy('ticket_id', 'ASC')
            ->leftJoin('departments', 'departments.department_name', '=', 'tickets.department')
            ->leftJoin('ticket_pics', 'tickets.pic_id', '=', 'ticket_pics.pic_id')
            ->select(
                'tickets.ticket_id',
                'tickets.status',
                'tickets.department',
                'tickets.category',
                'tickets.priority',
                'tickets.priority_reason',
                'tickets.case_title',
                'tickets.case_description',
                'tickets.document',
                'tickets.due_date_from',
                'tickets.due_date_to',
                'tickets.estimated_due_date_from',
                'tickets.estimated_due_date_to',
                'tickets.pic_id',
                'tickets.pic_name',
                'ticket_pics.pic_shortname',
                'tickets.difficulty',
                'tickets.progress',
                'tickets.project_name',
                'tickets.remark',
                'tickets.created_by',
                'tickets.created_at',
                'tickets.updated_at',
                'departments.department_shortname'
            )
            ->get();

        $tickets_priority = Ticket::
            orderByRaw("FIELD(priority , 'Very High', 'High', 'Normal') ASC")
            ->leftJoin('departments', 'departments.department_name', '=', 'tickets.department')
            ->leftJoin('ticket_pics', 'tickets.pic_id', '=', 'ticket_pics.pic_id')
            ->whereIn('priority', array('Very High', 'High'))
            ->where('status', '=', 'Waiting')
            ->select(
                'tickets.ticket_id',
                'tickets.status',
                'tickets.category',
                'tickets.priority',
                'tickets.case_title',
                'tickets.case_description',
                'tickets.document',
                'tickets.pic_id',
                'tickets.pic_name',
                'ticket_pics.pic_shortname',
                'tickets.difficulty',
                'tickets.progress',
                'tickets.project_name',
                'tickets.remark',
                'tickets.created_by',
                'tickets.created_at',
                'tickets.updated_at',
                'departments.department_shortname'
            )
            ->get();

        $ticket_approvers = TicketApprover::get();
        // $counts = Ticket::leftJoin('departments', 'departments.department_name', '=', 'tickets.department')
        // ->select('departments.department_shortname', 'tickets.status', 'tickets.ticket_id',  'tickets.priority', 'tickets.priority_reason')
        // ->orderBy('departments.department_shortname', 'ASC')
        // ->orderBy('tickets.status', 'ASC')
        // ->get();
        $counts = db::select("SELECT
         d.department_shortname,
         t.status,
         t.ticket_id,
         t.category,
         t.priority,
         t.priority_reason,
         DATE_FORMAT( t.created_at, '%Y-%m' ) AS created_at
         FROM
         tickets AS t
         LEFT JOIN departments AS d ON d.department_name = t.department
         ORDER BY
         d.department_shortname ASC,
         t.status ASC");
        $departments = db::table('departments')
            ->where('department_shortname', '!=', 'JPN')
            ->orderBy('department_name', 'ASC')
            ->get();
        $statuses = $this->status;

        $res1Software = array();

        foreach ($departments as $department) {
            for ($i = 0; $i < count($statuses); $i++) {
                array_push($res1Software, [
                    'department_shortname' => $department->department_shortname,
                    'status' => $statuses[$i],
                    'cnt' => 0,
                ]);
            }
        }

        $res2Software = array();

        for ($i = 0; $i < count($res1Software); $i++) {
            $cnt = 0;
            foreach ($counts as $count) {
                if ($count->category == 'Pembuatan Aplikasi Baru' || $count->category == 'Pengembangan Aplikasi Lama' || $count->category == 'Perbaikan Aplikasi Error/Bug') {
                    if ($count->department_shortname == $res1Software[$i]['department_shortname'] && $count->status == $res1Software[$i]['status']) {
                        $cnt += 1;
                    }
                }
            }
            array_push($res2Software, [
                'department_shortname' => $res1Software[$i]['department_shortname'],
                'status' => $res1Software[$i]['status'],
                'cnt' => $cnt,
            ]);
        }

        $res1Hardware = array();

        foreach ($departments as $department) {
            for ($i = 0; $i < count($statuses); $i++) {
                array_push($res1Hardware, [
                    'department_shortname' => $department->department_shortname,
                    'status' => $statuses[$i],
                    'cnt' => 0,
                ]);
            }
        }

        $res2Hardware = array();

        for ($i = 0; $i < count($res1Hardware); $i++) {
            $cnt = 0;
            foreach ($counts as $count) {
                if ($count->category == 'Perbaikan Hardware/Software' || $count->category == 'Perbaikan Jaringan' || $count->category == 'Pengadaan Hardware/Software' || $count->category == 'Pemasangan Hardware/Software') {
                    if ($count->department_shortname == $res1Hardware[$i]['department_shortname'] && $count->status == $res1Hardware[$i]['status']) {
                        $cnt += 1;
                    }
                }
            }
            array_push($res2Hardware, [
                'department_shortname' => $res1Hardware[$i]['department_shortname'],
                'status' => $res1Hardware[$i]['status'],
                'cnt' => $cnt,
            ]);
        }

        $response = array(
            'status' => true,
            'tickets' => $tickets,
            'countsSoftware' => $res2Software,
            'countsHardware' => $res2Hardware,
            'charts' => $counts,
            'ticket_approvers' => $ticket_approvers,
            'tickets_priority' => $tickets_priority,
        );
        return Response::json($response);
    }

    public function fetchTicketMonitoringNew(Request $request)
    {

        $first = date("Y-m-d", strtotime('-30 days'));

        $tanggal = "";
        $priority = "";

        if (strlen($request->get('date_from')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('date_from')));
            $tanggal = "and DATE(tickets.created_at) >= '" . $date_from . "'";

            if (strlen($request->get('date_to')) > 0) {

                $date_from = date('Y-m-d', strtotime($request->get('date_from')));
                $date_to = date('Y-m-d', strtotime($request->get('date_to')));

                $tanggal = "and DATE(tickets.created_at) >= '" . $date_from . "'";
                $tanggal = $tanggal . "and DATE(tickets.created_at) <= '" . $date_to . "'";
            }
        }

        $check = Ticket::where('status', '=', 'finished')
            ->orderBy('created_at', 'asc')
            ->select(db::raw('date(created_at) as created_at'))
            ->first();

        if ($first > date("Y-m-d", strtotime($check->created_at))) {
            $first = date("Y-m-d", strtotime($check->created_at));
        }

        if (strlen($request->get('priority')) > 0) {
            $priority = "and priority = '" . $request->get('priority') . "'";
        }

        $month_data = db::select("
         SELECT
         LEFT(MONTHNAME(created_at), 3) as bulan,
         year(created_at) as tahun,
         sum( CASE WHEN `status` = 'Waiting' THEN 1 ELSE 0 END ) AS jumlah_belum,
         sum( CASE WHEN `status` = 'InProgress'  THEN 1 ELSE 0 END ) AS jumlah_progress,
         sum( CASE WHEN `status` = 'Finished' THEN 1 ELSE 0 END ) AS jumlah_sudah
         FROM
         tickets
         WHERE
         deleted_at is null
         and DATE(created_at) >= '" . $first . "'
         " . $tanggal . "
         " . $priority . "
         GROUP BY
         tahun,LEFT(MONTHNAME(created_at), 3)
         order by tahun, month(created_at) ASC
         ");

        $tickets = db::select("SELECT
         tickets.ticket_id,
         tickets.status,
         tickets.department,
         tickets.category,
         tickets.priority,
         tickets.priority_reason,
         tickets.case_title,
         tickets.case_description,
         tickets.document,
         tickets.due_date_from,
         tickets.due_date_to,
         tickets.estimated_due_date_from,
         tickets.estimated_due_date_to,
         tickets.pic_id,
         tickets.pic_name,
         tickets.difficulty,
         tickets.progress,
         tickets.project_name,
         tickets.remark,
         tickets.created_by,
         tickets.created_at,
         tickets.updated_at,
         ticket_pics.pic_shortname,
         d.department_shortname
         FROM
         tickets
         LEFT JOIN departments AS d ON d.department_name = tickets.department
         LEFT JOIN ticket_pics ON tickets.pic_id = ticket_pics.pic_id
         WHERE
         tickets.deleted_at is null
         and DATE(tickets.created_at) >= '" . $first . "'
         " . $tanggal . "
         " . $priority . "
         ORDER BY FIELD(priority,'Very High','High','Normal') ASC,
         tickets.ticket_id DESC");

        // $tickets = Ticket::
        // orderByRaw("FIELD(priority , 'Very High', 'High', 'Normal') ASC")
        // ->orderBy('ticket_id', 'ASC')
        // ->leftJoin('departments', 'departments.department_name', '=', 'tickets.department')
        // ->leftJoin('ticket_pics', 'tickets.pic_id', '=', 'ticket_pics.pic_id')
        // ->select(
        //     'tickets.ticket_id',
        //     'tickets.status',
        //     'tickets.department',
        //     'tickets.category',
        //     'tickets.priority',
        //     'tickets.priority_reason',
        //     'tickets.case_title',
        //     'tickets.case_description',
        //     'tickets.document',
        //     'tickets.due_date_from',
        //     'tickets.due_date_to',
        //     'tickets.estimated_due_date_from',
        //     'tickets.estimated_due_date_to',
        //     'tickets.pic_id',
        //     'tickets.pic_name',
        //     'ticket_pics.pic_shortname',
        //     'tickets.difficulty',
        //     'tickets.progress',
        //     'tickets.project_name',
        //     'tickets.remark',
        //     'tickets.created_by',
        //     'tickets.created_at',
        //     'tickets.updated_at',
        //     'departments.department_shortname'
        // )
        // ->get();

        $ticket_approvers = TicketApprover::get();

        $counts = db::select("SELECT
         d.department_shortname,
         t.status,
         t.ticket_id,
         t.category,
         t.priority,
         t.priority_reason,
         DATE_FORMAT( t.created_at, '%Y-%m' ) AS created_at
         FROM
         tickets AS t
         LEFT JOIN departments AS d ON d.department_name = t.department
         ORDER BY
         d.department_shortname ASC,
         t.status ASC");

        $response = array(
            'status' => true,
            'tickets' => $tickets,
            'charts' => $counts,
            'ticket_approvers' => $ticket_approvers,
            'month_data' => $month_data,
        );
        return Response::json($response);
    }

    public function detailTicketBulan(Request $request)
    {

        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $status = $request->get('status');
        $remark = $request->get('remark');

        if ($status != null) {
            if ($status == "Ticket Open") {
                $stat = 'and status = "Waiting" ';
            } else if ($status == "Ticket Progress") {
                $stat = 'and status = "InProgress"';
            } else if ($status == "Ticket Close") {
                $stat = 'and status = "Finished"';
            }
        } else {
            $stat = '';
        }

        if ($remark != null) {
            $remark = 'and priority = "' . $remark . '"';
        } else {
            $remark = '';
        }

        $query = "select tickets.* FROM tickets where deleted_at is null and left(monthname(created_at),3) = '" . $bulan . "' and year(created_at) = '" . $tahun . "' " . $stat . " " . $remark . " ";

        $detail = db::select($query);

        return DataTables::of($detail)

            ->make(true);
    }

    public function indexDailyReport()
    {
        $tickets = Ticket::where('status', '=', 'InProgress')->get();
        return view('daily_reports.index', array(
            'timeline_categories' => $this->timeline_category,
            'tickets' => $tickets,

        ))->with('page', 'Daily Report');
    }

    public function fetchTicketTimeline($id)
    {
        if ($id == '-') {
            $ticket_timelines = db::select("SELECT
                tt.id,
                tt.ticket_id,
                t.project_name,
                tt.pic_id,
                tt.pic_name,
                tt.timeline_date,
                tt.timeline_category,
                tt.timeline_description,
                tt.duration,
                tt.timeline_attachment,
                tt.progress_update
                FROM
                ticket_timelines AS tt
                LEFT JOIN tickets AS t ON t.ticket_id = tt.ticket_id
                order by  tt.timeline_date DESC, tt.pic_name ASC");
        } else if ($id == 'new') {
            $ticket_timelines = db::select("SELECT
                tt.id,
                tt.ticket_id,
                t.project_name,
                tt.pic_id,
                tt.pic_name,
                tt.timeline_date,
                tt.timeline_category,
                tt.timeline_description,
                tt.duration,
                tt.timeline_attachment,
                tt.progress_update
                FROM
                ticket_timelines AS tt
                LEFT JOIN tickets AS t ON t.ticket_id = tt.ticket_id
                order by  tt.timeline_date DESC, tt.pic_name ASC");

            return DataTables::of($ticket_timelines)

                ->editColumn('timeline_date', function ($ticket) {
                    return date('d-M-Y', strtotime($ticket->timeline_date));
                })

                ->editColumn('project_name', function ($ticket) {
                    if ($ticket->project_name == null) {
                        return 'Non Project / Ticket';
                    } else {
                        return $ticket->project_name;
                    }

                })

                ->editColumn('progress_update', function ($ticket) {
                    return $ticket->progress_update . ' %';
                })

                ->rawColumns(['timeline_date' => 'timeline_date'])
                ->make(true);
        } else {
            $ticket_timelines = Ticket::where('ticket_id', '=', $id)->first();
        }

        $response = array(
            'status' => true,
            'ticket_timelines' => $ticket_timelines,
        );
        return Response::json($response);
    }

    public function fetchTicketTimelineCategory(Request $request)
    {

        $ticket_category = "";
        if ($request->get('ticket') == '-') {
            $ticket_timelines = db::select("SELECT
                tt.id,
                tt.ticket_id,
                t.project_name,
                tt.pic_id,
                tt.pic_name,
                tt.timeline_date,
                tt.timeline_category,
                tt.timeline_description,
                tt.duration,
                tt.timeline_attachment,
                tt.progress_update
                FROM
                ticket_timelines AS tt
                LEFT JOIN tickets AS t ON t.ticket_id = tt.ticket_id
                order by  tt.timeline_date DESC, tt.pic_name ASC");
        } else if ($request->get('ticket') == 'new') {
            $ticket_timelines = db::select("SELECT
                tt.id,
                tt.ticket_id,
                t.project_name,
                tt.pic_id,
                tt.pic_name,
                tt.timeline_date,
                tt.timeline_category,
                tt.timeline_description,
                tt.duration,
                tt.timeline_attachment,
                tt.progress_update
                FROM
                ticket_timelines AS tt
                LEFT JOIN tickets AS t ON t.ticket_id = tt.ticket_id
                order by  tt.timeline_date DESC, tt.pic_name ASC");

            return DataTables::of($ticket_timelines)

                ->editColumn('timeline_date', function ($ticket) {
                    return date('d-M-Y', strtotime($ticket->timeline_date));
                })

                ->editColumn('project_name', function ($ticket) {
                    if ($ticket->project_name == null) {
                        return 'Non Project / Ticket';
                    } else {
                        return $ticket->project_name;
                    }

                })

                ->editColumn('progress_update', function ($ticket) {
                    return $ticket->progress_update . ' %';
                })

                ->rawColumns(['timeline_date' => 'timeline_date'])
                ->make(true);
        } else {
            $ticket_timelines = Ticket::where('ticket_id', '=', $request->get('ticket'))->first();

            if ($request->get('ticket_category') != null) {
                $ticket_category = TicketTimeline::where('ticket_id', '=', $request->get('ticket'))
                    ->where('timeline_category', '=', $request->get('ticket_category'))
                    ->orderBy('id', 'desc')
                    ->first();
            }
        }

        $response = array(
            'status' => true,
            'ticket_timelines' => $ticket_timelines,
            'ticket_category' => $ticket_category,
        );
        return Response::json($response);
    }

    public function indexTicketDetail($id)
    {
        $title = "Detail MIS Ticket";
        $title_jp = "";

        $ticket = Ticket::where('ticket_id', '=', $id)->first();
        $ticket_approver = TicketApprover::where('ticket_id', '=', $id)->get();
        $ticket_attachment = TicketAttachment::where('ticket_id', '=', $id)->get();
        $ticket_costdown = TicketCostdown::where('ticket_id', '=', $id)->get();
        $ticket_timeline = TicketTimeline::select('ticket_timelines.*', DB::RAW("TIME_TO_SEC(duration) as durations"))->where('ticket_id', '=', $id)->orderBy('timeline_date', 'DESC')->orderBy('created_at', 'DESC')->get();
        $ticket_equipment = TicketEquipment::where('ticket_id', '=', $id)->get();
        $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
        $equipments = db::select("SELECT
         ai.kode_item,
         ai.deskripsi,
         round( ai.harga / aer.rate, 2 ) AS price_usd
         FROM
         acc_items AS ai
         LEFT JOIN ( SELECT * FROM acc_exchange_rates WHERE periode = '" . date('Y-m-01') . "' ) AS aer ON ai.currency = aer.currency");
        $mis_members = EmployeeSync::where('department', '=', 'Management Information System Department')->orderBy('hire_date', 'ASC')->whereNull('end_date')->get();

        $standard_cost = db::select("SELECT * FROM standart_costs");

        return view('about_mis.ticket.detail', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'standard_cost' => $standard_cost,
            'departments' => $departments,
            'ticket' => $ticket,
            'ticket_approver' => $ticket_approver,
            'ticket_attachment' => $ticket_attachment,
            'ticket_costdown' => $ticket_costdown,
            'ticket_timeline' => $ticket_timeline,
            'ticket_equipment' => $ticket_equipment,
            'mis_members' => $mis_members,
            'equipments' => $equipments,
            'categories' => $this->category,
            'priorities' => $this->priority,
            'difficulties' => $this->difficulty,
            'statuses' => $this->status,
            'costdowns' => $this->costdown,
            'timeline_categories' => $this->timeline_category,
        ))->with('page', 'MIS Ticket')->with('head', 'Ticket');

    }

    public function indexTicket($id)
    {
        if ($id == 'mis') {
            $title = "List MIS Ticket";
            $title_jp = "";

            $employee = EmployeeSync::where('employee_id', '=', strtoupper(Auth::user()->username))->first();
            $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
            $standard_cost = db::select("SELECT * FROM standart_costs");

            return view('about_mis.ticket.index', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'standard_cost' => $standard_cost,
                'employee' => $employee,
                'departments' => $departments,
                'categories' => $this->category,
                'priorities' => $this->priority,
                'difficulties' => $this->difficulty,
                'statuses' => $this->status,
                'costdowns' => $this->costdown,
            ))->with('page', 'MIS Ticket')->with('head', 'Ticket');
        }
    }

    public function editTicket(Request $request)
    {
        try {
            $ticket = Ticket::where('ticket_id', '=', $request->input('ticket_id'))->first();

            $ticket->status = $request->input('status');
            $ticket->department = $request->input('department');
            $ticket->category = $request->input('category');
            $ticket->priority = $request->input('priority');
            $ticket->priority_reason = $request->input('reason');
            $ticket->case_title = $request->input('title');
            $ticket->case_description = $request->input('description');
            $ticket->case_before = $request->input('before');
            $ticket->case_after = $request->input('after');
            $ticket->document = $request->input('doc');
            // $ticket->due_date_from = $request->input('due_from');
            $ticket->due_date_to = $request->input('due_to');
            $ticket->estimated_due_date_from = $request->input('estimated_due_from');
            $ticket->estimated_due_date_to = $request->input('estimated_due_to');
            $ticket->pic_id = $request->input('pic_id');
            $ticket->pic_name = $request->input('pic_name');
            $ticket->difficulty = $request->input('difficulty');
            $ticket->project_name = $request->input('project_name');

            if (count($request->input('costdown')) > 0) {
                foreach ($request->input('costdown') as $costdown) {
                    $col = explode('~', $costdown);

                    $ticket_costdown = new TicketCostdown([
                        'ticket_id' => $request->input('ticket_id'),
                        'category' => $col[0],
                        'cost_description' => $col[1],
                        'cost_amount' => $col[2],
                        'remark' => $col[3],
                    ]);

                    $ticket_costdown->save();

                }
            }

            if (count($request->input('equipment')) > 0) {
                foreach ($request->input('equipment') as $equipment) {
                    $col = explode('~', $equipment);

                    $ticket_equipment = new TicketEquipment([
                        'ticket_id' => $request->input('ticket_id'),
                        'item_id' => $col[0],
                        'item_description' => $col[1],
                        'quantity' => $col[2],
                        'item_price' => $col[3],
                        'created_by' => Auth::id(),
                    ]);

                    $ticket_equipment->save();

                }
            }

            $ticket->save();

            $response = array(
                'status' => true,
                'message' => 'Ticket berhasil diubah',
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

    public function fetchTicketPDF($id)
    {
        $ticket = Ticket::where('ticket_id', '=', $id)->first();
        $costdown = TicketCostdown::where('ticket_id', '=', $id)->get();
        $approver = TicketApprover::where('ticket_id', '=', $id)->get();

        $data = [
            'ticket' => $ticket,
            'costdown' => $costdown,
            'approver' => $approver,
        ];

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('about_mis.ticket.pdf_approval', array(
            'data' => $data,
        ));

        return $pdf->stream("general.pointing_call.safety_riding_pdf");
    }

    public function fetchTicket(Request $request)
    {
        $category = $request->get('category');

        $tickets = Ticket::orderBy('tickets.created_at', 'DESC')
            ->leftjoin('users', 'users.id', '=', 'tickets.created_by');

        if ($request->get('status') != 'all') {
            $tickets = $tickets->where('status', '=', $request->get('status'));
        }

        if (Auth::user()->role_code != 'S-MIS') {
            $tickets = $tickets->where(function ($query) {
                $employee = EmployeeSync::where('employee_id', '=', strtoupper(Auth::user()->username))->first();
                $query->where('tickets.created_by', '=', Auth::id())
                    ->orWhere('department', '=', $employee->department);
            });
        }

        $tickets = $tickets->get();

        $response = array(
            'status' => true,
            'tickets' => $tickets,
        );
        return Response::json($response);
    }

    public function approvalTicketMonitoring(Request $request)
    {

    }

    public function approvalTicketResend(Request $request)
    {
        try {
            $ticket_approver = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))
                ->whereNull('status')
                ->first();

            $ticket = Ticket::where('ticket_id', '=', $request->get('ticket_id'))->first();
            $approver = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))->get();
            $cd = TicketCostdown::where('ticket_id', '=', $request->get('ticket_id'))->get();
            $attachment = TicketAttachment::where('ticket_id', '=', $request->get('ticket_id'))->first();
            $filename = "";

            if ($attachment != null) {
                $filename = $attachment->file_name;
            }

            $data = [
                'code' => $ticket_approver->remark,
                'ticket' => $ticket,
                'costdown' => $cd,
                'approver' => $approver,
                'filename' => $filename,
            ];

            Mail::to($ticket_approver->approver_email)
                ->bcc(['ympi-mis-ML@music.yamaha.com'])
                ->send(new SendEmail($data, 'mis_ticket_approval'));

            $response = array(
                'status' => true,
                'message' => 'Email berhasil dikirim ulang.',
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

    public function approvalTicketReject(Request $request)
    {
        $ticket = Ticket::where('ticket_id', '=', $request->get('ticket_id'))->first();
        $cd = TicketCostdown::where('ticket_id', '=', $request->get('ticket_id'))->get();
        $approver = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))->get();

        $data = [
            'code' => $request->get('code'),
            'ticket' => $ticket,
            'costdown' => $cd,
            'approver' => $approver,
            'filename' => "",
        ];

        return view('about_mis.ticket.mail_approval_reject', array(
            'data' => $data)
        );
    }

    public function approvalTicket(Request $request)
    {
        if ($request->get('status') == null) {
            $ticket = Ticket::where('ticket_id', '=', $request->get('ticket_id'))->first();
            $cd = TicketCostdown::where('ticket_id', '=', $request->get('ticket_id'))->get();
            $approver = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))->get();
            // $attachment = TicketAttachment::where('ticket_id', '=', $request->get('ticket_id'))->first();

            // $filename = "";
            // if ($attachment != null) {
            //     $filename = $attachment->file_name;
            // }

            $data = [
                'code' => $request->get('code'),
                'ticket' => $ticket,
                'costdown' => $cd,
                'approver' => $approver,
                'filename' => "",
            ];

            return view('about_mis.ticket.mail_approval', array(
                'data' => $data));
        }
        try {
            $ticket_approver = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))
                ->where('remark', '=', $request->get('code'))
                ->first();

            $approvers = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))->get();

            for ($i = 0; $i < count($approvers); $i++) {
                if ($approvers[$i]->approver_id == $ticket_approver->approver_id) {
                    if ($i > 0) {
                        if ($approvers[$i - 1]->status != 'Approved') {
                            return view('about_mis.ticket.notification', array(
                                'title' => 'Ticket Approval Failed.',
                                'title_jp' => '',
                                'code' => 3,
                                'ticket_approver' => $ticket_approver,
                            ))->with('page', 'MIS Ticket')->with('head', 'Ticket Confirmation');
                        }
                    }
                    break;
                }
            }

            if (Auth::user()->role_code != 'S-MIS') {
                if (strtoupper(strtoupper(Auth::user()->username)) != $ticket_approver->approver_id) {
                    return view('about_mis.ticket.notification', array(
                        'title' => 'Ticket Approval Not Authorized',
                        'title_jp' => '',
                        'code' => 4,
                        'ticket_approver' => $ticket_approver,
                    ))->with('page', 'MIS Ticket')->with('head', 'Ticket Confirmation');
                }
            }

            if ($ticket_approver->status != null) {
                return view('about_mis.ticket.notification', array(
                    'title' => 'Ticket Approval',
                    'title_jp' => '',
                    'code' => 1,
                    'ticket_approver' => $ticket_approver,

                ))->with('page', 'MIS Ticket')->with('head', 'Ticket');
            } else {
                $ticket_approver->status = $request->get('status');
                $ticket_approver->approved_at = date('Y-m-d H:i:s');
                $ticket_approver->save();

                $ticket = Ticket::where('ticket_id', '=', $request->get('ticket_id'))->first();

                if ($request->get('status') == 'Approved') {

                    $next_email = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))
                        ->whereNull('status')
                        ->orderBy('id', 'ASC')
                        ->first();

                    if ($next_email != null) {
                        $cd = TicketCostdown::where('ticket_id', '=', $request->get('ticket_id'))->get();
                        $attachment = TicketAttachment::where('ticket_id', '=', $request->get('ticket_id'))->first();
                        $approver = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))->get();

                        $filename = "";
                        if ($attachment != null) {
                            $filename = $attachment->file_name;
                        }

                        $data = [
                            'code' => $next_email->remark,
                            'ticket' => $ticket,
                            'costdown' => $cd,
                            'approver' => $approver,
                            'filename' => $filename,
                        ];

                        Mail::to($next_email->approver_email)
                            ->bcc(['ympi-mis-ML@music.yamaha.com'])
                            ->send(new SendEmail($data, 'mis_ticket_approval'));
                    } else {
                        $cd = TicketCostdown::where('ticket_id', '=', $request->get('ticket_id'))->get();
                        $attachment = TicketAttachment::where('ticket_id', '=', $request->get('ticket_id'))->first();
                        $approver = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))->get();
                        $ticket->status = 'Waiting';
                        $ticket->save();

                        $filename = "";
                        if ($attachment != null) {
                            $filename = $attachment->file_name;
                        }

                        $data = [
                            'code' => 'fully_approved',
                            'ticket' => $ticket,
                            'costdown' => $cd,
                            'approver' => $approver,
                            'filename' => $filename,
                        ];

                        $cc = ['agus.yulianto@music.yamaha.com'];

                        if (strlen($ticket->document) > 3) {
                            array_push($cc, 'rani.nurdiyana.sari@music.yamaha.com');
                            array_push($cc, 'widura@music.yamaha.com');
                        }

                        Mail::to(['ympi-mis-ML@music.yamaha.com'])
                            ->cc($cc)
                            ->send(new SendEmail($data, 'mis_ticket_approval'));
                    }

                    if ($request->get('priority')) {
                        $ticket->priority = 'Normal';
                        $ticket->save();
                    }
                }

                if ($request->get('status') == 'Rejected') {
                    $cd = TicketCostdown::where('ticket_id', '=', $request->get('ticket_id'))->get();
                    $attachment = TicketAttachment::where('ticket_id', '=', $request->get('ticket_id'))->first();
                    $approver = TicketApprover::where('ticket_id', '=', $request->get('ticket_id'))->get();

                    $user = User::where('id', '=', $ticket->created_by)->first();
                    $ticket->status = $request->get('status');
                    $ticket->reject_reason = $request->get('reject_reason');
                    $ticket->save();

                    $filename = "";
                    if ($attachment != null) {
                        $filename = $attachment->file_name;
                    }

                    $to = array();

                    if (str_contains($user->email, '@music.yamaha.com')) {
                        array_push($to, $user->email);
                    }

                    foreach ($approver as $row) {
                        if ($row->status == 'Approved') {
                            array_push($to, $row->approver_email);
                        }
                    }

                    $data = [
                        'code' => 'rejected',
                        'ticket' => $ticket,
                        'costdown' => $cd,
                        'approver' => $approver,
                        'filename' => $filename,
                    ];

                    Mail::to($to)
                        ->cc('ympi-mis-ML@music.yamaha.com')
                        ->send(new SendEmail($data, 'mis_ticket_approval'));
                }

            }

            return view('about_mis.ticket.notification', array(
                'title' => 'Ticket Approval',
                'title_jp' => '',
                'code' => 2,
                'ticket_approver' => $ticket_approver,

            ))->with('page', 'MIS Ticket')->with('head', 'Ticket Confirmation');
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function inputTicketTimeline(Request $request)
    {
        try {
            $filename = "";
            if (count($request->file('attachment')) > 0) {
                $file_destination = 'files/mis_ticket';
                $file = $request->file('attachment');
                $filename = $request->input('ticket_id') . date('YmdHis') . '(' . $request->input('file_name') . ').' . $request->input('extension');
                $file->move($file_destination, $filename);

                $ticket_attachment = new TicketAttachment([
                    'ticket_id' => $request->input('ticket_id'),
                    'file_name' => $filename,
                    'file_extension' => $request->input('extension'),
                    'remark' => 'timeline',
                    'created_by' => Auth::id(),
                ]);
                $ticket_attachment->save();
            }

            $ticket_timeline = new TicketTimeline([
                'ticket_id' => $request->input('ticket_id'),
                'pic_id' => $request->input('pic_id'),
                'pic_name' => $request->input('pic_name'),
                'timeline_date' => $request->input('date'),
                'timeline_category' => $request->input('category'),
                'timeline_description' => $request->input('description'),
                'duration' => $request->input('duration'),
                'progress_update' => $request->input('progress'),
                'progress_category' => $request->input('progress_category'),
                'timeline_attachment' => $filename,
                'created_by' => Auth::id(),
            ]);
            $ticket_timeline->save();

            // $timelines = TicketTimeline::where('ticket_id', '=', $request->input('ticket_id'))->get();
            if ($request->input('ticket_id') != 'MIS00000') {
                $ticket = Ticket::where('ticket_id', '=', $request->input('ticket_id'))->first();
                $ticket->progress = $request->input('progress');
                $ticket->save();
            }

            $response = array(
                'status' => true,
                // 'timelines' => $timelines,
                // 'ticket' => $ticket,
                'message' => 'Timeline berhasil ditambahkan.',
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

    public function inputTicket(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', 'mis_ticket')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $ticket_id = $code_generator->prefix . $number;
            $filename = null;

            $duedate = date('Y-m-d', strtotime($request->input('due_to')));

            $ticket = new Ticket([
                'ticket_id' => $ticket_id,
                'status' => 'Approval',
                'department' => $request->input('department'),
                'category' => $request->input('category'),
                'priority' => $request->input('priority'),
                'priority_reason' => $request->input('reason'),
                'case_title' => $request->input('title'),
                'case_description' => $request->input('description'),
                'case_before' => $request->input('before'),
                'case_after' => $request->input('after'),
                'document' => $request->input('doc'),
                // 'due_date_from' => $request->input('due_from'),
                'due_date_to' => $duedate,
                'created_by' => Auth::id(),
            ]);

            if (count($request->file('attachment')) > 0) {
                $filename = "";
                $file_destination = 'files/mis_ticket';
                $file = $request->file('attachment');
                $filename = $ticket_id . date('YmdHis') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);

                $ticket_attachment = new TicketAttachment([
                    'ticket_id' => $ticket_id,
                    'file_name' => $filename,
                    'file_extension' => $request->input('extension'),
                    'remark' => 'tampilan_sistem',
                    'created_by' => Auth::id(),
                ]);
                $ticket_attachment->save();
            }

            if (count($request->file('attachment_flow_before')) > 0) {
                $filename_before = "";
                $file_destination = 'files/mis_ticket';
                $file = $request->file('attachment_flow_before');
                $filename_before = $ticket_id . date('YmdHis') . 'flowbefore.' . $request->input('extension_flow_before');
                $file->move($file_destination, $filename_before);

                $ticket_attachment = new TicketAttachment([
                    'ticket_id' => $ticket_id,
                    'file_name' => $filename_before,
                    'file_extension' => $request->input('extension_flow_before'),
                    'remark' => 'flow_before',
                    'created_by' => Auth::id(),
                ]);
                $ticket_attachment->save();
            }

            if (count($request->file('attachment_flow_after')) > 0) {
                $filename_after = "";
                $file_destination = 'files/mis_ticket';
                $file = $request->file('attachment_flow_after');
                $filename_after = $ticket_id . date('YmdHis') . 'flowafter.' . $request->input('extension_flow_after');
                $file->move($file_destination, $filename_after);

                $ticket_attachment = new TicketAttachment([
                    'ticket_id' => $ticket_id,
                    'file_name' => $filename_after,
                    'file_extension' => $request->input('extension_flow_after'),
                    'remark' => 'flow_after',
                    'created_by' => Auth::id(),
                ]);
                $ticket_attachment->save();
            }

            $approver = db::table('approvers')->where('remark', '=', 'Manager')
                ->where('department', '=', $request->input('department'))
                ->first();

            if (Auth::user()->role_code != 'S-MIS') {

                if ($request->input('priority') == 'High' || $request->input('priority') == 'Very High') {
                    $approve_manager = new TicketApprover([
                        'ticket_id' => $ticket_id,
                        'approver_id' => $approver->approver_id,
                        'approver_name' => $approver->approver_name,
                        'approver_email' => $approver->approver_email,
                        'remark' => $approver->remark,
                    ]);
                } else if ($request->input('category') == 'Pembuatan Aplikasi Baru' || $request->input('category') == 'Pengembangan Aplikasi Lama') {
                    $approve_manager = new TicketApprover([
                        'ticket_id' => $ticket_id,
                        'approver_id' => $approver->approver_id,
                        'approver_name' => $approver->approver_name,
                        'approver_email' => $approver->approver_email,
                        'remark' => $approver->remark,
                    ]);
                }

                $approve_chief_mis = new TicketApprover([
                    'ticket_id' => $ticket_id,
                    'approver_id' => 'PI0103002',
                    'approver_name' => 'Agus Yulianto',
                    'approver_email' => 'agus.yulianto@music.yamaha.com',
                    'remark' => 'Chief MIS',
                ]);

                if ($request->input('priority') == 'High' || $request->input('priority') == 'Very High') {
                    $approve_manager_mis = new TicketApprover([
                        'ticket_id' => $ticket_id,
                        'approver_id' => 'PI0109004',
                        'approver_name' => 'Budhi Apriyanto',
                        'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                        'remark' => 'Manager MIS',
                    ]);
                } else if ($request->input('category') == 'Pembuatan Aplikasi Baru' || $request->input('category') == 'Pengembangan Aplikasi Lama') {
                    $approve_manager_mis = new TicketApprover([
                        'ticket_id' => $ticket_id,
                        'approver_id' => 'PI0109004',
                        'approver_name' => 'Budhi Apriyanto',
                        'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                        'remark' => 'Manager MIS',
                    ]);
                }
            } else {
                if ($request->input('priority') == 'High' || $request->input('priority') == 'Very High') {
                    // $approve_manager = new TicketApprover([
                    //     'ticket_id' => $ticket_id,
                    //     'approver_id' => $approver->approver_id,
                    //     'approver_name' => $approver->approver_name,
                    //     'approver_email' => $approver->approver_email,
                    //     'remark' => $approver->remark,
                    //     'status' => 'Approved',
                    //     'approved_at' => date('Y-m-d H:i:s')
                    // ]);

                    $approve_manager = new TicketApprover([
                        'ticket_id' => $ticket_id,
                        'approver_id' => '-',
                        'approver_name' => '-',
                        'approver_email' => '-',
                        'remark' => $approver->remark,
                        'status' => 'Approved',
                        'approved_at' => date('Y-m-d H:i:s'),
                    ]);
                } else if ($request->input('category') == 'Pembuatan Aplikasi Baru' || $request->input('category') == 'Pengembangan Aplikasi Lama') {
                    // $approve_manager = new TicketApprover([
                    //     'ticket_id' => $ticket_id,
                    //     'approver_id' => $approver->approver_id,
                    //     'approver_name' => $approver->approver_name,
                    //     'approver_email' => $approver->approver_email,
                    //     'remark' => $approver->remark,
                    //     'status' => 'Approved',
                    //     'approved_at' => date('Y-m-d H:i:s')
                    // ]);

                    $approve_manager = new TicketApprover([
                        'ticket_id' => $ticket_id,
                        'approver_id' => '-',
                        'approver_name' => '-',
                        'approver_email' => '-',
                        'remark' => $approver->remark,
                        'status' => 'Approved',
                        'approved_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                $approve_chief_mis = new TicketApprover([
                    'ticket_id' => $ticket_id,
                    'approver_id' => 'PI0103002',
                    'approver_name' => 'Agus Yulianto',
                    'approver_email' => 'agus.yulianto@music.yamaha.com',
                    'remark' => 'Chief MIS',
                    // 'status' => 'Approved',
                    // 'approved_at' => date('Y-m-d H:i:s')
                ]);

                if ($request->input('priority') == 'High' || $request->input('priority') == 'Very High') {
                    $approve_manager_mis = new TicketApprover([
                        'ticket_id' => $ticket_id,
                        'approver_id' => 'PI0109004',
                        'approver_name' => 'Budhi Apriyanto',
                        'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                        'remark' => 'Manager MIS',
                        // 'status' => 'Approved',
                        // 'approved_at' => date('Y-m-d H:i:s')
                    ]);
                } else if ($request->input('category') == 'Pembuatan Aplikasi Baru' || $request->input('category') == 'Pengembangan Aplikasi Lama') {
                    $approve_manager_mis = new TicketApprover([
                        'ticket_id' => $ticket_id,
                        'approver_id' => 'PI0109004',
                        'approver_name' => 'Budhi Apriyanto',
                        'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                        'remark' => 'Manager MIS',
                        // 'status' => 'Approved',
                        // 'approved_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $cd = array();

            foreach ($request->input('costdown') as $costdown) {
                $col = explode('~', $costdown);

                $ticket_costdown = new TicketCostdown([
                    'ticket_id' => $ticket_id,
                    'category' => $col[0],
                    'cost_description' => $col[1],
                    'cost_amount' => $col[2],
                    'remark' => $col[3],
                ]);

                $ticket_costdown->save();

                array_push($cd, [
                    'category' => $col[0],
                    'cost_description' => $col[1],
                    'cost_amount' => $col[2],
                    'remark' => $col[3],
                ]);
            }

            $code_generator->index = $code_generator->index + 1;
            if ($request->input('priority') == 'High' || $request->input('priority') == 'Very High') {
                $approve_manager->save();
            } else if ($request->input('category') == 'Pembuatan Aplikasi Baru' || $request->input('category') == 'Pengembangan Aplikasi Lama') {
                $approve_manager->save();
            }
            $approve_chief_mis->save();
            if ($request->input('priority') == 'High' || $request->input('priority') == 'Very High') {
                $approve_manager_mis->save();
            } else if ($request->input('category') == 'Pembuatan Aplikasi Baru' || $request->input('category') == 'Pengembangan Aplikasi Lama') {
                $approve_manager_mis->save();
            }
            $code_generator->save();
            $ticket->save();

            $approver = TicketApprover::where('ticket_id', '=', $ticket_id)->get();

            $data = [
                'code' => 'Manager',
                'ticket' => $ticket,
                'costdown' => $cd,
                'approver' => $approver,
                'filename' => $filename,
            ];

            if (Auth::user()->role_code != 'S-MIS') {

                if ($request->input('priority') == 'High' || $request->input('priority') == 'Very High') {
                    Mail::to($approve_manager->approver_email)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->send(new SendEmail($data, 'mis_ticket_approval'));

                    $response = array(
                        'status' => true,
                        'message' => 'Ticket berhasil dibuat, proses approval dikirim melalui email.',
                    );
                    return Response::json($response);
                } else if ($request->input('category') == 'Pembuatan Aplikasi Baru' || $request->input('category') == 'Pengembangan Aplikasi Lama') {
                    Mail::to($approve_manager->approver_email)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->send(new SendEmail($data, 'mis_ticket_approval'));

                    $response = array(
                        'status' => true,
                        'message' => 'Ticket berhasil dibuat, proses approval dikirim melalui email.',
                    );
                    return Response::json($response);
                } else {
                    $data = [
                        'code' => 'Chief MIS',
                        'ticket' => $ticket,
                        'costdown' => $cd,
                        'approver' => $approver,
                        'filename' => $filename,
                    ];

                    Mail::to($approve_chief_mis->approver_email)
                        ->bcc(['ympi-mis-ML@music.yamaha.com'])
                        ->send(new SendEmail($data, 'mis_ticket_approval'));

                    $response = array(
                        'status' => true,
                        'message' => 'Ticket berhasil dibuat, proses approval dikirim melalui email.',
                    );
                    return Response::json($response);
                }
            } else {
                // $data = [
                //     'code' => 'fully_approved',
                //     'ticket' => $ticket,
                //     'costdown' => $cd,
                //     'approver' => $approver,
                //     'filename' => $filename
                // ];

                // Mail::to(['ympi-mis-ML@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])
                // ->send(new SendEmail($data, 'mis_ticket_approval'));

                // $response = array(
                //     'status' => true,
                //     'message' => 'Ticket berhasil dibuat',
                // );
                // return Response::json($response);

                $data = [
                    'code' => 'Chief MIS',
                    'ticket' => $ticket,
                    'costdown' => $cd,
                    'approver' => $approver,
                    'filename' => $filename,
                ];

                Mail::to($approve_chief_mis->approver_email)
                    ->bcc(['ympi-mis-ML@music.yamaha.com'])
                    ->send(new SendEmail($data, 'mis_ticket_approval'));

                $response = array(
                    'status' => true,
                    'message' => 'Ticket berhasil dibuat, proses approval dikirim melalui email.',
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

    public function getNotifTicket()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(strtoupper(Auth::user()->username));
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $notif = 0;

            $tanggungan = db::select("
               SELECT DISTINCT
               tickets.ticket_id
               FROM
               tickets
               JOIN ticket_approvers ON tickets.ticket_id = ticket_approvers.ticket_id
               WHERE
               CONCAT( ticket_approvers.`status` ) IS NULL
               and tickets.deleted_at is null
               and tickets.`status` != 'Rejected'
               ");

            $ticket = [];
            foreach ($tanggungan as $tag) {
                array_push($ticket, $tag->ticket_id);
            }

            // dd($ticket);

            $jumlah_tanggungan = 0;

            for ($i = 0; $i < count($ticket); $i++) {
                // var_dump($ticket[$i]);
                $tanggungan_user = db::select("
                  SELECT
                  ( SELECT approver_id FROM ticket_approvers a WHERE a.id = ( ticket_approvers.id ) ) next
                  FROM
                  ticket_approvers
                  WHERE
                  `status` IS NULL
                  AND ticket_id = '" . $ticket[$i] . "'
                  ORDER BY
                  id ASC
                  LIMIT 1
                  ");

                if (count($tanggungan_user) > 0 && $tanggungan_user[0]->next == $user) {
                    $jumlah_tanggungan += 1;
                }
            }

            // if (count($tanggungan_user) > 0) {
            $notif = $jumlah_tanggungan;
            // }

            return $notif;
        }
    }

    public function PdfMisForm($form_id)
    {
        $form = db::table('ticket_forms')->where('form_id', '=', $form_id)->first();

        $form_approvers = db::table('ticket_form_approvers')->where('form_id', '=', $form_id)
            ->get();

        $data = [
            'form' => $form,
            'form_approvers' => $form_approvers,
        ];

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('about_mis.form.form_mis', array(
            'data' => $data,
        ));

        $pdf->save(public_path() . "/data_file/form_mis/" . $form_id . ".pdf");

        return redirect('/data_file/form_mis/' . $form_id . '.pdf');
    }

    public function MonitoringMember()
    {
        $title = 'Member MIS';
        $title_jp = '';
        $fy = db::select('SELECT DISTINCT fiscal_year FROM weekly_calendars ORDER BY id DESC');

        return view('about_mis.pic_mis', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'fy' => $fy,
        ))->with('page', 'Wastewater Treatment Monitoring');
    }

    public function fetchMonitoringMember(Request $request)
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

            $software_member = db::select('SELECT employee_id, name
                FROM
                employee_syncs
                WHERE
                section = "Software Section"
                AND end_date IS NULL
                AND employee_id != "PI1412008"
                ORDER BY
                employee_id ASC');

            // $result_month = db::select('SELECT
            //     count(id) as jumlah,
            //     DATE_FORMAT( updated_at, "%Y-%m" ) AS bulan
            //     FROM
            //     tickets
            //     WHERE
            //     `status` = "Finished"
            //     GROUP BY
            //     DATE_FORMAT(
            //         updated_at,
            //         "%Y-%m")');

            $result_month_finish = db::select('SELECT
                count(id) as jumlah,
                DATE_FORMAT( due_date_to, "%Y-%m" ) AS bulan
                FROM
                tickets
                WHERE
                `status` = "Finished"
                and pic_id != "PI1412008"
                GROUP BY
                DATE_FORMAT(
                due_date_to,
                "%Y-%m")');

            $result_month_open = db::select('SELECT
                count( id ) AS jumlah,
                DATE_FORMAT( due_date_to, "%Y-%m" ) AS bulan
                FROM
                tickets
                WHERE
                `status` = "Finished"
                and pic_id != "PI1412008"
                GROUP BY
                DATE_FORMAT( due_date_to, "%Y-%m" )');

            // $result_member = db::select("SELECT
            //     sum(case when `status` = 'Finished' then 1 else 0 end) as jumlah_finished,
            //     sum(case when `status` = 'InProgress' then 1 else 0 end) as jumlah_progress,
            //     pic_id
            //     FROM
            //     tickets
            //     WHERE
            //     deleted_at is null
            //     GROUP BY
            //     pic_id");

            $result_member = db::select('SELECT mstr.mon as bulan, mstr.mun as bulan2, mstr.employee_id as pic_id, IFNULL(datas.jumlah, 0) as jumlah from
                (SELECT * from
                (SELECT DATE_FORMAT( week_date, "%Y-%m" ) as mon, DATE_FORMAT( week_date, "%b-%Y" ) as mun from weekly_calendars where fiscal_year = "'.$p.'" group by DATE_FORMAT( week_date, "%Y-%m" ), DATE_FORMAT( week_date, "%b-%Y" )) wk
                cross join (
                SELECT employee_id, name
                FROM
                employee_syncs
                WHERE
                section = "Software Section"
                AND end_date IS NULL
                AND employee_id != "PI1412008"
                ORDER BY
                employee_id ASC
                ) emp) as mstr
                left join (
                SELECT
                count( id ) AS jumlah,
                DATE_FORMAT( due_date_to, "%Y-%m" ) AS bulan,
                pic_id
                FROM
                tickets
                WHERE
                `status` = "Finished"
                and pic_id is not null
                GROUP BY
                DATE_FORMAT( due_date_to, "%Y-%m" ),
                pic_id ) as datas on mstr.mon = datas.bulan and mstr.employee_id = datas.pic_id
                ORDER BY mstr.mon asc'
            );

//             SELECT
//     b.bulan,
//     b.bulan2,
//     GROUP_CONCAT( b.jumlah SEPARATOR '_' ) AS jumlah,
//     GROUP_CONCAT( b.pic_id SEPARATOR '_' ) AS pic_id
// FROM
//     (
//     SELECT
//         DATE_FORMAT( due_date_to, "%Y-%m" ) AS bulan,
//         DATE_FORMAT( due_date_to, "%b-%Y" ) AS bulan2,
//         count( id ) AS jumlah,
//         pic_id
//     FROM
//         tickets
//     WHERE
//         `status` = "Finished"
//         AND DATE_FORMAT( due_date_to, "%Y-%m" ) >= "2022-04"
//         AND DATE_FORMAT( due_date_to, "%Y-%m" ) <= "2023-03"
//         AND pic_id IS NOT NULL
//     GROUP BY
//         DATE_FORMAT( due_date_to, "%Y-%m" ),
//         DATE_FORMAT( due_date_to, "%b-%Y" ),
//         pic_id
//     ) b
// GROUP BY
//     b.bulan,
//     b.bulan2

            $response = array(
                'status' => true,
                'wc' => $wc,
                'fy' => $fy,
                'software_member' => $software_member,
                'result_month_finish' => $result_month_finish,
                'result_month_open' => $result_month_open,
                'result_member' => $result_member,
                'p' => $p,
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

    public function indexTicketMonitoringCategory()
    {
        $title = 'Monitoring Ticket By Category';
        $title_jp = '';

        $dept = db::select('SELECT DISTINCT department FROM employee_syncs where department is not null');

        return view('about_mis.project_category', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'dept' => $dept,
        ))->with('page', 'Monitoring Ticket By Category');
    }

    public function fetchTicketMonitoringCategory(Request $request)
    {
        try {
            $dept = '';

            if ($request->get('department') != null) {
                $dept = 'and department = "' . $request->get('department') . '"';
            } else {
                $dept = '';
            }

            $data = db::select('SELECT COUNT(id) as jumlah, `group` FROM `tickets` as jumlah where `status` != "Rejected" ' . $dept . ' GROUP BY `group`');

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

    public function indexComplaintCreate($loc)
    {
        $emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();
        return view('about_mis.complaint.create')
            ->with('title', 'MIS Complaint Submission')
            ->with('title_jp', '')
            ->with('emp', $emp)
            ->with('loc', $loc)
            ->with('complaint_id', Auth::user()->username)
            ->with('complaint_name', Auth::user()->name)
            ->with('page', 'MIS Complaint Submission')
            ->with('jpn', '');
    }

    public function inputComplaint(Request $request)
    {
        try {
            $filename = null;

            $code_generator = CodeGenerator::where('note', '=', 'mis_complaint')->first();
            $complaint_id = $code_generator->prefix . sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $code_generator->index = $code_generator->index + 1;

            if (count($request->file('fileData')) > 0) {
                $tujuan_upload = 'data_file/mis/complaint';
                $file = $request->file('fileData');
                $filename = $complaint_id . '_' . date('YmdHisa') . '.' . $request->get('extension');
                $file->move($tujuan_upload, $filename);
            }

            $employee_id = $request->get('complaint_id');
            $name = $request->get('complaint_name');
            $detail_complaint = $request->get('detail_complaint');
            $location = $request->get('location');

            $input = DB::connection('ympimis_2')->table('mis_complaints')->insert([
                'complaint_id' => $complaint_id,
                'category' => 'JARINGAN',
                'employee_id' => $employee_id,
                'name' => $name,
                'detail_complaint' => $detail_complaint,
                'location' => $location,
                'evidence' => $filename,
                'created_by' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $code_generator->save();

            $data = DB::connection('ympimis_2')->table('mis_complaints')->where('complaint_id', $complaint_id)->first();

            $mail_to = [];
            array_push($mail_to, 'agus.yulianto@music.yamaha.com');
            array_push($mail_to, 'anton.budi.santoso@music.yamaha.com');

            Mail::to($mail_to)
                ->bcc(['ympi-mis-ML@music.yamaha.com'])
                ->send(new SendEmail($data, 'mis_complaint'));

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
    public function indexMonitoringComplaint()
    {

        return view('about_mis.complaint.index',
            array(
                'title' => 'Complaint Monitoring',
                'title_jp' => '',
            )
        )->with('page', 'Live');
    }

    public function fetchMonitoringMISComplaint(Request $request)
    {

        $tangg = "";

        if ($request->get('datefrom') != null) {
            $tangg = "where DATE_FORMAT( created_at, '%Y-%m' ) = '" . $request->get('datefrom') . "'";
        } else {
            $tangg = "";
        }

        $data_kategori = db::connection('ympimis_2')->select("
            SELECT
            location,
            sum( CASE WHEN handling IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
            sum( CASE WHEN handling IS NOT NULL THEN 1 ELSE 0 END ) AS jumlah_sudah
            FROM
            mis_complaints
            " . $tangg . "
            GROUP BY location
            ");

        $data_list = db::connection('ympimis_2')->select("
            SELECT
    *,
            DATE_FORMAT(created_at, '%Y-%m-%d') as tanggal,
            DATE_FORMAT(created_at, '%Y-%m') as bulan
            FROM
            mis_complaints
            " . $tangg . "
            ");

        $data_bulan = db::connection('ympimis_2')->select("
            SELECT
            location,
            DATE_FORMAT(created_at, '%Y-%m') AS bulans,
            year(created_at) as tahun,
            sum( CASE WHEN handling IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
            sum( CASE WHEN handling IS NOT NULL THEN 1 ELSE 0 END ) AS jumlah_sudah
            FROM
            mis_complaints
            " . $tangg . "
            GROUP BY bulans,tahun,location
            ORDER BY tahun, MONTHNAME(created_at) ASC
            ");

        $year = date('Y');

        $response = array(
            'status' => true,
            'data_kategori' => $data_kategori,
            'data_list' => $data_list,
            'year' => $year,
            'data_bulan' => $data_bulan,
        );

        return Response::json($response);
    }
}
