<?php

namespace App\Http\Controllers;

use App\Agreement;
use App\AgreementAttachment;
use App\CodeGenerator;
use App\Department;
use App\Employee;
use App\EmployeeSync;
use App\GeneralAirVisualLog;
use App\GeneralAttendance;
use App\GeneralAttendanceLog;
use App\GeneralDoctor;
use App\GeneralShoesLog;
use App\GeneralShoesRequest;
use App\GeneralShoesStock;
use App\GeneralTransportation;
use App\GeneralTransportationData;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\SafetyRiding;
use App\SafetyRidingApprover;
use App\SafetyRidingRecord;
use App\User;
use App\WeeklyCalendar;
use Auth;
use DataTables;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;

class GeneralController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth');

        $this->printers = [
            'Barcode Printer Sax',
            'Barrel-Printer',
            'FLO Printer 101',
            'FLO Printer 102',
            'FLO Printer 103',
            'FLO Printer 104',
            'FLO Printer 105',
            'FLO Printer LOG',
            'FLO Printer RC',
            'FLO Printer VN',
            'KDO ZPRO',
            'MIS',
            'MIS2',
            'Stockroom-Printer',
            'Welding-Printer',
        ];

        $this->std_email = array(
            'widura@music.yamaha.com',
            'vidiya.chalista@music.yamaha.com',
            'rani.nurdiyana.sari@music.yamaha.com',
            'syafrizal.carnov.purwanto@music.yamaha.com',
        );

        $this->agreement_statuses = [
            'In Use',
            'Terminated',
        ];

        $this->regulation_statuses = [
            'Sudah Implementasi',
            'Belum Implementasi',
        ];

        $this->categories = [
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

        $this->approvals = [
            ['department' => 'ACC', 'approval' => 'Investment'],
            ['department' => 'GA', 'approval' => 'Permintaan Driver'],
            ['department' => 'HR', 'approval' => 'Surat Izin Keluar'],
            ['department' => 'HR', 'approval' => 'Tunjangan Keluarga'],
            ['department' => 'HR', 'approval' => 'Uang Simpati'],
            ['department' => 'MIS', 'approval' => 'MIS Ticket'],
            ['department' => 'MIS', 'approval' => 'MIS Form'],
            ['department' => 'PC', 'approval' => 'Extra Order'],
            ['department' => 'PC', 'approval' => 'Sakurentsu'],
            ['department' => 'PC', 'approval' => '3M'],
            ['department' => 'PCH', 'approval' => 'Purchase Requisition'],
            ['department' => 'PCH', 'approval' => 'Purchase Order'],
            ['department' => 'PE', 'approval' => 'Trial Request'],
            ['department' => 'QA', 'approval' => 'CPAR & CAR'],
            ['department' => 'QA', 'approval' => 'Kensa Certificate'],
            ['department' => 'STD', 'approval' => 'Small Group Activity'],
            ['department' => 'ALL', 'approval' => 'MIRAI Approval'],
        ];
    }

    public function indexYMPIStampLog()
    {
        $title = 'Corporate Stamp Records';
        $title_jp = '';

        $categories = [
            ['department' => 'Human Resources Department', 'category' => 'Perjanjian Kerja'],
            ['department' => 'Human Resources Department', 'category' => 'Perjanjian Kerjasama'],
            ['department' => 'Human Resources Department', 'category' => 'Surat Keputusan'],
            ['department' => 'Human Resources Department', 'category' => 'Sanksi Karyawan'],
            ['department' => 'Human Resources Department', 'category' => 'Pengumuman Karyawan'],
            ['department' => 'Human Resources Department', 'category' => 'Surat Menyurat'],
            ['department' => 'Human Resources Department', 'category' => 'Berita Acara Serah Terima Pekerjaan'],
            ['department' => 'Logistic Department', 'category' => 'Dokumen Bea & Cukai'],
            ['department' => 'Logistic Department', 'category' => 'Invoice'],
            ['department' => 'Logistic Department', 'category' => 'Surat Perjanjian'],
            ['department' => 'Logistic Department', 'category' => 'Pengajuan Perijinan'],
            ['department' => 'Logistic Department', 'category' => 'Surat Kuasa'],
            ['department' => 'Accounting Department', 'category' => 'Debit Note'],
            ['department' => 'Accounting Department', 'category' => 'Surat Kuasa'],
            ['department' => 'Accounting Department', 'category' => 'Perjanjian Kerjasama'],
            ['department' => 'Accounting Department', 'category' => 'Surat Menyurat'],
            ['department' => 'General Affairs Department', 'category' => 'Surat Menyurat'],
            ['department' => 'General Affairs Department', 'category' => 'Perjanjian Kontrak Vendor'],
            ['department' => 'General Affairs Department', 'category' => 'SPK Service Kendaraan'],
            ['department' => 'Standardization Department', 'category' => 'Surat Menyurat'],
            ['department' => 'Standardization Department', 'category' => 'Rewarding'],
            ['department' => 'Production Control Department', 'category' => 'Surat Menyurat'],
            ['department' => 'Production Engineering Department', 'category' => 'Surat Menyurat'],
            ['department' => 'Production Engineering Department', 'category' => 'Berita Acara Serah Terima Pekerjaan'],
            ['department' => 'Purchasing Control Department', 'category' => 'Master Transaction Agreement'],
            ['department' => 'Procurement Department', 'category' => 'Master Transaction Agreement'],
            ['department' => 'Maintenance Department', 'category' => 'Berita Acara Serah Terima Pekerjaan'],
            ['department' => 'Maintenance Department', 'category' => 'Surat Menyurat'],
        ];

        $employees = db::select("SELECT
            es.employee_id,
            es.name,
            es.department,
            e.tag
            FROM
            employee_syncs AS es
            LEFT JOIN employees AS e ON e.employee_id = es.employee_id
            WHERE
            (
                es.end_date IS NULL
                OR es.end_date >= date(
                    now()))
            AND es.department IS NOT NULL
            AND es.grade_code <> 'J0-'");

        return view('general.stamp_log', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'categories' => $categories,
            'employees' => $employees,
        ))->with('page', 'YMPI Stamp Log');
    }

    public function fetchYMPIStampLog(Request $request)
    {
        try {
            $general_stamps = db::connection('ympimis_2')->table('general_stamps')->first();
            $general_stamp_logs = db::connection('ympimis_2')->table('general_stamp_logs')->orderBy('created_at', 'desc')->get();

            $response = array(
                'status' => true,
                'general_stamps' => $general_stamps,
                'general_stamp_logs' => $general_stamp_logs,
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

    public function updateYMPIStampLog(Request $request)
    {
        try {
            if (strlen($request->get('tag')) == 10) {
                $employees = db::table('employees')->where('tag', '=', $request->get('tag'))->first();
            }
            if (strlen($request->get('tag')) == 9) {
                $employees = db::table('employees')->where('employee_id', '=', $request->get('tag'))->first();
            }

            if (!$employees) {
                $response = array(
                    'status' => false,
                    'message' => 'Employee data not found.',
                );
                return Response::json($response);
            }

            $general_stamps = db::connection('ympimis_2')->table('general_stamps')->where('created_by', '=', $employees->employee_id)->first();

            if (!$general_stamps) {
                $response = array(
                    'status' => false,
                    'message' => 'Your employee data did not match with previous stamp users.',
                );
                return Response::json($response);
            }

            db::connection('ympimis_2')->table('general_stamp_logs')->insert([
                'department' => $general_stamps->department,
                'category' => $general_stamps->category,
                'date_from' => $general_stamps->date_from,
                'date_to' => date('Y-m-d H:i:s'),
                'created_by' => $general_stamps->created_by,
                'created_by_name' => $general_stamps->created_by_name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            db::connection('ympimis_2')->table('general_stamps')->where('created_by', '=', $employees->employee_id)->delete();

            $response = array(
                'status' => true,
                'message' => 'Stamp has been returned',
                'department' => $general_stamps->department,
                'category' => $general_stamps->category,
                'date_from' => $general_stamps->date_from,
                'date_to' => date('Y-m-d H:i:s'),
                'created_by' => $general_stamps->created_by,
                'created_by_name' => $general_stamps->created_by_name,
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

    public function inputYMPIStampLog(Request $request)
    {
        try {
            db::connection('ympimis_2')->table('general_stamps')->insert([
                'department' => $request->get('department'),
                'category' => $request->get('category'),
                'date_from' => date('Y-m-d H:i:s'),
                'created_by' => $request->get('employee_id'),
                'created_by_name' => $request->get('employee_name'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'Data has been submitted',
                'department' => $request->get('department'),
                'category' => $request->get('category'),
                'date_from' => $request->get('Y-m-d H:i:s'),
                'created_by' => $request->get('employee_id'),
                'created_by_name' => $request->get('employee_name'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
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

    public function fetchResumeApproval(Request $request)
    {
        try {
            $date_from = "2022-01-01";
            $date_to = "2022-12-12";
            $approvals = array();
            foreach ($this->approvals as $row) {
                array_push($approvals, $row['approval']);
            }
            $applicant = "";
            $status = "";
            $resumes = array();

            if (strlen($request->get('filterApplicationDate')) > 0) {
                $date = explode(' - ', $request->get('filterApplicationDate'));

                $date_from = date('Y-m-d', strtotime($date[0]));
                $date_to = date('Y-m-d', strtotime($date[1]));
            }
            if (count($request->get('filterApproval')) > 0) {
                $approvals = $request->get('filterApproval');
            }

            if (count($request->get('filterApplicant')) > 0) {
                $applicants = $request->get('filterApplicant');
                for ($i = 0; $i < count($applicants); $i++) {
                    $applicant = $applicant . "'" . $applicants[$i] . "'";
                    if ($i != (count($applicants) - 1)) {
                        $applicant = $applicant . ',';
                    }
                }
                $applicant = $applicant;
            }

            if (count($request->get('filterStatus')) > 0) {
                $statuses = $request->get('filterStatus');
                for ($i = 0; $i < count($statuses); $i++) {
                    $status = $status . "'" . $statuses[$i] . "'";
                    if ($i != (count($status) - 1)) {
                        $status = $status . ',';
                    }
                }
                $status = $status;
            }

            $mis_ticket = array();
            if (in_array("MIS Ticket", $approvals)) {

                $mis_ticket_where = "";
                if (strlen($applicant) > 0) {
                    $mis_ticket_where = "AND u.username in (" . $applicant . ")";
                }

                $mis_ticket_status = "";
                if (strlen($status) > 0) {
                    $mis_ticket_status = "Having STATUS in (" . $status . ")";
                }

                $mis_ticket = db::select("SELECT
                    'MIS Ticket' AS approval,
                    ta.final_date AS final_date,
                    ta.approver_id AS approver_id,
                    ta.approver_name AS approver_name,
                    ta.approver_position AS approver_position,
                    t.ticket_id AS id,
                    IF
                    (
                        t.STATUS = 'Approval',
                        'Partially Approved',
                        IF
                        ( t.STATUS = 'Rejected', 'Rejected', 'Fully Approved' )) AS STATUS,
                    concat( '(MIS Ticket) ', t.case_title ) AS SUBJECT,
                    IF
                    ( t.department = 'Human Resources Department', 'YES', 'NO' ) AS hr_matter,
                    '-' AS category,
                    u.username AS applicant_id,
                    u.NAME AS applicant_name,
                    t.created_at AS application_date,
                    '-' AS amount,
                    '-' AS unit
                    FROM
                    tickets AS t
                    LEFT JOIN users AS u ON u.id = t.created_by
                    LEFT JOIN (
                        SELECT
                        a.ticket_id,
                        a.approver_id,
                        a.approver_name,
                        a.remark AS approver_position,
                        a.updated_at AS final_date
                        FROM
                        ticket_approvers AS a
                        JOIN ( SELECT ticket_id, max( id ) AS max_id FROM ticket_approvers AS b GROUP BY ticket_id ) AS b ON b.max_id = a.id
                        ) AS ta ON ta.ticket_id = t.ticket_id
                    WHERE
                    t.created_at >= '" . $date_from . "'
                    AND t.created_at <= '" . $date_to . "'
                    " . $mis_ticket_where . "
                    " . $mis_ticket_status . "
                    ORDER BY
                    final_date DESC");

                foreach ($mis_ticket as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'final_date' => $row->final_date,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->approver_position,
                        'id' => $row->id,
                        'status' => $row->STATUS,
                        'subject' => ucwords($row->SUBJECT),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }
            }

            $mis_form = array();
            if (in_array("MIS Form", $approvals)) {

                $mis_form_where = "";
                if (strlen($applicant) > 0) {
                    $mis_form_where = "AND tf.created_by in (" . $applicant . ")";
                }

                $mis_form_status = "";
                if (strlen($status) > 0) {
                    $mis_form_status = "Having STATUS in (" . $status . ")";
                }

                $mis_form = db::select("SELECT
                 'MIS Form' AS approval,
                 td.final_date AS final_date,
                 td.approver_id AS approver_id,
                 td.approver_name AS approver_name,
                 td.approver_position AS approver_position,
                 tf.form_id AS id,
                 IF
                 (
                     tf.STATUS = 'Approval',
                     'Partially Approved',
                     IF
                     ( tf.STATUS = 'Rejected', 'Rejected', 'Fully Approved' )) AS STATUS,
                 tf.form_name AS SUBJECT,
                 'NO' AS hr_matter,
                 '-' AS category,
                 tf.created_by AS applicant_id,
                 tf.created_by_name AS applicant_name,
                 tf.created_at AS application_date,
                 '-' AS amount,
                 '-' AS unit
                 FROM
                 ticket_forms AS tf
                 LEFT JOIN ( SELECT
                     a.form_id,
                     a.approver_id,
                     a.approver_name,
                     a.position AS approver_position,
                     updated_at AS final_date
                     FROM
                     ticket_form_approvers AS a
                     JOIN ( SELECT form_id, max( id ) AS max_id FROM ticket_form_approvers AS b WHERE position <> 'Security' AND position <> 'MIS' GROUP BY form_id ) AS b ON b.max_id = a.id ) AS td ON td.form_id = tf.form_id
                 WHERE
                 tf.created_at >= '" . $date_from . "'
                 AND tf.created_at <= '" . $date_to . "'
                 " . $mis_form_where . "
                 " . $mis_form_status . "
                 ORDER BY
                 final_date DESC");

                foreach ($mis_form as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'final_date' => $row->final_date,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->approver_position,
                        'id' => $row->id,
                        'status' => $row->STATUS,
                        'subject' => ucwords($row->SUBJECT),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }
            }

            $mirai_approval = array();
            if (in_array("MIRAI Approval", $approvals)) {
                $mirai_approval_where = "";
                if (strlen($applicant) > 0) {
                    $mirai_approval_where = "AND tf.created_by in (" . $applicant . ")";
                }

                $mirai_approval_status = "";
                if (strlen($status) > 0) {
                    $mirai_approval_status = "Having STATUS in (" . $status . ")";
                }

                $mirai_approval = db::select("SELECT
                    'MIRAI Approval' AS approval,
                    td.final_date AS final_date,
                    td.approver_id AS approver_id,
                    td.approver_name AS approver_name,
                    td.approver_position as approver_position,
                    t.no_transaction AS id,
                    IF
                    ( t.remark = 'Close', 'Fully Approved', IF ( t.remark = 'Rejected', 'Rejected', 'Partially Approved' ) ) AS STATUS,
                    concat( '(MIRAI Approval) ', t.judul ) AS SUBJECT,
                    IF
                    ( t.department = 'Human Resources Department', 'YES', 'NO' ) AS hr_matter,
                    IF
                    (
                        t.judul LIKE '%PERJALANAN DINAS%'
                        OR t.judul LIKE '%Business Trip%'
                        OR t.judul LIKE '%Bussines%',
                        'KG28 Application for overseas service & KG29 Pengajuan dinas luar di dalam negeri',
                        IF
                        ( t.judul LIKE '%Disposal%', '(IV)-5 Acquisition, remodeling, repair, disposal, lease of fixed assets (machines, molds, etc)', '-' )
                        ) AS category,
                    UPPER( u.username ) AS applicant_id,
                    u.NAME AS applicant_name,
                    t.created_at AS application_date,
                    '-' AS amount,
                    '-' AS unit
                    FROM
                    appr_sends AS t
                    LEFT JOIN users AS u ON u.id = t.created_by
                    LEFT JOIN (
                        SELECT
                        a.request_id,
                        a.approver_id,
                        a.approver_name,
                        a.remark AS approver_position,
                        a.updated_at AS final_date
                        FROM
                        appr_approvals AS a
                        JOIN ( SELECT request_id, max( id ) AS max_id FROM appr_approvals AS b GROUP BY request_id ) AS b ON b.max_id = a.id
                        ) AS td ON td.request_id = t.no_transaction
                    WHERE
                    t.created_at >= '" . $date_from . "'
                    AND t.created_at <= '" . $date_to . "'
                    " . $mirai_approval_where . "
                    " . $mirai_approval_status . "
                    ORDER BY
                    final_date DESC");

                foreach ($mirai_approval as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'final_date' => $row->final_date,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->approver_position,
                        'id' => $row->id,
                        'status' => $row->STATUS,
                        'subject' => ucwords($row->SUBJECT),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }
            }

            $investment = array();
            if (in_array("Investment", $approvals)) {

                $investment_where = "";
                if (strlen($applicant) > 0) {
                    $investment_where = "AND applicant_id in (" . $applicant . ")";
                }

                $investment_status = "";
                if (strlen($status) > 0) {
                    $investment_status = "Having STATUS in (" . $status . ")";
                }

                $investment = db::select("SELECT
                 'Investment' AS approval,
                 SUBSTRING_INDEX(approval_presdir,'/',1) AS approver_id,
                 SUBSTRING_INDEX(SUBSTRING_INDEX(approval_presdir,'/',2),'/',-1)  AS approver_name,
                 'President Director' AS approver_position,
                 SUBSTRING_INDEX(approval_presdir,'/',-1)  AS final_date,
                 t.reff_number AS id,
                 IF
                 (
                     t.posisi = 'finished',
                     'Fully Approved',
                     'Partially Approved'
                     ) AS STATUS,
                 t.subject AS SUBJECT,
                 IF
                 ( t.applicant_department = 'Human Resources Department', 'YES', 'NO' ) AS hr_matter,
                 '(IV)-5 Acquisition, remodeling, repair, disposal, lease of fixed assets (machines, molds, etc)' AS category,
                 applicant_id,
                 applicant_name,
                 t.created_at AS application_date,
                 total_ori AS amount,
                 currency AS unit
                 FROM
                 acc_investments AS t
                 LEFT JOIN users AS u ON u.id = t.created_by
                 JOIN acc_investment_budgets as tu ON t.reff_number = tu.reff_number
                 WHERE
                 t.created_at >= '" . $date_from . "'
                 AND t.created_at <= '" . $date_to . "'
                 " . $investment_where . "
                 " . $investment_status . "
                 ORDER BY
                 final_date DESC
                 ");

                foreach ($investment as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->approver_position,
                        'final_date' => $row->final_date,
                        'id' => $row->id,
                        'status' => $row->STATUS,
                        'subject' => ucwords($row->SUBJECT),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }
            }

            $purchase_requisition = array();
            if (in_array("Purchase Requisition", $approvals)) {
                $pr_where = "";
                if (strlen($applicant) > 0) {
                    $pr_where = "AND t.emp_id in (" . $applicant . ")";
                }

                $pr_status = "";
                if (strlen($status) > 0) {
                    $pr_status = "Having STATUS in (" . $status . ")";
                }

                $purchase_requisition = db::select("SELECT
                 'Purchase Requisition' AS approval,
                 gm AS approver_id,
                 `name` AS approver_name,
                 position AS approver_position,
                 dateapprovalgm AS final_date,
                 t.no_pr AS id,
                 IF
                 (
                     t.posisi = 'pch',
                     'Fully Approved',
                     'Partially Approved'
                     ) AS STATUS,
                 concat(
                     '(Purchase Requisition) No ',
                     t.no_pr
                     ) AS SUBJECT,
                 IF
                 ( t.department = 'Human Resources Department', 'YES', 'NO' ) AS hr_matter,
                 'KG21 Purchase of goods and services that dont fall under any of the other rules and use of expenses' AS category,
                 emp_id AS applicant_id,
                 emp_name AS applicant_name,
                 submission_date AS application_date,
                 '-' AS amount,
                 '-' AS unit
                 FROM
                 acc_purchase_requisitions AS t
                 LEFT JOIN employee_syncs AS emp ON emp.employee_id = t.gm
                 WHERE
                 t.submission_date >= '" . $date_from . "'
                 AND t.submission_date <= '" . $date_to . "'
                 " . $pr_where . "
                 " . $pr_status . "
                 ORDER BY
                 final_date DESC
                 ");

                foreach ($purchase_requisition as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->approver_position,
                        'final_date' => $row->final_date,
                        'id' => $row->id,
                        'status' => $row->STATUS,
                        'subject' => ucwords($row->SUBJECT),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }
            }

            $purchase_order = array();
            if (in_array("Purchase Order", $approvals)) {
                $po_where = "";
                if (strlen($applicant) > 0) {
                    $po_where = "AND t.buyer_id in (" . $applicant . ")";
                }

                $po_status = "";
                if (strlen($status) > 0) {
                    $po_status = "Having STATUS in (" . $status . ")";
                }

                $purchase_order = db::select("SELECT
                 'Purchase Order' AS approval,
                 authorized3 AS approver_id,
                 authorized3_name AS approver_name,
                 'General Manager' AS approver_position,
                 date_approval_authorized3 AS final_date,
                 t.no_po AS id,
                 IF
                 (
                     t.posisi = 'pch',
                     'Fully Approved',
                     'Partially Approved'
                     ) AS STATUS,
                 concat(
                     '(Purchase Order) No ',
                     t.no_po
                     ) AS SUBJECT,
                 IF
                 ( no_po like '%HR%', 'YES', 'NO' ) AS hr_matter,
                 '-' AS category,
                 buyer_id AS applicant_id,
                 buyer_name AS applicant_name,
                 tgl_po AS application_date,
                 '-' AS amount,
                 currency AS unit
                 FROM
                 acc_purchase_orders AS t
                 LEFT JOIN users AS u ON u.id = t.created_by
                 WHERE
                 t.tgl_po >= '" . $date_from . "'
                 AND t.tgl_po <= '" . $date_to . "'
                 " . $po_where . "
                 " . $po_status . "
                 ORDER BY
                 final_date DESC
                 ");

                foreach ($purchase_order as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->approver_position,
                        'final_date' => $row->final_date,
                        'id' => $row->id,
                        'status' => $row->STATUS,
                        'subject' => ucwords($row->SUBJECT),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }
            }

            $sik = array();
            if (in_array("Surat Izin Keluar", $approvals)) {
                $sik_where = "";
                if (strlen($applicant) > 0) {
                    $sik_where = "AND u.username in (" . $applicant . ")";
                }

                $sik_status = "";
                if (strlen($status) > 0) {
                    $sik_status = "Having STATUS in (" . $status . ")";
                }

                $sik = db::select("SELECT
                    'Surat Izin Keluar' AS approval,
                    td.approver_id,
                    td.approver_name,
                    REPLACE(CONCAT(td.position,' ',td.approver_position),'Senior ','') as approver_position,
                    td.final_date AS final_date,
                    t.request_id AS id,
                    t.remark AS STATUS,
                    proper (
                        concat( '(Surat Izin Keluar) ', t.purpose_category, ' - ', t.purpose, ' - ', t.purpose_detail )) AS SUBJECT,
                    'YES' AS hr_matter,
                    '-' AS category,
                    u.username AS applicant_id,
                    u.NAME AS applicant_name,
                    t.created_at AS application_date,
                    '-' AS amount,
                    '-' AS unit
                    FROM
                    hr_leave_requests AS t
                    LEFT JOIN users AS u ON u.id = t.created_by
                    LEFT JOIN (
                        SELECT
                        a.request_id,
                        a.approver_id,
                        a.approver_name,
                        a.remark AS approver_position,
                        employee_syncs.position AS position,
                        a.updated_at AS final_date
                        FROM
                        hr_leave_request_approvals AS a
                        JOIN ( SELECT request_id, max( id ) AS max_id FROM hr_leave_request_approvals WHERE remark != 'Security' GROUP BY request_id ) AS b ON b.max_id = a.id
                        left join employee_syncs on employee_syncs.employee_id = a.approver_id
                        ) AS td ON td.request_id = t.request_id
                    WHERE
                    t.created_at >= '" . $date_from . "'
                    AND t.created_at <= '" . $date_to . "'
                    " . $sik_where . "

                    " . $sik_status . "
                    ORDER BY
                    final_date DESC
                    ");

                foreach ($sik as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'final_date' => $row->final_date,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->approver_position,
                        'id' => $row->id,
                        'status' => $row->STATUS,
                        'subject' => ucwords($row->SUBJECT),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }
            }

            $certificate = array();
            if (in_array("Kensa Certificate", $approvals)) {
                $certificate_where = "";
                if (strlen($applicant) > 0) {
                    $certificate_where = "AND u.auditor_id in (" . $applicant . ")";
                }

                $certificate_status = "";
                if (strlen($status) > 0) {
                    $certificate_status = "Having STATUS in (" . $status . ")";
                }

                $certificate = db::connection('ympimis_2')->select("SELECT
                    'Kensa Certificate' AS approval,
                    td.approver_id,
                    td.approver_name,
                    td.approver_position,
                    td.final_date AS final_date,
                    t.certificate_id AS id,
                    IF
                    ( td.`status` = 1, 'Partially Approved', 'Fully Approved' ) AS STATUS,
                    concat( '(Kensa Certificate) YMPI-QA-', t.`code`, '-', t.code_number, t.number, ' - ', t.description ) AS SUBJECT,
                    'NO' AS hr_matter,
                    '-' AS category,
                    u.auditor_id AS applicant_id,
                    u.auditor_name AS applicant_name,
                    t.created_at AS application_date,
                    '-' AS amount,
                    '-' AS unit
                    FROM
                    qa_certificate_codes AS t
                    LEFT JOIN (
                        SELECT
                        certificate_id,
                        auditor_id,
                        auditor_name
                        FROM
                        qa_certificates
                        GROUP BY
                        certificate_id,
                        auditor_id,
                        auditor_name UNION ALL
                        SELECT
                        certificate_id,
                        auditor_id,
                        auditor_name
                        FROM
                        qa_certificate_inprocesses
                        GROUP BY
                        certificate_id,
                        auditor_id,
                        auditor_name
                        ) AS u ON u.certificate_id = t.certificate_id
                    LEFT JOIN (
                        SELECT
                        a.certificate_id,
                        a.approver_id,
                        a.approver_name,
                        a.remark AS approver_position,
                        a.updated_at AS final_date,
                        b.`status`
                        FROM
                        qa_certificate_approvals AS a
                        JOIN (
                            SELECT
                            certificate_id,
                            IF
                            (
                                GROUP_CONCAT( approver_status ) IS NULL,
                                min( id ),
                                max( id )) AS max_id,
                            GROUP_CONCAT( priority ) AS `status`
                            FROM
                            qa_certificate_approvals
                            GROUP BY
                            certificate_id
                            ) AS b ON b.max_id = a.id
                        ) AS td ON td.certificate_id = t.certificate_id
                    WHERE
                    t.updated_at >= '" . $date_from . "'
                    AND t.updated_at <= '" . $date_to . "'
                    AND t.certificate_id IS NOT NULL
                    " . $certificate_where . "
                    " . $certificate_status . "
                    ORDER BY
                    final_date DESC
                    ");

                foreach ($certificate as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'final_date' => $row->final_date,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->approver_position,
                        'id' => $row->id,
                        'status' => $row->STATUS,
                        'subject' => ucwords($row->SUBJECT),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }
            }

            $sga = array();
            if (in_array("Small Group Activity", $approvals)) {
                $sga_where = "";
                if (strlen($applicant) > 0) {
                    $sga_where = "AND u.secretariat_approver_id in (" . $applicant . ")";
                }

                $sga_status = "";
                if (strlen($status) > 0) {
                    $sga_status = "Having STATUS in (" . $status . ")";
                }

                $sga = db::connection('ympimis_2')->select("
                    SELECT DISTINCT
                    ( t.periode ) AS id,
                    'Small Group Activity' AS approval,
                    td.final_date AS final_date,
                    td.approver_id,
                    td.approver_name,
                    td.approver_position,
                    IF
                    ( t.`remark` = 'Closed', 'Fully Approved', 'Partially Approved' ) AS STATUS,
                    concat( '(SGA) ', REPLACE ( t.`periode`, '_', ' ' ) ) AS SUBJECT,
                    'NO' AS hr_matter,
                    '-' AS category,
                    t.secretariat_approver_id AS applicant_id,
                    t.secretariat_approver_name AS applicant_name,
                    t.created_at AS application_date,
                    '-' AS amount,
                    '-' AS unit
                    FROM
                    sga_teams AS t
                    LEFT JOIN (
                        SELECT
                        periode,
                        IF
                        (
                            periode LIKE '%Final%',
                            SPLIT_STRING ( presdir_approver_status, '_', 2 ),
                            SPLIT_STRING ( dgm_approver_status, '_', 2 )) AS final_date,
                        IF
                        ( periode LIKE '%Final%', presdir_approver_id, dgm_approver_id ) AS approver_id,
                        IF
                        ( periode LIKE '%Final%', presdir_approver_name, dgm_approver_name ) AS approver_name,
                        IF
                        ( periode LIKE '%Final%', 'President Director', 'General Manager' ) AS approver_position
                        FROM
                        sga_teams
                        GROUP BY
                        periode,
                        presdir_approver_status,
                        dgm_approver_status,
                        presdir_approver_id,
                        dgm_approver_id,
                        presdir_approver_name,
                        dgm_approver_name
                        ) AS td ON td.periode = t.periode
                    WHERE
                    t.remark IS NOT NULL
                    " . $sga_where . "
                    " . $sga_status . "
                    ORDER BY
                    final_date DESC
                    ");

                foreach ($sga as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'final_date' => $row->final_date,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->approver_position,
                        'id' => $row->id,
                        'status' => $row->STATUS,
                        'subject' => ucwords($row->SUBJECT),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }
            }

            $extra_order = array();
            if (in_array("Extra Order", $approvals)) {

                $extra_order_where = "";
                if (strlen($applicant) > 0) {
                    $extra_order_where = "AND users.username in (" . $applicant . ")";
                }

                $extra_order_status = "";
                if (strlen($status) > 0) {
                    $extra_order_status = "Having STATUS in (" . $status . ")";
                }

                $extra_order = db::select("
                    SELECT
                    approvals.eo_number,
                    approvals.attention,
                    approvals.destination_shortname,
                    IF
                    ( approvals.qty = approvals.approved, 'Fully Approved', 'Partially Approved' ) AS `status`,
                    approvals.applicant_date,
                    approvals.final_approval_date,
                    users.username,
                    users.`name`,
                    max_approvers.approver_id,
                    max_approvers.approver_name,
                    max_approvers.remark
                    FROM
                    (
                        SELECT
                        extra_orders.eo_number,
                        extra_orders.attention,
                        extra_orders.destination_shortname,
                        COUNT( extra_order_approvals.id ) AS qty,
                        SUM(
                            IF
                            ( extra_order_approvals.`status` = 'Approved', 1, 0 )) AS approved,
                        MAX( extra_order_approvals.created_at ) AS applicant_date,
                        MAX( extra_order_approvals.id ) AS final_approval_id,
                        MAX( extra_order_approvals.updated_at ) AS final_approval_date,
                        MAX( extra_order_approvals.created_by ) AS applicant_id
                        FROM
                        extra_orders
                        LEFT JOIN extra_order_approvals ON extra_order_approvals.eo_number = extra_orders.eo_number
                        GROUP BY
                        extra_orders.eo_number,
                        extra_orders.attention,
                        extra_orders.destination_shortname
                        ) AS approvals
                    LEFT JOIN ( SELECT * FROM extra_order_approvals WHERE approval_order = 5 ) AS max_approvers ON max_approvers.id = approvals.final_approval_id
                    LEFT JOIN users ON users.id = approvals.applicant_id
                    WHERE
                    approvals.qty > 0
                    AND DATE_FORMAT( approvals.applicant_date, '%Y-%m-%d' ) >= '" . $date_from . "'
                    AND DATE_FORMAT( approvals.applicant_date, '%Y-%m-%d' ) <= '" . $date_to . "'
                    " . $extra_order_where . "
                    " . $extra_order_status . "
                    ORDER BY
                    approvals.final_approval_date DESC;");

                foreach ($extra_order as $row) {
                    array_push($resumes, [
                        'approval' => 'Extra Order',
                        'final_date' => $row->final_approval_date,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->remark,
                        'id' => $row->eo_number,
                        'status' => $row->status,
                        'subject' => '(Extra Order)<br>Recipient : ' . ucwords($row->attention) . '<br>Destination : ' . strtoupper($row->destination_shortname),
                        'hr_matter' => 'NO',
                        'category' => '-',
                        'applicant_id' => strtoupper($row->username),
                        'applicant_name' => ucwords($row->name),
                        'application_date' => $row->applicant_date,
                        'amount' => '-',
                        'unit' => '-',
                    ]);
                }

            }

            $tunjangan_keluarga = array();
            if (in_array("Tunjangan Keluarga", $approvals)) {

                $tunjangan_keluarga_where = "";
                if (strlen($applicant) > 0) {
                    $tunjangan_keluarga_where = "AND users.username in (" . $applicant . ")";
                }

                $tunjangan_keluarga_status = "";
                if (strlen($status) > 0) {
                    $tunjangan_keluarga_status = "Having STATUS in (" . $status . ")";
                }

                $tunjangan_keluarga = db::select("
                    SELECT
                    'Tunjangan Keluarga' AS approval,
                    DATE_FORMAT( td.final_date, '%Y-%m-%d' ) AS final_date,
                    td.approver_id AS approver_id,
                    td.approver_name AS approver_name,
                    'Manager HR/GA' as approver_position,
                    t.request_id AS id,
                    IF
                    (
                        t.remark = 'Sudah Download',
                        'Fully Approved',
                        IF
                        ( t.remark = 'Reject', 'Rejected', IF ( t.remark = 'Done', 'Fully Approved', 'Partially Approved' ) )
                        ) AS STATUS,
                    concat( '(Tunjangan Keluarga) ', t.permohonan ) AS SUBJECT,
                    IF
                    ( t.department = 'Human Resources Department', 'YES', 'NO' ) AS hr_matter,
                    '-' AS category,
                    UPPER( u.username ) AS applicant_id,
                    u.NAME AS applicant_name,
                    t.created_at AS application_date,
                    '-' AS amount,
                    '-' AS unit
                    FROM
                    uang_keluargas AS t
                    LEFT JOIN users AS u ON u.id = t.created_by
                    LEFT JOIN (
                        SELECT
                        a.request_id,
                        a.approver_id,
                        a.approver_name,
                        a.remark AS approver_position,
                        a.updated_at AS final_date
                        FROM
                        hr_approvals AS a
                        JOIN ( SELECT request_id, max( id ) AS max_id FROM hr_approvals AS b GROUP BY request_id ) AS b ON b.max_id = a.id
                        ) AS td ON td.request_id = t.request_id
                    WHERE
                    DATE_FORMAT( t.created_at, '%Y-%m-%d' ) >= '" . $date_from . "'
                    AND DATE_FORMAT( t.created_at, '%Y-%m-%d' ) <= '" . $date_to . "'
                    " . $tunjangan_keluarga_where . "
                    " . $tunjangan_keluarga_status . "
                    ORDER BY
                    final_date DESC");

                foreach ($tunjangan_keluarga as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'final_date' => $row->final_date,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->approver_position,
                        'id' => $row->id,
                        'status' => $row->STATUS,
                        'subject' => ucwords($row->SUBJECT),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }

            }

            $uang_simpati = array();
            if (in_array("Uang Simpati", $approvals)) {

                $uang_simpati_where = "";
                if (strlen($applicant) > 0) {
                    $uang_simpati_where = "AND users.username in (" . $applicant . ")";
                }

                $uang_simpati_status = "";
                if (strlen($status) > 0) {
                    $uang_simpati_status = "Having STATUS in (" . $status . ")";
                }

                $uang_simpati = db::select("
                    SELECT
                    'Uang Simpati' AS approval,
                    DATE_FORMAT( td.final_date, '%Y-%m-%d' ) AS final_date,
                    td.approver_id AS approver_id,
                    td.approver_name AS approver_name,
                    'Manager HR/GA' as approver_position,
                    t.request_id AS id,
                    IF
                    (
                        t.remark = 'Sudah Download',
                        'Fully Approved',
                        IF
                        ( t.remark = 'Reject', 'Rejected', IF ( t.remark = 'Done', 'Fully Approved', 'Partially Approved' ) )
                        ) AS STATUS,
                    SUBSTRING_INDEX( t.permohonan, '/', 1 ) AS SUBJECT,
                    IF
                    ( t.department = 'Human Resources Department', 'YES', 'NO' ) AS hr_matter,
                    '-' AS category,
                    UPPER( u.username ) AS applicant_id,
                    u.NAME AS applicant_name,
                    t.created_at AS application_date,
                    SUBSTRING_INDEX( t.permohonan, '/', - 1 ) AS amount,
                    '-' AS unit
                    FROM
                    uang_simpatis AS t
                    LEFT JOIN users AS u ON u.id = t.created_by
                    LEFT JOIN (
                        SELECT
                        a.request_id,
                        a.approver_id,
                        a.approver_name,
                        a.remark AS approver_position,
                        a.updated_at AS final_date
                        FROM
                        hr_approvals AS a
                        JOIN ( SELECT request_id, max( id ) AS max_id FROM hr_approvals AS b GROUP BY request_id ) AS b ON b.max_id = a.id
                        ) AS td ON td.request_id = t.request_id
                    WHERE
                    DATE_FORMAT( t.created_at, '%Y-%m-%d' ) >= '" . $date_from . "'
                    AND DATE_FORMAT( t.created_at, '%Y-%m-%d' ) <= '" . $date_to . "'
                    " . $uang_simpati_where . "
                    " . $uang_simpati_status . "
                    ORDER BY
                    final_date DESC");

                foreach ($uang_simpati as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'final_date' => $row->final_date,
                        'approver_id' => $row->approver_id,
                        'approver_name' => $row->approver_name,
                        'approver_position' => $row->approver_position,
                        'id' => $row->id,
                        'status' => $row->STATUS,
                        'subject' => ucwords($row->SUBJECT),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }

            }

            $tiga_em = array();
            if (in_array("3M", $approvals)) {

                $tiga_em_where = "";
                if (strlen($applicant) > 0) {
                    $tiga_em_where = "AND sakurentsu_three_ms.created_by in (" . $applicant . ")";
                }

                $tiga_em_status = "";
                if (strlen($status) > 0) {
                    $tiga_em_status = "Having status in (" . $status . ")";
                }

                $tiga_em = db::select("
                    SELECT '3M' as approval, appr.app_at as final_date, appr.approver_id, appr.approver_name, appr.position ,form_identity_number as id, IF(appr.app_at is null,'Partially Approved', 'Fully Approved') as `status`, title as `subject`, 'NO' as hr_matter, '-' as category, employee_syncs.employee_id as applicant_id, employee_syncs.name as applicant_name, sakurentsu_three_ms.created_at as application_date, '-' as amount, '-' as unit from sakurentsu_three_ms
                    left join employee_syncs on sakurentsu_three_ms.created_by = employee_syncs.employee_id
                    left join (select form_id, approver_id, approver_name, position, DATE_FORMAT(approve_at,'%Y-%m-%d') app_at from sakurentsu_three_m_approvals where position = 'President Director') appr on appr.form_id = sakurentsu_three_ms.id
                    where DATE_FORMAT(sakurentsu_three_ms.created_at, '%Y-%m-%d') >=  '" . $date_from . "'
                    AND DATE_FORMAT(sakurentsu_three_ms.created_at, '%Y-%m-%d') <= '" . $date_to . "'
                    " . $tiga_em_where . "
                    " . $tiga_em_status . "
                    ORDER BY final_date desc");

                $final_appr = db::select("SELECT employee_id, `name`, position from employee_syncs where position = 'President Director' and end_date is null");

                foreach ($tiga_em as $row) {
                    array_push($resumes, [
                        'approval' => $row->approval,
                        'final_date' => $row->final_date,
                        'approver_id' => $final_appr[0]->employee_id,
                        'approver_name' => $final_appr[0]->name,
                        'approver_position' => $final_appr[0]->position,
                        'id' => $row->id,
                        'status' => $row->status,
                        'subject' => '( 3M ) ' . ucwords($row->subject),
                        'hr_matter' => $row->hr_matter,
                        'category' => $row->category,
                        'applicant_id' => strtoupper($row->applicant_id),
                        'applicant_name' => $row->applicant_name,
                        'application_date' => $row->application_date,
                        'amount' => $row->amount,
                        'unit' => $row->unit,
                    ]);
                }
            }

            usort($resumes, function ($a, $b) {
                return strcmp($a['final_date'], $b['final_date']);
            });

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

    public function indexResumeApprovalNew()
    {
        $title = 'Resume Approval';
        $title_jp = '';

        $employees = EmployeeSync::where('position', 'NOT LIKE', '%Operator%')
            ->orderBy('hire_date', 'asc')->get();

        return view('general.resume_approval_new', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'approvals' => $this->approvals,
            'categories' => $this->categories,
        ))->with('page', 'Resume Approval');
    }

    public function fetchGeneralPointingCall(Request $request)
    {
        $pic = "";
        $pic_next = "";
        $safety_riding_records = "";
        $safety_riding_approvers = "";

        if ($request->get('location') == 'japanese') {
            $pics = db::table('pointing_calls')
                ->where('location', '=', $request->get('location'))
                ->where('point_title', '=', 'pic')
                ->whereNull('deleted_at')
                ->get();

            $pointing_calls = db::table('pointing_calls')
                ->where('location', '=', $request->get('location'))
                ->where('point_title', '<>', 'pic')
                ->where('point_title', '<>', 'safety_riding')
                ->where('remark', '1')
                ->whereNull('deleted_at')
                ->get();

            $response = array(
                'status' => true,
                'pointing_calls' => $pointing_calls,
                'pics' => $pics,
            );
            return Response::json($response);
        }

        if ($request->get('location') == 'aturan_keselamatan') {
            $pointing_calls = db::connection('ympimis_2')->table('pointing_calls')
                ->where('location', '=', $request->get('location'))
                ->whereNull('deleted_at')
                ->get();

            $response = array(
                'status' => true,
                'pointing_calls' => $pointing_calls,
            );
            return Response::json($response);
        }

        if ($request->get('location') == 'national') {

            $pic = db::table('pointing_call_pics')->where('remark', '=', 1)->first();
            $index_next = $pic->index + 1;
            $pic_next = db::table('pointing_call_pics')->where('index', '=', $index_next)->first();
// $pics = db::table('pointing_calls')
// ->where('location', '=', $request->get('location'))
// ->where('point_title', '=', 'pic')
// ->whereNull('deleted_at')
// ->get();

// $pointing_calls = db::table('pointing_calls')
// ->where('location', '=', $request->get('location'))
// ->where('point_title', '<>', 'pic')
// ->where('point_title', '<>', 'safety_riding')
// ->where('remark', '1')
// ->whereNull('deleted_at')
// ->get();

            $first = date('Y-m-01');
            $last = date('Y-m-t');

            $weekly_calendars = WeeklyCalendar::where('week_date', '>=', $first)
                ->where('week_date', '<=', $last)
                ->select('week_date', db::raw('date_format(week_date, "%d") as header'), 'remark')
                ->get();

            $safety_ridings = "";
            $department = "";

            $employee = Employee::where('employees.employee_id', '=', Auth::user()->username)
                ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'employees.employee_id')
                ->select('employees.remark', 'employee_syncs.department')
                ->first();

            if ($employee) {
                $where_department = "AND safety_ridings.department = '" . $employee->department . "'";

                if ($employee->department == 'Educational Instrument (EI) Department') {
                    $where_department = "AND (safety_ridings.department = '" . $employee->department . "' or safety_ridings.department = 'Woodwind Instrument - Key Parts Process (WI-KPP) Department')";
                }
                if ($employee->department == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $where_department = "AND (safety_ridings.department = '" . $employee->department . "' or safety_ridings.department = 'Educational Instrument (EI) Department')";
                }
                if ($employee->department == 'Woodwind Instrument - Welding Process (WI-WP) Department') {
                    $where_department = "AND (safety_ridings.department = '" . $employee->department . "' or safety_ridings.department = 'Woodwind Instrument - Body Parts Process (WI-BPP) Department')";
                }
                if ($employee->department == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
                    $where_department = "AND (safety_ridings.department = '" . $employee->department . "' or safety_ridings.department = 'Woodwind Instrument - Welding Process (WI-WP) Department')";
                }
                if ($employee->department == 'Procurement Department') {
                    $where_department = "AND (safety_ridings.department = '" . $employee->department . "' or safety_ridings.department = 'Purchasing Control Department')";
                }
                if ($employee->department == 'Purchasing Control Department') {
                    $where_department = "AND (safety_ridings.department = '" . $employee->department . "' or safety_ridings.department = 'Procurement Department')";
                }

                $safety_ridings = db::select("SELECT
                        *
                    FROM
                    safety_ridings
                    JOIN employee_syncs ON employee_syncs.employee_id = safety_ridings.employee_id
                    WHERE
                    location = 'OFC'
                    AND period = '" . $first . "'
                    " . $where_department . "
                    ORDER BY
                    hire_date");

                $department = $employee->department;
            }

            $pointing_calls = db::select("SELECT
                pc.point_title,
                pc.point_description,
                pc.point_no,
                pm.point_max
                FROM
                pointing_calls AS pc
                LEFT JOIN (
                    SELECT
                    point_title,
                    max( point_no ) AS point_max
                    FROM
                    `pointing_calls`
                    WHERE
                    location = '" . $request->get('location') . "'
                    AND deleted_at IS NULL
                    AND point_title <> 'pic'
                    AND point_title <> 'safety_riding'
                    GROUP BY
                    point_title
                    ) AS pm ON pm.point_title = pc.point_title
                    WHERE
                    pc.location = '" . $request->get('location') . "'
                    AND pc.remark = '1'
                    AND pc.deleted_at IS NULL
                    AND pc.point_title <> 'pic'
                    AND pc.point_title <> 'safety_riding'");

            $response = array(
                'status' => true,
                'pic_next' => $pic_next,
                'pic' => $pic,
                'pointing_calls' => $pointing_calls,
                'safety_ridings' => $safety_ridings,
                // 'safety_riding_records' => $safety_riding_records,
                'weekly_calendars' => $weekly_calendars,
                // 'safety_riding_approvers' => $safety_riding_approvers,
                'department' => $department,
                'employee' => $employee,
            );
            return Response::json($response);
        }
    }

    public function indexCo()
    {
        $title = 'CO2 Monitoring';
        $title_jp = '??';

        return view('general.co_map', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'CO2 Monitoring');
    }

    public function fetchCo()
    {
        $datas = db::select('SELECT DATE_FORMAT(data_time,"%H:%i") as jam, unit, MAX(sensor_value) as sensor_value from sensor_datas where category = "CO2" and DATE_FORMAT(data_time,"%H %i") >= "06 00" and DATE_FORMAT(data_time,"%Y-%m-%d") = "' . date('Y-m-d') . '"
                group by DATE_FORMAT(data_time,"%H:%i"), unit
                order by data_time asc');

        $last_data = db::select('SELECT id, unit, sensor_value
                FROM sensor_datas
                WHERE id IN (
                    SELECT MAX(id)
                    FROM sensor_datas
                    where DATE_FORMAT(data_time,"%Y-%m-%d") = "' . date('Y-m-d') . '" and category = "CO2"
                    GROUP BY unit
                    )
                    order by unit asc');

        $response = array(
            'status' => true,
            'datas' => $datas,
            'last_data' => $last_data,
        );
        return Response::json($response);
    }

    public function indexSafetyRidingRecord($id)
    {
        $param = explode("_", $id);

        $title = "Safety Riding";
        $title_jp = "『安全運転宣言』　実践記録表";
        $month = date('Y-m', strtotime($param[0]));
        $location = $param[1];
        $department = $param[2];

        return view('general.pointing_call.safety_riding_record', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'mon' => $month,
            'location' => $location,
            'department' => $department,
            'param' => $id,
        ))->with('page', 'Safety Riding');
    }

    public function inputSafetyRidingRecord(Request $request)
    {
        $safety_riding_record = SafetyRidingRecord::where('employee_id', '=', $request->get('employee_id'))
            ->where('due_date', '=', $request->get('due_date'))
            ->first();

        if ($safety_riding_record == "") {
            $safety_riding_record = new SafetyRidingRecord([
                'period' => date('Y-m-01', strtotime($request->get('due_date'))),
                'location' => $request->get('location'),
                'department' => $request->get('department'),
                'due_date' => $request->get('due_date'),
                'employee_id' => $request->get('employee_id'),
                'remark' => $request->get('remark'),
                'created_by' => Auth::id(),
            ]);
        } else {
            $safety_riding_record->remark = $request->get('remark');
        }

        $safety_riding_record->save();

        $response = array(
            'status' => true,
            'safety_riding_record' => $safety_riding_record,
        );
        return Response::json($response);
    }

    public function approveSafetyRidingRecord(Request $request)
    {
        $remark = explode('-', $request->get('remark'));
        $period = date('Y-m-01', strtotime($request->get('month')));

        try {
            if ($remark[0] == 'manager') {
                $approver = db::select("SELECT
                         *
                           FROM
                           approvers
                           WHERE
                           department = '" . $request->get('department') . "'
                           AND remark = 'Manager'");
                $check_approver = 0;

                foreach ($approver as $row) {
                    if ($row->approver_id == Auth::user()->username) {
                        $check_approver = 1;
                    }
                }

                if (!str_contains(Auth::user()->role_code, 'MIS')) {
                    if ($check_approver = 0) {
                        $response = array(
                            'status' => false,
                            'message' => "Anda tidak memiliki otoritas melakukan konfirmasi.",
                        );
                        return Response::json($response);
                    }
                }

                $chief = db::table('approvers')->where('department', '=', $request->get('department'))
                    ->where('remark', '=', 'Chief')
                    ->whereNotNull('approver_id')
                    ->first();

                if ($chief != "") {
                    $check_chief = SafetyRidingApprover::where('location', '=', $request->get('location'))
                        ->where('period', '=', $period)
                        ->where('department', '=', $request->get('department'))
                        ->where('remark', '=', 'chief-' . $remark[1])
                        ->first();

                    if ($check_chief == "") {
                        $response = array(
                            'status' => false,
                            'message' => "Approver sebelumnya belum melakukan konfirmasi.",
                        );
                        return Response::json($response);
                    }
                }

                $check_janji = SafetyRiding::where('location', '=', $request->get('location'))
                    ->where('period', '=', $period)
                    ->where('department', '=', $request->get('department'))
                    ->get();

                if (count($check_janji) <= 0) {
                    $response = array(
                        'status' => false,
                        'message' => "Janji Safety Riding belum dibuat.",
                    );
                    return Response::json($response);
                }

                foreach ($check_janji as $row) {
                    if ($row->safety_riding == "") {
                        $response = array(
                            'status' => false,
                            'message' => "Karyawan " . $row->employee_name . " belum membuat janji.",
                        );
                        return Response::json($response);
                    }
                }

                if ($remark[1] == 'after') {
                    $check_record = db::select("SELECT
                                sr.employee_id,
                                sr.employee_name,
                                sr.safety_riding,
                                srr.due_date
                                FROM
                                safety_ridings AS sr
                                CROSS JOIN ( SELECT week_date FROM weekly_calendars WHERE date_format( weekly_calendars.week_date, '%Y-%m-01' ) = '" . $period . "' ) AS wc
                                    LEFT JOIN safety_riding_records AS srr ON srr.due_date = wc.week_date
                                    WHERE
                                    sr.location = '" . $request->get('location') . "'
                                    AND sr.department = '" . $request->get('department') . "'
                                    AND sr.period = '" . $period . "'
                                    ORDER BY
                                    sr.employee_id ASC");

                    foreach ($check_record as $row) {
                        if ($row->due_date == "") {
                            $response = array(
                                'status' => false,
                                'message' => "Terdapat Janji Safety Riding yang belum dilakukan check-in.",
                            );
                            return Response::json($response);
                        }
                    }
                }

                if ($chief == "") {
                    $approve = new SafetyRidingApprover([
                        'period' => $period,
                        'department' => $request->get('department'),
                        'employee_id' => Auth::user()->username,
                        'employee_name' => Auth::user()->name,
                        'remark' => 'chief-' . $remark[1],
                        'location' => $request->get('location'),
                        'created_by' => Auth::id(),
                    ]);
                    $approve->save();
                }
            }

            if ($remark[0] == 'chief') {

                $approver = db::select("SELECT
                                *
                                FROM
                                approvers
                                WHERE
                                department = '" . $request->get('department') . "'
                                AND remark = 'Chief'");
                $check_approver = 0;

                foreach ($approver as $row) {
                    if ($row->approver_id == Auth::user()->username) {
                        $check_approver = 1;
                    }
                }

                if (!str_contains(Auth::user()->role_code, 'MIS')) {
                    if ($check_approver = 0) {
                        $response = array(
                            'status' => false,
                            'message' => "Anda tidak memiliki otoritas melakukan konfirmasi.",
                        );
                        return Response::json($response);
                    }
                }

                $check_janji = SafetyRiding::where('location', '=', $request->get('location'))
                    ->where('period', '=', $period)
                    ->where('department', '=', $request->get('department'))
                    ->get();

                if (count($check_janji) <= 0) {
                    $response = array(
                        'status' => false,
                        'message' => "Janji Safety Riding belum dibuat.",
                    );
                    return Response::json($response);
                }

                foreach ($check_janji as $row) {
                    if ($row->safety_riding == "") {
                        $response = array(
                            'status' => false,
                            'message' => "Karyawan " . $row->employee_name . " belum membuat janji.",
                        );
                        return Response::json($response);
                    }
                }

                if ($remark[1] == 'after') {
                    $check_record = db::select("SELECT
                                 sr.employee_id,
                                 sr.employee_name,
                                 sr.safety_riding,
                                 srr.due_date
                                 FROM
                                 safety_ridings AS sr
                                 CROSS JOIN ( SELECT week_date FROM weekly_calendars WHERE date_format( weekly_calendars.week_date, '%Y-%m-01' ) = '" . $period . "' ) AS wc
                                     LEFT JOIN safety_riding_records AS srr ON srr.due_date = wc.week_date
                                     WHERE
                                     sr.location = '" . $request->get('location') . "'
                                     AND sr.department = '" . $request->get('department') . "'
                                     AND sr.period = '" . $period . "'
                                     ORDER BY
                                     sr.employee_id ASC");

                    foreach ($check_record as $row) {
                        if ($row->due_date == "") {
                            $response = array(
                                'status' => false,
                                'message' => "Terdapat Janji Safety Riding yang belum dilakukan check-in.",
                            );
                            return Response::json($response);
                        }
                    }
                }
            }

            $approve = new SafetyRidingApprover([
                'period' => $period,
                'department' => $request->get('department'),
                'employee_id' => Auth::user()->username,
                'employee_name' => Auth::user()->name,
                'remark' => $request->get('remark'),
                'location' => $request->get('location'),
                'created_by' => Auth::id(),
            ]);
            $approve->save();

            $response = array(
                'status' => true,
                'message' => 'Janji Safety Riding berhasil dikonfirmasi.',
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

    public function fetchSafetyRidingRecord(Request $request)
    {

        $first = date('Y-m-01', strtotime($request->get('month')));
        $last = date('Y-m-t', strtotime($request->get('month')));
        $month = date('F Y');
        $mon = date('m', strtotime($request->get('month')));
        $year = date('Y', strtotime($request->get('month')));

// $employee = Employee::where('employees.employee_id', '=', Auth::user()->username)
// ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'employees.employee_id')
// ->select('employees.remark', 'employee_syncs.department')
// ->first();

        $weekly_calendars = WeeklyCalendar::where('week_date', '>=', $first)
            ->where('week_date', '<=', $last)
            ->select('week_date', db::raw('date_format(week_date, "%d") as header'), 'remark')
            ->get();

        $safety_ridings = SafetyRiding::where('location', '=', 'OFC')
            ->where('period', '=', $first)
            ->where('department', '=', $request->get('department'))
            ->select(
                'safety_ridings.period',
                'safety_ridings.department',
                'safety_ridings.employee_id',
                'safety_ridings.employee_name',
                'safety_ridings.safety_riding'
            )
            ->get();

        $safety_riding_records = SafetyRidingRecord::where('period', '=', $first)
            ->where('location', '=', $request->get('location'))
            ->where('department', '=', $request->get('department'))
            ->get();

        $safety_riding_approvers = SafetyRidingApprover::where('period', '=', $first)
            ->where('location', '=', $request->get('location'))
            ->where('department', '=', $request->get('department'))
            ->get();

        if (count($safety_ridings) <= 0) {
            $response = array(
                'status' => false,
                'message' => "Janji Safety Riding belum dibuat.",
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'month' => $month,
            'mon' => $mon,
            'year' => $year,
            'weekly_calendars' => $weekly_calendars,
            'safety_ridings' => $safety_ridings,
            'safety_riding_records' => $safety_riding_records,
            'safety_riding_approvers' => $safety_riding_approvers,
        );
        return Response::json($response);
    }

    public function indexSafetyRiding()
    {
        $title = "Safety Riding";
        $title_jp = "『安全運転宣言』　実践記録表";

        $employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();

        $employees = EmployeeSync::where('department', '=', $employee->department)->orderBy('hire_date', 'asc')->get();

        return view('general.pointing_call.safety_riding', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
        ))->with('page', 'Safety Riding');
    }

    public function fetchSafetyRiding()
    {
        $having = "";
        if (!str_contains(Auth::user()->role_code, 'STD')) {

            $employee = Employee::where('employees.employee_id', '=', Auth::user()->username)
                ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'employees.employee_id')
                ->select('employees.remark', 'employee_syncs.department')
                ->first();

            $having = "WHERE sr.department = '" . $employee->department . "'";
        }

        $safety_ridings = db::select("SELECT
                       sr.period,
                       sr.location,
                       sr.department,
                       sr.department_shortname,
                       sr.total_emp,
                       sr.total_sr,
                       IFNULL( srr.maru, 0 ) AS maru,
                       IFNULL( srr.batsu, 0 ) AS batsu,
                       wc.total_date
                       FROM
                       (
                           SELECT
                           sr.period,
                           sr.location,
                           sr.department,
                           d.department_shortname,
                           count( sr.employee_id ) AS total_emp,
                           count(
                               IF
                               ( sr.safety_riding = '', NULL, 1 )) AS total_sr
                           FROM
                           safety_ridings AS sr
                           LEFT JOIN departments AS d ON d.department_name = sr.department
                           GROUP BY
                           sr.period,
                           sr.location,
                           sr.department,
                           d.department_shortname
                           ) AS sr
                       LEFT JOIN (
                           SELECT
                           srr.period,
                           srr.location,
                           srr.department,
                           count(
                               IF
                               ( srr.remark = 'maru', 1, NULL )) AS maru,
                           count(
                               IF
                               ( srr.remark = 'batsu', 1, NULL )) AS batsu
                           FROM
                           safety_riding_records AS srr
                           GROUP BY
                           period,
                           location,
                           department
                           ) AS srr ON srr.period = sr.period
                       AND srr.location = sr.location
                       AND srr.department = sr.department
                       LEFT JOIN (
                           SELECT
                           date_format( week_date, '%Y-%m-01' ) AS period,
                           count( week_date ) AS total_date
                           FROM
                           weekly_calendars
                           GROUP BY
                           date_format( week_date, '%Y-%m-01' )) AS wc ON wc.period = sr.period
                       " . $having . "
                       ORDER BY
                       sr.period DESC");

        $approvers = db::select("SELECT
                       period,
                       department,
                       employee_id,
                       employee_name,
                       remark,
                       location,
                       created_at
                       FROM
                       safety_riding_approvers");

        $results = array();

        foreach ($safety_ridings as $safety_riding) {
            $cb_name = "";
            $cb_at = "";
            $ca_name = "";
            $ca_at = "";
            $mb_name = "";
            $mb_at = "";
            $ma_name = "";
            $ma_at = "";

            foreach ($approvers as $approver) {
                if ($approver->location == $safety_riding->location && $approver->period == $safety_riding->period && $approver->department == $safety_riding->department && $approver->remark == 'chief-before') {
                    $cb_name = $approver->employee_name;
                    $cb_at = $approver->created_at;
                }
                if ($approver->location == $safety_riding->location && $approver->period == $safety_riding->period && $approver->department == $safety_riding->department && $approver->remark == 'manager-before') {
                    $mb_name = $approver->employee_name;
                    $mb_at = $approver->created_at;
                }
                if ($approver->location == $safety_riding->location && $approver->period == $safety_riding->period && $approver->department == $safety_riding->department && $approver->remark == 'chief-after') {
                    $ca_name = $approver->employee_name;
                    $ca_at = $approver->created_at;
                }
                if ($approver->location == $safety_riding->location && $approver->period == $safety_riding->period && $approver->department == $safety_riding->department && $approver->remark == 'manager-after') {
                    $ma_name = $approver->employee_name;
                    $ma_at = $approver->created_at;
                }
            }

            array_push($results, [
                'period' => $safety_riding->period,
                'location' => $safety_riding->location,
                'department' => $safety_riding->department,
                'department_shortname' => $safety_riding->department_shortname,
                'total_emp' => $safety_riding->total_emp,
                'total_sr' => $safety_riding->total_sr,
                'maru' => $safety_riding->maru,
                'batsu' => $safety_riding->batsu,
                'total_date' => $safety_riding->total_date,
                'cb_name' => $cb_name,
                'cb_at' => $cb_at,
                'ca_name' => $ca_name,
                'ca_at' => $ca_at,
                'mb_name' => $mb_name,
                'mb_at' => $mb_at,
                'ma_name' => $ma_name,
                'ma_at' => $ma_at,
            ]);
        }

        $response = array(
            'status' => true,
            'safety_ridings' => $results,
        );
        return Response::json($response);
    }

    public function fetchSafetyRidingPdf($id)
    {

        $param = explode('_', $id);

        $first = date('Y-m-01', strtotime($param[0]));
        $last = date('Y-m-t', strtotime($param[0]));

        $weekly_calendars = WeeklyCalendar::where('week_date', '>=', $first)
            ->where('week_date', '<=', $last)
            ->get();

        $safety_ridings = db::select("SELECT
                       sr.period,
                       sr.location,
                       sr.department,
                       sr.employee_id,
                       sr.employee_name,
                       sr.safety_riding,
                       u.`name`,
                       srr.due_date,
                       srr.remark
                       FROM
                       safety_ridings AS sr
                       LEFT JOIN users AS u ON u.id = sr.created_by
                       LEFT JOIN safety_riding_records AS srr ON srr.employee_id = sr.employee_id
                       AND srr.period = sr.period
                       WHERE
                       sr.location = '" . $param[1] . "'
                       AND sr.period = '" . $param[0] . "'
                       AND sr.department = '" . $param[2] . "'");

        $safety_riding_approvers = SafetyRidingApprover::where('period', '=', $first)
            ->where('location', '=', $param[1])
            ->where('department', '=', $param[2])
            ->get();

        $cb_name = "";
        $cb_at = "";
        $mb_name = "";
        $mb_at = "";
        $ca_name = "";
        $ca_at = "";
        $ma_name = "";
        $ma_at = "";

        foreach ($safety_riding_approvers as $safety_riding_approver) {
            if ($safety_riding_approver->remark == 'chief-before') {
                $cb_name = $safety_riding_approver->employee_name;
                $cb_at = $safety_riding_approver->created_at;
            }
            if ($safety_riding_approver->remark == 'manager-before') {
                $mb_name = $safety_riding_approver->employee_name;
                $mb_at = $safety_riding_approver->created_at;
            }
            if ($safety_riding_approver->remark == 'chief-after') {
                $ca_name = $safety_riding_approver->employee_name;
                $ca_at = $safety_riding_approver->created_at;
            }
            if ($safety_riding_approver->remark == 'manager-after') {
                $ma_name = $safety_riding_approver->employee_name;
                $ma_at = $safety_riding_approver->created_at;
            }
        }

        $safety_riding_records = SafetyRidingRecord::where('period', '=', $first)
            ->where('location', '=', $param[1])
            ->where('department', '=', $param[2])
            ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');

// $pdf->loadView('general.pointing_call.safety_riding_pdf', array(
//     'weekly_calendars' => $weekly_calendars,
//     'safety_ridings' => $safety_ridings,
//     'chief' => $chief,
//     'manager' => $manager
// ));

// return $pdf->stream("general.pointing_call.safety_riding_pdf");

        return view('general.pointing_call.safety_riding_pdf', array(
            'weekly_calendars' => $weekly_calendars,
            'safety_ridings' => $safety_ridings,
            'safety_riding_records' => $safety_riding_records,
            'cb_name' => $cb_name,
            'cb_at' => $cb_at,
            'mb_name' => $mb_name,
            'mb_at' => $mb_at,
            'ca_name' => $ca_name,
            'ca_at' => $ca_at,
            'ma_name' => $ma_name,
            'ma_at' => $ma_at,
        ));
    }

    public function fetchSafetyRidingMember(Request $request)
    {
        $employee = Employee::where('employees.employee_id', '=', Auth::user()->username)
            ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'employees.employee_id')
            ->select('employees.remark', 'employee_syncs.department')
            ->first();

        $where_department = "AND employee_syncs.department = '" . $employee->department . "'";

        // if($employee->department == 'Purchasing Control Department' || $employee->department == 'Purchasing Department'){
        //     $where_department = "AND employee_syncs.department = 'Procurement Department'";
        // }

        $employees = db::select("SELECT
                       employee_syncs.employee_id,
                       employee_syncs.name,
                       employee_syncs.department,
                       employees.remark,
                       sr.safety_riding
                       FROM
                       employees
                       LEFT JOIN employee_syncs ON employee_syncs.employee_id = employees.employee_id
                       LEFT JOIN ( SELECT employee_id, safety_riding FROM safety_ridings WHERE safety_ridings.period = '" . date('Y-m-01', strtotime($request->get('period'))) . "' ) AS sr ON sr.employee_id = employees.employee_id
                           WHERE
                           employees.remark = '" . $employee->remark . "'
                           " . $where_department . "
                           AND employee_syncs.end_date IS NULL");

        $response = array(
            'status' => true,
            'employees' => $employees,
        );
        return Response::json($response);
    }

    public function createSafetyRiding(Request $request)
    {
        try {

            foreach ($request->get('safety_ridings') as $safety_riding) {
                $safety = explode('_', $safety_riding);

                $input = SafetyRiding::updateOrCreate(
                    [
                        'period' => date('Y-m-01', strtotime($request->get('period'))),
                        'employee_id' => $safety[0],
                        'department' => $request->get('department'),
                    ],
                    [
                        'period' => date('Y-m-01', strtotime($request->get('period'))),
                        'location' => $request->get('location'),
                        'department' => $request->get('department'),
                        'employee_id' => $safety[0],
                        'employee_name' => $safety[1],
                        'safety_riding' => $safety[2],
                        'created_by' => Auth::id(),
                    ]
                );

                $input->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Safety Riding berhasil dibuat',
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

    public function indexAgreement()
    {

        $title = "Control Law & Agreement";
        $title_jp = "";

        $employees = EmployeeSync::orderBy('department', 'asc')->whereNotNull('department')->get();

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
            ->first();

        $dept = Department::select('alias')
            ->distinct()
            ->get();

        if (count($emp) == 0) {
            $emp = "";
        }

        return view('general.agreements.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'emp' => $emp,
            'dept' => $dept,
            'agreement_statuses' => $this->agreement_statuses,
            'regulation_statuses' => $this->regulation_statuses,
        ));
    }

    public function indexRegulation()
    {

        $title = "Regulation Data";
        $title_jp = "";

        return view('general.agreements.index_regulation', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'regulation_statuses' => $this->regulation_statuses,
        ));

    }

    public function editAgreement(Request $request)
    {
        $filename = "";
        $file_destination = 'files/agreements';

        if (count($request->file('newAttachment')) > 0) {
            $file = $request->file('newAttachment');
            $filename = date('YmdHis') . '.' . $request->input('extension');
            $file->move($file_destination, $filename);
        }
        // else{
        //     $response = array(
        //         'status' => false,
        //         'message' => 'Please select file to attach'
        //     );
        //     return Response::json($response);
        // }

        try {
            $agreement = Agreement::where('id', '=', $request->get('newId'))->first();

            if ($agreement->department == $request->input('newDepartment') && $agreement->vendor == $request->input('newVendor') && $agreement->description == $request->input('newDescription') && $agreement->valid_from == $request->input('newValidFrom') && $agreement->valid_to == $request->input('newValidTo') && $agreement->status == $request->input('newStatus') && $agreement->remark == $request->input('newRemark')) {

                $response = array(
                    'status' => false,
                    'message' => 'Tidak ada perubahan yang dibuat',
                );
                return Response::json($response);

            }

            $agreement->department = $request->input('newDepartment');
            $agreement->vendor = $request->input('newVendor');
            $agreement->description = $request->input('newDescription');
            $agreement->valid_from = $request->input('newValidFrom');
            $agreement->valid_to = $request->input('newValidTo');
            $agreement->status = $request->input('newStatus');
            $agreement->remark = $request->input('newRemark');
            $agreement->created_by = Auth::user()->username;
            $agreement->save();

            if (count($request->file('newAttachment')) > 0) {
                $attachment = new AgreementAttachment([
                    'agreement_id' => $request->get('newId'),
                    'file_name' => $filename,
                    'created_by' => Auth::user()->username,
                ]);
                $attachment->save();
            }

            $agreements = db::select("
                         SELECT
                         'update_agreement' as cat,
                         a.id,
                         a.department,
                         d.department_shortname,
                         a.vendor,
                         a.description,
                         a.valid_from,
                         a.valid_to,
                         TIMESTAMPDIFF( DAY, a.valid_from, a.valid_to ) AS total_validity,
                         TIMESTAMPDIFF( DAY, date( now()), a.valid_to ) AS validity,
                         a.`status`,
                         a.remark,
                         a.created_at,
                         a.updated_at,
                         a.created_by,
                         es.`name`,
                         att.file_name
                         FROM
                         agreements AS a
                         LEFT JOIN employee_syncs AS es ON es.employee_id = a.created_by
                         LEFT JOIN departments AS d ON d.department_name = a.department
                         LEFT JOIN (
                             SELECT
                             agreement_id,
                             file_name
                             FROM
                             agreement_attachments
                             WHERE
                             id = ( SELECT id FROM agreement_attachments WHERE agreement_id = " . $request->get('newId') . " ORDER BY created_at DESC LIMIT 1 )) AS att ON att.agreement_id = a.id
                                 WHERE
                                 a.id = " . $request->get('newId') . "");

            $manager = db::select("select email from send_emails where remark = '" . $request->input('newDepartment') . "'");

            Mail::to(['adhi.satya.indradhi@music.yamaha.com', Auth::user()->email])->cc(['prawoto@music.yamaha.com', $manager[0]->email])->bcc(['aditya.agassi@music.yamaha.com'])->send(new SendEmail($agreements, 'update_agreement'));

            $response = array(
                'status' => true,
                'message' => 'Agreement Updated',
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

    public function downloadAgreement(Request $request)
    {
        $filenames = $request->get('file_name');
        $paths = array();

        if (is_array($filenames)) {
            foreach ($filenames as $filename) {
                $path = "files/agreements/" . $filename;
                array_push($paths,
                    [
                        "download" => asset($path),
                        "filename" => $filename,
                    ]);
            }
        } else {
            $path = "files/agreements/" . $filenames;
            array_push($paths,
                [
                    "download" => asset($path),
                    "filename" => $filenames,
                ]);
        }

        $response = array(
            'status' => true,
            'file_paths' => $paths,
        );
        return Response::json($response);
    }

    public function downloadRegulation(Request $request)
    {
        $filenames = $request->get('file_name');
        $paths = array();

        if (is_array($filenames)) {
            foreach ($filenames as $filename) {
                $path = "files/agreements/regulation/" . $filename;
                array_push($paths,
                    [
                        "download" => asset($path),
                        "filename" => $filename,
                    ]);
            }
        } else {
            $path = "files/agreements/regulation/" . $filenames;
            array_push($paths,
                [
                    "download" => asset($path),
                    "filename" => $filenames,
                ]);
        }

        $response = array(
            'status' => true,
            'file_paths' => $paths,
        );
        return Response::json($response);
    }

    public function fetchAgreementDetail(Request $request)
    {
        $employees = EmployeeSync::orderBy('department', 'asc')
            ->select('department')
            ->distinct()
            ->get();

        $agreement = Agreement::where('agreements.id', '=', $request->get('id'))
            ->first();

        $response = array(
            'status' => true,
            'employees' => $employees,
            'agreement' => $agreement,
            'agreement_statuses' => $this->agreement_statuses,
            'regulation_statuses' => $this->regulation_statuses,
        );
        return Response::json($response);
    }

    public function fetchAgreementDownload(Request $request)
    {
        $files = AgreementAttachment::where('agreement_id', '=', $request->get('id'))->orderBy('created_at', 'desc')->get();

        $response = array(
            'status' => true,
            'files' => $files,
        );
        return Response::json($response);
    }

    public function createAgreement(Request $request)
    {
        $filename = "";
        $file_destination = 'files/agreements';

        if (count($request->file('newAttachment')) > 0) {
            $file = $request->file('newAttachment');
            $filename = date('YmdHis') . '.' . $request->input('extension');
            $file->move($file_destination, $filename);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Please select file to attach',
            );
            return Response::json($response);
        }

        try {
            $agreement = new Agreement([
                'category' => 'agreement',
                'department' => $request->input('newDepartment'),
                'vendor' => $request->input('newVendor'),
                'description' => $request->input('newDescription'),
                'valid_from' => $request->input('newValidFrom'),
                'valid_to' => $request->input('newValidTo'),
                'status' => $request->input('newStatus'),
                'remark' => $request->input('newRemark'),
                'created_by' => Auth::user()->username,
            ]);

            $agreement->save();

            $attachment = new AgreementAttachment([
                'agreement_id' => $agreement->id,
                'file_name' => $filename,
                'created_by' => Auth::user()->username,
            ]);

            $attachment->save();

            $agreements = db::select("SELECT
                               'new_agreement' as cat,
                               a.id,
                               a.department,
                               d.department_shortname,
                               a.vendor,
                               a.description,
                               a.valid_from,
                               a.valid_to,
                               TIMESTAMPDIFF( DAY, a.valid_from, a.valid_to ) AS total_validity,
                               TIMESTAMPDIFF( DAY, date( now()), a.valid_to ) AS validity,
                               a.`status`,
                               a.remark,
                               a.created_at,
                               a.updated_at,
                               a.created_by,
                               es.`name`,
                               att.file_name
                               FROM
                               agreements AS a
                               LEFT JOIN employee_syncs AS es ON es.employee_id = a.created_by
                               LEFT JOIN departments AS d ON d.department_name = a.department
                               LEFT JOIN (
                                   SELECT
                                   agreement_id,
                                   file_name
                                   FROM
                                   agreement_attachments
                                   WHERE
                                   id = ( SELECT id FROM agreement_attachments WHERE agreement_id = " . $agreement->id . " ORDER BY created_at DESC LIMIT 1 )) AS att ON att.agreement_id = a.id
                                       WHERE
                                       a.id = " . $agreement->id . "");

            $manager = db::select("select email from send_emails where remark = '" . $request->input('newDepartment') . "'");

            Mail::to(['adhi.satya.indradhi@music.yamaha.com', Auth::user()->email])->cc(['prawoto@music.yamaha.com', $manager[0]->email])->bcc(['aditya.agassi@music.yamaha.com'])->send(new SendEmail($agreements, 'new_agreement'));

            $response = array(
                'status' => true,
                'message' => 'New Agreement Successfully Added',
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

    public function fetchAgreement()
    {
        $employee_id = Auth::user()->username;
        $employee = EmployeeSync::where('employee_id', '=', $employee_id)->first();
        $departments = db::table('departments')->whereNull('deleted_at')->orderBy('department_shortname', 'ASC')->get();

        $where = "";
        // if($employee->department != 'Management Information System Department' && $employee->department != 'Human Resources Department' && Auth::user()->role_code != 'S'){
        //     $where = "WHERE a.department = '".$employee->department."'";
        // }

        $agreements = db::select("SELECT
                                a.id,
                                a.department,
                                d.department_shortname,
                                a.vendor,
                                a.description,
                                a.valid_from,
                                a.valid_to,
                                TIMESTAMPDIFF( DAY, a.valid_from, a.valid_to ) AS total_validity,
                                TIMESTAMPDIFF( DAY, date( now()), a.valid_to ) AS validity,
                                a.`status`,
                                a.remark,
                                a.created_at,
                                a.updated_at,
                                a.created_by,
                                es.`name`,
                                COALESCE ( aa.att, 0 ) AS att
                                FROM
                                agreements AS a
                                LEFT JOIN employee_syncs AS es ON es.employee_id = a.created_by
                                LEFT JOIN ( SELECT agreement_id, count( id ) AS att FROM agreement_attachments GROUP BY agreement_id ) AS aa ON aa.agreement_id = a.id
                                LEFT JOIN departments AS d ON d.department_name = a.department
                                WHERE category = 'agreement'
                                ORDER BY a.id DESC
                                ");

        $regulations = db::select("SELECT
                                a.*,
                                aa.*,
                                department_shortname
                                FROM
                                agreements AS a
                                LEFT JOIN employee_syncs AS es ON es.employee_id = a.created_by
                                LEFT JOIN ( SELECT agreement_id, count( id ) AS att FROM agreement_attachments GROUP BY agreement_id ) AS aa ON aa.agreement_id = a.id
                                LEFT JOIN departments AS d ON d.department_name = a.department
                                WHERE
                                category = 'regulation'
                                ORDER BY
                                a.valid_from DESC
                                ");

        $response = array(
            'status' => true,
            'agreements' => $agreements,
            'regulations' => $regulations,
            'departments' => $departments,
        );
        return Response::json($response);
    }

    public function createRegulation(Request $request)
    {
        try {

            if ($request->input('newJenisPeraturan') == "Peraturan Tidak Terkait Operasional") {
                $agreement = new Agreement([
                    'category' => 'regulation',
                    'vendor' => $request->input('newNomorRegulasi'),
                    'description' => $request->input('newJudulRegulasi'),
                    'valid_from' => $request->input('newTanggalTerbit'),
                    'remark' => $request->input('newJenisPeraturan'),
                    'analisis' => $request->input('newAnalisa'),
                    'created_by' => Auth::user()->username,
                ]);

                $agreement->save();

                $response = array(
                    'status' => true,
                    'message' => 'New Regulation Successfully Added',
                );
                return Response::json($response);
            } else {
                $filename = "";
                $file_destination = 'files/agreements/regulation';

                if (count($request->file('newAttachment')) > 0) {
                    $file = $request->file('newAttachment');
                    $filename = date('YmdHis') . '.' . $request->input('extension');
                    $file->move($file_destination, $filename);
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Please select file to attach',
                    );
                    return Response::json($response);
                }

                $agreement = new Agreement([
                    'category' => 'regulation',
                    'department' => $request->input('newDepartment'),
                    'related_department' => $request->input('newDepartmentRelated'),
                    'vendor' => $request->input('newNomorRegulasi'),
                    'description' => $request->input('newJudulRegulasi'),
                    'valid_from' => $request->input('newTanggalTerbit'),
                    'remark' => $request->input('newJenisPeraturan'),
                    'analisis' => $request->input('newAnalisa'),
                    'implementation' => $request->input('newImplementation'),
                    'action' => $request->input('newAction'),
                    'penalty' => $request->input('newPenalty'),
                    'status' => $request->input('newStatus'),
                    'status_due_date' => $request->input('newTargetImplementasi'),
                    // 'company_impact' => $request->input('newCompanyImpact'),
                    'created_by' => Auth::user()->username,
                ]);

                $agreement->save();

                $attachment = new AgreementAttachment([
                    'agreement_id' => $agreement->id,
                    'file_name' => $filename,
                    'created_by' => Auth::user()->username,
                ]);

                $attachment->save();

                $response = array(
                    'status' => true,
                    'message' => 'New Regulation Successfully Added',
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

    public function editRegulation(Request $request)
    {
        $filename = "";
        $file_destination = 'files/agreements/regulation';

        if (count($request->file('newAttachment')) > 0) {
            $file = $request->file('newAttachment');
            $filename = date('YmdHis') . '.' . $request->input('extension');
            $file->move($file_destination, $filename);
        }

        try {
            $agreement = Agreement::where('id', '=', $request->get('newId'))->first();

            $agreement->category = 'regulation';
            $agreement->department = $request->input('newDepartment');
            $agreement->related_department = $request->input('newDepartmentRelated');
            $agreement->vendor = $request->input('newNomorRegulasi');
            $agreement->description = $request->input('newJudulRegulasi');
            $agreement->valid_from = $request->input('newTanggalTerbit');
            $agreement->status = $request->input('newStatus');
            $agreement->status_due_date = $request->input('newTargetImplementasi');
            // $agreement->company_impact = $request->input('newCompanyImpact');
            $agreement->analisis = $request->input('newAnalisa');
            $agreement->implementation = $request->input('newImplementation');
            $agreement->action = $request->input('newAction');
            $agreement->penalty = $request->input('newPenalty');
            $agreement->created_by = Auth::user()->username;
            $agreement->save();

            if (count($request->file('newAttachment')) > 0) {
                $attachment = new AgreementAttachment([
                    'agreement_id' => $request->get('newId'),
                    'file_name' => $filename,
                    'created_by' => Auth::user()->username,
                ]);
                $attachment->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Regulation Updated',
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

    public function indexSafetyShoesLog()
    {
        $title = "Safety Shoes Log";
        $title_jp = "";

        $employees = EmployeeSync::orderBy('name', 'asc')->get();

        $users = User::where('username', 'like', 'PI%')->get();

        return view('general.safety_shoes.safety_shoes_log', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'users' => $users,
        ));
    }

    public function indexSafetyShoes()
    {
        $title = "Safety Shoes Control";
        $title_jp = "安全靴管理システム";

        $employees = EmployeeSync::orderBy('name', 'asc')->get();
        $employees_user = User::get();
        $user = EmployeeSync::where('employee_id', Auth::user()->username)->first();

        return view('general.safety_shoes.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'employees_user' => $employees_user,
            'user' => $user,
            'printers' => $this->printers,
        ));
    }

    public function indexMosaic()
    {
        $title = "Yamaha Day Mosaic Art Project";
        $title_jp = "";

        $mosaics = db::select("SELECT COALESCE
                                        ( employee_syncs.department, 'Management' ) AS department,
                                        count( DISTINCT general_mosaics.employee_id ) AS count_person,
                                        count( general_mosaics.mosaic_id ) count_upload
                                        FROM
                                        general_mosaics
                                        LEFT JOIN employee_syncs ON employee_syncs.employee_id = general_mosaics.employee_id
                                        GROUP BY
                                        employee_syncs.department");

        return view('general.mosaic', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'mosaics' => $mosaics,
        ));
    }

    public function fetchMosaicDetail()
    {

    }

    public function indexReportSuratDokter()
    {
        $title = 'Laporan Dropbox Surat Dokter';
        $title_jp = '診断書のドロップボックス';

        return view('general.dropbox.surat_dokter_report', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Report Surat Dokter')
            ->with('head', 'HR Report');
    }

    public function confirmSuratDokterReport(Request $request)
    {
        try {

            $doctor = GeneralDoctor::where('id', '=', $request->get('id'))->first();

            if ($request->get('status') == '1') {
                $doctor->remark = 1;
                $doctor->save();

                $response = array(
                    'status' => true,
                    'message' => 'Data berhasil dikonfirmasi',
                );
                return Response::json($response);
            } else if ($request->get('status') == '2') {
                $doctor->remark = $request->get('status');
                $doctor->save();

                $response = array(
                    'status' => true,
                    'message' => 'Data berhasil ditolak',
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

    public function fetchReportSuratDokter(Request $request)
    {
        $month = date('Y-m');
        if (strlen($request->get('month_from')) > 0) {
            $month = date('Y-m', strtotime($request->get('month_from')));
        }

        $doctors = db::select('SELECT
                                        date( g.created_at ) AS tanggal_pengajuan,
                                        g.id,
                                        g.employee_id,
                                        e.`name`,
                                        e.`department`,
                                        e.`section`,
                                        g.doctor_name,
                                        g.diagnose,
                                        g.date_from,
                                        g.date_to,
                                        g.attachment_file,
                                        g.remark,
                                        g.created_by,
                                        e2.`name` AS created_name
                                        FROM
                                        `general_doctors` g
                                        LEFT JOIN employee_syncs AS e ON g.employee_id = e.employee_id
                                        LEFT JOIN employee_syncs e2 ON e2.employee_id = g.created_by
                                        WHERE
                                        date_format( g.created_at, "%Y-%m" ) = "' . $month . '"');

        $datas = array();

        foreach ($doctors as $doctor) {
            array_push($datas,
                [
                    "tanggal_pengajuan" => $doctor->tanggal_pengajuan,
                    "id" => $doctor->id,
                    "employee_id" => $doctor->employee_id,
                    "name" => $doctor->name,
                    "department" => $doctor->department,
                    "section" => $doctor->section,
                    "doctor_name" => $doctor->doctor_name,
                    "diagnose" => $doctor->diagnose,
                    "date_from" => $doctor->date_from,
                    "date_to" => $doctor->date_to,
                    "remark" => $doctor->remark,
                    "created_by" => $doctor->created_by,
                    "created_name" => $doctor->created_name,
                    "attachment_file" => asset('files/surat_dokter/' . $doctor->attachment_file),
                ]);

        }

        $response = array(
            'status' => true,
            'doctors' => $datas,
            'period' => date('F Y', strtotime($month)),
        );
        return Response::json($response);
    }

    public function indexSuratDokter()
    {
        $employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();

        $employees = EmployeeSync::whereNull('end_date')
            ->select('employee_id', 'name');

        if (!str_contains(Auth::user()->role_code, 'MIS') && Auth::user()->role_code != 'S' && !str_contains(Auth::user()->role_code, 'HR') && $employee) {
            $employees = $employees->where('department', '=', $employee->department);
        }

        $employees = $employees->orderBy('employee_id', 'ASC')->get();

        $title = 'Dropbox Report Surat Dokter';
        $title_jp = '';

        return view('general.dropbox.surat_dokter', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
        ))->with('page', 'Surat Dokter');

    }

    public function fetchSuratDokter(Request $request)
    {
        $first = date('Y-m-d', strtotime("-90 days"));

        $employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();
        $general_doctors = db::table('general_doctors')->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_doctors.employee_id');

        if (!str_contains(Auth::user()->role_code, 'MIS') && !str_contains(Auth::user()->role_code, 'HR') && $employee) {
            $general_doctors = $general_doctors->where('employee_syncs.department', '=', $employee->department);
            $general_doctors = $general_doctors->where('general_doctors.created_by', '=', Auth::user()->username);
        }

        if (strlen($request->get('date_from')) > 0) {
            $general_doctors = $general_doctors->where(DB::raw('DATE_FORMAT(general_doctors.created_at, "%Y-%m-%d")'), '>=', date('Y-m-d', strtotime($request->get('date_from'))));
        }
        if (strlen($request->get('date_to')) > 0) {
            $general_doctors = $general_doctors->where(DB::raw('DATE_FORMAT(general_doctors.created_at, "%Y-%m-%d")'), '<=', date('Y-m-d', strtotime($request->get('date_to'))));
        }
        if (strlen($request->get('date_from')) == 0 && strlen($request->get('date_from')) == 0) {
            $general_doctors = $general_doctors->where(DB::raw('DATE_FORMAT(general_doctors.created_at, "%Y-%m-%d")'), '>=', $first);
        }

        $general_doctors = $general_doctors->whereNull('general_doctors.deleted_at')
            ->select(
                'general_doctors.id',
                'general_doctors.employee_id',
                'employee_syncs.name',
                'general_doctors.doctor_name',
                'general_doctors.diagnose',
                'general_doctors.date_from',
                'general_doctors.date_to',
                'general_doctors.attachment_file',
                'remark',
                'created_by',
                db::raw('date(general_doctors.created_at) as created_at')
            )
            ->orderBy('general_doctors.created_at', 'DESC')
            ->get();

        $datas = array();

        foreach ($general_doctors as $general_doctor) {
            array_push($datas, [
                "id" => $general_doctor->id,
                "employee_id" => $general_doctor->employee_id,
                "attachment_file" => asset('files/surat_dokter/' . $general_doctor->attachment_file),
                "name" => $general_doctor->name,
                "doctor_name" => $general_doctor->doctor_name,
                "diagnose" => $general_doctor->diagnose,
                "date_from" => $general_doctor->date_from,
                "date_to" => $general_doctor->date_to,
                "remark" => $general_doctor->remark,
                "created_by" => $general_doctor->created_by,
                "created_at" => $general_doctor->created_at,
            ]);
        }

        $response = array(
            'status' => true,
            'general_doctors' => $datas,
        );
        return Response::json($response);
    }

    public function deleteSuratDokter(Request $request)
    {
        try {
            $general_doctor = GeneralDoctor::where('id', '=', $request->get('id'))->forceDelete();

            $response = array(
                'status' => true,
                'message' => 'Data berhasil dihapus',
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

    public function inputReceiveSafetyShoes(Request $request)
    {
        $msg = $request->get('msg');
        $request_id = $request->get('request_id');

        if ($msg == 'receive') {
            $user_receipt = $request->get('user_receipt');
            DB::beginTransaction();
            $request = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
                ->where('general_shoes_requests.request_id', $request_id)
                ->select(
                    'general_shoes_requests.condition',
                    'general_shoes_requests.metode',
                    'general_shoes_requests.merk',
                    'general_shoes_requests.gender',
                    'general_shoes_requests.size',
                    'general_shoes_requests.employee_id',
                    'general_shoes_requests.created_by',
                    'employee_syncs.name',
                    'employee_syncs.department',
                    'employee_syncs.section',
                    'employee_syncs.group',
                    'employee_syncs.sub_group'
                )
                ->get();

            for ($i = 0; $i < count($request); $i++) {
                try {
                    $log = new GeneralShoesLog([
                        'merk' => $request[$i]['merk'],
                        'gender' => $request[$i]['gender'],
                        'size' => $request[$i]['size'],
                        'quantity' => -1,
                        'status' => 'Pinjam',
                        'metode' => $request[$i]['metode'],
                        'condition' => $request[$i]['condition'],
                        'employee_id' => $request[$i]['employee_id'],
                        'name' => $request[$i]['name'],
                        'department' => $request[$i]['department'],
                        'section' => $request[$i]['section'],
                        'group' => $request[$i]['group'],
                        'sub_group' => $request[$i]['sub_group'],
                        'requested_by' => $request[$i]['created_by'],
                        'receipt_by' => $user_receipt,
                        'created_by' => Auth::id(),
                    ]);
                    $log->save();

                    $stock = GeneralShoesStock::where('gender', $request[$i]['gender'])
                        ->where('condition', $request[$i]['condition'])
                        ->where('merk', $request[$i]['merk'])
                        ->where('size', $request[$i]['size'])
                        ->first();

                    if ($stock) {
                        $stock->quantity = $stock->quantity - 1;
                        $stock->save();
                    }

                } catch (Exception $e) {
                    DB::rollback();
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            }

            try {
                $request = GeneralShoesRequest::where('request_id', $request_id)->forceDelete();
            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Request Safety Shoes berhasil diterima',
            );
            return Response::json($response);

        } else {
            DB::beginTransaction();
            $request = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
                ->where('general_shoes_requests.request_id', $request_id)
                ->select('employee_syncs.gender', 'general_shoes_requests.size', db::raw('COUNT(general_shoes_requests.id) AS qty'))
                ->groupBy('employee_syncs.gender', 'general_shoes_requests.size')
                ->get();

            for ($i = 0; $i < count($request); $i++) {
                $stock = GeneralShoesStock::where('gender', $request[$i]['gender'])
                    ->where('size', $request[$i]['size'])
                    ->first();

                if ($stock) {
                    $stock->temp_stock = $stock->temp_stock + $request[$i]['qty'];

                    try {
                        $stock->save();
                    } catch (Exception $e) {
                        DB::rollback();
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }

                }

            }

            try {
                $request = GeneralShoesRequest::where('request_id', $request_id)->forceDelete();
            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Request Safety Shoes berhasil ditolak',
            );
            return Response::json($response);
        }

    }

    public function inputSafetyShoesNew(Request $request)
    {
        $stock = $request->get('stock');
        $data = array();

        DB::beginTransaction();
        for ($i = 0; $i < count($stock); $i++) {
            try {
                $shoes = GeneralShoesStock::where('merk', $stock[$i]['merk'])
                    ->where('condition', 'Baru')
                    ->where('gender', $stock[$i]['gender'])
                    ->where('size', $stock[$i]['size'])
                    ->first();

                if ($shoes) {
                    $shoes->temp_stock = $shoes->temp_stock + $stock[$i]['qty'];
                    $shoes->quantity = $shoes->quantity + $stock[$i]['qty'];
                    $shoes->save();
                } else {
                    $shoes = new GeneralShoesStock([
                        'condition' => 'Baru',
                        'merk' => $stock[$i]['merk'],
                        'gender' => $stock[$i]['gender'],
                        'size' => $stock[$i]['size'],
                        'temp_stock' => $stock[$i]['qty'],
                        'quantity' => $stock[$i]['qty'],
                        'created_by' => Auth::id(),
                    ]);
                    $shoes->save();
                }

                array_push($data, [
                    'merk' => $stock[$i]['merk'],
                    'gender' => $stock[$i]['gender'],
                    'size' => $stock[$i]['size'],
                    'quantity' => $stock[$i]['qty'],
                    'status' => 'Baru',
                ]);

                $log = new GeneralShoesLog([
                    'merk' => $stock[$i]['merk'],
                    'gender' => $stock[$i]['gender'],
                    'size' => $stock[$i]['size'],
                    'quantity' => $stock[$i]['qty'],
                    'status' => 'Simpan',
                    'condition' => 'Baru',
                    'employee_id' => '',
                    'name' => '',
                    'department' => '',
                    'section' => '',
                    'group' => '',
                    'sub_group' => '',
                    'created_by' => Auth::id(),
                ]);
                $log->save();

            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        $mail_to = db::table('send_emails')
            ->where('remark', '=', 'safety_shoes')
            ->WhereNull('deleted_at')
            ->select('email')
            ->get();

        Mail::to($mail_to)
            ->bcc(['muhammad.ikhlas@music.yamaha.com'])
            ->send(new SendEmail($data, 'safety_shoes'));

        DB::commit();

        $response = array(
            'status' => true,
            'message' => 'Safety Shoes berhasil ditambahkan',
        );
        return Response::json($response);
    }

    public function inputSafetyShoes(Request $request)
    {
        $stock = $request->get('stock');
        $data = array();

        DB::beginTransaction();
        for ($i = 0; $i < count($stock); $i++) {
            try {
                if ($stock[$i]['status'] == 'Simpan') {
                    $shoes = GeneralShoesStock::where('merk', $stock[$i]['merk'])
                        ->where('gender', $stock[$i]['gender'])
                        ->where('size', $stock[$i]['size'])
                        ->first();

                    if ($shoes) {
                        $shoes->temp_stock = $shoes->temp_stock + $stock[$i]['qty'];
                        $shoes->quantity = $shoes->quantity + $stock[$i]['qty'];
                        $shoes->save();
                    } else {
                        $shoes = new GeneralShoesStock([
                            'condition' => 'Layak Pakai',
                            'merk' => $stock[$i]['merk'],
                            'gender' => $stock[$i]['gender'],
                            'size' => $stock[$i]['size'],
                            'temp_stock' => $stock[$i]['qty'],
                            'quantity' => $stock[$i]['qty'],
                            'created_by' => Auth::id(),
                        ]);
                        $shoes->save();
                    }
                }

                $emp = EmployeeSync::where('employee_id', $stock[$i]['employee_id'])->first();

                array_push($data, [
                    'employee_id' => $emp->employee_id,
                    'name' => $emp->name,
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'group' => $emp->group,
                    'merk' => $stock[$i]['merk'],
                    'gender' => $stock[$i]['gender'],
                    'size' => $stock[$i]['size'],
                    'quantity' => $stock[$i]['qty'],
                    'status' => $stock[$i]['status'],
                ]);

                $condition = '';
                if ($stock[$i]['status'] == 'Simpan') {
                    $condition = 'Layak Pakai';
                } else {
                    $condition = 'Tidak Layak';
                }

                $log = new GeneralShoesLog([
                    'merk' => $stock[$i]['merk'],
                    'gender' => $stock[$i]['gender'],
                    'size' => $stock[$i]['size'],
                    'quantity' => $stock[$i]['qty'],
                    'status' => $stock[$i]['status'],
                    'condition' => $condition,
                    'employee_id' => $emp->employee_id,
                    'name' => $emp->name,
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'group' => $emp->group,
                    'sub_group' => $emp->sub_group,
                    'created_by' => Auth::id(),
                ]);
                $log->save();
            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        $mail_to = db::table('send_emails')
            ->where('remark', '=', 'safety_shoes')
            ->WhereNull('deleted_at')
            ->select('email')
            ->get();

        Mail::to($mail_to)
            ->bcc(['muhammad.ikhlas@music.yamaha.com'])
            ->send(new SendEmail($data, 'safety_shoes'));

        DB::commit();

        $response = array(
            'status' => true,
            'message' => 'Safety Shoes berhasil ditambahkan',
        );
        return Response::json($response);
    }

    public function inputReqSafetyShoes(Request $request)
    {
        $employee = $request->get('employee');
        $printer = $request->get('printer');

        DB::beginTransaction();
        $prefix_now = 'REQ' . date("y") . date("m");
        $code_generator = CodeGenerator::where('note', '=', 'general-request')->first();
        if ($prefix_now != $code_generator->prefix) {
            $code_generator->prefix = $prefix_now;
            $code_generator->index = '0';
            $code_generator->save();
        }
        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
        $request_id = $code_generator->prefix . $number;
        $code_generator->index = $code_generator->index + 1;
        $code_generator->save();

        for ($i = 0; $i < count($employee); $i++) {

            $cek_baru = true;
            //CEK LAYAK PAKAI
            $stock = GeneralShoesStock::where('merk', $employee[$i]['merk'])
                ->where('size', $employee[$i]['size'])
                ->where('condition', 'Layak Pakai')
                ->first();

            if ($stock) {
                if ($stock->temp_stock >= 1) {
                    try {
                        $stock->temp_stock = $stock->temp_stock - 1;
                        $stock->save();
                        $cek_baru = false;

                        $request = new GeneralShoesRequest([
                            'request_id' => $request_id,
                            'employee_id' => $employee[$i]['employee_id'],
                            'gender' => $employee[$i]['gender'],
                            'merk' => $employee[$i]['merk'],
                            'size' => $employee[$i]['size'],
                            'condition' => 'Layak Pakai',
                            'metode' => 'Stock',
                            'created_by' => Auth::id(),
                        ]);
                        $request->save();
                    } catch (Exception $e) {
                        DB::rollback();
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);

                    }

                }
            }

            //CEK BARU
            if ($cek_baru) {
                $new = GeneralShoesStock::where('merk', $employee[$i]['merk'])
                    ->where('size', $employee[$i]['size'])
                    ->where('condition', 'Baru')
                    ->first();

                if ($new) {
                    if ($new->temp_stock > 0) {
                        try {
                            $new->temp_stock = $new->temp_stock - 1;
                            $new->save();

                            $request = new GeneralShoesRequest([
                                'request_id' => $request_id,
                                'employee_id' => $employee[$i]['employee_id'],
                                'gender' => $employee[$i]['gender'],
                                'merk' => $employee[$i]['merk'],
                                'size' => $employee[$i]['size'],
                                'condition' => 'Baru',
                                'metode' => 'Stock',
                                'created_by' => Auth::id(),
                            ]);
                            $request->save();
                        } catch (Exception $e) {
                            DB::rollback();
                            $response = array(
                                'status' => false,
                                'message' => $e->getMessage(),
                            );
                            return Response::json($response);
                        }

                    } else {
                        try {
                            $request = new GeneralShoesRequest([
                                'request_id' => $request_id,
                                'employee_id' => $employee[$i]['employee_id'],
                                'gender' => $employee[$i]['gender'],
                                'merk' => $employee[$i]['merk'],
                                'size' => $employee[$i]['size'],
                                'condition' => 'Baru',
                                'metode' => 'PR',
                                'created_by' => Auth::id(),
                            ]);
                            $request->save();
                        } catch (Exception $e) {
                            DB::rollback();
                            $response = array(
                                'status' => false,
                                'message' => $e->getMessage(),
                            );
                            return Response::json($response);
                        }
                    }
                } else {
                    try {
                        $request = new GeneralShoesRequest([
                            'request_id' => $request_id,
                            'employee_id' => $employee[$i]['employee_id'],
                            'gender' => $employee[$i]['gender'],
                            'merk' => $employee[$i]['merk'],
                            'size' => $employee[$i]['size'],
                            'condition' => 'Baru',
                            'metode' => 'PR',
                            'created_by' => Auth::id(),
                        ]);
                        $request->save();
                    } catch (Exception $e) {
                        DB::rollback();
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                }
            }
        }

        DB::commit();

        $data = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
            ->where('general_shoes_requests.request_id', $request_id)
            ->select(
                'general_shoes_requests.employee_id',
                db::raw("concat(SPLIT_STRING(employee_syncs.`name`, ' ', 1), ' ', SPLIT_STRING(employee_syncs.`name`, ' ', 2)) AS `name`"),
                'general_shoes_requests.merk',
                'general_shoes_requests.gender',
                'general_shoes_requests.size',
                'general_shoes_requests.metode',
                'general_shoes_requests.condition'
            )
            ->get();

        $mail_to = db::table('users')
            ->where('id', '=', Auth::id())
            ->where('email', 'like', '%music.yamaha%')
            ->first();

        if ($mail_to) {
            Mail::to($this->std_email)
                ->cc([$mail_to->email])
                ->bcc(['muhammad.ikhlas@music.yamaha.com'])
                ->send(new SendEmail($data, 'safety_shoes_request'));
        } else {
            Mail::to($this->std_email)
                ->bcc(['muhammad.ikhlas@music.yamaha.com'])
                ->send(new SendEmail($data, 'safety_shoes_request'));
        }

        $response = array(
            'status' => true,
            'message' => 'Safety Shoes berhasil ditambahkan',
        );
        return Response::json($response);
    }

    public function reprintReqSafetyShoes(Request $request)
    {
        $printer = $request->get('printer');
        $request_id = $request->get('request_id');

        $data = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
            ->leftJoin('users', 'users.id', '=', 'general_shoes_requests.created_by')
            ->where('general_shoes_requests.request_id', $request_id)
            ->where('general_shoes_requests.metode', 'Stock')
            ->select(
                'general_shoes_requests.condition',
                'employee_syncs.gender',
                'general_shoes_requests.merk',
                'general_shoes_requests.size',
                'users.name',
                db::raw('COUNT(general_shoes_requests.id) AS qty')
            )
            ->groupBy(
                'general_shoes_requests.condition',
                'employee_syncs.gender',
                'general_shoes_requests.merk',
                'general_shoes_requests.size',
                'users.name'
            )
            ->orderBy('employee_syncs.gender', 'ASC')
            ->orderBy('general_shoes_requests.size', 'ASC')
            ->get();

        $request = GeneralShoesRequest::where('request_id', $request_id)->first();
        if (count($data) > 0) {
            $this->safetyShoesSlipWh($data, $request, $printer);
        }

        $data = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
            ->leftJoin('users', 'users.id', '=', 'general_shoes_requests.created_by')

            ->where('general_shoes_requests.request_id', $request_id)
            ->select(
                'general_shoes_requests.employee_id',
                db::raw("concat(SPLIT_STRING(employee_syncs.`name`, ' ', 1), ' ', SPLIT_STRING(employee_syncs.`name`, ' ', 2)) AS `name`"),
                'general_shoes_requests.merk',
                'general_shoes_requests.gender',
                'general_shoes_requests.size',
                'general_shoes_requests.metode',
                'general_shoes_requests.condition',
                db::raw('users.name AS requester')
            )
            ->orderBy('general_shoes_requests.metode', 'DESC')
            ->get();

        $this->safetyShoesSlipStd($data, $request, $printer);
// $this->safetyShoesSlipPrd($data, $request, $printer);

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function safetyShoesSlipWh($data, $request, $printer_name)
    {
        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->initialize();
        $printer->setFont(Printer::MODE_FONT_A);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text(strtoupper("  safety shoes request  \n"));
        $printer->initialize();

        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($request->request_id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->setTextSize(1, 1);
        $printer->text($request->request_id . "\n");
        $printer->feed(1);

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $total_qty = 0;
        for ($i = 0; $i < count($data); $i++) {
            $gender = $this->writeString('(' . $data[$i]->gender . ')', 2, ' ');
            $size = $this->writeString($data[$i]->size, 2, ' ');
            $qty = $this->writeNumber($data[$i]->qty, 2, ' ');
            $merk = $this->writeString($data[$i]->merk, 8, ' ');
            $condition = $this->writeString('(' . $data[$i]->condition . ')', 13, ' ');

            $printer->text($merk . " " . $gender . " Size " . $size . " (" . $data[$i]->condition . ") -> " . $qty . " Pasang");
            $printer->feed(1);

        }
        $printer->feed(2);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setUnderline(true);
        $printer->text('Created At');
        $printer->feed(1);
        $printer->setUnderline(false);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $date = date('d-F-Y H:i:s', strtotime($request->created_at));
        $printer->textRaw($date);
        $printer->feed(2);

        $printer->initialize();
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setFont(Printer::MODE_FONT_B);
        $printer->text("*) Slip for Warehouse");
        $printer->feed(1);

        $printer->cut();
        $printer->close();
    }

    public function safetyShoesSlipStd($data, $request, $printer_name)
    {
        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->initialize();
        $printer->setFont(Printer::MODE_FONT_A);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text(strtoupper("  safety shoes request  \n"));
        $printer->initialize();

        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($request->request_id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->setTextSize(1, 1);
        $printer->text($request->request_id . "\n");
        $printer->feed(1);

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $count = 0;
        for ($i = 1; $i < count($data); $i++) {
            $count++;
            $order = $this->writeString($count . ".", 3, ' ');
            $gender = $this->writeString('(' . $data[$i]->gender . ')', 2, ' ');
            $size = $this->writeString($data[$i]->size, 2, ' ');
            $merk = $this->writeString($data[$i]->merk, 8, ' ');

            $printer->text($order . " " . $data[$i]->employee_id . " - " . $data[$i]->name);
            $printer->feed(1);
            $printer->text("    " . $merk . " " . $gender . " Size " . $size . " (" . $data[$i]->metode . " - " . $data[$i]->condition . ")");
            $printer->feed(1);

        }
        $printer->feed(2);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setUnderline(true);
        $printer->text('Created By');
        $printer->feed(1);
        $printer->setUnderline(false);

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->textRaw($data[0]->requester . "\n");
        $date = date('d-F-Y H:i:s', strtotime($request->created_at));
        $printer->textRaw($date);
        $printer->feed(2);

        $printer->initialize();
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setFont(Printer::MODE_FONT_B);
        $printer->text("*) Slip for Standardization");
        $printer->feed(1);

        $printer->cut();
        $printer->close();
    }

    public function safetyShoesSlipPrd($data, $request, $printer_name)
    {
        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->initialize();
        $printer->setFont(Printer::MODE_FONT_A);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text(strtoupper("  safety shoes request  \n"));
        $printer->initialize();

        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($request->request_id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->setTextSize(1, 1);
        $printer->text($request->request_id . "\n");
        $printer->feed(1);

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $count = 0;
        for ($i = 1; $i < count($data); $i++) {
            $count++;
            $order = $this->writeString($count . ".", 3, ' ');
            $gender = $this->writeString('(' . $data[$i]->gender . ')', 2, ' ');
            $size = $this->writeString($data[$i]->size, 2, ' ');
            $merk = $this->writeString($data[$i]->merk, 8, ' ');

            $printer->text($order . " " . $data[$i]->employee_id . " - " . $data[$i]->name);
            $printer->feed(1);
            $printer->text("    " . $merk . " " . $gender . " Size " . $size . " (" . $data[$i]->metode . " - " . $data[$i]->condition . ")");
            $printer->feed(1);

        }
        $printer->feed(2);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setUnderline(true);
        $printer->text('Created By');
        $printer->feed(1);
        $printer->setUnderline(false);

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->textRaw($data[0]->name . "\n");
        $date = date('d-F-Y H:i:s', strtotime($request->created_at));
        $printer->textRaw($date);
        $printer->feed(2);

        $printer->initialize();
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setFont(Printer::MODE_FONT_B);
        $printer->text("*) Slip for Production/Requester");
        $printer->feed(1);

        $printer->cut();
        $printer->close();
    }

    public function writeString($text, $maxLength, $char)
    {
        if ($maxLength > 0) {
            $textLength = 0;
            if ($text != null) {
                $textLength = strlen($text);
            } else {
                $text = "";
            }
            for ($i = 0; $i < ($maxLength - $textLength); $i++) {
                $text .= $char;
            }
        }
        return strtoupper($text);
    }

    public function writeNumber($text, $maxLength, $char)
    {
        $return = "";

        if ($maxLength > 0) {

            $textLength = strlen($text);
            for ($i = 0; $i < ($maxLength - $textLength); $i++) {
                $return .= $char;
            }

            $return .= $text;

        }
        return strtoupper($return);
    }

    public function inputSuratDokter(Request $request)
    {
        $filename = "";
        $file_destination = 'files/surat_dokter';

        if (count($request->file('attachment')) > 0) {
            $file = $request->file('attachment');
            $filename = md5($request->input('employee_id') . date('YmdHis')) . '.' . $request->input('extension');
            $file->move($file_destination, $filename);
        }

        try {
            GeneralDoctor::create([
                'employee_id' => $request->input('employee_id'),
                'doctor_name' => $request->input('doctor_name'),
                'diagnose' => $request->input('diagnose'),
                'date_from' => date('Y-m-d', strtotime($request->input('date_from'))),
                'date_to' => date('Y-m-d', strtotime($request->input('date_to'))),
                'attachment_file' => $filename,
                'remark' => 0,
                'created_by' => Auth::user()->username,
            ]);

            $response = array(
                'status' => true,
                'message' => 'Data baru berhasil ditambahkan',
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

    public function indexReportTransportation()
    {
        $title = 'Online Attendace And Transportation Report';
        $title_jp = '出社・移動費のオンライン報告';

        return view('general.dropbox.online_transportation_report', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Report Attendances & Tsransportations')
            ->with('head', 'HR Report');
    }

    public function indexOnlineTransportation()
    {
        $title = 'Online Attendace And Transportation Record';
        $title_jp = '';

        $employee = EmployeeSync::leftJoin('domiciles', 'domiciles.employee_id', '=', 'employee_syncs.employee_id')
            ->select('employee_syncs.employee_id', 'employee_syncs.name', 'employee_syncs.grade_code', 'employee_syncs.department', 'employee_syncs.section', 'domiciles.domicile_address', 'employee_syncs.zona')
            ->where('employee_syncs.employee_id', '=', Auth::user()->username)
            ->first();

        if (!$employee) {
            $employee = array();
        }

        // if(!str_contains(Auth::user()->role_code,'MIS') && Auth::user()->role_code != 'S' && substr($employee->grade_code, 0, 1) != 'L' && substr($employee->grade_code, 0, 1) != 'M'){
        //     return view('404');
        // }

        return view('general.dropbox.online_transportation', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $employee,
        ))->with('head', 'Online Attendance And Transportation');
    }

    public function deleteOnlineTransportation(Request $request)
    {
        try {
            $general_transporation = GeneralTransportation::where('id', '=', $request->get('id'))->first();
            $general_transporation->forceDelete();

            $response = array(
                'status' => true,
                'message' => 'Data berhasil dihapus',
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

    public function inputOnlineTransportation(Request $request)
    {
        $filename = "";
        $file_destination = 'files/general_transportation';

// if (count($request->file('newAttachment')) > 0) {
//     $file = $request->file('newAttachment');
//     $filename = md5($request->input('employee_id').date('YmdHis')).'.'.$request->input('extension');
//     $file->move($file_destination, $filename);
// }

        try {
            $ivms = DB::SELECT("SELECT
                                           *
                                         FROM
                                         ivms.ivms_attendance_triggers
                                         WHERE
                                         employee_id = '" . $request->input('employee_id') . "'
                                         AND auth_date = '" . date('Y-m-d', strtotime($request->input('newDate'))) . "'");
// if (count($ivms) > 0) {
            $general = GeneralTransportation::where('employee_id', $request->input('employee_id'))->where('attend_code', $request->input('newAttend'))->where('check_date', date('Y-m-d', strtotime($request->input('newDate'))))->get();

            if (count($general) == 0) {
                if ($request->get('newAttend') == 'cuti' || $request->get('newAttend') == 'izin' || $request->get('newAttend') == 'sakit' || $request->get('newAttend') == 'wfh' || $request->get('newAttend') == 'dinas') {
                    if ($filename == "") {
                        GeneralTransportation::create([
                            'employee_id' => $request->input('employee_id'),
                            'grade' => $request->input('grade'),
                            'zona' => $request->input('zona'),
                            'check_date' => date('Y-m-d', strtotime($request->input('newDate'))),
                            'attend_code' => $request->input('newAttend'),
                            'vehicle' => $request->input('newVehicle'),
                            'origin' => $request->input('newOrigin'),
                            'destination' => $request->input('newDestination'),
                            'highway_amount' => $request->input('newHighwayAmount'),
                            'distance' => $request->input('newDistance'),
                            // 'highway_attachment' => $filename,
                            'remark' => 0,
                            'created_by' => Auth::id(),
                        ]);
                    } else {
                        GeneralTransportation::create([
                            'employee_id' => $request->input('employee_id'),
                            'grade' => $request->input('grade'),
                            'zona' => $request->input('zona'),
                            'check_date' => date('Y-m-d', strtotime($request->input('newDate'))),
                            'attend_code' => $request->input('newAttend'),
                            'vehicle' => $request->input('newVehicle'),
                            'origin' => $request->input('newOrigin'),
                            'destination' => $request->input('newDestination'),
                            'highway_amount' => $request->input('newHighwayAmount'),
                            'distance' => $request->input('newDistance'),
                            'highway_attachment' => $filename,
                            'remark' => 0,
                            'created_by' => Auth::id(),
                        ]);
                    }

                    $general_data = GeneralTransportationData::firstOrNew(['employee_id' => $request->input('employee_id'), 'attend_code' => $request->input('newAttend')]);
                    $general_data->employee_id = $request->input('employee_id');
                    $general_data->attend_code = $request->input('newAttend');
                    $general_data->vehicle = $request->input('newVehicle');
                    $general_data->origin = $request->input('newOrigin');
                    $general_data->distance = $request->input('newDistance');
                    $general_data->destination = $request->input('newDestination');
                    $general_data->highway_amount = $request->input('newHighwayAmount');
                    $general_data->created_by = Auth::id();
                    $general_data->save();

                    $response = array(
                        'status' => true,
                        'message' => 'Data baru berhasil ditambahkan',
                    );
                    return Response::json($response);
                } else {
                    if (count($ivms) > 0) {
                        if ($filename == "") {
                            GeneralTransportation::create([
                                'employee_id' => $request->input('employee_id'),
                                'grade' => $request->input('grade'),
                                'zona' => $request->input('zona'),
                                'check_date' => date('Y-m-d', strtotime($request->input('newDate'))),
                                'attend_code' => $request->input('newAttend'),
                                'vehicle' => $request->input('newVehicle'),
                                'origin' => $request->input('newOrigin'),
                                'destination' => $request->input('newDestination'),
                                'highway_amount' => $request->input('newHighwayAmount'),
                                'distance' => $request->input('newDistance'),
                                // 'highway_attachment' => $filename,
                                'remark' => 0,
                                'created_by' => Auth::id(),
                            ]);
                        } else {
                            GeneralTransportation::create([
                                'employee_id' => $request->input('employee_id'),
                                'grade' => $request->input('grade'),
                                'zona' => $request->input('zona'),
                                'check_date' => date('Y-m-d', strtotime($request->input('newDate'))),
                                'attend_code' => $request->input('newAttend'),
                                'vehicle' => $request->input('newVehicle'),
                                'origin' => $request->input('newOrigin'),
                                'destination' => $request->input('newDestination'),
                                'highway_amount' => $request->input('newHighwayAmount'),
                                'distance' => $request->input('newDistance'),
                                'highway_attachment' => $filename,
                                'remark' => 0,
                                'created_by' => Auth::id(),
                            ]);
                        }

                        $general_data = GeneralTransportationData::firstOrNew(['employee_id' => $request->input('employee_id'), 'attend_code' => $request->input('newAttend')]);
                        $general_data->employee_id = $request->input('employee_id');
                        $general_data->attend_code = $request->input('newAttend');
                        $general_data->vehicle = $request->input('newVehicle');
                        $general_data->origin = $request->input('newOrigin');
                        $general_data->distance = $request->input('newDistance');
                        $general_data->destination = $request->input('newDestination');
                        $general_data->highway_amount = $request->input('newHighwayAmount');
                        $general_data->created_by = Auth::id();
                        $general_data->save();

                        $response = array(
                            'status' => true,
                            'message' => 'Data baru berhasil ditambahkan',
                        );
                        return Response::json($response);
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Checklog Anda tidak tersedia pada tanggal ' . date('Y-m-d', strtotime($request->input('newDate'))),
                        );
                        return Response::json($response);
                    }
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Data Pernah Ditambahkan',
                );
                return Response::json($response);
            }
// }else{
//     $response = array(
//         'status' => false,
//         'message' => 'Checklog Anda tidak tersedia pada tanggal '.date('Y-m-d', strtotime($request->input('newDate')))
//     );
//     return Response::json($response);
// }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function fetchOnlineTransportationData(Request $request)
    {
        try {
            $employee_id = $request->get('employee_id');
            $attend_code = $request->get('attend_code');

            $data = GeneralTransportationData::where('employee_id', $employee_id)->where('attend_code', $attend_code)->first();

            if (count($data) > 0) {
                $response = array(
                    'status' => true,
                    'datas' => $data,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => true,
                    'datas' => "",
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

    public function confirmOnlineTransportationReport(Request $request)
    {
        try {
            $transportation = GeneralTransportation::where('id', '=', $request->get('id'))->first();
            $transportation->remark = 1;
            $transportation->save();

            $response = array(
                'status' => true,
                'message' => 'Data berhasil dikonfirmasi',
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

    public function fetchSafetyShoesLog(Request $request)
    {

        $data = GeneralShoesLog::leftJoin(db::raw("(SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS request_user"), 'general_shoes_logs.requested_by', '=', 'request_user.id')
            ->leftJoin(db::raw("(SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS create_user"), 'general_shoes_logs.created_by', '=', 'create_user.id');

        if (strlen($request->get('datefrom')) > 0) {
            $data = $data->where(db::raw('date(general_shoes_logs.created_at)'), '>=', $request->get('datefrom'));
        }

        if (strlen($request->get('dateto')) > 0) {
            $data = $data->where(db::raw('date(general_shoes_logs.created_at)'), '<=', $request->get('dateto'));
        }

        if ($request->get('department') != null) {
            $data = $data->whereIn('general_shoes_logs.department', $request->get('department'));
        }

        if ($request->get('section') != null) {
            $data = $data->whereIn('general_shoes_logs.section', $request->get('section'));
        }

        if ($request->get('group') != null) {
            $data = $data->whereIn('general_shoes_logs.group', $request->get('group'));
        }

        if ($request->get('status') != null) {
            $data = $data->whereIn('general_shoes_logs.status', $request->get('status'));
        }

        if ($request->get('requested_by') != null) {
            $data = $data->whereIn('general_shoes_logs.requested_by', $request->get('requested_by'));
        }

        if ($request->get('created_by') != null) {
            $data = $data->whereIn('general_shoes_logs.created_by', $request->get('created_by'));
        }

        $data = $data->orderBy('general_shoes_logs.created_at', 'desc')
            ->select(
                'general_shoes_logs.merk',
                'general_shoes_logs.status',
                'general_shoes_logs.size',
                'general_shoes_logs.gender',
                'general_shoes_logs.employee_id',
                db::raw("concat(SPLIT_STRING(general_shoes_logs.name, ' ', 1), ' ', SPLIT_STRING(general_shoes_logs.name, ' ', 2)) as `name`"),
                'general_shoes_logs.department',
                'general_shoes_logs.section',
                'general_shoes_logs.group',
                'general_shoes_logs.sub_group',
                'general_shoes_logs.quantity',
                db::raw('request_user.name AS requester'),
                db::raw('create_user.name AS creator'),
                'general_shoes_logs.created_at'
            )
            ->get();

        return DataTables::of($data)->make(true);
    }

    public function fetchRequestSafetyShoes()
    {

        $request = db::select("
            SELECT r.request_id, r.created_at, u.`name`, COUNT(r.id) AS qty FROM general_shoes_requests r
                LEFT JOIN employee_syncs e ON e.employee_id = r.employee_id
                LEFT JOIN users u ON u.id = r.created_by
            GROUP BY r.request_id, r.created_at, u.`name`
            ORDER BY r.created_at ASC");

        $response = array(
            'status' => true,
            'request' => $request,
        );
        return Response::json($response);

    }

    public function fetchDetailSafetyShoes(Request $request)
    {
        $request_id = $request->get('request_id');

        $data = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
            ->leftJoin('users', 'users.id', '=', 'general_shoes_requests.created_by')
            ->where('general_shoes_requests.request_id', $request_id)
            ->select(
                'employee_syncs.employee_id',
                'employee_syncs.name',
                'employee_syncs.gender',
                'employee_syncs.department',
                'employee_syncs.section',
                'employee_syncs.group',
                'general_shoes_requests.gender',
                'general_shoes_requests.merk',
                'general_shoes_requests.size',
                db::raw('users.name AS requester')
            )
            ->get();

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchSafetyShoes()
    {
        $data = GeneralShoesStock::where('quantity', '>', 0)->get();

        $resume = GeneralShoesStock::where('quantity', '>', 0)
            ->select('size', 'gender', db::raw('sum(quantity) AS quantity'))
            ->groupBy('size', 'gender')
            ->get();

        $response = array(
            'status' => true,
            'data' => $data,
            'resume' => $resume,
        );
        return Response::json($response);
    }

    public function fetchSafetyShoesDetail(Request $request)
    {
        $data = GeneralShoesStock::where('gender', $request->get('gender'))
            ->where('size', $request->get('size'))
            ->where('quantity', '>', 0)
            ->get();

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);

    }

    public function fetchOnlineTransportationResumeReport(Request $request)
    {
        $month = date('Y-m');
        if (strlen($request->get('month_from')) > 0) {
            $month = date('Y-m', strtotime($request->get('month_from')));
        }

        $transportations = db::select("SELECT
                                        A.employee_id,
                                        employee_syncs.name,
                                        A.zona,
                                        A.att_in,
                                        A.att_out,
                                        A.remark_in,
                                        A.remark_out,
                                        A.id_in,
                                        A.id_out,
                                        A.grade,
                                        A.check_date,
                                        A.attend_code,
                                        A.attend_count,
                                        A.vehicle,
                                        A.highway_amount_in,
                                        A.highway_amount_out,
                                        A.distance_in,
                                        A.distance_out,
                                        A.origin_in,
                                        A.origin_out,
                                        A.destination_in,
                                        A.destination_out,
                                        A.highway_amount_total,
                                        A.distance_total,
                                        A.remark
                                        FROM
                                        (
                                            SELECT
                                            attendance.zona,
                                            max( attendance.att_in ) AS att_in,
                                            max( attendance.att_out ) AS att_out,
                                            max( attendance.remark_in ) AS remark_in,
                                            max( attendance.remark_out ) AS remark_out,
                                            max( attendance.id_in ) AS id_in,
                                            max( attendance.id_out ) AS id_out,
                                            attendance.employee_id,
                                            attendance.grade,
                                            attendance.check_date,
                                            attendance.attend_code,
                                            max( attendance.attend_count ) AS attend_count,
                                            max( attendance.vehicle ) AS vehicle,
                                            max( attendance.highway_in ) AS highway_amount_in,
                                            max( attendance.highway_out ) AS highway_amount_out,
                                            max( attendance.distance_in ) AS distance_in,
                                            max( attendance.distance_out ) AS distance_out,
                                            max( attendance.origin_in ) AS origin_in,
                                            max( attendance.origin_out ) AS origin_out,
                                            max( attendance.destination_in ) AS destination_in,
                                            max( attendance.destination_out) AS destination_out,
                                            max( attendance.highway_in ) + max( attendance.highway_out ) AS highway_amount_total,
                                            if(attendance.check_date = '2023-02-04' OR attendance.check_date = '2023-02-05',max( attendance.distance_in ) + max( attendance.distance_out ),if(max( attendance.distance_in ) + max( attendance.distance_out ) > 150,150,max( attendance.distance_in ) + max( attendance.distance_out ))) AS distance_total,
                                                min( attendance.remark ) AS remark
                                            FROM
                                            (
                                                SELECT
                                                zona,
                                                highway_attachment AS att_in,
                                                0 AS att_out,
                                                remark AS remark_in,
                                                0 AS remark_out,
                                                id AS id_in,
                                                0 AS id_out,
                                                employee_id,
                                                grade,
                                                check_date,
                                                'hadir' AS attend_code,
                                                1 AS attend_count,
                                                vehicle,
                                                highway_amount AS highway_in,
                                                0 AS highway_out,
                                                distance AS distance_in,
                                                0 AS distance_out,
                                                origin AS origin_in,
                                                0 AS origin_out,
                                                destination AS destination_in,
                                                0 AS destination_out,
                                                remark
                                                FROM
                                                `general_transportations`
                                                WHERE
                                                DATE_FORMAT( check_date, '%Y-%m' ) = '" . $month . "'
                                                AND remark = 1
                                                AND attend_code = 'in' UNION ALL
                                                SELECT
                                                zona,
                                                0 AS att_in,
                                                highway_attachment AS att_out,
                                                0 AS remark_in,
                                                remark AS remark_out,
                                                0 AS id_in,
                                                id AS id_out,
                                                employee_id,
                                                grade,
                                                check_date,
                                                'hadir' AS attend_code,
                                                1 AS attend_count,
                                                vehicle,
                                                0 AS highway_in,
                                                highway_amount AS highway_out,
                                                0 AS distance_in,
                                                distance AS distance_out,
                                                0 AS origin_in,
                                                origin AS origin_out,
                                                0 AS destination_in,
                                                destination AS destination_out,
                                                remark
                                                FROM
                                                `general_transportations`
                                                WHERE
                                                DATE_FORMAT( check_date, '%Y-%m' ) = '" . $month . "'
                                                AND remark = 1
                                                AND attend_code = 'out' UNION ALL
                                                SELECT
                                                zona,
                                                0 AS att_in,
                                                0 AS att_out,
                                                remark AS remark_in,
                                                0 AS remark_out,
                                                id AS id_in,
                                                0 AS id_out,
                                                employee_id,
                                                grade,
                                                check_date,
                                                attend_code,
                                                0 AS attend_count,
                                                0 AS vehicle,
                                                0 AS highway_in,
                                                0 AS highway_out,
                                                0 AS distance_in,
                                                0 AS distance_out,
                                                0 AS origin_in,
                                                0 AS origin_out,
                                                0 AS destination_in,
                                                0 AS destination_out,
                                                remark
                                                FROM
                                                `general_transportations`
                                                WHERE
                                                DATE_FORMAT( check_date, '%Y-%m' ) = '" . $month . "'
                                                AND remark = 1
                                                AND attend_code <> 'out'
                                                AND attend_code <> 'in'
                                                ) AS attendance
                                                GROUP BY
                                                zona,
                                                employee_id,
                                                grade,
                                                check_date,
                                                attend_code
                                                ) AS A
                                                LEFT JOIN employee_syncs ON A.employee_id = employee_syncs.employee_id
                                                ORDER BY
                                                A.employee_id ASC,
                                                A.check_date ASC");

        $datas = array();

        foreach ($transportations as $transportation) {
            $fuel = 0;
            $divider = 0;
            $multiplier = 0;
            $grade = "";

            if ($transportation->grade != null) {
                $grade = $transportation->grade;
            }

            if (substr($grade, 0, 1) == 'M') {
                $divider = 5;
                if ($transportation->check_date < '2022-09-03') {
                    $multiplier = 7650;
                } else {
                    $multiplier = 10000;
                }
            } else if (substr($grade, 0, 1) == 'L') {
                $divider = 7;
                if ($transportation->check_date < '2022-09-03') {
                    $multiplier = 7650;
                } else {
                    $multiplier = 10000;
                }
            } else {
                $divider = $transportation->distance_total;
                $multiplier = 0;
            }

            if ($transportation->vehicle == 'car') {
                if ($transportation->distance_total <= 150) {
                    $fuel = ($transportation->distance_total / $divider) * $multiplier;
                } else {
                    if ($transportation->check_date == '2023-02-04' || $transportation->check_date == '2023-02-05') {
                        $fuel = ($transportation->distance_total / $divider) * $multiplier;
                    } else {
                        $fuel = (150 / $divider) * $multiplier;
                    }
                }
            }

            if ($transportation->vehicle == 'other') {
                if ($transportation->zona == '1') {
                    $fuel = 11000;
                } else if ($transportation->zona == '2') {
                    $fuel = 12400;
                } else {
                    $fuel = 17000;
                }
            }

            if (substr($grade, 0, 1) == 'M' || substr($grade, 0, 1) == 'L') {
                $total_amount = $fuel + $transportation->highway_amount_total;
            } else {
                $total_amount = 0;
            }

            array_push($datas,
                [
                    "zona" => $transportation->zona,
                    "att_in" => asset('files/general_transportation/' . $transportation->att_in),
                    "att_out" => asset('files/general_transportation/' . $transportation->att_out),
                    "remark_in" => $transportation->remark_in,
                    "remark_out" => $transportation->remark_out,
                    "id_in" => $transportation->id_in,
                    "id_out" => $transportation->id_out,
                    "employee_id" => $transportation->employee_id,
                    "name" => $transportation->name,
                    "grade" => $transportation->grade,
                    "check_date" => $transportation->check_date,
                    "attend_code" => $transportation->attend_code,
                    "attend_count" => $transportation->attend_count,
                    "vehicle" => $transportation->vehicle,
                    "highway_amount_in" => $transportation->highway_amount_in,
                    "highway_amount_out" => $transportation->highway_amount_out,
                    "distance_in" => $transportation->distance_in,
                    "distance_out" => $transportation->distance_out,
                    "origin_in" => $transportation->origin_in,
                    "destination_in" => $transportation->destination_in,
                    "origin_out" => $transportation->origin_out,
                    "destination_out" => $transportation->destination_out,
                    "highway_amount_total" => $transportation->highway_amount_total,
                    "distance_total" => $transportation->distance_total,
                    "remark" => $transportation->remark,
                    "fuel" => round($fuel, 2),
                    "total_amount" => $total_amount,
                ]);
        }

        $response = array(
            'status' => true,
            'transportations' => $datas,
        );
        return Response::json($response);

    }

    public function fetchOnlineTransportationReport(Request $request)
    {
        $month = date('Y-m');
        if (strlen($request->get('month_from')) > 0) {
            $month = date('Y-m', strtotime($request->get('month_from')));
        }

        $transportations = GeneralTransportation::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_transportations.employee_id')
            ->select('general_transportations.id', 'general_transportations.employee_id', 'employee_syncs.name', 'employee_syncs.grade_code', 'general_transportations.check_date', 'general_transportations.vehicle', 'general_transportations.origin', 'general_transportations.destination', 'general_transportations.attend_code', 'general_transportations.highway_amount', 'general_transportations.distance', 'general_transportations.highway_attachment')
            ->where(db::raw('date_format(general_transportations.check_date, "%Y-%m")'), '=', $month)
            ->where('general_transportations.remark', '=', 0)
            ->get();

        $datas = array();

        foreach ($transportations as $transportation) {
            array_push($datas,
                [
                    "id" => $transportation->id,
                    "employee_id" => $transportation->employee_id,
                    "name" => $transportation->name,
                    "grade_code" => $transportation->grade_code,
                    "check_date" => $transportation->check_date,
                    "attend_code" => $transportation->attend_code,
                    "vehicle" => $transportation->vehicle,
                    "origin" => $transportation->origin,
                    "destination" => $transportation->destination,
                    "highway_amount" => $transportation->highway_amount,
                    "distance" => $transportation->distance,
                    "highway_attachment" => asset('files/general_transportation/' . $transportation->highway_attachment),
                ]);

        }

        $response = array(
            'status' => true,
            'transportations' => $datas,
            'period' => date('F Y', strtotime($month)),
        );
        return Response::json($response);
    }

    public function fetchOnlineTransportation(Request $request)
    {
        $date_from = date('Y-m-01');
        $date_to = date('Y-m-t');

        if (strlen($request->get('date_from')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('date_from')));
        }

        if (strlen($request->get('date_to')) > 0) {
            $date_to = date('Y-m-d', strtotime($request->get('date_to')));
        }

        $transportations = db::select("
      SELECT
      calendar.week_date AS check_date,
      calendar.remark AS h,
      COALESCE ( attendance.zona, 0 ) AS zona,
      COALESCE ( attendance.att_in, 0 ) AS att_in,
      COALESCE ( attendance.att_out, 0 ) AS att_out,
      COALESCE ( attendance.remark_in, 0 ) AS remark_in,
      COALESCE ( attendance.remark_out, 0 ) AS remark_out,
      COALESCE ( attendance.id_in, 0 ) AS id_in,
      COALESCE ( attendance.id_out, 0 ) AS id_out,
      COALESCE ( attendance.employee_id, 0 ) AS employee_id,
      COALESCE ( attendance.grade, 0 ) AS grade,
      COALESCE ( attendance.attend_code, 0 ) AS attend_code,
      COALESCE ( attendance.vehicle, 0 ) AS vehicle,
      COALESCE ( attendance.origin_in, 0 ) AS origin_in,
      COALESCE ( attendance.destination_in, 0 ) AS destination_in,
      COALESCE ( attendance.origin_out, 0 ) AS origin_out,
      COALESCE ( attendance.destination_out, 0 ) AS destination_out,
      COALESCE ( attendance.highway_amount_in, 0 ) AS highway_amount_in,
      COALESCE ( attendance.highway_amount_out, 0 ) AS highway_amount_out,
      COALESCE ( attendance.distance_in, 0 ) AS distance_in,
      COALESCE ( attendance.distance_out, 0 ) AS distance_out,
      COALESCE ( attendance.highway_amount_total, 0 ) AS highway_amount_total,
      COALESCE ( attendance.distance_total, 0 ) AS distance_total,
      attendance.remark AS remark
      FROM
      ( SELECT * FROM weekly_calendars WHERE week_date >= '" . $date_from . "' AND week_date <= '" . $date_to . "' ) AS calendar
          LEFT JOIN (
          SELECT
          zona,
          max( att_in ) AS att_in,
          max( att_out ) AS att_out,
          max( remark_in ) AS remark_in,
          max( remark_out ) AS remark_out,
          max( id_in ) AS id_in,
          max( id_out ) AS id_out,
          employee_id,
          grade,
          check_date,
          attend_code,
          max( vehicle ) AS vehicle,
          max( origin_in ) AS origin_in,
          max( destination_in ) AS destination_in,
          max( origin_out ) AS origin_out,
          max( destination_out ) AS destination_out,
          max( highway_in ) AS highway_amount_in,
          max( highway_out ) AS highway_amount_out,
          max( distance_in ) AS distance_in,
          max( distance_out ) AS distance_out,
          max( highway_in ) + max( highway_out ) AS highway_amount_total,
          max( distance_in ) + max( distance_out ) AS distance_total,
          min( remark ) AS remark
          FROM
          (
          SELECT
          zona,
          highway_attachment AS att_in,
          0 AS att_out,
          remark AS remark_in,
          0 AS remark_out,
          id AS id_in,
          0 AS id_out,
          employee_id,
          grade,
          check_date,
          'hadir' AS attend_code,
          vehicle,
          origin as origin_in,
          destination as destination_in,
          0 AS origin_out,
          0 AS destination_out,
          highway_amount AS highway_in,
          0 AS highway_out,
          distance AS distance_in,
          0 AS distance_out,
          remark
          FROM
          `general_transportations`
          WHERE
          employee_id = '" . Auth::user()->username . "'
          AND check_date >= '" . $date_from . "'
          AND check_date <= '" . $date_to . "'
          AND attend_code = 'in' UNION ALL
          SELECT
          zona,
          0 AS att_in,
          highway_attachment AS att_out,
          0 AS remark_in,
          remark AS remark_out,
          0 AS id_in,
          id AS id_out,
          employee_id,
          grade,
          check_date,
          'hadir' AS attend_code,
          vehicle,
          0 AS origin_in,
          0 AS destination_in,
          origin as origin_out,
          destination as destination_out,
          0 AS highway_in,
          highway_amount AS highway_out,
          0 AS distance_in,
          distance AS distance_out,
          remark
          FROM
          `general_transportations`
          WHERE
          employee_id = '" . Auth::user()->username . "'
          AND check_date >= '" . $date_from . "'
          AND check_date <= '" . $date_to . "'
          AND attend_code = 'out' UNION ALL
          SELECT
          zona,
          0 AS att_in,
          0 AS att_out,
          remark AS remark_in,
          0 AS remark_out,
          id AS id_in,
          0 AS id_out,
          employee_id,
          grade,
          check_date,
          attend_code,
          0 AS vehicle,
          0 AS origin_in,
          0 AS destination_in,
          0 AS origin_out,
          0 AS destination_out,
          0 AS highway_in,
          0 AS highway_out,
          0 AS distance_in,
          0 AS distance_out,
          remark
          FROM
          `general_transportations`
          WHERE
          employee_id = '" . Auth::user()->username . "'
          AND check_date >= '" . $date_from . "'
          AND check_date <= '" . $date_to . "'
          AND attend_code <> 'out'
          AND attend_code <> 'in'
          ) AS A
          GROUP BY
          zona,
          employee_id,
          grade,
          check_date,
          attend_code
          ) AS attendance ON calendar.week_date = attendance.check_date
          ORDER BY
          calendar.week_date ASC");

        $datas = array();

        foreach ($transportations as $transportation) {
            array_push($datas,
                [
                    "h" => $transportation->h,
                    "zona" => $transportation->zona,
                    "att_in" => asset('files/general_transportation/' . $transportation->att_in),
                    "att_out" => asset('files/general_transportation/' . $transportation->att_out),
                    "remark_in" => $transportation->remark_in,
                    "remark_out" => $transportation->remark_out,
                    "id_in" => $transportation->id_in,
                    "id_out" => $transportation->id_out,
                    "employee_id" => $transportation->employee_id,
                    "grade" => $transportation->grade,
                    "check_date" => $transportation->check_date,
                    "attend_code" => $transportation->attend_code,
                    "origin_in" => $transportation->origin_in,
                    "destination_in" => $transportation->destination_in,
                    "origin_out" => $transportation->origin_out,
                    "destination_out" => $transportation->destination_out,
                    "vehicle" => $transportation->vehicle,
                    "highway_amount_in" => $transportation->highway_amount_in,
                    "highway_amount_out" => $transportation->highway_amount_out,
                    "distance_in" => $transportation->distance_in,
                    "distance_out" => $transportation->distance_out,
                    "highway_amount_total" => $transportation->highway_amount_total,
                    "distance_total" => $transportation->distance_total,
                    "remark" => $transportation->remark,
                ]);

        }

        $response = array(
            'status' => true,
            'transportations' => $datas,
        );
        return Response::json($response);
    }

    public function editOnlineTransportation(Request $request)
    {
        try {

            $datas = GeneralTransportation::where('id', $request->get('id'))->first();
            $response = array(
                'status' => true,
                'datas' => $datas,
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

    public function updateOnlineTransportation(Request $request)
    {
        try {

            $datas = GeneralTransportation::where('id', $request->get('id_transport'))->first();
            $datas->check_date = $request->get('editDate');
            $datas->distance = $request->get('editDistance');
            $datas->origin = $request->get('editOrigin');
            $datas->destination = $request->get('editDestination');
            $datas->highway_amount = $request->get('editHighwayAmount');
            $datas->save();

            $response = array(
                'status' => true,
                'datas' => $datas,
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

    public function indexOmiVisitor()
    {
        $title = 'Koyami Visitor';
        $title_jp = '売店来客';

        return view('general.omi_visitor', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('head', 'Koyami Visitor');
    }

    public function fetchOmiVisitor()
    {
        $visitors = db::connection('rfid')->table('omi_lists')->get();

        $response = array(
            'status' => true,
            'visitors' => $visitors,
        );
        return Response::json($response);
    }

    public function inputOmiVisitor(Request $request)
    {
        try {
            $employee = db::table('employees')->where('tag', '=', $request->get('tag'))->first();

            $visitor = db::connection('rfid')->table('omi_lists')
                ->where('employee_id', '=', $employee->employee_id)
                ->select('employee_id', 'created_at', db::raw('now() as now'), db::raw('timestampdiff(second, created_at, "' . date('Y-m-d H:i:s') . '") as duration'))
                ->first();

            if (count($visitor) > 0) {
                if ($visitor->duration <= 5) {
                    $response = array(
                        'status' => false,
                        'message' => 'Leadtime',
                        'from' => $visitor->created_at,
                        'to' => $visitor->now,
                        'duration' => $visitor->duration,
                    );
                    return Response::json($response);
                }

                $input_log = db::connection('rfid')->table('omi_logs')->insert([
                    'employee_id' => $employee->employee_id,
                    'last_seen_1' => $visitor->created_at,
                    'last_seen_2' => date('Y-m-d H:i:s'),
                    'created_at' => $visitor->created_at,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $delete = db::connection('rfid')->table('omi_lists')
                    ->where('employee_id', '=', $employee->employee_id)
                    ->delete();

                $response = array(
                    'status' => true,
                    'message' => 'berhasil_keluar',
                );
                return Response::json($response);
            } else {
                $count_visitor = db::connection('rfid')->table('omi_lists')->count();

                if ($count_visitor < 14) {
                    $visitor = db::connection('rfid')->table('omi_lists')->insert([
                        'employee_id' => $employee->employee_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $response = array(
                        'status' => true,
                        'message' => 'silahkan_masuk',
                    );
                    return Response::json($response);
                } else {
                    $response = array(
                        'status' => true,
                        'message' => 'dilarang_masuk',
                    );
                    return Response::json($response);
                }
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexGeneralPointingCall($id)
    {
        if ($id == 'japanese') {
            $title = 'Japanese Pointing Calls';
            $title_jp = '駐在員指差し呼称';

            return view('general.pointing_call.japanese', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'default_language' => 'jp',
                'location' => $id,
            ))->with('head', 'Pointing Calls');
        }
        if ($id == 'national') {
            $title = 'National Staff Pointing Calls';
            $title_jp = 'ナショナル・スタッフ用の指差し呼称';

            return view('general.pointing_call.national', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'default_language' => 'id',
                'location' => $id,
            ))->with('head', 'Pointing Calls');
        }
        if ($id == 'aturan_keselamatan') {
            $title = 'Aturan Keselamatan YMPI';
            $title_jp = 'YMPI安全掟';

            return view('general.pointing_call.aturan_keselamatan', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'default_language' => 'id',
                'location' => $id,
            ))->with('head', 'Pointing Calls');
        }
    }

    public function editGeneralPointingCallPic(Request $request)
    {

        $pics = db::table('pointing_calls')
            ->where('location', '=', $request->get('location'))
            ->where('point_title', '=', $request->get('point_title'))
            ->update([
                'remark' => 0,
            ]);

        $pic = db::table('pointing_calls')
            ->where('id', '=', $request->get('id'))
            ->update([
                'remark' => 1,
            ]);

        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function indexGeneralAttendanceCheck()
    {
        $title = "Attendance Check";
        $title_jp = "";

        $purposes = GeneralAttendance::orderBy('purpose_code', 'asc')
            ->select('purpose_code')
            ->distinct()
            ->get();

        return view('general.attendance_check', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'purposes' => $purposes,
        ))->with('head', 'GA Control')->with('page', 'Driver Control');
    }

    public function scanSafetyShoes(Request $request)
    {

        $request_id = $request->get('request_id');

        $data = GeneralShoesRequest::leftJoin('users', 'users.id', '=', 'general_shoes_requests.created_by')
            ->where('general_shoes_requests.request_id', $request_id)
            ->where('general_shoes_requests.metode', 'Stock')
            ->select(
                'general_shoes_requests.gender',
                'general_shoes_requests.merk',
                'general_shoes_requests.size',
                'users.name',
                db::raw('COUNT(general_shoes_requests.id) AS qty')
            )
            ->groupBy(
                'general_shoes_requests.gender',
                'general_shoes_requests.merk',
                'general_shoes_requests.size',
                'users.name'
            )
            ->orderBy('general_shoes_requests.gender', 'ASC')
            ->orderBy('general_shoes_requests.size', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function scanGeneralAttendanceCheck(Request $request)
    {
        $employee = Employee::where('tag', '=', $request->get('tag'))->first();

        if ($employee == "") {
            $response = array(
                'status' => false,
                'message' => 'Tag karyawan tidak terdaftar, hubungi bagian MIS.',
            );
            return Response::json($response);
        }

        $attendance = GeneralAttendance::where('employee_id', '=', $employee->employee_id)
            ->where('due_date', '=', date('Y-m-d'))
            ->first();

        if ($attendance == "" || $attendance->due_date > date('Y-m-d')) {
            $response = array(
                'status' => false,
                'message' => 'Karyawan tidak ada pada schedule.',
            );
            return Response::json($response);
        }

        if ($attendance->attend_date != null) {
            $response = array(
                'status' => false,
                'message' => 'Karyawan sudah menghadiri schedule.',
            );
            return Response::json($response);
        }

        try {
            $attendance->attend_date = date('Y-m-d H:i:s');
            $attendance->save();

            $response = array(
                'status' => true,
                'message' => $employee->name . ' berhasil hadir.',
            );
            return Response::json($response);

        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'Berhasil',
        );
        return Response::json($response);

    }

    public function fetchGeneralAttendanceCheck(Request $request)
    {

        try {
            $now = date('Y-m-d');

            $attendance_lists = db::select("SELECT
           ga.purpose_code,
           ga.employee_id,
           es.`name`,
           ga.attend_date
           FROM
           general_attendances AS ga
           LEFT JOIN employee_syncs AS es ON ga.employee_id = es.employee_id
           WHERE
           ga.due_date = '" . $now . "'
           ORDER BY
           attend_date DESC");

// $query = "SELECT DISTINCT
// purpose_code,
// employee_id,
// due_date,
// NAME,
// departments.department_shortname AS department,
// attend_date
// FROM
// (
// SELECT
// general_attendances.purpose_code,
// general_attendances.employee_id,
// general_attendances.due_date,
// employee_syncs.`name`,
// employee_syncs.department,
// DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
// FROM
// general_attendances
// LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id
// WHERE
// general_attendances.due_date = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."' UNION ALL
// SELECT
// general_attendances.purpose_code,
// general_attendances.employee_id,
// general_attendances.due_date,
// employee_syncs.`name`,
// employee_syncs.department,
// DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
// FROM
// general_attendances
// LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id
// WHERE
// DATE( general_attendances.attend_date ) = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."'
// ) AS attendances
// LEFT JOIN
// departments on departments.department_name = attendances.department
// WHERE employee_id like 'PI%'
// ORDER BY
// attend_date DESC,
// NAME ASC";

// $attendance_lists = db::select($query);

            $response = array(
                'status' => true,
                'attendance_lists' => $attendance_lists,
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

    public function indexQueue($remark)
    {
        if ($remark == 'mcu') {
            $title = "Medical Check Up Queue";
            $title_jp = "??";
        }

        return view('general.queue.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'remark' => $remark,
        ));
    }

    public function fetchQueue($remark, Request $request)
    {
        try {
            $auth = EmployeeSync::where('employee_syncs.employee_id', Auth::user()->username)->first();
            $now = date('Y-m-d');
            if ($remark == 'mcu') {
                $data_registrasi = DB::SELECT("SELECT
                meetings.id,
                SPLIT_STRING ( description, ' - ', 2 ) AS loc,
                meeting_details.*,
                employee_syncs.`name`,
                departments.department_shortname ,
                employee_syncs.section,
                shiftdaily_code
                FROM
                meetings
                JOIN meeting_details ON meeting_details.meeting_id = meetings.id
                AND meeting_details.STATUS = 0 and DATE(meeting_details.created_at) = '" . $now . "'
                JOIN employee_syncs ON employee_syncs.employee_id = meeting_details.employee_id
                left JOIN departments ON departments.department_name = employee_syncs.department
                left join sunfish_shift_syncs on sunfish_shift_syncs.employee_id = employee_syncs.employee_id
                and shift_date = '" . $now . "'
                WHERE
                `subject` = 'Medical Check Up'
                and SPLIT_STRING ( description, ' - ', 2 ) = 'Registrasi'
                order By meeting_details.created_at asc
                ");

                $data_clinic = DB::SELECT("SELECT
                meetings.id,
                SPLIT_STRING ( description, ' - ', 2 ) AS loc,
                meeting_details.*,
                employee_syncs.`name`,
                departments.department_shortname ,
                employee_syncs.section
                FROM
                meetings
                JOIN meeting_details ON meeting_details.meeting_id = meetings.id
                AND meeting_details.STATUS = 0 and DATE(meeting_details.created_at) = '" . $now . "'
                JOIN employee_syncs ON employee_syncs.employee_id = meeting_details.employee_id
                left JOIN departments ON departments.department_name = employee_syncs.department
                WHERE
                `subject` = 'Medical Check Up'
                and SPLIT_STRING ( description, ' - ', 2 ) = 'Darah'
                order By meeting_details.created_at asc
                ");

                $data_thorax = DB::SELECT("SELECT
                meetings.id,
                SPLIT_STRING ( description, ' - ', 2 ) AS loc,
                meeting_details.*,
                employee_syncs.`name`,
                departments.department_shortname ,
                employee_syncs.section
                FROM
                meetings
                JOIN meeting_details ON meeting_details.meeting_id = meetings.id
                AND meeting_details.STATUS = 0 and DATE(meeting_details.created_at) = '" . $now . "'
                JOIN employee_syncs ON employee_syncs.employee_id = meeting_details.employee_id
                left JOIN departments ON departments.department_name = employee_syncs.department
                WHERE
                `subject` = 'Medical Check Up'
                and SPLIT_STRING ( description, ' - ', 2 ) = 'Thorax'
                order By meeting_details.created_at asc
                ");

                $data_audiometri = DB::SELECT("SELECT
                meetings.id,
                SPLIT_STRING ( description, ' - ', 2 ) AS loc,
                meeting_details.*,
                employee_syncs.`name`,
                departments.department_shortname ,
                employee_syncs.section
                FROM
                meetings
                JOIN meeting_details ON meeting_details.meeting_id = meetings.id
                AND meeting_details.STATUS = 0 and DATE(meeting_details.created_at) = '" . $now . "'
                JOIN employee_syncs ON employee_syncs.employee_id = meeting_details.employee_id
                left JOIN departments ON departments.department_name = employee_syncs.department
                WHERE
                `subject` = 'Medical Check Up'
                and SPLIT_STRING ( description, ' - ', 2 ) = 'Audiometri'
                order By meeting_details.created_at asc
                ");
            }

            if (count($auth) > 0) {
                $response = array(
                    'status' => true,
                    'data_thorax' => $data_thorax,
                    'data_audiometri' => $data_audiometri,
                    'data_clinic' => $data_clinic,
                    'data_registrasi' => $data_registrasi,
                    'section' => $auth->section,
                    'now' => $now,
                );
            } else {
                $response = array(
                    'status' => true,
                    'data_thorax' => $data_thorax,
                    'data_audiometri' => $data_audiometri,
                    'data_clinic' => $data_clinic,
                    'data_registrasi' => $data_registrasi,
                    'section' => "",
                    'now' => $now,
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

//  -------------------------   OXYMETER ------------
    public function indexOxymeterCheck()
    {
        $title = "Oximeter Check";
        $title_jp = "オキシメーター検査";

// $employees = EmployeeSync::orderBy('department', 'asc')->get();

        return view('general.oxymeter.index_check', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ));
    }

    public function postOxymeterCheck(Request $request)
    {
        try {
            $att_log = GeneralAttendanceLog::firstOrNew(array('employee_id' => $request->get('employee_id'), 'due_date' => date('Y-m-d'), 'purpose_code' => 'Oxymeter'));

            $att_log->attend_date = date('Y-m-d H:i:s');

            if ($request->get('ctg') == 'oxygen') {
                $att_log->remark = $request->get('value');
                if ($request->get('value') < 95) {
                    $att_log->status = 'Lapor <br> Masih Bekerja';
                }
            } else {
                $att_log->remark2 = $request->get('value');
            }

            $att_log->created_by = Auth::user()->id;

            $att_log->save();

            if ($request->get('ctg') != 'oxygen' && $att_log->remark > 0 && $att_log->remark < 95 && $att_log->remark2) {
                $data = GeneralAttendanceLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_attendance_logs.employee_id')
                    ->where('general_attendance_logs.employee_id', '=', $request->get('employee_id'))
                    ->where('general_attendance_logs.due_date', '=', date('Y-m-d'))
                    ->where('general_attendance_logs.purpose_code', '=', 'Oxymeter')
                    ->select('general_attendance_logs.employee_id', 'employee_syncs.name', 'employee_syncs.section', 'general_attendance_logs.remark', 'general_attendance_logs.remark2')
                    ->first();

                $section = EmployeeSync::where('employee_id', '=', $request->get('employee_id'))->first();

                $mail_to = db::select("select email from send_emails where remark = '" . $section->section . "'");

                Mail::to($mail_to)->bcc(['nasiqul.ibat@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])->send(new SendEmail($data, 'notification_oxymeter'));
            }

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

    public function fetchOxymeterHistory(Request $request)
    {
// DB::connection()->enableQueryLog();
        $oxy_log = GeneralAttendanceLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_attendance_logs.employee_id')
// ->leftJoin('sunfish_shift_syncs', 'sunfish_shift_syncs.employee_id', '=', 'general_attendance_logs.employee_id')
            ->leftJoin('sunfish_shift_syncs', function ($join) {
                $join->on('sunfish_shift_syncs.employee_id', '=', 'general_attendance_logs.employee_id')
                    ->on('sunfish_shift_syncs.shift_date', '=', 'general_attendance_logs.due_date');
            })
            ->where('purpose_code', '=', 'Oxymeter');

        if (strlen($request->get('username')) > 0) {
            $dpt = EmployeeSync::where('employee_id', '=', $request->get('username'))->first();
            $oxy_log = $oxy_log->whereRaw('(employee_syncs.department = "' . $dpt->department . '" OR general_attendance_logs.created_by = "' . Auth::user()->id . '")');
        }

        if (strlen($request->get('dt')) > 0) {
            $oxy_log = $oxy_log->where('general_attendance_logs.due_date', '=', $request->get('dt'));
        }

        $oxy_log = $oxy_log->orderBy('updated_at', 'desc');

        if (strlen($request->get('limit')) > 0) {
            $oxy_log = $oxy_log->limit($request->get('limit'));
        }

        $oxy_log = $oxy_log->select('general_attendance_logs.updated_at', 'general_attendance_logs.employee_id', 'employee_syncs.name', 'general_attendance_logs.remark', 'general_attendance_logs.remark2', 'general_attendance_logs.status')->get();

        $response = array(
            'status' => true,
            'datas' => $oxy_log,
// 'query' => DB::getQueryLog()
        );
        return Response::json($response);
    }

    public function indexOxymeterMonitoring()
    {
        $title = "Oximeter Monitoring";
        $title_jp = "オキシメーターモニター";

        return view('general.oxymeter.index_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ));
    }

    public function fetchOxymeterMonitoring(Request $request)
    {

        if ($request->get('dt')) {
            $dt = $request->get('dt');
        } else {
            $dt = date('Y-m-d');
        }

        $oxy_log = GeneralAttendanceLog::leftJoin('employees', 'employees.employee_id', '=', 'general_attendance_logs.employee_id')
            ->where('purpose_code', '=', 'Oxymeter')
            ->where('general_attendance_logs.due_date', '=', $dt)
            ->select('general_attendance_logs.remark', db::raw('COUNT(general_attendance_logs.remark) as qty'))
            ->groupBy('general_attendance_logs.remark')
            ->get();

        $pulse_log = GeneralAttendanceLog::leftJoin('employees', 'employees.employee_id', '=', 'general_attendance_logs.employee_id')
            ->where('purpose_code', '=', 'Oxymeter')
            ->where('general_attendance_logs.due_date', '=', $dt)
            ->select('general_attendance_logs.remark2', db::raw('COUNT(general_attendance_logs.remark2) as qty'))
            ->groupBy('general_attendance_logs.remark2')
            ->get();

        $shift_log = db::select("SELECT sunfish_shift_syncs.employee_id, employees.name, employees.remark, shiftdaily_code, attend_code, departments.department_shortname, section, `group`, oxymeter.remark as oxy, oxymeter.remark2 as pulse, oxymeter.attend_date as check_time, oxymeter.status from sunfish_shift_syncs
      left join employees on employees.employee_id = sunfish_shift_syncs.employee_id
      left join (select * from general_attendance_logs where purpose_code = 'Oxymeter' and due_date = '" . $dt . "') as oxymeter on oxymeter.employee_id = sunfish_shift_syncs.employee_id
          left join employee_syncs on sunfish_shift_syncs.employee_id = employee_syncs.employee_id
          left join departments on employee_syncs.department = departments.department_name
          where shift_date = '" . $dt . "' and employee_syncs.end_date is null");

        $clinic_visit = db::connection('clinic')->table('patient_list')->select('employee_id')->get();

        $response = array(
            'status' => true,
            'oxy_datas' => $oxy_log,
            'pulse_datas' => $pulse_log,
            'shift' => $shift_log,
            'clinic' => $clinic_visit,
            'date' => date('d M Y', strtotime($dt)),
        );
        return Response::json($response);
    }

    public function UploadOxymeter(Request $request)
    {
        $date = $request->get('date');
        $cek_by = $request->get('username');
        $upload = $request->get('upload');
        $uploadRows = preg_split("/\r?\n/", $upload);

        DB::beginTransaction();

        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);

            $month = date('Y-m-d', strtotime(str_replace('/', '-', $uploadColumn[0])));
            $material_number = $uploadColumn[1];
            $quantity = $uploadColumn[2];

            try {
                $att_log = GeneralAttendanceLog::firstOrNew(array('employee_id' => $uploadColumn[0], 'due_date' => $date, 'purpose_code' => 'Oxymeter'));

                $att_log->attend_date = date('Y-m-d H:i:s');

                $att_log->remark = $uploadColumn[1];
                if ((int) $uploadColumn[1] < 95) {
                    $att_log->status = 'Lapor';
                }

                $att_log->remark2 = $uploadColumn[2];

                $att_log->created_by = Auth::user()->id;

                $att_log->save();

                if ($uploadColumn[1] < 95) {
                    $data = GeneralAttendanceLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_attendance_logs.employee_id')
                        ->where('general_attendance_logs.employee_id', '=', $uploadColumn[0])
                        ->where('general_attendance_logs.due_date', '=', $date)
                        ->where('general_attendance_logs.purpose_code', '=', 'Oxymeter')
                        ->select('general_attendance_logs.employee_id', 'employee_syncs.name', 'employee_syncs.section', 'general_attendance_logs.remark', 'general_attendance_logs.remark2')
                        ->first();

                    $section = EmployeeSync::where('employee_id', '=', $uploadColumn[0])->first();

                    $mail_to = db::select("select email from send_emails where remark = '" . $section->section . "'");

                    Mail::to($mail_to)->bcc(['nasiqul.ibat@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])->send(new SendEmail($data, 'notification_oxymeter'));
                }
            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

//  -----------  AIR VISUAL -----------
    public function indexAirVisual()
    {
        $title = "CO2 Monitor";
        $title_jp = "二酸化炭素モニター";

        return view('general.air_visual_map', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ));
    }

    public function postAirVisual()
    {
        $result = "";

        $arr_api = [
            // 'https://www.airvisual.com/api/v2/node/6051b1f079e60a367adc1a45',
            'https://device.iqair.com/v2/6358a76287bcb4e85a9d0182',
            'https://www.airvisual.com/api/v2/node/606bb8c5efbeaf90b42b8e98',
            'https://www.airvisual.com/api/v2/node/60c71272d0ee39c0b5a7fb41',
            'https://www.airvisual.com/api/v2/node/606bbb0c97b9294b00b2b297',
            'https://www.airvisual.com/api/v2/node/606bbe7797b929136cb2b2a4',
            'https://www.airvisual.com/api/v2/node/6221817d6664d6ce12ca73c9',
            // 'https://www.airvisual.com/api/v2/node/60dbedab1b6bb0409ab4f1c'
        ];

        $true_date = "";

        if (Auth::user()->username == "display") {
            foreach ($arr_api as $api) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, $api);
                $result = curl_exec($ch);
                curl_close($ch);

                $arr = json_decode($result, true);
                $s = "";

                for ($i = count($arr['historical']['instant']) - 1; $i > 0; $i--) {
                    $s = str_replace('T', ' ', $arr['historical']['instant'][$i]['ts']);
                    $times = substr(explode(' ', $s)[1], 0, -5);
                    $dates = explode(' ', $s)[0];

                    $ts = date_create($dates . " " . $times);

// $ts2 = date('Y-m-d H:i:s', strtotime($times) + 60*420);

                    date_add($ts, date_interval_create_from_date_string('7 hours'));
                    $true_date = date_format($ts, 'Y-m-d H:i:s');

                    $air_log = GeneralAirVisualLog::firstOrNew(array('data_time' => $true_date));
                    $air_log->get_at = date('Y-m-d H:i:00');
                    $air_log->remark = $arr['historical']['instant'][$i]['ts'];
                    $air_log->co = $arr['historical']['instant'][$i]['co'];
                    $air_log->temperature = $arr['historical']['instant'][$i]['tp'];
                    $air_log->humidity = $arr['historical']['instant'][$i]['hm'];
                    $air_log->location = $arr['settings']['node_name'];
                    $air_log->created_at = date('Y-m-d H:i:s');
                    $air_log->updated_at = date('Y-m-d H:i:s');

                    $air_log->save();
                }
            }
        }

        // $datas_2 = db::SELECT("SELECT DATE_FORMAT(data_time, '%H:%i') as data_time2, AVG(sensor_value) as co2 from sensor_datas where data_time >= '2022-04-07 06:45:00' and category = 'CO2' and unit = 'newalat6' GROUP BY data_time2 order by data_time asc");

        $datas = GeneralAirVisualLog::whereRaw('DATE_FORMAT(data_time,"%Y-%m-%d %H:%i:%s") >= "' . date('Y-m-d 06:45:00') . '"')
            ->select('location', 'data_time', 'co', 'temperature', 'humidity', db::raw('DATE_FORMAT(data_time, "%H:%i") as data_time2'))
            ->orderBy('id', 'asc')
            ->get();

        $last_data = db::select('SELECT id, location, co, temperature, humidity FROM general_air_visual_logs
       WHERE id IN (
           SELECT MAX(id)
           FROM general_air_visual_logs
           GROUP BY location
           ) and DATE_FORMAT(created_at,"%Y-%m-%d %H:%i:%s") >= "' . date('Y-m-d 06:45:00') . '"
       order by location asc');

        $response = array(
            'status' => true,
            'message' => '',
            'datas' => $datas,
            // 'datas_2' => $datas_2,
            'time' => $true_date,
            'last_data' => $last_data,
        );
        return Response::json($response);
    }

    public function getAirVisual()
    {
        $datas = GeneralAirVisualLog::whereRaw('DATE_FORMAT(data_time,"%Y-%m-%d %H:%i:%s") >= "' . date('Y-m-d 06:00:00') . '"')
            ->select('location', 'data_time', 'co', 'temperature', 'humidity', db::raw('DATE_FORMAT(data_time, "%H:%i") as data_time2'))
            ->orderBy('id', 'asc')
            ->get();

        $last_data = db::select('SELECT id, location, co, temperature, humidity FROM general_air_visual_logs
       WHERE id IN (
           SELECT MAX(id)
           FROM general_air_visual_logs
           GROUP BY location
           ) and DATE_FORMAT(created_at,"%Y-%m-%d %H:%i:%s") >= "' . date('Y-m-d 06:00:00') . '"
       order by location asc');

        $response = array(
            'status' => true,
            'datas' => $datas,
            'last_data' => $last_data,
        );
        return Response::json($response);
    }

    public function excelOnlineTransportation(Request $request)
    {
        $month = date('Y-m');
        if (strlen($request->get('monthfrom')) > 0) {
            $month = date('Y-m', strtotime($request->get('monthfrom')));
        }

        $transportations = db::select("SELECT
       A.employee_id,
       employee_syncs.name,
       A.zona,
       A.att_in,
       A.att_out,
       A.remark_in,
       A.remark_out,
       A.id_in,
       A.id_out,
       A.grade,
       A.check_date,
       A.attend_code,
       A.attend_count,
       A.vehicle,
       A.highway_amount_in,
       A.highway_amount_out,
       A.distance_in,
       A.distance_out,
       A.origin_in,
       A.origin_out,
       A.destination_in,
       A.destination_out,
       A.highway_amount_total,
       A.distance_total,
       A.remark
       FROM
       (
           SELECT
           attendance.zona,
           max( attendance.att_in ) AS att_in,
           max( attendance.att_out ) AS att_out,
           max( attendance.remark_in ) AS remark_in,
           max( attendance.remark_out ) AS remark_out,
           max( attendance.id_in ) AS id_in,
           max( attendance.id_out ) AS id_out,
           attendance.employee_id,
           attendance.grade,
           attendance.check_date,
           attendance.attend_code,
           max( attendance.attend_count ) AS attend_count,
           max( attendance.vehicle ) AS vehicle,
           max( attendance.highway_in ) AS highway_amount_in,
           max( attendance.highway_out ) AS highway_amount_out,
           max( attendance.distance_in ) AS distance_in,
           max( attendance.distance_out ) AS distance_out,
           max( attendance.origin_in ) AS origin_in,
           max( attendance.origin_out ) AS origin_out,
           max( attendance.destination_in ) AS destination_in,
           max( attendance.destination_out) AS destination_out,
           max( attendance.highway_in ) + max( attendance.highway_out ) AS highway_amount_total,
           max( attendance.distance_in ) + max( attendance.distance_out ) AS distance_total,
           min( attendance.remark ) AS remark
           FROM
           (
               SELECT
               zona,
               highway_attachment AS att_in,
               0 AS att_out,
               remark AS remark_in,
               0 AS remark_out,
               id AS id_in,
               0 AS id_out,
               employee_id,
               grade,
               check_date,
               'hadir' AS attend_code,
               1 AS attend_count,
               vehicle,
               highway_amount AS highway_in,
               0 AS highway_out,
               distance AS distance_in,
               0 AS distance_out,
               origin AS origin_in,
               0 AS origin_out,
               destination AS destination_in,
               0 AS destination_out,
               remark
               FROM
               `general_transportations`
               WHERE
               DATE_FORMAT( check_date, '%Y-%m' ) = '" . $month . "'
               AND remark = 1
               AND attend_code = 'in' UNION ALL
               SELECT
               zona,
               0 AS att_in,
               highway_attachment AS att_out,
               0 AS remark_in,
               remark AS remark_out,
               0 AS id_in,
               id AS id_out,
               employee_id,
               grade,
               check_date,
               'hadir' AS attend_code,
               1 AS attend_count,
               vehicle,
               0 AS highway_in,
               highway_amount AS highway_out,
               0 AS distance_in,
               distance AS distance_out,
               0 AS origin_in,
               origin AS origin_out,
               0 AS destination_in,
               destination AS destination_out,
               remark
               FROM
               `general_transportations`
               WHERE
               DATE_FORMAT( check_date, '%Y-%m' ) = '" . $month . "'
               AND remark = 1
               AND attend_code = 'out' UNION ALL
               SELECT
               zona,
               0 AS att_in,
               0 AS att_out,
               remark AS remark_in,
               0 AS remark_out,
               id AS id_in,
               0 AS id_out,
               employee_id,
               grade,
               check_date,
               attend_code,
               0 AS attend_count,
               0 AS vehicle,
               0 AS highway_in,
               0 AS highway_out,
               0 AS distance_in,
               0 AS distance_out,
               0 AS origin_in,
               0 AS origin_out,
               0 AS destination_in,
               0 AS destination_out,
               remark
               FROM
               `general_transportations`
               WHERE
               DATE_FORMAT( check_date, '%Y-%m' ) = '" . $month . "'
               AND remark = 1
               AND attend_code <> 'out'
               AND attend_code <> 'in'
               ) AS attendance
               GROUP BY
               zona,
               employee_id,
               grade,
               check_date,
               attend_code
               ) AS A
               LEFT JOIN employee_syncs ON A.employee_id = employee_syncs.employee_id
               ORDER BY
               A.employee_id ASC,
               A.check_date ASC");

        $datas = array();

        foreach ($transportations as $transportation) {
            $fuel = 0;
            $divider = 0;
            $multiplier = 0;
            $grade = "";

            if ($transportation->grade != null) {
                $grade = $transportation->grade;
            }

            if (substr($grade, 0, 1) == 'M') {
                $divider = 5;
                if ($transportation->check_date < '2022-09-03') {
                    $multiplier = 7650;
                } else {
                    $multiplier = 10000;
                }
            } else if (substr($grade, 0, 1) == 'L') {
                $divider = 7;
                if ($transportation->check_date < '2022-09-03') {
                    $multiplier = 7650;
                } else {
                    $multiplier = 10000;
                }
            } else {
                $divider = $transportation->distance_total;
                $multiplier = 0;
            }

            if ($transportation->vehicle == 'car') {
                if ($transportation->distance_total <= 150) {
                    $fuel = ($transportation->distance_total / $divider) * $multiplier;
                } else {
                    $fuel = (150 / $divider) * $multiplier;
                }
            }

            if ($transportation->vehicle == 'other') {
                if ($transportation->zona == '1') {
                    $fuel = 11000;
                } else if ($transportation->zona == '2') {
                    $fuel = 12400;
                } else {
                    $fuel = 17000;
                }
            }

            if (substr($grade, 0, 1) == 'M' || substr($grade, 0, 1) == 'L') {
                $total_amount = $fuel + $transportation->highway_amount_total;
            } else {
                $total_amount = 0;
            }

            if (in_array($transportation->employee_id, $datas)) {

            } else {
                array_push($datas,
                    [
                        "employee_id" => $transportation->employee_id,
                        "name" => $transportation->name,
                        "total_amount" => $total_amount,
                    ]);
            }
        }

        $data = array(
            'datas' => $datas,
        );

        ob_clean();
        Excel::create('Report Transportation', function ($excel) use ($data) {
            $excel->sheet('Payroll Component Upload', function ($sheet) use ($data) {
                return $sheet->loadView('general.dropbox.excel_online_transportation', $data);
            });
        })->export('xlsx');

// return view('general.dropbox.excel_online_transportation',$data);
    }

    public function indexCompetitionRegistration()
    {
        $title = 'YMPI Competition Registration';
        $title_jp = 'YMPIコンペティション申し込み';

        $emp = EmployeeSync::where('employee_id', strtoupper(Auth::user()->username))->first();

        $check_ofc = Employee::where('employee_id', $emp->employee_id)->first();

        $div_sec = '';
        $ofc = '';

        if ($check_ofc->remark == 'OFC') {
            $div_sec = $emp->division;
            $ofc = 'Office';
        } else {
            $ofc = 'Produksi';
            $div_sec = $emp->section;
        }

        return view('general.competition.registration', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'emp' => $emp,
            'div_sec' => $div_sec,
            'ofc' => $ofc,
        ))->with('page', 'YMPI Competition');
    }

    public function fetchCompetitionParticipant()
    {
        try {
            $emp = EmployeeSync::where('end_date', null)->get();
            $emp_putra = EmployeeSync::where('end_date', null)->where('gender', 'L')->get();
            $emp_putri = EmployeeSync::where('end_date', null)->where('gender', 'P')->get();

            $response = array(
                'status' => true,
                'emp' => $emp,
                'emp_putra' => $emp_putra,
                'emp_putri' => $emp_putri,
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

    public function inputCompetitionRegistration(Request $request)
    {
        try {
            $category = $request->get('category');
            $count = DB::connection('ympimis_2')->select("SELECT
              category,
              count( DISTINCT ( no_urut ) ) AS count
              FROM
              general_competition_registers
              where category = '" . $category . "'
              GROUP BY
              category");

            $limit = DB::connection('ympimis_2')->select("SELECT
              `limit`
              FROM
              general_competition_limits
              where category = '" . $category . "'");

            if (count($count) > 0) {
                if ($count[0]->count == $limit[0]->limit) {
                    $response = array(
                        'status' => false,
                        'message' => 'Mohon maaf, pendaftaran ' . $category . ' sudah penuh.',
                    );
                    return Response::json($response);
                }
                $new_count = $count[0]->count + 1;
            } else {
                $new_count = 1;
            }

            $players = $request->get('players');

            for ($i = 0; $i < count($players); $i++) {
                $check = DB::connection("ympimis_2")->select("SELECT
                 *
                   FROM
                   `general_competition_registers`
                   WHERE
                   category = '" . $category . "'
                   AND employee_id = '" . explode(' - ', $players[$i])[0] . "'");
                if (count($check) > 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Mohon Maaf, Karyawan ' . explode(' - ', $players[$i])[1] . ' sudah terdaftar di ' . $category,
                    );
                    return Response::json($response);
                }
            }

            if ($category == 'Mobile Legends') {
                $team_name = $request->get('team_name');
                $phone_no = $request->get('phone_no');
                $player_1 = $request->get('player_1');
                $player_2 = $request->get('player_2');
                $player_3 = $request->get('player_3');
                $player_4 = $request->get('player_4');
                $player_5 = $request->get('player_5');
                $player_6 = $request->get('player_6');

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_1)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 1,
                    'player_name' => 'Player 1 / Ketua Tim',
                    'employee_id' => explode(' - ', $player_1)[0],
                    'name' => explode(' - ', $player_1)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_2)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 2,
                    'player_name' => 'Player 2',
                    'employee_id' => explode(' - ', $player_2)[0],
                    'name' => explode(' - ', $player_2)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_3)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 3,
                    'player_name' => 'Player 3',
                    'employee_id' => explode(' - ', $player_3)[0],
                    'name' => explode(' - ', $player_3)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_4)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 4,
                    'player_name' => 'Player 4',
                    'employee_id' => explode(' - ', $player_4)[0],
                    'name' => explode(' - ', $player_4)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_5)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 5,
                    'player_name' => 'Player 5',
                    'employee_id' => explode(' - ', $player_5)[0],
                    'name' => explode(' - ', $player_5)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                if ($player_6 != '') {
                    $emp = EmployeeSync::where('employee_id', explode(' - ', $player_6)[0])->first();

                    $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                        'category' => $category,
                        'no_urut' => $new_count,
                        'team_name' => $team_name,
                        'phone_no' => $phone_no,
                        'player_index' => 6,
                        'player_name' => 'Player 6 (Cadangan)',
                        'employee_id' => explode(' - ', $player_6)[0],
                        'name' => explode(' - ', $player_6)[1],
                        'department' => $emp->department,
                        'section' => $emp->section,
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                    if (substr($emp->phone, 0, 1) == '+') {
                        $phone = substr($emp->phone, 1, 15);
                    } else if (substr($emp->phone, 0, 1) == '0') {
                        $phone = "62" . substr($emp->phone, 1, 15);
                    } else {
                        $phone = $emp->phone;
                    }

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
                        CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));
                    curl_exec($curl);
                }
            }

            if ($category == 'Penalti Putra' || $category == 'Penalti Putri') {
                $team_name = $request->get('team_name');
                $phone_no = $request->get('phone_no');
                $official = $request->get('official');
                $player_1 = $request->get('player_1');
                $player_2 = $request->get('player_2');
                $player_3 = $request->get('player_3');
                $player_4 = $request->get('player_4');
                $player_5 = $request->get('player_5');
                $player_6 = $request->get('player_6');
                $player_7 = $request->get('player_7');

                $emp = EmployeeSync::where('employee_id', explode(' - ', $official)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 0,
                    'player_name' => 'Official',
                    'employee_id' => explode(' - ', $official)[0],
                    'name' => explode(' - ', $official)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_1)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 1,
                    'player_name' => 'Player 1 (Inti)',
                    'employee_id' => explode(' - ', $player_1)[0],
                    'name' => explode(' - ', $player_1)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_2)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 2,
                    'player_name' => 'Player 2 (Inti)',
                    'employee_id' => explode(' - ', $player_2)[0],
                    'name' => explode(' - ', $player_2)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_3)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 3,
                    'player_name' => 'Player 3 (Inti)',
                    'employee_id' => explode(' - ', $player_3)[0],
                    'name' => explode(' - ', $player_3)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_4)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 4,
                    'player_name' => 'Player 4 (Inti)',
                    'employee_id' => explode(' - ', $player_4)[0],
                    'name' => explode(' - ', $player_4)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_5)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 5,
                    'player_name' => 'Player 5 (Kiper)',
                    'employee_id' => explode(' - ', $player_5)[0],
                    'name' => explode(' - ', $player_5)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                if ($player_6 != '') {
                    $emp = EmployeeSync::where('employee_id', explode(' - ', $player_6)[0])->first();

                    $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                        'category' => $category,
                        'no_urut' => $new_count,
                        'team_name' => $team_name,
                        'phone_no' => $phone_no,
                        'player_index' => 6,
                        'player_name' => 'Player 6 (Cadangan)',
                        'employee_id' => explode(' - ', $player_6)[0],
                        'name' => explode(' - ', $player_6)[1],
                        'department' => $emp->department,
                        'section' => $emp->section,
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    //Whatsapp
                    $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                    if (substr($emp->phone, 0, 1) == '+') {
                        $phone = substr($emp->phone, 1, 15);
                    } else if (substr($emp->phone, 0, 1) == '0') {
                        $phone = "62" . substr($emp->phone, 1, 15);
                    } else {
                        $phone = $emp->phone;
                    }

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
                        CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));
                    curl_exec($curl);
                }

                if ($player_7 != '') {
                    $emp = EmployeeSync::where('employee_id', explode(' - ', $player_7)[0])->first();

                    $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                        'category' => $category,
                        'no_urut' => $new_count,
                        'team_name' => $team_name,
                        'phone_no' => $phone_no,
                        'player_index' => 7,
                        'player_name' => 'Player 7 (Cadangan)',
                        'employee_id' => explode(' - ', $player_7)[0],
                        'name' => explode(' - ', $player_7)[1],
                        'department' => $emp->department,
                        'section' => $emp->section,
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    //Whatsapp
                    $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                    if (substr($emp->phone, 0, 1) == '+') {
                        $phone = substr($emp->phone, 1, 15);
                    } else if (substr($emp->phone, 0, 1) == '0') {
                        $phone = "62" . substr($emp->phone, 1, 15);
                    } else {
                        $phone = $emp->phone;
                    }

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
                        CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));
                    curl_exec($curl);
                }
            }

            if ($category == 'Indiaka') {
                $team_name = $request->get('team_name');
                $phone_no = $request->get('phone_no');
                $official = $request->get('official');
                $player_1 = $request->get('player_1');
                $player_2 = $request->get('player_2');
                $player_3 = $request->get('player_3');
                $player_4 = $request->get('player_4');
                $player_5 = $request->get('player_5');
                $player_6 = $request->get('player_6');
                $player_7 = $request->get('player_7');

                $emp = EmployeeSync::where('employee_id', explode(' - ', $official)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 0,
                    'player_name' => 'Official',
                    'employee_id' => explode(' - ', $official)[0],
                    'name' => explode(' - ', $official)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_1)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 1,
                    'player_name' => 'Player 1 (Putra)',
                    'employee_id' => explode(' - ', $player_1)[0],
                    'name' => explode(' - ', $player_1)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_2)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 2,
                    'player_name' => 'Player 2 (Putra)',
                    'employee_id' => explode(' - ', $player_2)[0],
                    'name' => explode(' - ', $player_2)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_3)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 3,
                    'player_name' => 'Player 3 (Putri)',
                    'employee_id' => explode(' - ', $player_3)[0],
                    'name' => explode(' - ', $player_3)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_4)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 4,
                    'player_name' => 'Player 4 (Putri)',
                    'employee_id' => explode(' - ', $player_4)[0],
                    'name' => explode(' - ', $player_4)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                if ($player_5 != '') {
                    $emp = EmployeeSync::where('employee_id', explode(' - ', $player_5)[0])->first();

                    $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                        'category' => $category,
                        'no_urut' => $new_count,
                        'team_name' => $team_name,
                        'phone_no' => $phone_no,
                        'player_index' => 5,
                        'player_name' => 'Player 5 (Cadangan)',
                        'employee_id' => explode(' - ', $player_5)[0],
                        'name' => explode(' - ', $player_5)[1],
                        'department' => $emp->department,
                        'section' => $emp->section,
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    //Whatsapp
                    $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                    if (substr($emp->phone, 0, 1) == '+') {
                        $phone = substr($emp->phone, 1, 15);
                    } else if (substr($emp->phone, 0, 1) == '0') {
                        $phone = "62" . substr($emp->phone, 1, 15);
                    } else {
                        $phone = $emp->phone;
                    }

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
                        CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));
                    curl_exec($curl);
                }

                if ($player_6 != '') {
                    $emp = EmployeeSync::where('employee_id', explode(' - ', $player_6)[0])->first();

                    $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                        'category' => $category,
                        'no_urut' => $new_count,
                        'team_name' => $team_name,
                        'phone_no' => $phone_no,
                        'player_index' => 6,
                        'player_name' => 'Player 6 (Cadangan)',
                        'employee_id' => explode(' - ', $player_6)[0],
                        'name' => explode(' - ', $player_6)[1],
                        'department' => $emp->department,
                        'section' => $emp->section,
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    //Whatsapp
                    $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                    if (substr($emp->phone, 0, 1) == '+') {
                        $phone = substr($emp->phone, 1, 15);
                    } else if (substr($emp->phone, 0, 1) == '0') {
                        $phone = "62" . substr($emp->phone, 1, 15);
                    } else {
                        $phone = $emp->phone;
                    }

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
                        CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));
                    curl_exec($curl);
                }

                if ($player_7 != '') {
                    $emp = EmployeeSync::where('employee_id', explode(' - ', $player_7)[0])->first();

                    $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                        'category' => $category,
                        'no_urut' => $new_count,
                        'team_name' => $team_name,
                        'phone_no' => $phone_no,
                        'player_index' => 7,
                        'player_name' => 'Player 7 (Cadangan)',
                        'employee_id' => explode(' - ', $player_7)[0],
                        'name' => explode(' - ', $player_7)[1],
                        'department' => $emp->department,
                        'section' => $emp->section,
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    //Whatsapp
                    $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                    if (substr($emp->phone, 0, 1) == '+') {
                        $phone = substr($emp->phone, 1, 15);
                    } else if (substr($emp->phone, 0, 1) == '0') {
                        $phone = "62" . substr($emp->phone, 1, 15);
                    } else {
                        $phone = $emp->phone;
                    }

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
                        CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));
                    curl_exec($curl);
                }
            }

            if ($category == 'Karaoke') {
                $phone_no = $request->get('phone_no');
                $singer = $request->get('singer');
                $song = $request->get('song');
                $player_1 = $request->get('player_1');

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_1)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $category . '-' . $new_count,
                    'phone_no' => $phone_no,
                    'player_index' => 1,
                    'player_name' => $category,
                    'employee_id' => explode(' - ', $player_1)[0],
                    'name' => explode(' - ', $player_1)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'song' => $song,
                    'singer' => $singer,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);
            }

            if ($category == 'Music Acoustic') {
                $team_name = $request->get('team_name');
                $phone_no = $request->get('phone_no');
                $singer = $request->get('singer');
                $song = $request->get('song');
                $player_1 = $request->get('player_1');
                $player_2 = $request->get('player_2');
                $player_3 = $request->get('player_3');
                $player_4 = $request->get('player_4');

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_1)[0])->first();

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name,
                    'phone_no' => $phone_no,
                    'player_index' => 1,
                    'player_name' => 'Vokalis',
                    'employee_id' => explode(' - ', $player_1)[0],
                    'name' => explode(' - ', $player_1)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'song' => $song,
                    'singer' => $singer,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);

                if ($player_2 != '') {
                    $emp = EmployeeSync::where('employee_id', explode(' - ', $player_2)[0])->first();

                    $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                        'category' => $category,
                        'no_urut' => $new_count,
                        'team_name' => $team_name,
                        'phone_no' => $phone_no,
                        'player_index' => 1,
                        'player_name' => 'Gitaris 1',
                        'employee_id' => explode(' - ', $player_2)[0],
                        'name' => explode(' - ', $player_2)[1],
                        'department' => $emp->department,
                        'section' => $emp->section,
                        'song' => $song,
                        'singer' => $singer,
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    //Whatsapp
                    $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                    if (substr($emp->phone, 0, 1) == '+') {
                        $phone = substr($emp->phone, 1, 15);
                    } else if (substr($emp->phone, 0, 1) == '0') {
                        $phone = "62" . substr($emp->phone, 1, 15);
                    } else {
                        $phone = $emp->phone;
                    }

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
                        CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));
                    curl_exec($curl);
                }

                if ($player_3 != '') {
                    $emp = EmployeeSync::where('employee_id', explode(' - ', $player_3)[0])->first();

                    $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                        'category' => $category,
                        'no_urut' => $new_count,
                        'team_name' => $team_name,
                        'phone_no' => $phone_no,
                        'player_index' => 1,
                        'player_name' => 'Gitaris 2',
                        'employee_id' => explode(' - ', $player_3)[0],
                        'name' => explode(' - ', $player_3)[1],
                        'department' => $emp->department,
                        'section' => $emp->section,
                        'song' => $song,
                        'singer' => $singer,
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    //Whatsapp
                    $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                    if (substr($emp->phone, 0, 1) == '+') {
                        $phone = substr($emp->phone, 1, 15);
                    } else if (substr($emp->phone, 0, 1) == '0') {
                        $phone = "62" . substr($emp->phone, 1, 15);
                    } else {
                        $phone = $emp->phone;
                    }

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
                        CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));
                    curl_exec($curl);
                }

                if ($player_4 != '') {
                    $emp = EmployeeSync::where('employee_id', explode(' - ', $player_4)[0])->first();

                    $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                        'category' => $category,
                        'no_urut' => $new_count,
                        'team_name' => $team_name,
                        'phone_no' => $phone_no,
                        'player_index' => 1,
                        'player_name' => 'Bassist',
                        'employee_id' => explode(' - ', $player_4)[0],
                        'name' => explode(' - ', $player_4)[1],
                        'department' => $emp->department,
                        'section' => $emp->section,
                        'song' => $song,
                        'singer' => $singer,
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    //Whatsapp
                    $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                    if (substr($emp->phone, 0, 1) == '+') {
                        $phone = substr($emp->phone, 1, 15);
                    } else if (substr($emp->phone, 0, 1) == '0') {
                        $phone = "62" . substr($emp->phone, 1, 15);
                    } else {
                        $phone = $emp->phone;
                    }

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
                        CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));
                    curl_exec($curl);
                }
            }

            if ($category == 'Senam Taiso') {
                $team_name = $request->get('team_name');
                $phone_no = $request->get('phone_no');
                $player_name = $request->get('player_name');
                $player_1 = $request->get('player_1');
                $location = $request->get('location');
                $attribute = $request->get('attribute');

                $emp = EmployeeSync::where('employee_id', explode(' - ', $player_1)[0])->first();

                $check_taiso = DB::connection('ympimis_2')->table('general_competition_registers')->where('player_name', $player_name)->first();

                if ($check_taiso) {
                    $response = array(
                        'status' => false,
                        'message' => 'Bagian Anda sudah terdaftar.',
                    );
                    return Response::json($response);
                }

                $insert = DB::connection('ympimis_2')->table('general_competition_registers')->insert([
                    'category' => $category,
                    'no_urut' => $new_count,
                    'team_name' => $team_name . '-' . $new_count,
                    'phone_no' => $phone_no,
                    'location' => $location,
                    'attribute' => $attribute,
                    'player_index' => 1,
                    'player_name' => $player_name,
                    'employee_id' => explode(' - ', $player_1)[0],
                    'name' => explode(' - ', $player_1)[1],
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                //Whatsapp
                $messages = "Anda telah didaftarkan YMPI Competition (" . $category . ") oleh " . Auth::user()->name . ' pada tanggal ' . date('d F Y');

                if (substr($emp->phone, 0, 1) == '+') {
                    $phone = substr($emp->phone, 1, 15);
                } else if (substr($emp->phone, 0, 1) == '0') {
                    $phone = "62" . substr($emp->phone, 1, 15);
                } else {
                    $phone = $emp->phone;
                }

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);
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

    public function indexCompeitionRegistrationReport()
    {
        $title = 'YMPI Competition Registration';
        $title_jp = 'YMPIコンペティション申し込み';

        return view('general.competition.report', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'YMPI Competition');
    }

    public function fetchCompeitionRegistrationReport(Request $request)
    {
        try {
            $where = '';
            if ($request->get('category') != '') {
                $where = "WHERE `category` = '" . $request->get('category') . "'";
            }
            $competition = DB::connection('ympimis_2')->SELECT("SELECT
      *
              FROM
              general_competition_registers
              " . $where . "");

            $emp = EmployeeSync::where('end_date', null)->get();

            $all = DB::connection('ympimis_2')->select("SELECT
              category,
              count(
                  DISTINCT ( no_urut )) AS qty
              FROM
              `general_competition_registers`
              GROUP BY
              category");

            $limit = DB::connection('ympimis_2')->select("SELECT
              category,
              `limit`
              FROM
              `general_competition_limits` ");

            $response = array(
                'status' => true,
                'competition' => $competition,
                'all' => $all,
                'limit' => $limit,
                'emp' => $emp,
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

    public function indexCompetitionAttendance()
    {
        $title = 'YMPI Competition Attendance';
        $title_jp = 'YMPIコンペティション';

        $category = DB::connection('ympimis_2')->select("SELECT DISTINCT
         ( category )
         FROM
         general_competition_registers");

        $team_name = DB::connection('ympimis_2')->select("SELECT DISTINCT
         ( category ),
         IF
         ( category = 'Karaoke', CONCAT( team_name, '_', employee_id, '_', `name` ), team_name ) AS team_name
         FROM
         general_competition_registers
         ORDER BY
         category,
         team_name");

        return view('general.competition.attendance', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'category' => $category,
            'team_name' => $team_name,
        ))->with('page', 'YMPI Competition');
    }

    public function updateCompetitionAttendance(Request $request)
    {
        try {
            $category = $request->get('category');
            $team_name = $request->get('team_name');
            $update = DB::connection('ympimis_2')->table('general_competition_registers')->where('category', $category)->where('team_name', $team_name)->update([
                'attendance' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $teams = DB::connection('ympimis_2')->table('general_competition_registers')->where('category', $category)->where('team_name', $team_name)->get();
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

    public function fetchCompetitionAttendance(Request $request)
    {
        try {
            $category = $request->get('category');
            $team_name = $request->get('team_name');
            $teams = DB::connection('ympimis_2')->table('general_competition_registers')->where('category', $category)->where('team_name', $team_name)->orderby('attendance', 'desc')->get();
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

    public function scanCompetitionAttendance(Request $request)
    {
        try {
            $category = $request->get('category');
            $team_name = $request->get('team_name');
            $tag = $request->get('tag');
            if (str_contains($tag, 'PI')) {
                $emp = DB::connection('ympimis_2')->table('general_competition_registers')->where('employee_id', $tag)->where('category', $category)->where('team_name', $team_name)->first();
                if ($emp) {
                    $update = DB::connection('ympimis_2')->table('general_competition_registers')->where('employee_id', $tag)->where('category', $category)->where('team_name', $team_name)->update([
                        'attendance' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $response = array(
                        'status' => true,
                    );
                    return Response::json($response);
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Employee Invalid',
                    );
                    return Response::json($response);
                }
            } else {
                $tags = Employee::where('tag', $tag)->first();
                if ($tags) {
                    $emp = DB::connection('ympimis_2')->table('general_competition_registers')->where('employee_id', $tags->employee_id)->where('category', $category)->where('team_name', $team_name)->first();
                    if ($emp) {
                        $update = DB::connection('ympimis_2')->table('general_competition_registers')->where('employee_id', $tags->employee_id)->where('category', $category)->where('team_name', $team_name)->update([
                            'attendance' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $response = array(
                            'status' => true,
                        );
                        return Response::json($response);
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'ID Card Invalid',
                        );
                        return Response::json($response);
                    }
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'ID Card Invalid',
                    );
                    return Response::json($response);
                }
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }
    public function indexXibo()
    {
        $title = 'YMPI Guest Display';
        $title_jp = '';
        return view('general.xibo.index')->with('title', $title)->with('title_jp', $title_jp)->with('page', 'XIBO');
    }

    public function fetchXibo(Request $request)
    {
        try {
            $xibo = DB::connection('ympimis_2')->select("SELECT
                `code`,
                category,
                title,
                `status`
                FROM
                `xibos`
                GROUP BY
                `code`,
                category");

            $xibo_all = DB::connection('ympimis_2')->table('xibos')->get();
            $response = array(
                'status' => true,
                'xibo' => $xibo,
                'xibo_all' => $xibo_all,
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

    public function updateXibo(Request $request)
    {
        try {
            $code = $request->get('code');
            $category = $request->get('category');
            $title = $request->get('title');
            $title_jp = $request->get('title_jp');
            $color_title = $request->get('color_title');
            $edit_count = $request->get('edit_count');
            $edit_count_content = $request->get('edit_count_content');
            $sub_title = $request->get('sub_title');
            $sub_title_jp = $request->get('sub_title_jp');
            $color_sub_title = $request->get('color_sub_title');
            $content = $request->get('content');
            $content_jp = $request->get('content_jp');
            $color_content = $request->get('color_content');
            $content_font_size = $request->get('content_font_size');
            $status = $request->get('status');

            $delete = DB::connection('ympimis_2')->table('xibos')->where('code', $code)->delete();

            for ($i = 0; $i < count($sub_title); $i++) {
                $insert = DB::connection('ympimis_2')->table('xibos')->insert([
                    'code' => $code,
                    'category' => $category,
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'color_title' => $color_title,
                    'sub_title' => $sub_title[$i],
                    'sub_title_jp' => $sub_title_jp[$i],
                    'color_sub_title' => $color_sub_title[$i],
                    'content' => $content[$i],
                    'content_jp' => $content_jp[$i],
                    'color_content' => $color_content[$i],
                    'content_font_size' => $content_font_size[$i],
                    'status' => $status,
                    'created_by' => Auth::user()->id,
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

    public function copyXibo(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', 'xibo')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $new_code = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $code = $request->get('code');

            $xibo = DB::connection('ympimis_2')->table('xibos')->where('code', $code)->first();

            $insert = DB::connection('ympimis_2')->table('xibos')->insert([
                'code' => $new_code,
                'category' => $xibo->category,
                'title' => $xibo->title,
                'title_jp' => $xibo->title_jp,
                'color_title' => $xibo->color_title,
                'sub_title' => $xibo->sub_title,
                'sub_title_jp' => $xibo->sub_title_jp,
                'color_sub_title' => $xibo->color_sub_title,
                'content' => $xibo->content,
                'content_jp' => $xibo->content_jp,
                'color_content' => $xibo->color_content,
                'content_font_size' => $xibo->content_font_size,
                'status' => $xibo->status,
                'created_by' => Auth::user()->id,
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

    public function inputXibo(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', 'xibo')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $code = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $category = $request->get('category');
            $title = $request->get('title');
            $title_jp = $request->get('title_jp');
            $color_title = $request->get('color_title');
            $edit_count = $request->get('edit_count');
            $edit_count_content = $request->get('edit_count_content');
            $sub_title = $request->get('sub_title');
            $sub_title_jp = $request->get('sub_title_jp');
            $color_sub_title = $request->get('color_sub_title');
            $content = $request->get('content');
            $content_jp = $request->get('content_jp');
            $color_content = $request->get('color_content');
            $content_font_size = $request->get('content_font_size');
            $status = $request->get('status');

            for ($i = 0; $i < count($sub_title); $i++) {
                $insert = DB::connection('ympimis_2')->table('xibos')->insert([
                    'code' => $code,
                    'category' => $category,
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'color_title' => $color_title,
                    'sub_title' => $sub_title[$i],
                    'sub_title_jp' => $sub_title_jp[$i],
                    'color_sub_title' => $color_sub_title[$i],
                    'content' => $content[$i],
                    'content_jp' => $content_jp[$i],
                    'color_content' => $color_content[$i],
                    'content_font_size' => $content_font_size[$i],
                    'status' => $status,
                    'created_by' => Auth::user()->id,
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

    public function deleteXibo(Request $request)
    {
        try {
            $delete = DB::connection('ympimis_2')->table('xibos')->where('code', $request->get('code'))->delete();
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

    public function indexXiboDisplay($code)
    {
        $apiKey = "GYhzWhGEkprqemup8Ps7VVts2jrt9kU8";
        $cityId = "208971";
        $googleApiUrl = "http://dataservice.accuweather.com/forecasts/v1/daily/1day/" . $cityId . "?apikey=" . $apiKey . "&language=en-us&details=true&metric=true";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_close($ch);
        $weather = json_decode($response);

        if ($code == 'active') {
            $xibo = DB::connection('ympimis_2')->table('xibos')->where('status', '1')->first();
        } else {
            $xibo = DB::connection('ympimis_2')->table('xibos')->where('code', $code)->first();
        }
        $title = 'YMPI Guest Display';
        $title_jp = '';
        if ($xibo->category == 'template-3') {
            return view('general.xibo.display_jp')->with('title', $title)->with('title_jp', $title_jp)->with('code', $code)->with('weather', $weather);
        } else {
            return view('general.xibo.display')->with('code', $code)->with('weather', $weather);
        }
    }

    public function fetchXiboDisplay(Request $request)
    {
        try {
            if ($request->get('code') == 'active') {
                $xibo = DB::connection('ympimis_2')->table('xibos')->where('status', '1')->get();
            } else {
                $xibo = DB::connection('ympimis_2')->table('xibos')->where('code', $request->get('code'))->get();
            }

            $response = array(
                'status' => true,
                'xibo' => $xibo,
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
