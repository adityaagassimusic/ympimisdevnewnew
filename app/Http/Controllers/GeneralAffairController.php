<?php

namespace App\Http\Controllers;

use App\AccBudget;
use App\AccExchangeRate;
use App\Approver;
use App\Bento;
use App\BentoMenu;
use App\BentoQuota;
use App\CanteenBudgetHistory;
use App\CanteenItem;
use App\CanteenItemCategory;
use App\CanteenLiveCooking;
use App\CanteenLiveCookingAdmin;
use App\CanteenLiveCookingMenu;
use App\CanteenPurchaseRequisition;
use App\CanteenPurchaseRequisitionItem;
use App\CodeGenerator;
use App\Department;
use App\Driver;
use App\DriverDetail;
use App\DriverList;
use App\DriverLog;
use App\Employee;
use App\EmployeeSync;
use App\GeneralAttendance;
use App\HrLeaveRequest;
use App\HrLeaveRequestApproval;
use App\HrLeaveRequestDetail;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\SunfishShiftSync;
use App\UniformLog;
use App\UniformStock;
use App\User;
use App\WeeklyCalendar;
use Carbon\Carbon;
use DataTables;
use Excel;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;
use App\ApprovalPresidentDirector;
use App\ApprovalPresidentDirectorApprove;

class GeneralAffairController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->uom = ['Can', 'Kg', 'Pack', 'Btr', 'Pcs', 'Klg', 'Btl', 'Ekor', 'Ball', 'Ikat', 'Tusuk', 'Box', 'Butir', 'Dus', 'Jrg', 'Ltr', 'Papan', 'lbr', 'Pouch', 'Roll', 'Galon', 'Lot', 'Loaf'];

        $this->dgm = 'PI0109004';
        $this->gm = 'PI1206001';
        $this->gm_acc = 'PI1712018';
        $this->mcu = [
            'PR0000001',
            'PR0000002',
            'PR0000003',
            'PR0000004',
            'PI1106002',
            'PI0109004',
            'PI1206001',
            'PI1301001',
            'PI1507003',
            'PI1712018',
            'PI2111044',
            'PI9709001',
            'PI0103002',
            'PI0906001',
            'PI1412008',
            'PI1910002',
            'PI1910003',
            'PI2002021',
            'PI2009022',
            'PI2101043',
            'PI2101044',
            'PI9707011',
        ];

        $this->gym = [
            'PI1802029',
            'PI1412008',
            'PI0904002',
        ];
    }

    public function indexLocker()
    {
        $title = "Locker Room Control";
        $title_jp = "ロッカー室の管理";

        $employees = db::table('employee_syncs')->get();

        return view('general_affairs.locker.locker_index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
        ))->with('head', 'GA Control')->with('page', 'Locker Room Control');
    }

    public function fetchLocker()
    {
        $lockers = db::connection('ympimis_2')->table('lockers')
            ->select(
                'id',
                'locker_id',
                'employee_id',
                'employee_name',
                'department_name',
                'section_name',
                db::raw('date_format(updated_at, "%Y-%m-%d") as last_update')
            )
            ->whereNull('deleted_at')
            ->get();

        $locker_logs = db::connection('ympimis_2')->table('locker_logs')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'DESC')
            ->get();

        $response = array(
            'status' => true,
            'lockers' => $lockers,
            'locker_logs' => $locker_logs,
        );
        return Response::json($response);
    }

    public function fetchLockerDetail(Request $request)
    {

    }

    public function editLocker(Request $request)
    {
        try {
            $employee = db::table('employee_syncs')->where('employee_id', '=', $request->get('employee_id'))
                ->first();

            if ($request->get('category') == 'vacant') {
                $locker = db::connection('ympimis_2')->table('lockers')
                    ->where('locker_id', '=', $request->get('locker_id'))
                    ->first();

                $locker_logs = db::connection('ympimis_2')->table('locker_logs')
                    ->insert([
                        'locker_id' => $locker->locker_id,
                        'employee_id' => $employee->employee_id,
                        'employee_name' => $employee->name,
                        'department_name' => $employee->department,
                        'section_name' => $employee->section,
                        'created_by' => Auth::id(),
                        'date_from' => date('Y-m-d', strtotime($locker->updated_at)),
                        'date_to' => date('Y-m-d'),
                    ]);

                $locker_update = db::connection('ympimis_2')->table('lockers')
                    ->where('locker_id', '=', $request->get('locker_id'))
                    ->update([
                        'employee_id' => "",
                        'employee_name' => "",
                        'department_name' => "",
                        'section_name' => "",
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }
            if ($request->get('category') == 'occupied') {
                $locker_update = db::connection('ympimis_2')->table('lockers')
                    ->where('locker_id', '=', $request->get('locker_id'))
                    ->update([
                        'employee_id' => $employee->employee_id,
                        'employee_name' => $employee->name,
                        'department_name' => $employee->department,
                        'section_name' => $employee->section,
                        'updated_at' => date('Y-m-d H:i:s'),
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
            'message' => 'Locker has been updated',
        );
        return Response::json($response);
    }

    public function indexBentoReport()
    {
        $title = "Japanese Food Order Report";
        $title_jp = "和食弁当の予約";

        $menus = BentoMenu::orderBy('due_date', 'desc')->where('due_date', '>=', date('Y-m-01'))->get();
        $quotas = BentoQuota::orderBy('due_date', 'desc')->where('due_date', '>=', date('Y-m-01'))->get();

        return view('general_affairs.bento_report', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('head', 'GA Control')->with('page', 'Japanese Food Order Report');
    }

    public function inputBentoMenu(Request $request)
    {
        if (strlen($request->get('menu')) <= 0 || strlen($request->get('menu')) <= 0) {
            $response = array(
                'status' => false,
                'message' => 'Please fill up menu name and quota.',
            );
            return Response::json($response);
        }
        try {
            $bento_menu = BentoQuota::where('due_date', '=', $request->get('date'))
                ->update([
                    'serving_quota' => $request->get('quota'),
                    'menu' => $request->get('menu'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Menu updated',
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

    public function indexBentoApprove()
    {
        $title = 'Bento Approval';
        $title_jp = '';

        return view('general_affairs.bento_approve', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('head', 'Bento Request');
    }

    public function approveBento(Request $request)
    {
        try {

            $ids = array();

            if ($request->get('rejected') != null) {
                foreach ($request->get('rejected') as $reject) {
                    array_push($ids, $reject);
                }

                $rejected = Bento::whereIn('id', $request->get('rejected'))
                    ->update([
                        'status' => 'Rejected',
                        'approver_id' => Auth::user()->username,
                        'approver_name' => Auth::user()->name,
                    ]);
            }

            if ($request->get('approved') != null) {
                foreach ($request->get('approved') as $approve) {
                    array_push($ids, $approve);
                }

                $approved = Bento::whereIn('id', $request->get('approved'))
                    ->update([
                        'status' => 'Approved',
                        'approver_id' => Auth::user()->username,
                        'approver_name' => Auth::user()->name,
                    ]);
            }

            $list_jp = Bento::whereIn('id', $ids)
                ->where('grade_code', '=', 'J0-')
                ->select(
                    db::raw('min(due_date) as min_date'),
                    db::raw('max(due_date) as max_date')
                )
                ->first();

            $lists = Bento::whereIn('bentos.id', $ids)
                ->where('bentos.grade_code', '!=', 'J0-')
                ->leftJoin('users', 'users.username', '=', 'bentos.order_by')
                ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'bentos.order_by')
                ->select(
                    'users.email',
                    'employee_syncs.phone',
                    db::raw('min(bentos.due_date) as min_date'),
                    db::raw('max(bentos.due_date) as max_date')
                )
                ->groupBy(
                    'users.email',
                    'employee_syncs.phone'
                )
                ->get();

            if (strlen($list_jp->max_date) > 0) {
                $first = date('Y-m-01', strtotime($list_jp->max_date));
                $last = date('Y-m-t', strtotime($list_jp->max_date));
                $bento_lists = db::select("SELECT
                 j.employee_id,
                 j.employee_name,
                 u.email,
                 b.due_date,
                 b.revise,
                 b.status
                 FROM
                 japaneses AS j
                 LEFT JOIN ( SELECT * FROM bentos WHERE due_date >= '" . $first . "' AND due_date <= '" . $last . "' ) AS b ON b.employee_id = j.employee_id
                  LEFT JOIN users AS u ON u.username = j.employee_id");

                $calendars = WeeklyCalendar::where('week_date', '>=', $first)
                    ->where('week_date', '<=', $last)
                    ->get();

                $mail_to = array();
                foreach ($bento_lists as $bento_list) {
                    if (!in_array($bento_list->email, $mail_to)) {
                        array_push($mail_to, $bento_list->email);
                    }
                }

                $bentos = [
                    'bento_lists' => $bento_lists,
                    'calendars' => $calendars,
                    'code' => 'japanese',
                ];

                // Mail::to(
                // $mail_to
                // )
                //     ->cc([
                //         'rianita.widiastuti@music.yamaha.com',
                //         'putri.sukma.riyanti@music.yamaha.com',
                //     ])
                //     ->bcc([
                //         'ympi-mis-ML@music.yamaha.com',
                //     ])
                //     ->send(new SendEmail($bentos, 'bento_approve'));

                $rejected_lists = [];

                if ($request->get('rejected') != null) {
                    $rejected_lists = Bento::whereIn('id', $request->get('rejected'))->get();
                }
                $rejected_emails = [];

                if (count($rejected_lists) > 0) {
                    foreach ($rejected_lists as $rejected_list) {
                        if (!in_array($rejected_list->email, $rejected_emails)) {
                            $rejecteds = Bento::whereIn('id', $request->get('rejected'))
                                ->where('charge_to', '=', $rejected_list->charge_to)
                                ->get();

                            Mail::to($rejected_list->email)
                                ->cc([
                                    'rianita.widiastuti@music.yamaha.com',
                                    'putri.sukma.riyanti@music.yamaha.com',
                                ])
                                ->bcc([
                                    'ympi-mis-ML@music.yamaha.com',
                                ])
                                ->send(new SendEmail($rejecteds, 'bento_reject'));
                        }
                        array_push($rejected_emails, $rejected_list->email);
                    }
                }
            }

            if (count($lists) > 0) {
                foreach ($lists as $list) {
                    $in_id = "";

                    for ($x = 0; $x < count($ids); $x++) {
                        $in_id = $in_id . "'" . $ids[$x] . "'";
                        if ($x != count($ids) - 1) {
                            $in_id = $in_id . ",";
                        }
                    }

                    $bento_lists = db::select("SELECT
                       b.employee_id,
                       b.employee_name,
                       b.due_date,
                       b.revise,
                       b.status,
                       b.email,
                       es.grade_code
                       FROM
                       bentos AS b
                       LEFT JOIN employee_syncs AS es ON b.employee_id = es.employee_id
                       LEFT JOIN users AS u ON b.order_by = u.username
                       WHERE
                       b.id in (" . $in_id . ")
                        AND u.email = '" . $list->email . "'");

                    $calendars = WeeklyCalendar::where('week_date', '>=', $list->min_date)
                        ->where('week_date', '<=', $list->max_date)
                        ->get();

                    $bentos = [
                        'bento_lists' => $bento_lists,
                        'calendars' => $calendars,
                        'code' => 'national',
                    ];

                    $due_date = [];

                    foreach ($bento_lists as $bento_list) {
                        if ($bento_list->status == 'Rejected') {
                            $quota = BentoQuota::where('due_date', '=', $bento_list->due_date)->first();
                            $quota->serving_ordered = $quota->serving_ordered - 1;
                            $quota->save();
                        } else {
                            $attendance = new GeneralAttendance([
                                'purpose_code' => 'Bento',
                                'due_date' => $bento_list->due_date,
                                'employee_id' => $bento_list->employee_id,
                                'created_by' => Auth::id(),
                            ]);

                            $attendance->save();
                            array_push($due_date, date('d M Y', strtotime($bento_list->due_date)));
                        }
                    }

                    $due_dates = array_unique($due_date);

                    if (strpos($list->email, '@music.yamaha.com') == false) {
                        $curl = curl_init();

                        if (substr($list->phone, 0, 1) == '+') {
                            $phone = substr($list->phone, 1, 15);
                        } else if (substr($list->phone, 0, 1) == '0') {
                            $phone = "62" . substr($list->phone, 1, 15);
                        } else {
                            $phone = $list->phone;
                        }

                        $messages = "Order bento Anda telah dikonfirmasi untuk tanggal " . join(", ", $due_dates) . " Silahkan cek kembali di MIRAI.";

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
                    } else {
                        Mail::to($list->email)->cc([
                            'rianita.widiastuti@music.yamaha.com',
                            'putri.sukma.riyanti@music.yamaha.com',
                        ])
                            ->bcc([
                                'ympi-mis-ML@music.yamaha.com',
                            ])
                            ->send(new SendEmail($bentos, 'bento_approve'));

                        $curl = curl_init();

                        if (substr($list->phone, 0, 1) == '+') {
                            $phone = substr($list->phone, 1, 15);
                        } else if (substr($list->phone, 0, 1) == '0') {
                            $phone = "62" . substr($list->phone, 1, 15);
                        } else {
                            $phone = $list->phone;
                        }

                        $messages = "Order bento Anda telah dikonfirmasi untuk tanggal " . join(", ", $due_dates) . " Silahkan cek kembali di MIRAI.";

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
            }

            $response = array(
                'status' => true,
                'message' => 'Order Has Been Confirmed',
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

    public function approveLiveCooking(Request $request)
    {
        try {
            $approved = $request->get('approved');
            $rejected = $request->get('rejected');
            if (count($rejected) > 0) {
                $reject_info = [];
                $reject_emp = [];
                for ($i = 0; $i < count($rejected); $i++) {
                    $live_cooking = CanteenLiveCooking::where('id', $rejected[$i])->first();
                    array_push($reject_info, [
                        'ordered_by' => $live_cooking->order_by,
                        'ordered_for' => $live_cooking->order_for,
                        'date' => $live_cooking->due_date,
                    ]);
                    array_push($reject_emp, $live_cooking->order_by);
                    $live_cooking->status = 'Rejected';

                    $live_cooking_menu = CanteenLiveCookingMenu::where('due_date', $live_cooking->due_date)->first();
                    $live_cooking_menu->serving_ordered = $live_cooking_menu->serving_ordered - 1;
                    $live_cooking_menu->serving_ordered_pay = $live_cooking_menu->serving_ordered_pay - 1;
                    $live_cooking_menu->save();
                    $live_cooking->save();
                }
                $reject_emp_un = array_values(array_unique($reject_emp));
                for ($i = 0; $i < count($reject_emp_un); $i++) {
                    $emp = EmployeeSync::where('employee_id', $reject_emp_un[$i])->first();
                    if (substr($emp->phone, 0, 1) == '+') {
                        $phone = substr($emp->phone, 1, 15);
                    } else if (substr($emp->phone, 0, 1) == '0') {
                        $phone = "62" . substr($emp->phone, 1, 15);
                    } else {
                        $phone = $emp->phone;
                    }
                    $due_date = [];
                    $reject_all = [];
                    for ($j = 0; $j < count($reject_info); $j++) {
                        if ($reject_info[$j]['ordered_by'] == $reject_emp_un[$i]) {
                            array_push($due_date, $reject_info[$j]['date']);
                            $emps = EmployeeSync::where('employee_id', $reject_info[$j]['ordered_for'])->first();
                            array_push($reject_all, [
                                'name' => $emps->name,
                                'date' => $reject_info[$j]['date'],
                            ]);
                        }
                    }
                    $due_date_un = array_values(array_unique($due_date));

                    $messages = "Order Live Cooking Anda Direject untuk tanggal " . join(", ", $due_date_un) . ". Silahkan cek kembali di MIRAI.";

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

                    $user = User::where(DB::RAW("UPPER(username)"), strtoupper($reject_emp_un[$i]))->first();
                    if (str_contains($user->email, '@music.yamaha.com')) {
                        $mail_to = $user->email;

                        $data = [
                            'datas' => $reject_all,
                            'calendar' => $due_date_un,
                        ];
                        Mail::to($mail_to)
                            ->cc([
                                'rianita.widiastuti@music.yamaha.com',
                                'putri.sukma.riyanti@music.yamaha.com',
                            ])
                            ->bcc([
                                'ympi-mis-ML@music.yamaha.com',
                            ])
                            ->send(new SendEmail($data, 'live_cooking_reject'));
                    }
                }
            } else {
                $approve_info = [];
                $approve_emp = [];
                for ($i = 0; $i < count($approved); $i++) {
                    $live_cooking = CanteenLiveCooking::where('id', $approved[$i])->first();
                    array_push($approve_info, [
                        'ordered_by' => $live_cooking->order_by,
                        'ordered_for' => $live_cooking->order_for,
                        'date' => $live_cooking->due_date,
                    ]);
                    array_push($approve_emp, $live_cooking->order_by);
                    $live_cooking->status = 'Confirmed';

                    $live_cooking->save();
                }
                $approve_emp_un = array_values(array_unique($approve_emp));
                for ($i = 0; $i < count($approve_emp_un); $i++) {
                    $mail_to = '';
                    $emp = EmployeeSync::where('employee_id', $approve_emp_un[$i])->first();
                    if (substr($emp->phone, 0, 1) == '+') {
                        $phone = substr($emp->phone, 1, 15);
                    } else if (substr($emp->phone, 0, 1) == '0') {
                        $phone = "62" . substr($emp->phone, 1, 15);
                    } else {
                        $phone = $emp->phone;
                    }
                    $due_date = [];
                    $approve_all = [];
                    for ($j = 0; $j < count($approve_info); $j++) {
                        if ($approve_info[$j]['ordered_by'] == $approve_emp_un[$i]) {
                            array_push($due_date, $approve_info[$j]['date']);
                            $emps = EmployeeSync::where('employee_id', $approve_info[$j]['ordered_for'])->first();
                            array_push($approve_all, [
                                'name' => $emps->name,
                                'date' => $approve_info[$j]['date'],
                            ]);
                        }
                    }
                    $due_date_un = array_values(array_unique($due_date));

                    $messages = "Order Live Cooking Anda telah dikonfirmasi untuk tanggal " . join(", ", $due_date_un) . ". Silahkan cek kembali di MIRAI.";

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

                    $user = User::where(DB::RAW("UPPER(username)"), strtoupper($approve_emp_un[$i]))->first();
                    if (str_contains($user->email, '@music.yamaha.com')) {
                        $mail_to = $user->email;

                        $data = [
                            'datas' => $approve_all,
                            'calendar' => $due_date_un,
                        ];
                        Mail::to($mail_to)
                            ->cc([
                                'rianita.widiastuti@music.yamaha.com',
                                'putri.sukma.riyanti@music.yamaha.com',
                            ])
                            ->bcc([
                                'ympi-mis-ML@music.yamaha.com',
                            ])
                            ->send(new SendEmail($data, 'live_cooking_approve'));
                    }
                }
            }
            $response = array(
                'status' => true,
                'message' => 'Confirmation Succeeded',
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

    public function indexBentoJapanese($id)
    {
        $title = "Japanese Food Order";
        $title_jp = "和食弁当の予約";

        $month = date('Y-m');
        if ($id != "") {
            $month = $id;
        }

        return view('general_affairs.bento_japanese', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'mon' => $month,
        ))->with('head', 'GA Control')->with('page', 'Japanese Food Order Japanese');
    }

    public function approveBentoJapanese(Request $request)
    {
        try {
            $month = date('Y-m', strtotime($request->get('month')));

            $mails = Bento::where('grade_code', '=', 'J0-')
                ->where('status', '=', 'Waiting')
                ->where(db::raw('date_format(due_date, "%Y-%m")'), '=', $month)
                ->select('email')
                ->distinct()
                ->get();

            $mail_to = array();
            foreach ($mails as $mail) {
                array_push($mail_to, $mail->email);
            }

            $bento = Bento::where('grade_code', '=', 'J0-')
                ->where('status', '=', 'Waiting')
                ->where(db::raw('date_format(due_date, "%Y-%m")'), '=', $month)
                ->update([
                    'status' => 'Approved',
                    'approver_id' => Auth::user()->username,
                    'approver_name' => Auth::user()->name,
                ]);

            if (!$bento) {
                $response = array(
                    'status' => false,
                    'message' => 'Tidak ada pesanan yang harus dikonfirmasi.',
                );
                return Response::json($response);
            }

            $calendars = BentoQuota::where(db::raw('date_format(due_date, "%Y-%m")'), '=', $month)
                ->select('due_date', db::raw('date_format(due_date, "%d") as header'), 'menu', 'remark')
                ->orderBy('due_date', 'ASC')
                ->get();

            $japaneses = db::select("SELECT
              *
              FROM
              japaneses
              WHERE
              deleted_at IS NULL
              ORDER BY
              id ASC");

            $bento_lists = db::select("SELECT
              j.employee_id,
              j.employee_name,
              b.due_date,
              b.revise,
              b.status,
              b.location
              FROM
              japaneses AS j
              LEFT JOIN ( SELECT * FROM bentos WHERE date_format(due_date, '%Y-%m') = '" . $month . "' ) AS b ON b.employee_id = j.employee_id
                 WHERE
                 j.deleted_at IS NULL
                 ORDER BY
                 j.id ASC");

            $datas = [
                'bento_lists' => $bento_lists,
                'calendars' => $calendars,
                'month' => $month,
                'japaneses' => $japaneses,
                'remark' => 'information',
            ];

            if (count($mail_to) > 0) {
                Mail::to(
                    $mail_to
                )
                    ->cc([
                        'rianita.widiastuti@music.yamaha.com',
                        'putri.sukma.riyanti@music.yamaha.com',
                        'ninik.islami@music.yamaha.com',
                        'novita.siswindarti@music.yamaha.com',
                        'ilmi.fauziah@music.yamaha.com',
                    ])
                    ->bcc([
                        'ympi-mis-ML@music.yamaha.com',
                    ])
                    ->send(new SendEmail($datas, 'bento_information'));
            }

            $response = array(
                'status' => true,
                'message' => 'Semua pesanan berhasil dikonfirmasi.',
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

    public function fetchBentoJapanese(Request $request)
    {
        try {
            $month = date('Y-m', strtotime($request->get('month')));
            $m = date('m', strtotime($request->get('month')));
            $y = date('Y', strtotime($request->get('month')));
            $japaneses = db::select("SELECT
             *
               FROM
               japaneses
               WHERE
               deleted_at IS NULL
               ORDER BY
               id ASC");

            $calendars = BentoQuota::where(db::raw('date_format(due_date, "%Y-%m")'), '=', $month)
                ->select('due_date', db::raw('date_format(due_date, "%d") as header'), 'menu', 'remark')
                ->orderBy('due_date', 'ASC')
                ->get();

            $bento_lists = db::select("SELECT
               j.employee_id,
               j.employee_name,
               b.due_date,
               b.revise,
               b.status,
               b.location
               FROM
               japaneses AS j
               LEFT JOIN ( SELECT * FROM bentos WHERE date_format(due_date, '%Y-%m') = '" . $month . "' ) AS b ON b.employee_id = j.employee_id
                WHERE j.deleted_at IS NULL
                ORDER BY j.id ASC");

            $response = array(
                'status' => true,
                'japaneses' => $japaneses,
                'calendars' => $calendars,
                'bento_lists' => $bento_lists,
                'm' => $m,
                'y' => $y,
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

    public function inputBentoJapanese(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', 'bento')->first();
            if ($code_generator->prefix != date('ym')) {
                $code_generator->prefix = date('ym');
                $code_generator->index = '0';
                $code_generator->save();
            }
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $order_id = $code_generator->prefix . $number;

            if ($request->get('order') == 'false') {
                $order = 'Cancelled';
            } else {
                $order = 'Waiting';
            }

            $bento = Bento::where('employee_id', '=', $request->get('employee_id'))
                ->where('due_date', '=', $request->get('due_date'))
                ->first();

            if ($bento == null) {
                $bento = new Bento([
                    'order_id' => $order_id,
                    'order_by' => $request->get('employee_id'),
                    'order_by_name' => $request->get('employee_name'),
                    'charge_to' => $request->get('employee_id'),
                    'charge_to_name' => $request->get('employee_name'),
                    'due_date' => $request->get('due_date'),
                    'employee_id' => $request->get('employee_id'),
                    'employee_name' => $request->get('employee_name'),
                    'grade_code' => 'J0-',
                    'email' => $request->get('email'),
                    'department' => '',
                    'section' => '',
                    'status' => $order,
                    'location' => $request->get('location'),
                    'created_by' => Auth::id(),
                ]);
            } else {
                $bento->status = $order;
                $bento->location = $request->get('location');
                $bento->revise = $bento->revise + 1;
            }

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();
            $bento->save();

            $response = array(
                'status' => true,
                'message' => 'Your order has been submitted<br>ご注文が送信されました',
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

    public function indexBento()
    {

        $title = "Japanese Food Order";
        $title_jp = "和食弁当の予約";

        if (str_contains(Auth::user()->role_code, 'GA') || str_contains(Auth::user()->role_code, 'MIS')) {
            $yemi = User::where('role_code', '=', 'YEMI')
                ->orderBy('name', 'asc')
                ->select(db::raw('username as employee_id'), 'name', db::raw('"J0-" as grade_code'));

            $employees = EmployeeSync::orderBy('name', 'asc')
                ->whereNull('end_date')
                ->select('employee_id', 'name', 'grade_code')
                ->union($yemi)
                ->get();
        } else {
            $employees = EmployeeSync::orderBy('name', 'asc')
                ->whereNull('end_date')
                ->where('grade_code', '!=', 'J0-')
                ->select('employee_id', 'name', 'grade_code')
                ->get();
        }

        $location = 'YMPI';

        $bentos = BentoMenu::orderBy('due_date', 'desc')
            ->select(db::raw('date_format(due_date, "%b %Y") as period'), 'menu_image')
            ->take(2)
            ->get();

        return view('general_affairs.bento', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'location' => $location,
            'bentos' => $bentos,
        ))->with('head', 'GA Control')->with('page', 'Japanese Food Order');
    }

    public function indexDriverLog()
    {
        $title = "Driver Log";
        $title_jp = "";

        $driver_lists = DriverList::orderBy('name', 'asc')->get();
        $employees = EmployeeSync::orderBy('name', 'asc')->get();

        return view('general_affairs.driver_log', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'driver_lists' => $driver_lists,
            'employees' => $employees,
        ))->with('head', 'GA Control')->with('page', 'Driver Control');
    }

    public function indexDriverMonitoring()
    {
        $title = "Driver Monitoring";
        $title_jp = "ドライバー管理システム";

        $car_detail = DB::SELECT("SELECT
           plat_no,
           car
           FROM
           driver_lists ");

        return view('general_affairs.driver_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'car_detail' => $car_detail,
        ))->with('head', 'GA Control')->with('page', 'Driver Monitoring');
    }

    public function indexDriver()
    {
        $title = "Request Driver";
        $title_jp = "ドライバー管理システム";

        $employees = EmployeeSync::orderBy('name', 'asc')->get();
        $driver_lists = DriverList::orderBy('name', 'asc')->whereNotNull('driver_id')->get();

        $car_detail = DB::SELECT("SELECT
           plat_no,
           car
           FROM
           driver_lists ");

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)->join('departments', 'employee_syncs.department', 'departments.department_name')->first();

        return view('general_affairs.driver', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'emp' => $emp,
            'driver_lists' => $driver_lists,
            'car_detail' => $car_detail,
            'car_detail2' => $car_detail,
        ))->with('head', 'GA Control')->with('page', 'Driver Control');
    }

    public function fetchDriverMonitoring(Request $request)
    {
        try {
            $driver_lists = DriverList::orderBy('name', 'asc')->whereNotNull('driver_id')->where('remark', 'YMPI')->get();
            $orders = [];
            $orders2 = [];
            $duty_time_alarm = [];
            
            for ($i = 0; $i < count($driver_lists); $i++) {
                $order = DB::SELECT("SELECT
                   *,
                 (
                  SELECT
                  GROUP_CONCAT( CONCAT( remark, '-', `name` ) )
                  FROM
                  driver_details
                  LEFT JOIN employee_syncs ON employee_syncs.employee_id = driver_details.remark
                  WHERE
                  driver_details.driver_id = drivers.id
                  AND category = 'passenger'
                  ) AS passenger,
                 (
                  SELECT
                  GROUP_CONCAT( remark )
                  FROM
                  driver_details
                  LEFT JOIN employee_syncs ON employee_syncs.employee_id = driver_details.remark
                  WHERE
                  driver_details.driver_id = drivers.id
                  AND category = 'destination'
                  ) AS destination,
                 employee_syncs.`name` AS request
                 FROM
                 `drivers`
                 LEFT JOIN employee_syncs ON employee_syncs.employee_id = created_by
                 WHERE
                 (DATE( date_from ) = DATE(
                  NOW())
                 AND driver_id = '" . $driver_lists[$i]->driver_id . "')
                 OR
                 ((
                 duty_status = 'on duty'
                 AND driver_id = '" . $driver_lists[$i]->driver_id . "'))
                 ORDER BY date_from");
                $list_antrian = [];
                $list_antrian2 = [];
                // if (count($order) > 0) {
                for ($j = 0; $j < 11; $j++) {
                    if (array_key_exists($j, $order) == true) {
                        if ($order[$j]->request != null) {
                            $requester = $order[$j]->request;
                        } else {
                            $requester = explode(' ', $order[$j]->request)[0] . ' ' . explode(' ', $order[$j]->request)[1];
                        }
                        if ($order[$j]->duty_status == 'on duty') {
                            array_push($list_antrian, 'By: ' . $requester . '<br>To: ' . $order[$j]->destination_city . '<br>' . date('H:i a', strtotime($order[$j]->date_from)) . ' - ' . date('H:i a', strtotime($order[$j]->date_to)) . '<br>' . $order[$j]->plat_no . ' - ' . $order[$j]->car . '</span>_sedang_' . $order[$j]->id . '_' . $order[$j]->passenger);
                            array_push($list_antrian2, 'By: ' . $requester . '<br>To: ' . $order[$j]->destination_city . '<br>(' . $order[$j]->destination . ')<br>' . date('H:i a', strtotime($order[$j]->date_from)) . ' - ' . date('H:i a', strtotime($order[$j]->date_to)) . '<br>' . $order[$j]->plat_no . ' - ' . $order[$j]->car . '_sedang_' . $order[$j]->id . '_' . $order[$j]->passenger);
                        } else if ($order[$j]->duty_status == null) {
                            array_push($list_antrian, 'By: ' . $requester . '<br>To: ' . $order[$j]->destination_city . '<br>' . date('H:i a', strtotime($order[$j]->date_from)) . ' - ' . date('H:i a', strtotime($order[$j]->date_to)) . '<br>' . $order[$j]->plat_no . ' - ' . $order[$j]->car . '_akan_' . $order[$j]->id . '_' . $order[$j]->passenger);
                            array_push($list_antrian2, 'By: ' . $requester . '<br>To: ' . $order[$j]->destination_city . '<br>(' . $order[$j]->destination . ')<br>' . date('H:i a', strtotime($order[$j]->date_from)) . ' - ' . date('H:i a', strtotime($order[$j]->date_to)) . '<br>' . $order[$j]->plat_no . ' - ' . $order[$j]->car . '_akan_' . $order[$j]->id . '_' . $order[$j]->passenger);
                            array_push($duty_time_alarm, [
                                'time' => date('Y-m-d H:i:s', strtotime($order[$j]->date_from)),
                            ]);
                        } else if ($order[$j]->duty_status == 'completed') {
                            array_push($list_antrian, 'By: ' . $requester . '<br>To: ' . $order[$j]->destination_city . '<br>' . date('H:i a', strtotime($order[$j]->date_from)) . ' - ' . date('H:i a', strtotime($order[$j]->date_to)) . '<br>' . $order[$j]->plat_no . ' - ' . $order[$j]->car . '_complete_' . $order[$j]->id . '_' . $order[$j]->passenger);
                            array_push($list_antrian2, 'By: ' . $requester . '<br>To: ' . $order[$j]->destination_city . '<br>(' . $order[$j]->destination . ')<br>' . date('H:i a', strtotime($order[$j]->date_from)) . ' - ' . date('H:i a', strtotime($order[$j]->date_to)) . '<br>' . $order[$j]->plat_no . ' - ' . $order[$j]->car . '_complete_' . $order[$j]->id . '_' . $order[$j]->passenger);
                        }
                    }
                    if (array_key_exists($j, $order) == false) {
                        array_push($list_antrian, '<br>_idle_0');
                        array_push($list_antrian2, '<br>_idle_0');
                    }
                }

                array_push($orders, [
                    'employee_id' => $driver_lists[$i]->driver_id,
                    'employee_name' => $driver_lists[$i]->name,
                    'position' => $driver_lists[$i]->position,
                    'queue_1' => $list_antrian[0],
                    'queue_2' => $list_antrian[1],
                    'queue_3' => $list_antrian[2],
                    'queue_4' => $list_antrian[3],
                    'queue_5' => $list_antrian[4],
                    'queue_6' => $list_antrian[5],
                    'queue_7' => $list_antrian[6],
                    'queue_8' => $list_antrian[7],
                    'queue_9' => $list_antrian[8],
                    'queue_10' => $list_antrian[9],
                    'queue_11' => $list_antrian[10],
                ]);

                array_push($orders2, [
                    'employee_id' => $driver_lists[$i]->driver_id,
                    'employee_name' => $driver_lists[$i]->name,
                    'position' => $driver_lists[$i]->position,
                    'queue_1' => $list_antrian2[0],
                    'queue_2' => $list_antrian2[1],
                    'queue_3' => $list_antrian2[2],
                    'queue_4' => $list_antrian2[3],
                    'queue_5' => $list_antrian2[4],
                    'queue_6' => $list_antrian2[5],
                    'queue_7' => $list_antrian2[6],
                    'queue_8' => $list_antrian2[7],
                    'queue_9' => $list_antrian2[8],
                    'queue_10' => $list_antrian2[9],
                    'queue_11' => $list_antrian2[10],
                ]);
                // }
            }

            $response = array(
                'status' => true,
                'driver_lists' => $driver_lists,
                'orders' => $orders,
                'orders2' => $orders2,
                'duty_time_alarm' => $duty_time_alarm,
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

    public function scanDriverMonitoring(Request $request)
    {
        try {
            $tag = $request->get('tag');
            $emp = DB::SELECT("SELECT
             drivers.id,
             employees.employee_id,
             employees.`name`,
             drivers.duty_status,
             drivers.duty_from,
             drivers.duty_to
             FROM
             employees
             JOIN drivers ON drivers.driver_id = employees.employee_id
             WHERE
             employees.tag = '" . $tag . "'
             AND drivers.id = '" . $request->get('id') . "'");
            if (count($emp) > 0) {
                if ($emp[0]->duty_status == null) {
                    $cek = Driver::where('driver_id', $emp[0]->employee_id)->where('duty_status', 'on duty')->get();
                    if (count($cek) > 0) {
                        $response = array(
                            'status' => false,
                            'message' => 'Tugas Anda sebelumnya selesai.',
                        );
                        return Response::json($response);
                    } else {
                        $driver = Driver::where('id', $emp[0]->id)->first();
                        $driver->duty_status = 'on duty';
                        $driver->duty_from = date('Y-m-d H:i:s');
                        $driver->save();
                        $message = 'Anda siap menjalankan tugas.';
                    }
                } else if ($emp[0]->duty_status == 'on duty') {
                    $driver = Driver::where('id', $emp[0]->id)->first();
                    $driver->duty_status = 'completed';
                    $driver->duty_to = date('Y-m-d H:i:s');
                    $driver->save();
                    $message = 'Tugas Anda Sudah Selesai.';
                }
                $response = array(
                    'status' => true,
                    'message' => $message,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Tugas Anda tidak tersedia.',
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

    public function fetchBentoOrderLog(Request $request)
    {
        $bentos = Bento::whereNull('deleted_at');

        if (strlen($request->get('dateFrom') > 0)) {
            $bentos = $bentos->where('due_date', '>=', $request->get('dateFrom'));
        }

        if (strlen($request->get('dateTo') > 0)) {
            $bentos = $bentos->where('due_date', '<=', $request->get('dateTo'));
        }

        if ($request->get('status') != null) {
            $bentos = $bentos->whereIn('status', $request->get('status'));

        }

        $bentos = $bentos->get();

        $response = array(
            'status' => true,
            'bentos' => $bentos,
        );
        return Response::json($response);
    }

    public function fetchBentoQuota(Request $request)
    {
        $due_date = date('Y-m-d', strtotime($request->get('due_date')));
        $bento_quota = BentoQuota::where('due_date', '=', $due_date)->first();

        $response = array(
            'status' => true,
            'bento_quota' => $bento_quota,
        );
        return Response::json($response);
    }

    public function fetchBentoOrderCount()
    {
        if (str_contains(Auth::user()->role_code, 'GA') || str_contains(Auth::user()->role_code, 'MIS')) {
            $count = Bento::where('status', '=', 'Waiting')->count('id');

            $response = array(
                'status' => true,
                'count' => $count,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }
    }

    public function fetchBentoOrderEdit(Request $request)
    {
        // if($request->get('color') == 'black'){
        $bento = Bento::where('id', '=', $request->get('color'))
        // ->where('employee_name', '=', $request->get('employee_name'))
        // ->where('status', '=', 'Cancelled')
            ->first();
        // }
        // else if($request->get('color') == '#ff6090'){
        //     $bento = Bento::where('due_date', '=', $request->get('due_date'))
        //     ->where('employee_name', '=', $request->get('employee_name'))
        //     ->where('status', '=', 'Rejected')
        //     ->first();
        // }
        // else if($request->get('color') == '#ff6090'){
        //     $bento = Bento::where('due_date', '=', $request->get('due_date'))
        //     ->where('employee_name', '=', $request->get('employee_name'))
        //     ->where('status', '=', 'Approved')
        //     ->first();
        // }
        // else if($request->get('color') == 'yellow'){
        //     $bento = Bento::where('due_date', '=', $request->get('due_date'))
        //     ->where('employee_name', '=', $request->get('employee_name'))
        //     ->where('status', '=', 'Waiting')
        //     ->first();
        // }
        // else{
        //     $bento = Bento::where('due_date', '=', $request->get('due_date'))
        //     ->where('employee_name', '=', $request->get('employee_name'))
        //     ->where('status', '<>', 'Cancelled')
        //     ->first();
        // }

        $response = array(
            'status' => true,
            'bento' => $bento,
        );
        return Response::json($response);
    }

    public function fetchBentoOrderList(Request $request)
    {
        if ($request->get('date')) {

            $now = date('Y-m-d', strtotime($request->get('date')));

            $bentos = Bento::where('due_date', '=', $now)->get();

            $response = array(
                'status' => true,
                'bentos' => $bentos,
            );
            return Response::json($response);
        } else if ($request->get('resume')) {
            if (str_contains(Auth::user()->role_code, 'GA') || str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'HR')) {
                $period = date('Y-m', strtotime($request->get('resume')));

                $calendars = WeeklyCalendar::where(db::raw('date_format(week_date, "%Y-%m")'), '=', $period)
                    ->select('week_date', db::raw('date_format(week_date, "%d") as header'), 'remark')
                    ->get();

                // $bentos = Bento::where(db::raw('date_format(due_date, "%Y-%m")'), '=', $period)
                // ->select('order_by', 'order_by_name', 'employee_id', 'employee_name', 'due_date', db::raw('date_format(due_date, "%d") as header'))
                // ->get();

                $bentos = db::select("SELECT
                  charge_to AS employee_id,
                  charge_to_name AS employee_name,
                  due_date,
                  count( id ) AS qty
                  FROM
                  `bentos`
                  WHERE
                  date_format( due_date, '%Y-%m' ) = '" . $period . "'
                  AND status = 'Approved'
                  AND deleted_at IS NULL
                  GROUP BY
                  charge_to,
                  charge_to_name,
                  due_date");

                $response = array(
                    'status' => true,
                    'bentos' => $bentos,
                    'calendars' => $calendars,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'You do not have permission to access this data',
                );
                return Response::json($response);
            }
        } else {
            $now = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
            $last = date('Y-m-d', strtotime(carbon::now()->addDays(20)));

            if (str_contains(Auth::user()->role_code, 'GA') || str_contains(Auth::user()->role_code, 'MIS')) {
                $unconfirmed = Bento::get();
            } else {
                $unconfirmed = db::select("SELECT
                  *
                  FROM
                  bentos
                  WHERE
                  created_by = '" . Auth::id() . "'
                  OR employee_id = '" . Auth::user()->username . "'
                  OR order_by = '" . Auth::user()->username . "'");
            }

            $menus = BentoQuota::whereNull('deleted_at')->get();

            $response = array(
                'status' => true,
                'unconfirmed' => $unconfirmed,
                'menus' => $menus,
            );
            return Response::json($response);
        }
    }

    public function editBentoOrder(Request $request)
    {
        try {

            if ($request->get('status') == 'edit') {
                $bento = Bento::where('id', '=', $request->get("id"))->first();
                $employee_id = explode('_', $request->get('employee_id'));
                $bento_quota_old = BentoQuota::where('due_date', '=', $bento->due_date)->first();
                $bento_quota_new = BentoQuota::where('due_date', '=', $request->get('due_date'))->first();
                $now = date('Y-m-d H:i:s');
                $limit = date('Y-m-d', strtotime($bento->due_date));

                $date_old = date('Y-m-d', strtotime($bento->due_date));
                if (
                    $bento->order_by == $request->get('order_by') &&
                    $bento->order_by_name == $request->get('order_by_name') &&
                    $bento->charge_to == $request->get('charge_to') &&
                    $bento->charge_to_name == $request->get('charge_to_name') &&
                    $bento->due_date == $request->get('due_date') &&
                    $bento->employee_id == $employee_id[0]
                ) {
                    $response = array(
                        'status' => false,
                        'message' => 'There is no change in your order<br>ご注文に変更なし',
                    );
                    return Response::json($response);
                }

                if (strlen($bento_quota_new->menu) <= 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'There is order(s) without menu or in holiday.',
                    );
                    return Response::json($response);
                }

                if ($now > $limit) {
                    $response = array(
                        'status' => false,
                        'message' => 'Can not edit order, time limit reached. Max change request one day before.',
                    );
                    return Response::json($response);
                }

                // $diff = date_diff(date_create(date('Y-m-d')), date_create(date('Y-m-d', strtotime($bento->due_date))));
                // if($diff->format("%R%a") <= 0){
                //     $response = array(
                //         'status' => false,
                //         'message' => 'Your order exceeded time limit. Max order change day before'
                //     );
                //     return Response::json($response);
                // }

                if ($employee_id[2] != 'J0-') {
                    if ($bento_quota_new->serving_quota - $bento_quota_new->serving_ordered <= 0) {
                        $response = array(
                            'status' => false,
                            'message' => 'Maximum quota reached, please check your order.',
                        );
                        return Response::json($response);
                    }
                    $bento_quota_old->serving_ordered = $bento_quota_old->serving_ordered - 1;
                    $bento_quota_old->save();

                    if ($bento->status == 'Approved') {
                        $attendance = GeneralAttendance::where('due_date', '=', $date_old)
                            ->where('employee_id', '=', $bento->employee_id)
                            ->first();
                        $attendance->forceDelete();
                    }
                }

                $bento->order_by = $request->get('order_by');
                $bento->order_by_name = $request->get('order_by_name');
                $bento->charge_to = $request->get('charge_to');
                $bento->charge_to_name = $request->get('charge_to_name');
                $bento->due_date = $request->get('due_date');
                $bento->revise = $bento->revise + 1;

                $bento->employee_id = $employee_id[0];

                $employee = EmployeeSync::where('employee_id', '=', $employee_id[0])->first();

                if ($employee != "") {
                    $bento->employee_name = $employee->name;
                    $bento->department = $employee->department;
                    $bento->section = $employee->section;
                } else {
                    $bento->employee_name = $employee_id[1];
                    $bento->department = 'YEMI';
                    $bento->section = 'YEMI';
                }

                if ($employee_id[2] != 'J0-') {
                    $bento_quota = BentoQuota::where('due_date', '=', $request->get('due_date'))->first();
                    $bento_quota->serving_ordered = $bento_quota->serving_ordered + 1;
                    $bento_quota->save();

                    if ($bento->status == 'Approved') {
                        if ($date_old == $request->get('due_date')) {
                            $attendance = new GeneralAttendance([
                                'purpose_code' => 'Bento',
                                'due_date' => $request->get('due_date'),
                                'employee_id' => $employee_id[0],
                                'created_by' => Auth::id(),
                            ]);

                            $attendance->save();
                        }
                    }
                }

                if ($date_old != $request->get('due_date')) {
                    $bento->status = 'Waiting';
                    $bento->approver_id = null;
                    $bento->approver_name = null;
                }

                $bento->save();

                $response = array(
                    'status' => true,
                    'message' => 'Your order has been edited, please wait for approval',
                );
                return Response::json($response);
            }

            if ($request->get('status') == 'cancel') {

                $bento = Bento::where('id', '=', $request->get('id'))->first();

                $now = date('Y-m-d H:i:s');
                $limit = date('Y-m-d 09:00:00', strtotime($bento->due_date));

                if ($now > $limit) {
                    $response = array(
                        'status' => false,
                        'message' => 'Can not cancel order, time limit reached. Max change on day 09:00',
                    );
                    return Response::json($response);
                }

                $bento_quota = BentoQuota::where('due_date', '=', $bento->due_date)->first();
                $bento_quota->serving_ordered = $bento_quota->serving_ordered - 1;
                $bento_quota->save();

                if ($bento->status == 'Approved') {
                    $attendance = GeneralAttendance::where('due_date', '=', $bento->due_date)
                        ->where('employee_id', '=', $bento->employee_id)
                        ->first();
                    $attendance->forceDelete();
                }

                $bento->status = 'Cancelled';
                $bento->revise = $bento->revise + 1;

                $bento->save();

                $user = User::where('username', '=', $bento->order_by)->first();
                $bento_lists = Bento::where('id', '=', $request->get('id'))->get();

                $response = array(
                    'status' => true,
                    'message' => 'Your order has been cancelled',
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

    public function inputBentoOrder(Request $request)
    {
        try {
            $order_lists = $request->get('order_list');
            $order_by = User::where('username', '=', $request->get('order_by'))->first();
            $charge_to = User::where('username', '=', $request->get('charge_to'))->first();
            $bento_lists = array();

            $code_generator = CodeGenerator::where('note', '=', 'bento')->first();
            if ($code_generator->prefix != date('ym')) {
                $code_generator->prefix = date('ym');
                $code_generator->index = '0';
                $code_generator->save();
            }
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $order_id = $code_generator->prefix . $number;

            $check_quota = array();
            $check_menu = array();

            foreach ($order_lists as $order_list) {
                $order = explode("_", $order_list);
                if ($order[2] != 'J0-') {
                    array_push($check_quota, $order[1]);
                }
                array_push($check_menu, $order[1]);
            }

            $check = array_count_values($check_quota);
            $check2 = array_count_values($check_menu);

            if (count($check) > 0) {
                foreach ($check as $key => $val) {
                    $bento_quota = BentoQuota::where('due_date', '=', $key)->first();
                    if ($bento_quota->serving_quota - $bento_quota->serving_ordered < $val) {
                        $response = array(
                            'status' => false,
                            'message' => 'Maximum quota reached, please check your order.',
                        );
                        return Response::json($response);
                    }
                }
            }

            foreach ($check2 as $key => $val) {
                $bento_quota = BentoQuota::where('due_date', '=', $key)->first();
                $diff = date_diff(date_create(date('Y-m-d')), date_create(date('Y-m-d', strtotime($key))));
                if ($diff->format("%R%a") <= 0) {
                    if (!str_contains(Auth::user()->role_code, 'MIS')) {
                        $response = array(
                            'status' => false,
                            'message' => 'Your order exceeded time limit. Max order one day before',
                        );
                        return Response::json($response);
                    }
                }
                if (strlen($bento_quota->menu) <= 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'There is order(s) with no menu yet or in holiday.',
                    );
                    return Response::json($response);
                }
            }

            foreach ($order_lists as $order_list) {
                $order = explode("_", $order_list);
                $bento_quota = BentoQuota::where('due_date', '=', $order[1])->first();

                if ($order_by->role_code == 'YEMI') {
                    $employee = User::where('username', '=', $order[0])->first();
                    $bento = new Bento([
                        'order_id' => $order_id,
                        'order_by' => $order_by->username,
                        'order_by_name' => $order_by->name,
                        'charge_to' => $order[0],
                        'charge_to_name' => $employee->name,
                        'due_date' => $order[1],
                        'employee_id' => $order[0],
                        'employee_name' => $employee->name,
                        'grade_code' => $order[2],
                        'email' => $employee->email,
                        'department' => 'YEMI',
                        'section' => 'YEMI',
                        'status' => 'Waiting',
                        'created_by' => Auth::id(),
                    ]);
                } else {
                    $employee = EmployeeSync::where('employee_id', '=', $order[0])
                        ->leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                        ->select('employee_syncs.name', 'employee_syncs.department', 'employee_syncs.section', 'users.email', 'employee_syncs.grade_code')
                        ->first();

                    if ($employee->grade_code == 'J0-') {
                        $bento = new Bento([
                            'order_id' => $order_id,
                            'order_by' => $order_by->username,
                            'order_by_name' => $order_by->name,
                            'charge_to' => $order[0],
                            'charge_to_name' => $employee->name,
                            'due_date' => $order[1],
                            'employee_id' => $order[0],
                            'grade_code' => $order[2],
                            'employee_name' => $employee->name,
                            'email' => $employee->email,
                            'department' => $employee->department,
                            'section' => $employee->section,
                            'status' => 'Waiting',
                            'location' => 'YMPI',
                            'created_by' => Auth::id(),
                        ]);
                    } else {
                        $bento = new Bento([
                            'order_id' => $order_id,
                            'order_by' => $order_by->username,
                            'order_by_name' => $order_by->name,
                            'charge_to' => $charge_to->username,
                            'charge_to_name' => $charge_to->name,
                            'due_date' => $order[1],
                            'employee_id' => $order[0],
                            'employee_name' => $employee->name,
                            'grade_code' => $order[2],
                            'email' => $employee->email,
                            'department' => $employee->department,
                            'section' => $employee->section,
                            'status' => 'Waiting',
                            'location' => 'YMPI',
                            'created_by' => Auth::id(),
                        ]);
                        $bento_quota->serving_ordered = $bento_quota->serving_ordered + 1;
                        $bento_quota->save();
                    }
                }

                $bento->save();

                array_push($bento_lists,
                    [
                        'id' => $bento->id,
                        'order_id' => $bento->order_id,
                        'order_by' => $bento->order_by,
                        'order_by_name' => $bento->order_by_name,
                        'charge_to' => $bento->charge_to,
                        'charge_to_name' => $bento->charge_to_name,
                        'due_date' => $bento->due_date,
                        'employee_id' => $bento->employee_id,
                        'employee_name' => $bento->employee_name,
                        'grade_code' => $bento->grade_code,
                        'email' => $bento->email,
                        'department' => $bento->department,
                        'section' => $bento->section,
                        'status' => $bento->status,
                        'created_by' => $bento->created_by,
                    ]);
            }

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $response = array(
                'status' => true,
                'message' => 'Your order has been created, please wait for approval<br>予約完了です。ご確認をお待ちください。',
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

    public function fetchDriverLog(Request $request)
    {
        $driver_logs = DriverLog::orderBy('driver_logs.driver_id');
        $drivers = Driver::orderBy('drivers.driver_id');

        if ($request->get('driver_id') != null) {
            $driver_logs = $driver_logs->whereIn('driver_logs.driver_id', $request->get('driver_id'));
            $drivers = $drivers->whereIn('drivers.driver_id', $request->get('driver_id'));
        }
        if ($request->get('status') != null) {
            $driver_logs = $driver_logs->whereIn('driver_logs.status', $request->get('status'));
            $drivers = $drivers->whereIn('drivers.remark', $request->get('status'));
        }
        if (strlen($request->get('datefrom')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
            $driver_logs = $driver_logs->where(db::raw('date_format(driver_logs.date_from, "%Y-%m-%d")'), '>=', $date_from);
            $drivers = $drivers->where(db::raw('date_format(drivers.date_from, "%Y-%m-%d")'), '>=', $date_from);
        }
        if (strlen($request->get('dateto')) > 0) {
            $date_to = date('Y-m-d', strtotime($request->get('dateto')));
            $driver_logs = $driver_logs->where(db::raw('date_format(driver_logs.date_to, "%Y-%m-%d")'), '<=', $date_to);
            $drivers = $drivers->where(db::raw('date_format(drivers.date_to, "%Y-%m-%d")'), '<=', $date_to);
        }

        $driver_logs = $driver_logs->leftJoin('employee_syncs as request', 'request.employee_id', '=', 'driver_logs.created_by')
            ->leftJoin('employee_syncs as approve', 'approve.employee_id', '=', 'driver_logs.approved_by')
            ->leftJoin('employee_syncs as receive', 'receive.employee_id', '=', 'driver_logs.received_by')
            ->select('driver_logs.request_id as id', 'driver_logs.driver_id', 'driver_logs.duty_from', 'driver_logs.duty_to', 'driver_logs.name', 'driver_logs.purpose', 'driver_logs.destination_city', 'driver_logs.date_from', 'driver_logs.date_to', 'request.employee_id as request_employee_id', 'request.name as request_name', 'approve.employee_id as approve_employee_id', 'approve.name as approve_name', 'receive.employee_id as receive_employee_id', 'receive.name as receive_name', 'driver_logs.status')
            ->distinct()
            ->get();

        $drivers = $drivers->leftJoin('employee_syncs as request', 'request.employee_id', '=', 'drivers.created_by')
            ->leftJoin('employee_syncs as approve', 'approve.employee_id', '=', 'drivers.approved_by')
            ->leftJoin('employee_syncs as receive', 'receive.employee_id', '=', 'drivers.received_by')
            ->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'drivers.duty_from', 'drivers.duty_to', 'request.employee_id as request_employee_id', 'request.name as request_name', 'approve.employee_id as approve_employee_id', 'approve.name as approve_name', 'receive.employee_id as receive_employee_id', 'receive.name as receive_name', 'drivers.remark as status')
            ->get();

        $logs = array();

        if (count($driver_logs) > 0) {
            foreach ($driver_logs as $driver_log) {
                array_push($logs,
                    [
                        "id" => $driver_log->id,
                        "driver_id" => $driver_log->driver_id,
                        "name" => $driver_log->name,
                        "purpose" => $driver_log->purpose,
                        "destination_city" => $driver_log->destination_city,
                        "date_from" => $driver_log->date_from,
                        "date_to" => $driver_log->date_to,
                        "duty_from" => $driver_log->duty_from,
                        "duty_to" => $driver_log->duty_to,
                        "request_employee_id" => $driver_log->request_employee_id,
                        "request_name" => $driver_log->request_name,
                        "approve_employee_id" => $driver_log->approve_employee_id,
                        "approve_name" => $driver_log->approve_name,
                        "receive_employee_id" => $driver_log->receive_employee_id,
                        "receive_name" => $driver_log->receive_name,
                        "status" => $driver_log->status,
                    ]);
            }
        }

        if (count($drivers) > 0) {
            foreach ($drivers as $driver) {
                array_push($logs,
                    [
                        "id" => $driver->id,
                        "driver_id" => $driver->driver_id,
                        "name" => $driver->name,
                        "purpose" => $driver->purpose,
                        "destination_city" => $driver->destination_city,
                        "date_from" => $driver->date_from,
                        "date_to" => $driver->date_to,
                        "duty_from" => $driver->duty_from,
                        "duty_to" => $driver->duty_to,
                        "request_employee_id" => $driver->request_employee_id,
                        "request_name" => $driver->request_name,
                        "approve_employee_id" => $driver->approve_employee_id,
                        "approve_name" => $driver->approve_name,
                        "receive_employee_id" => $driver->receive_employee_id,
                        "receive_name" => $driver->receive_name,
                        "status" => $driver->status,
                    ]);
            }
        }

        $response = array(
            'status' => true,
            'logs' => $logs,
        );
        return Response::json($response);
    }

    public function fetchDriverRequest(Request $request)
    {
        $requests = Driver::leftJoin('employee_syncs as request', 'request.employee_id', '=', 'drivers.created_by')
            ->select('drivers.id', 'drivers.purpose', 'drivers.destination_city', 'request.name as requested_by', 'drivers.date_from', 'drivers.date_to')
            ->whereIn('drivers.remark', ['requested', 'accepted'])
            ->orderBy('drivers.created_at', 'asc')
            ->get();

        $response = array(
            'status' => true,
            'requests' => $requests,
        );
        return Response::json($response);
    }

    public function importDriverDuty(Request $request)
    {
        if ($request->hasFile('duty')) {
            $username = Auth::user()->username;
            $file = $request->file('duty');
            $data = file_get_contents($file);

            $rows = explode("\r\n", $data);
            foreach ($rows as $row) {
                if (strlen($row) > 0) {
                    $row = explode("\t", $row);
                    if ($row[0] != 'NIK') {
                        $driver_list = DriverList::where('driver_id', '=', $row[0])->first();
                        $driver = new Driver([
                            'driver_id' => $row[0],
                            'name' => $driver_list->name,
                            'purpose' => $row[1],
                            'destination_city' => $row[2],
                            'date_from' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $row[3]))),
                            'date_to' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $row[4]))),
                            'created_by' => $username,
                            'approved_by' => $username,
                            'received_by' => $username,
                            'remark' => 'received',
                        ]);
                        $driver->save();
                    }
                }
            }
            return redirect('/index/ga_control/driver')->with('status', 'Tugas driver berhasil di import.')->with('page', 'Material');
        } else {
            return redirect('/index/ga_control/driver')->with('error', 'Harus ada file.')->with('page', 'Material');
        }
    }

    public function acceptDriverRequest(Request $request)
    {
        try {
            if ($request->get('cat') == 'accept') {
                $driver = Driver::find($request->get('id'));
                $driver_list = DriverList::where('driver_id', '=', $request->get('driver_id'))->first();

                $driver->driver_id = $request->get('driver_id');
                $driver->name = $driver_list->name;
                $driver->purpose = $request->get('purpose');
                $driver->destination_city = $request->get('destination_city');
                $driver->date_from = $request->get('start_time');
                $driver->date_to = $request->get('end_time');
                $driver->plat_no = $request->get('plat_no');
                $driver->car = $request->get('car');
                $driver->received_by = Auth::user()->username;
                $driver->remark = 'received';
                $driver->save();

                $emp = Employee::where('employee_id', $request->get('driver_id'))->first();

                $requester = EmployeeSync::where('employee_id', Auth::user()->username)->first();

                if (substr($driver_list->phone_no, 0, 1) == '+') {
                    $phone = substr($driver_list->phone_no, 1, 15);
                } else if (substr($driver_list->phone_no, 0, 1) == '0') {
                    $phone = "62" . substr($driver_list->phone_no, 1, 15);
                } else {
                    $phone = $driver_list->phone_no;
                }

                $driver = Driver::find($request->get('id'));

                $destination = DriverDetail::where('driver_id', $request->get('id'))->where('category', 'destination')->get();
                $dests = [];
                for ($i = 0; $i < count($destination); $i++) {
                    array_push($dests, $destination[$i]->remark);
                }

                $day = str_replace(' ', '%20', date('l, d F Y', strtotime($driver->date_from)));

                $start_time = date('H:i a', strtotime($driver->date_from));
                $start_time_replace = str_replace(" ", "%20", $start_time);

                $end_time = date('H:i a', strtotime($driver->date_to));
                $end_time_replace = str_replace(" ", "%20", $end_time);

                $destinations = str_replace(" ", "%20", $driver->destination_city . '%20(' . join(",%20", $dests) . ')');
                $destinations = str_replace("&", "%26", $destinations);
                $name = str_replace(" ", "%20", $driver_list->name);

                $request_name = str_replace(" ", "%20", $requester->name);

                $message = 'Driver%20Order%0A%0ARequest%20by%20:%20' . $request_name . '%0ADay%20and%20Date%20:%20' . $day . '%0ADestination%20City%20:%20' . $destinations . '%0ADeparture%20:%20' . $start_time_replace . '%0AArrive%20:%20' . $end_time_replace . '%0APilot%20:%20' . $name . '%0A%0A-YMPI%20MIS%20Dept.-';

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
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . $message . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));

                curl_exec($curl);

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
                    CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . $message . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));

                curl_exec($curl);

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
                    CURLOPT_POSTFIELDS => 'receiver=628113669869&device=6281130561777&message=' . $message . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));

                curl_exec($curl);

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
                    CURLOPT_POSTFIELDS => 'receiver=628113669871&device=6281130561777&message=' . $message . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));

                curl_exec($curl);

                $driver = Driver::select('drivers.*', 'driver_lists.phone_no')->where('drivers.id', $request->get('id'))->join('driver_lists', 'driver_lists.driver_id', 'drivers.driver_id')->first();

                $users = User::where('username', $driver->created_by)->first();
                $data = [
                    'driver' => $driver,
                ];
                if (strpos($users->email, '@music.yamaha.com') !== false) {
                    Mail::to([$users->email])
                    // ->bcc(['aditya.agassi@music.yamaha.com'])
                        ->send(new SendEmail($data, 'driver_approval_notification'));
                }

                $code_generator = CodeGenerator::where('note', '=', 'leave_request')->first();
                $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                $request_id = $code_generator->prefix . $number;
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();

                $end_times = explode(' ', $request->get('end_time'));

                if ($end_times[1] == '16:00:00' || $end_times[1] == '05:00:00') {
                    $return_or_not = 'NO';
                } else {
                    $return_or_not = 'YES';
                }

                if ($driver_list->remark == 'YMPI') {
                    $leave_request = new HrLeaveRequest([
                        'request_id' => $request_id,
                        'position' => 'security',
                        'department' => 'General Affairs Department',
                        'date' => date('Y-m-d'),
                        'purpose_category' => 'DINAS',
                        'purpose' => 'LAIN-LAIN',
                        'purpose_detail' => $request->get('purpose'),
                        'time_departure' => $request->get('start_time'),
                        'time_arrived' => $request->get('end_time'),
                        'return_or_not' => $return_or_not,
                        'add_driver' => 'YES',
                        'driver_request_id' => $driver->id,
                        'remark' => 'Partially Approved',
                        'created_by' => Auth::user()->id,
                    ]);
                    $leave_request->save();

                    $emp = EmployeeSync::where('employee_id', $request->get('driver_id'))->first();

                    $detail = new HrLeaveRequestDetail([
                        'request_id' => $request_id,
                        'employee_id' => $request->get('driver_id'),
                        'name' => $driver_list->name,
                        'department' => $emp->department,
                        'section' => $emp->section,
                        'group' => $emp->group,
                        'sub_group' => $emp->sub_group,
                        'created_by' => Auth::user()->id,
                    ]);
                    $detail->save();

                    $users = User::where('username', Auth::user()->username)->first();
                    if (strpos($users->email, '@music.yamaha.com') !== false) {
                        $applicant_email = $users->email;
                    } else {
                        $applicant_email = '';
                    }

                    $applicant = new HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => Auth::user()->username,
                        'approver_name' => Auth::user()->name,
                        'approver_email' => $applicant_email,
                        'status' => "Approved",
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'Applicant',
                    ]);
                    $applicant->save();

                    $m = Approver::where('department', '=', $emp->department)
                        ->where('remark', '=', 'Manager')
                        ->first();

                    $manager = new HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => $m->approver_id,
                        'approver_name' => $m->approver_name,
                        'approver_email' => $m->approver_email,
                        'status' => "Approved",
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'Manager',
                    ]);
                    $manager->save();

                    $hr = new HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => "PI0811002",
                        'approver_name' => "Mahendra Putra",
                        'approver_email' => "mahendra.putra@music.yamaha.com",
                        'status' => "Approved",
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'HR',
                    ]);
                    $hr->save();

                    $ga = new HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => "PI0904002",
                        'approver_name' => "Heriyanto",
                        'approver_email' => "heriyanto@music.yamaha.com",
                        'status' => "Approved",
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'GA',
                    ]);
                    $ga->save();

                    $security = new HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => "",
                        'approver_name' => "",
                        'approver_email' => "",
                        'remark' => 'Security',
                    ]);
                    $security->save();
                }

                $response = array(
                    'status' => true,
                    'message' => 'Driver request berhasil diterima',
                    'tes' => $driver,
                );
                return Response::json($response);
            } else if ($request->get('cat') == 'reject') {
                $driver = Driver::where('id', '=', $request->get('id'))
                    ->first();

                $driver->received_by = Auth::user()->username;
                $driver->save();

                $drivers = Driver::where('drivers.id', '=', $request->get('id'))
                    ->leftJoin('driver_details', 'drivers.id', '=', 'driver_details.driver_id')
                    ->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'drivers.created_by', 'drivers.approved_by', 'drivers.received_by', 'drivers.remark as status', 'driver_details.remark', 'driver_details.category')
                    ->get();

                foreach ($drivers as $driver) {
                    $driver_log = new Driverlog([
                        'request_id' => $driver->id,
                        'driver_id' => $driver->driver_id,
                        'name' => $driver->name,
                        'purpose' => $driver->purpose,
                        'destination_city' => $driver->destination_city,
                        'date_from' => $driver->date_from,
                        'date_to' => $driver->date_to,
                        'created_by' => $driver->created_by,
                        'approved_by' => $driver->approved_by,
                        'received_by' => $driver->received_by,
                        'status' => 'rejected',
                        'remark' => $driver->remark,
                        'category' => $driver->category,
                    ]);
                    $driver_log->save();
                }

                $driver->forceDelete();

                $delete_driver_detail = DriverDetail::where('driver_id', '=', $request->get('id'))
                    ->first();

                if ($delete_driver_detail != null) {
                    $delete_driver_detail->forceDelete();
                }

                $response = array(
                    'status' => true,
                    'message' => 'Driver request berhasil ditolak',
                );
                return Response::json($response);
            } else if ($request->get('cat') == 'canceled') {
                $driver = Driver::where('id', '=', $request->get('id'))
                    ->first();

                $driver->received_by = Auth::user()->username;
                $driver->save();

                $drivers = Driver::where('drivers.id', '=', $request->get('id'))
                    ->leftJoin('driver_details', 'drivers.id', '=', 'driver_details.driver_id')
                    ->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'drivers.created_by', 'drivers.approved_by', 'drivers.received_by', 'drivers.remark as status', 'driver_details.remark', 'driver_details.category')
                    ->get();

                foreach ($drivers as $driver) {
                    $driver_log = new Driverlog([
                        'request_id' => $driver->id,
                        'driver_id' => $driver->driver_id,
                        'name' => $driver->name,
                        'purpose' => $driver->purpose,
                        'destination_city' => $driver->destination_city,
                        'date_from' => $driver->date_from,
                        'date_to' => $driver->date_to,
                        'created_by' => $driver->created_by,
                        'approved_by' => $driver->approved_by,
                        'received_by' => $driver->received_by,
                        'status' => 'canceled',
                        'remark' => $driver->remark,
                        'category' => $driver->category,
                    ]);
                    $driver_log->save();
                }

                $driver->forceDelete();

                $delete_driver_detail = DriverDetail::where('driver_id', '=', $request->get('id'))
                    ->first();

                if ($delete_driver_detail != null) {
                    $delete_driver_detail->forceDelete();
                }

                $response = array(
                    'status' => true,
                    'message' => 'Driver request berhasil ditolak',
                );
                return Response::json($response);
            } else if ($request->get('cat') == 'close') {
                $driver = Driver::where('id', '=', $request->get('id'))
                    ->first();

                $drivers = Driver::where('drivers.id', '=', $request->get('id'))
                    ->leftJoin('driver_details', 'drivers.id', '=', 'driver_details.driver_id')
                    ->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'drivers.created_by', 'drivers.approved_by', 'drivers.received_by', 'drivers.remark as status', 'driver_details.remark', 'driver_details.category')
                    ->get();

                foreach ($drivers as $driver) {
                    $driver_log = new Driverlog([
                        'request_id' => $driver->id,
                        'driver_id' => $driver->driver_id,
                        'name' => $driver->name,
                        'purpose' => $driver->purpose,
                        'destination_city' => $driver->destination_city,
                        'date_from' => $driver->date_from,
                        'date_to' => $driver->date_to,
                        'created_by' => $driver->created_by,
                        'approved_by' => $driver->approved_by,
                        'received_by' => $driver->received_by,
                        'status' => 'received',
                        'remark' => $driver->remark,
                        'category' => $driver->category,
                    ]);
                    $driver_log->save();
                }

                $driver->forceDelete();

                $delete_driver_detail = DriverDetail::where('driver_id', '=', $request->get('id'))
                    ->first();

                if ($delete_driver_detail != null) {
                    $delete_driver_detail->forceDelete();
                }

                $response = array(
                    'status' => true,
                    'message' => 'Driver request berhasil ditutup',
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

    public function createDriverDuty(Request $request)
    {
        try {
            $id = Auth::user()->username;
            $driver_list = DriverList::where('driver_id', '=', $request->get('driver_id'))->first();
            $driver = new Driver([
                'purpose' => $request->get('purpose'),
                'destination_city' => $request->get('destination_city'),
                'date_from' => $request->get('start_time'),
                'date_to' => $request->get('end_time'),
                'driver_id' => $request->get('driver_id'),
                'plat_no' => $request->get('plat_no'),
                'car' => $request->get('car'),
                'name' => $driver_list->name,
                'approved_by' => $id,
                'received_by' => $id,
                'created_by' => $id,
                'remark' => 'received',
            ]);
            $driver->save();

            $passengers = $request->get('passenger');
            $destinations = $request->get('destination');

            for ($i = 0; $i < count($passengers); $i++) {
                $passenger_detail = new DriverDetail([
                    'driver_id' => $driver->id,
                    'remark' => $passengers[$i]['employee_id'],
                    'passenger_name' => $passengers[$i]['passenger_name'],
                    'category' => 'passenger',
                ]);
                $passenger_detail->save();
            }

            for ($i = 0; $i < count($destinations); $i++) {
                $destination_detail = new DriverDetail([
                    'driver_id' => $driver->id,
                    'remark' => $destinations[$i]['destination'],
                    'category' => 'destination',
                ]);
                $destination_detail->save();
            }

            $dests = [];
            for ($i = 0; $i < count($destinations); $i++) {
                array_push($dests, $destinations[$i]['destination']);
            }

            $day = str_replace(' ', '%20', date('l, d F Y', strtotime($request->get('start_time'))));

            $start_time = date('H:i a', strtotime($request->get('start_time')));
            $start_time_replace = str_replace(" ", "%20", $start_time);

            $end_time = date('H:i a', strtotime($request->get('end_time')));
            $end_time_replace = str_replace(" ", "%20", $end_time);

            $destinationses = str_replace(" ", "%20", $request->get('destination_city') . '%20(' . join(",%20", $dests) . ')');
            $destinationses = str_replace("&", "%26", $destinationses);
            $name = str_replace(" ", "%20", $driver_list->name);

            $emp = EmployeeSync::where('employee_id', $id)->first();
            $request_name = str_replace(" ", "%20", $emp->name);

            $message = 'Driver%20Order%0A%0ARequest%20by%20:%20' . $request_name . '%0ADay%20and%20Date%20:%20' . $day . '%0ADestination%20City%20:%20' . $destinationses . '%0ADeparture%20:%20' . $start_time_replace . '%0AArrive%20:%20' . $end_time_replace . '%0APilot%20:%20' . $name . '%0A%0A-YMPI%20MIS%20Dept.-';

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
                CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . $message . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            ));

            curl_exec($curl);

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
                CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . $message . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            ));

            curl_exec($curl);

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
                CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . $message . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            ));

            curl_exec($curl);

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
                CURLOPT_POSTFIELDS => 'receiver=628113669871&device=6281130561777&message=' . $message . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            ));

            curl_exec($curl);

            $code_generator = CodeGenerator::where('note', '=', 'leave_request')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $request_id = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            if ($driver_list->remark == 'YMPI') {
                $leave_request = new HrLeaveRequest([
                    'request_id' => $request_id,
                    'position' => 'security',
                    'department' => 'General Affairs Department',
                    'date' => date('Y-m-d'),
                    'purpose_category' => 'DINAS',
                    'purpose' => 'LAIN-LAIN',
                    'purpose_detail' => $request->get('purpose'),
                    'time_departure' => $request->get('start_time'),
                    'time_arrived' => $request->get('end_time'),
                    'return_or_not' => $request->get('return_or_not'),
                    'add_driver' => 'YES',
                    'driver_request_id' => $driver->id,
                    'remark' => 'Partially Approved',
                    'created_by' => Auth::user()->id,
                ]);
                $leave_request->save();

                $emp = EmployeeSync::where('employee_id', $request->get('driver_id'))->first();

                $detail = new HrLeaveRequestDetail([
                    'request_id' => $request_id,
                    'employee_id' => $request->get('driver_id'),
                    'name' => $driver_list->name,
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'group' => $emp->group,
                    'sub_group' => $emp->sub_group,
                    'created_by' => Auth::user()->id,
                ]);
                $detail->save();

                $users = User::where('username', Auth::user()->username)->first();
                if (strpos($users->email, '@music.yamaha.com') !== false) {
                    $applicant_email = $users->email;
                } else {
                    $applicant_email = '';
                }

                $applicant = new HrLeaveRequestApproval([
                    'request_id' => $request_id,
                    'approver_id' => Auth::user()->username,
                    'approver_name' => Auth::user()->name,
                    'approver_email' => $applicant_email,
                    'status' => "Approved",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'Applicant',
                ]);
                $applicant->save();

                $m = Approver::where('department', '=', $emp->department)
                    ->where('remark', '=', 'Manager')
                    ->first();

                $manager = new HrLeaveRequestApproval([
                    'request_id' => $request_id,
                    'approver_id' => $m->approver_id,
                    'approver_name' => $m->approver_name,
                    'approver_email' => $m->approver_email,
                    'status' => "Approved",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'Manager',
                ]);
                $manager->save();

                $hr = new HrLeaveRequestApproval([
                    'request_id' => $request_id,
                    'approver_id' => "PI0811002",
                    'approver_name' => "Mahendra Putra",
                    'approver_email' => "mahendra.putra@music.yamaha.com",
                    'status' => "Approved",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'HR',
                ]);
                $hr->save();

                $ga = new HrLeaveRequestApproval([
                    'request_id' => $request_id,
                    'approver_id' => "PI0904002",
                    'approver_name' => "Heriyanto",
                    'approver_email' => "heriyanto@music.yamaha.com",
                    'status' => "Approved",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'GA',
                ]);
                $ga->save();

                $security = new HrLeaveRequestApproval([
                    'request_id' => $request_id,
                    'approver_id' => "",
                    'approver_name' => "",
                    'approver_email' => "",
                    'remark' => 'Security',
                ]);
                $security->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Tugas driver berhasil ditambahkan',
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

    public function createDriverRequest(Request $request)
    {
        try {
            $id = Auth::user()->username;
            $driver = new Driver([
                'purpose' => $request->get('purpose'),
                'destination_city' => $request->get('destination_city'),
                'date_from' => $request->get('start_time'),
                'date_to' => $request->get('end_time'),
                'created_by' => $id,
                'remark' => 'requested',
            ]);
            $driver->save();

            $passengers = $request->get('passenger');
            $destinations = $request->get('destination');

            for ($i = 0; $i < count($passengers); $i++) {
                $passenger_detail = new DriverDetail([
                    'driver_id' => $driver->id,
                    'remark' => $passengers[$i]['employee_id'],
                    'passenger_name' => $passengers[$i]['passenger_name'],
                    'category' => 'passenger',
                ]);
                $passenger_detail->save();
            }

            for ($i = 0; $i < count($destinations); $i++) {
                $destination_detail = new DriverDetail([
                    'driver_id' => $driver->id,
                    'remark' => $destinations[$i]['destination'],
                    'category' => 'destination',
                ]);
                $destination_detail->save();
            }

            $mail = EmployeeSync::leftJoin('send_emails', 'send_emails.remark', '=', 'employee_syncs.department')
                ->where('employee_syncs.employee_id', '=', $id)
                ->select('send_emails.email')
                ->first();

            $data = [
                'driver' => $driver,
                'requested_by' => Auth::user()->name,
            ];

            Mail::to($mail->email)
            // ->bcc(['aditya.agassi@music.yamaha.com'])
                ->send(new SendEmail($data, 'driver_request'));

            $response = array(
                'status' => true,
                'message' => 'Request Driver Berhasil Diajukan',
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

    public function approveRequest($id)
    {
        try {
            $driver = Driver::where('drivers.id', '=', $id)->first();

            if ($driver->remark == 'accepted') {
                $message = 'Approve driver request gagal';
                $message2 = 'Driver Request ID: ' . $id . ' sudah pernah disetujui';
                return view('general_affairs.driver_approval', array(
                    'head' => $id,
                    'message' => $message,
                    'message2' => $message2,
                ))->with('page', 'Driver Approval');
            }
            if ($driver->remark == 'received') {
                $approver = EmployeeSync::where('employee_id', '=', $driver->created_by)->first();
                $driver->approved_by = strtoupper(Auth::user()->username);
                $driver->save();

                $data = [
                    'driver' => $driver,
                ];

                $users = User::where('username', $driver->created_by)->first();
                if (strpos($users->email, '@music.yamaha.com') !== false) {
                    Mail::to([$users->email])
                    // ->bcc(['aditya.agassi@music.yamaha.com'])
                        ->send(new SendEmail($data, 'driver_approval_notification'));
                }

                Mail::to(['rianita.widiastuti@music.yamaha.com', 'heriyanto@music.yamaha.com', 'putri.sukma.riyanti@music.yamaha.com'])
                // ->bcc(['aditya.agassi@music.yamaha.com'])
                    ->send(new SendEmail($data, 'driver_approval_notification'));

                $message = 'Approval driver request berhasil';
                $message2 = 'Driver Request ID: ' . $id . ' berhasil disetujui';
                return view('workshop.wjo_approval_message', array(
                    'head' => $id,
                    'message' => $message,
                    'message2' => $message2,
                ))->with('page', 'WJO Approval');
            }
            if ($driver->remark == 'requested') {
                $approver = EmployeeSync::where('employee_id', '=', $driver->created_by)->first();
                $driver->approved_by = strtoupper(Auth::user()->username);
                $driver->remark = 'accepted';

                $driver->save();

                $data = [
                    'driver' => $driver,
                ];

                $users = User::where('username', $driver->created_by)->first();
                if (strpos($users->email, '@music.yamaha.com') !== false) {
                    Mail::to([$users->email])
                    // ->bcc(['aditya.agassi@music.yamaha.com'])
                        ->send(new SendEmail($data, 'driver_approval_notification'));
                }

                Mail::to(['rianita.widiastuti@music.yamaha.com', 'heriyanto@music.yamaha.com', 'putri.sukma.riyanti@music.yamaha.com'])
                // ->bcc(['aditya.agassi@music.yamaha.com'])
                    ->send(new SendEmail($data, 'driver_approval_notification'));

                $message = 'Approval driver request berhasil';
                $message2 = 'Driver Request ID: ' . $id . ' berhasil disetujui';
                return view('workshop.wjo_approval_message', array(
                    'head' => $id,
                    'message' => $message,
                    'message2' => $message2,
                ))->with('page', 'WJO Approval');
            }

        } catch (\Exception$e) {
            return view('workshop.wjo_approval_message', array(
                'head' => $id,
                'message' => 'ERROR!!!',
                'message2' => $e->getMessage(),
            ))->with('page', 'WJO Approval');
        }
    }

    public function rejectRequest($id, Request $request)
    {
        try {
            $driver = Driver::where('drivers.id', '=', $id)->first();

            if ($driver->remark == 'accepted' || $driver->remark == 'rejected') {
                $message = 'Reject driver request gagal';
                $message2 = 'Driver Request ID: ' . $id . ' sudah pernah disetujui/ditolak';
                return view('general_affairs.driver_approval', array(
                    'head' => $id,
                    'message' => $message,
                    'message2' => $message2,
                ))->with('page', 'Driver Approval');
            } else {
                $approver = EmployeeSync::where('employee_id', '=', $driver->created_by)->first();
                $driver->approved_by = $approver->nik_manager;
                $driver->remark = 'rejected';
                $driver->save();

                $drivers = Driver::where('drivers.id', '=', $request->get('id'))
                    ->leftJoin('driver_details', 'drivers.id', '=', 'driver_details.driver_id')
                    ->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'drivers.created_by', 'drivers.approved_by', 'drivers.received_by', 'drivers.remark as status', 'driver_details.remark', 'driver_details.category')
                    ->get();

                foreach ($drivers as $driver) {
                    $driver_log = new Driverlog([
                        'request_id' => $driver->id,
                        'driver_id' => $driver->driver_id,
                        'name' => $driver->name,
                        'purpose' => $driver->purpose,
                        'destination_city' => $driver->destination_city,
                        'date_from' => $driver->date_from,
                        'date_to' => $driver->date_to,
                        'created_by' => $driver->created_by,
                        'approved_by' => $driver->approved_by,
                        'received_by' => $driver->received_by,
                        'status' => 'rejected',
                        'remark' => $driver->remark,
                        'category' => $driver->category,
                    ]);
                    $driver_log->save();
                }

                $driver->forceDelete();

                $delete_driver_detail = DriverDetail::where('driver_id', '=', $request->get('id'))
                    ->first();

                if ($delete_driver_detail != null) {
                    $delete_driver_detail->forceDelete();
                }

                $message = 'Reject driver request berhasil';
                $message2 = 'Driver Request ID: ' . $id . ' berhasil ditolak';
                return view('workshop.wjo_approval_message', array(
                    'head' => $id,
                    'message' => $message,
                    'message2' => $message2,
                ))->with('page', 'WJO Approval');
            }
        } catch (\Exception$e) {
            return view('workshop.wjo_approval_message', array(
                'head' => $id,
                'message' => 'ERROR!!!',
                'message2' => $e->getMessage(),
            ))->with('page', 'WJO Approval');
        }
    }

    public function editDriverEdit(Request $request)
    {
        try {
            if ($request->get('cat') == 'save') {
                $driver_list = DriverList::where('driver_id', '=', $request->get('id'))->first();

                $driver_list->name = $request->get('name');
                $driver_list->phone_no = $request->get('no');
                $driver_list->plat_no = $request->get('plat');
                $driver_list->car = $request->get('car');
                $driver_list->position = $request->get('position');
                $driver_list->category = $request->get('category');

                $driver_list->save();

                $response = array(
                    'status' => true,
                    'message' => 'Data driver berhasil diperbaharui',
                );
                return Response::json($response);
            } else if ($request->get('cat') == 'new') {
                $driver_list = new DriverList([
                    'driver_id' => $request->get('id'),
                    'name' => $request->get('name'),
                    'phone_no' => $request->get('no'),
                    'plat_no' => $request->get('plat'),
                    'whatsapp_no' => $request->get('whatsapp'),
                    'remark' => $request->get('remark'),
                    'car' => $request->get('car'),
                    'category' => $request->get('category'),
                    'created_by' => Auth::id(),
                ]);

                $driver_list->save();

                $response = array(
                    'status' => true,
                    'message' => 'Driver baru berhasil ditambahkan',
                );
                return Response::json($response);
            } else if ($request->get('cat') == 'delete') {
                $driver_list = DriverList::where('driver_id', '=', $request->get('id'))->first();

                $driver_list->delete();

                $response = array(
                    'status' => true,
                    'message' => 'Driver berhasil dinonaktifkan',
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

    public function fetchDriverEdit(Request $request)
    {
        $driver_list = DriverList::where('driver_id', '=', $request->get('id'))->first();

        if ($driver_list == null) {
            $response = array(
                'status' => false,
                'message' => 'Klik pada NIK driver',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'Driver ditemukan',
            'driver_list' => $driver_list,
        );
        return Response::json($response);

    }

    public function fetchDriverDuty()
    {
        $now = date('Y-m-d H:i:s');
        $drivers = Driver::leftJoin('employee_syncs', 'drivers.created_by', '=', 'employee_syncs.employee_id')
            ->select('drivers.id', 'drivers.driver_id', 'drivers.duty_status', db::raw('date_format(drivers.duty_from, "%H:%i") as duty_from'), db::raw('date_format(drivers.duty_to, "%H:%i") as duty_to'), 'drivers.name as driver_name', 'drivers.purpose', 'drivers.destination_city', 'employee_syncs.name', db::raw('date_format(drivers.date_from, "%H:%i") as date_from'), db::raw('date_format(drivers.date_to, "%H:%i") as date_to'), 'drivers.remark')
            ->whereIn('drivers.remark', ['received'])
            ->whereNotNull('drivers.driver_id')
        // ->where('drivers.date_from', '<=', $now)
        // ->where('drivers.date_to', '>=', $now)
            ->where('drivers.duty_status', '=', 'on duty')
            ->orderBy('drivers.driver_id', 'asc')
            ->get();

        $response = array(
            'status' => true,
            'drivers' => $drivers,
        );
        return Response::json($response);
    }

    public function fetchDriver()
    {
        $driver_lists = DriverList::orderBy('driver_id', 'asc')
            ->where('remark', 'YMPI')
            ->whereNotNull('driver_id')
            ->get();

        $drivers = Driver::leftJoin('employee_syncs', 'drivers.created_by', '=', 'employee_syncs.employee_id')
            ->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.destination_city', 'employee_syncs.name', 'drivers.date_from', 'drivers.date_to', 'drivers.remark')
            ->whereIn('drivers.remark', ['requested', 'accepted', 'received', 'rejected'])
            ->whereNotNull('drivers.driver_id')
            ->orderBy('drivers.driver_id', 'asc')
            ->get();

        $response = array(
            'status' => true,
            'drivers' => $drivers,
            'driver_lists' => $driver_lists,
        );
        return Response::json($response);
    }

    public function fetchDriverDetail(Request $request)
    {
        $driver = Driver::where('drivers.id', '=', $request->get('id'))
            ->leftJoin('employee_syncs as request', 'request.employee_id', '=', 'drivers.created_by')
            ->leftJoin('employee_syncs as approve', 'approve.employee_id', '=', 'drivers.approved_by')
            ->leftJoin('employee_syncs as accept', 'accept.employee_id', '=', 'drivers.received_by')
            ->select('drivers.id', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'request.name as request_name', 'request.employee_id as request_id', 'approve.name as approve_name', 'approve.employee_id as approve_id', 'accept.name as accept_name', 'accept.employee_id as accept_id', 'drivers.driver_id', 'drivers.plat_no', 'drivers.car')
            ->first();

        $driver_lists = DriverList::orderBy('driver_lists.name', 'asc')->get();

        $passenger_detail = DriverDetail::where('driver_details.driver_id', '=', $request->get('id'))
            ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'driver_details.remark')
            ->where('driver_details.category', '=', 'passenger')
            ->get();

        $destination_detail = DriverDetail::where('driver_details.category', '=', 'destination')
            ->where('driver_details.driver_id', '=', $request->get('id'))
            ->get();

        $response = array(
            'status' => true,
            'driver' => $driver,
            'passenger_detail' => $passenger_detail,
            'destination_detail' => $destination_detail,
            'driver_lists' => $driver_lists,
        );
        return Response::json($response);
    }

    //LIVE COOKINGS

    public function indexLiveCooking()
    {
        $title = "Live Cooking Order";
        $title_jp = "ライブクッキングの予約";

        $user = Auth::user()->username;
        $role = Auth::user()->role_code;

        $menus = CanteenLiveCookingMenu::where('periode', date('Y-m'))->get();
        $today = CanteenLiveCookingMenu::where('due_date', date('Y-m-d'))->first();
        $dateOrder = date('Y-m-d', strtotime("-7 day", strtotime(date('Y-m-01'))));

        $t1 = strtotime(date('Y-m-d'));
        $t2 = strtotime(date('Y-' . date('m', strtotime('first day of +1 month')) . '-01'));

        $interval = $t2 - $t1;
        $total_sec = abs($t2 - $t1);
        $total_min = floor($total_sec / 60);
        $total_hour = floor($total_min / 60);
        $total_day = floor($total_hour / 24);
        $month_now = date('Y-m');

        $employees = EmployeeSync::where('end_date', null)->get();

        $admins = CanteenLiveCookingAdmin::where('employee_id', Auth::user()->username)->where('status', null)->get();

        // $employees = [];
        $kuota = [];
        $adminrole = '';
        if (count($admins) > 0) {
            if ($admins[0]->live_cooking_role == 'all' || $admins[0]->live_cooking_role == 'ga') {
                $employees = EmployeeSync::where('employee_syncs.end_date', null)->join('employees', 'employees.employee_id', 'employee_syncs.employee_id')->get();

                array_push($kuota, [
                    'dept' => 'all',
                    'sect' => 'all',
                    'kuota' => 10000,
                ]);
            } else {
                for ($k = 0; $k < count($admins); $k++) {

                    $department = '';
                    if ($admins[$k]->department != null) {
                        $departments = explode(",", $admins[$k]->department);
                        for ($i = 0; $i < count($departments); $i++) {
                            $department = $department . "'" . $departments[$i] . "'";
                            if ($i != (count($departments) - 1)) {
                                $department = $department . ',';
                            }
                        }
                        $departmentin = " and `department` in (" . $department . ") ";
                    } else {
                        $departmentin = "";
                    }

                    array_push($kuota, [
                        'dept' => $admins[$k]->department,
                        'sect' => $admins[$k]->section,
                        'kuota' => $admins[$k]->order_quota,
                    ]);

                    $employees = DB::SELECT("SELECT
                     *
                       FROM
                       employee_syncs
                       JOIN employees ON employees.employee_id = employee_syncs.employee_id
                       WHERE
                       employee_syncs.end_date IS NULL
                       AND employees.remark != 'OFC'
                       " . $departmentin . "
                       OR employee_syncs.end_date IS NULL
                       AND employees.remark IS NULL
                       " . $departmentin . " ");
                }
            }
            $adminrole = $admins[0]->live_cooking_role;
        }

        // array_push($kuota, [
        //     'dept' => 'Berbayar',
        //     'sect' => 'Berbayar',
        //     'kuota' => 20,
        // ]);

        $employees = EmployeeSync::where('end_date', null)->get();

        return view('general_affairs.live_cooking', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'menus' => $menus,
            'user' => $user,
            'admins' => $admins,
            'adminrole' => $adminrole,
            'role' => $role,
            'kuota' => $kuota,
            'today' => $today,
            'total_day' => 6,
            'monthTitle' => date('F Y'),
            'month_now' => $month_now,
        ))->with('head', 'GA Control')->with('page', 'Live Cooking Order');
    }

    public function indexLiveCookingConfirm()
    {
        $title = "Live Cooking Order Confirmation";
        $title_jp = "ライブクッキングの予約";

        $user = Auth::user()->username;
        $role = Auth::user()->role_code;

        $confirm = CanteenLiveCooking::where('status', 'Requested')->get();

        $emp = EmployeeSync::get();

        return view('general_affairs.live_cooking_confirm', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'user' => $user,
            'emp' => $emp,
            'role' => $role,
            'confirm' => $confirm,
        ))->with('head', 'GA Control')->with('page', 'Live Cooking Order');
    }

    public function fetchLiveCookingMenu($periode)
    {
        $live_cookings = DB::SELECT("SELECT
            DATE_FORMAT(due_date,'%d %M %Y') as due_date,menu_name
            FROM
            canteen_live_cooking_menus
            WHERE
            periode = '" . $periode . "'");

        $monthTitle = date("F Y", strtotime($periode));
        if (count($live_cookings) > 0) {
            return view('general_affairs.live_cooking_menu', array(
                'live_cookings' => $live_cookings,
                'monthTitle' => $monthTitle,
            ))->with('head', 'GA Control')->with('page', 'Live Cooking Order');
        } else {
            return redirect('index/ga_control/live_cooking')->with('error', 'Data Belum Tersedia');
        }
    }

    public function downloadFileExcelLiveCooking()
    {
        $file_path = public_path('data_file/TemplateMenuLiveCooking.xlsx');
        return response()->download($file_path);
    }

    public function uploadLiveCookingMenu(Request $request)
    {
        $filename = "";
        $file_destination = 'data_file/live_cooking';

        if ($request->file('newAttachment') != null) {
            try {
                $file = $request->file('newAttachment');
                $filename = 'live_cooking_' . date('YmdHisa') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);

                $excel = 'data_file/live_cooking/' . $filename;
                $rows = Excel::load($excel, function ($reader) {
                    $reader->noHeading();
                    $reader->skipRows(1);

                    $reader->each(function ($row) {
                    });
                })->toObject();

                for ($i = 0; $i < count($rows); $i++) {

                    $calendar = WeeklyCalendar::where('week_date', date('Y-m-d', strtotime($rows[$i][0])))->first();

                    if ($calendar->remark != 'H') {
                        $menu = CanteenLiveCookingMenu::updateOrCreate(
                            [
                                'due_date' => date('Y-m-d', strtotime($rows[$i][0])),
                            ],
                            [
                                'periode' => $request->get('menuDate'),
                                'due_date' => date('Y-m-d', strtotime($rows[$i][0])),
                                'menu_name' => $rows[$i][1],
                                'serving_quota' => $rows[$i][2],
                                'serving_ordered' => 0,
                                'serving_ordered_pay' => 0,
                                'created_by' => Auth::id(),
                            ]
                        );
                        $menu->save();
                    }
                }

                $response = array(
                    'status' => true,
                    'message' => 'Menu succesfully uploaded',
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

    public function fetchLiveCookingOrderList(Request $request)
    {
        try {

            $now = date('Y-m-d', strtotime(carbon::now()));
            $last = date('Y-m-d', strtotime(carbon::now()->addDays(20)));

            $periode = date('Y-m');
            $nextPeriode = date('Y-m', strtotime('+ 1 month'));

            $periodes = DB::SELECT("SELECT
               *
             FROM
             ( SELECT DISTINCT ( periode ) FROM canteen_live_cooking_menus ORDER BY created_at DESC LIMIT 2 ) a
             ORDER BY
             a.periode");

            $periode = $periodes[0]->periode;
            $nextPeriode = $periodes[1]->periode;

            // $roles = CanteenLiveCookingAdmin::where('employee_id',Auth::user()->username)->first();

            // if ($roles->live_cooking_role == 'ga') {
            // $resumes = DB::SELECT("SELECT
            //     canteen_live_cookings.id AS id_live,
            //     order_by,
            //     emp_by.`name` AS name_by,
            //     order_for,
            //     emp_for.`name` AS name_for,
            //     due_date,
            //     `status`,
            //     remark,
            //     emp_by.department,
            //     emp_by.section
            //     FROM
            //     canteen_live_cookings
            //     LEFT JOIN employee_syncs emp_by ON emp_by.employee_id = canteen_live_cookings.order_by
            //     LEFT JOIN employee_syncs emp_for ON emp_for.employee_id = canteen_live_cookings.order_for
            //     where DATE_FORMAT(due_date,'%Y-%m') = '".$periode."'
            //     order by due_date");

            //     $quotas = CanteenLiveCookingMenu::where('due_date', '>=', $now)
            //     ->where('due_date', '<=', $last)
            //     ->select(db::raw('date_format(due_date, "%a, %d %b %Y") as due_date'), 'serving_quota', 'serving_ordered')
            //     ->get();
            // }else if($roles->live_cooking_role == 'all'){
            //     $resumes = DB::SELECT("SELECT
            //         canteen_live_cookings.id AS id_live,
            //         order_by,
            //         emp_by.`name` AS name_by,
            //         order_for,
            //         emp_for.`name` AS name_for,
            //         due_date,
            //         `status`,
            //         remark,
            //         emp_by.department,
            //         emp_by.section
            //         FROM
            //         canteen_live_cookings
            //         LEFT JOIN employee_syncs emp_by ON emp_by.employee_id = canteen_live_cookings.order_by
            //         LEFT JOIN employee_syncs emp_for ON emp_for.employee_id = canteen_live_cookings.order_for
            //         where DATE_FORMAT(due_date,'%Y-%m') = '".$periode."'
            //         order by due_date");
            //     $quotas = CanteenLiveCookingMenu::where('due_date', '>=', $now)
            //     ->where('due_date', '<=', $last)
            //     ->select(db::raw('date_format(due_date, "%a, %d %b %Y") as due_date'), 'serving_quota', 'serving_ordered')
            //     ->get();
            // }else if($roles->live_cooking_role == 'prod'){

            $resume_confirmation = DB::SELECT("SELECT
             count(id) as qty
             FROM
             canteen_live_cookings
             WHERE
             status = 'Requested'");

            $resumes = DB::SELECT("SELECT
             canteen_live_cookings.id AS id_live,
             order_by,
             emp_by.`name` AS name_by,
             order_for,
             emp_for.`name` AS name_for,
             due_date,
             `status`,
             remark,
             emp_by.department,
             emp_by.section
             FROM
             canteen_live_cookings
             LEFT JOIN employee_syncs emp_by ON emp_by.employee_id = canteen_live_cookings.order_by
             LEFT JOIN employee_syncs emp_for ON emp_for.employee_id = canteen_live_cookings.order_for
             WHERE
             order_by = '" . Auth::user()->username . "'
             OR
             order_for = '" . Auth::user()->username . "'
             order by due_date ");

            $quotas = DB::SELECT("SELECT
             date_format( due_date, '%a, %d %b %Y' ) AS due_date,
             due_date as due_dates,
             serving_quota,serving_ordered
             FROM
             canteen_live_cooking_menus
             WHERE
             due_date >= '" . $now . "'
             AND due_date <= '" . $last . "'
             GROUP BY
             due_date,serving_quota,serving_ordered");

            $menus = DB::SELECT("SELECT
             a.*
             FROM
             (
              SELECT
              week_date,
              weekly_calendars.remark,
              ( SELECT menu_name FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS menu_name,
              ( SELECT serving_quota FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS serving_quota,
              ( SELECT serving_ordered FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS serving_ordered,
              ( SELECT COALESCE(serving_ordered_pay,0) as serving_ordered_pay FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS serving_ordered_pay
              FROM
              weekly_calendars
              WHERE
              DATE_FORMAT( week_date, '%Y-%m' ) = '" . $periode . "' UNION ALL
              SELECT
              week_date,
              weekly_calendars.remark,
              ( SELECT menu_name FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS menu_name,
              ( SELECT serving_quota FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS serving_quota,
              ( SELECT serving_ordered FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS serving_ordered,
              ( SELECT COALESCE(serving_ordered_pay,0) as serving_ordered_pay FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS serving_ordered_pay
              FROM
              weekly_calendars
              WHERE
              DATE_FORMAT( week_date, '%Y-%m' ) = '" . $nextPeriode . "'
          ) a");
            // }

            $calendars = WeeklyCalendar::where(db::raw('date_format(week_date, "%Y-%m")'), '=', $periode)
                ->select('week_date', db::raw('date_format(week_date, "%d") as header'), 'remark')
                ->get();

            $response = array(
                'status' => true,
                'resumes' => $resumes,
                'quota' => $quotas,
                'calendars' => $calendars,
                'menus' => $menus,
                'resume_confirmation' => $resume_confirmation,
                'now' => $now,
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

    public function fetchLiveCookingEmployees(Request $request)
    {
        try {
            $sisa_kuota = 20;
            if ($request->get('department') == 'all' || $request->get('department') == 'Berbayar') {
                $kuotas = CanteenLiveCookingMenu::where('due_date', $request->get('due_date'))->first();
                $sisa_kuota = 20 - $kuotas->serving_ordered_pay;
                $empys = DB::SELECT("SELECT
                 *
                   FROM
                   employee_syncs
                   JOIN employees ON employees.employee_id = employee_syncs.employee_id
                   WHERE
                   employee_syncs.end_date IS NULL");
            } else {
                $department = '';
                if ($request->get('department') != null) {
                    $departments = explode(",", $request->get('department'));
                    for ($i = 0; $i < count($departments); $i++) {
                        $department = $department . "'" . $departments[$i] . "'";
                        if ($i != (count($departments) - 1)) {
                            $department = $department . ',';
                        }
                    }
                    $departmentin = " and `department` in (" . $department . ") ";
                } else {
                    $departmentin = "";
                }

                $section = '';
                if ($request->get('section') != null) {
                    $sections = explode(",", $request->get('section'));
                    for ($i = 0; $i < count($sections); $i++) {
                        $section = $section . "'" . $sections[$i] . "'";
                        if ($i != (count($sections) - 1)) {
                            $section = $section . ',';
                        }
                    }
                    $sectionin = " and `section` in (" . $section . ") ";
                } else {
                    $sectionin = "";
                }
                if ($request->get('roles') == 'ofc') {
                    $empys = DB::SELECT("SELECT
                        *
                       FROM
                       employee_syncs
                       JOIN employees ON employees.employee_id = employee_syncs.employee_id
                       WHERE
                       (employee_syncs.end_date IS NULL
                        AND employees.remark = 'OFC'
                        " . $departmentin . ")");
                } else if ($request->get('roles') == 'prod') {
                    if ($request->get('section') == 'Assembly Process Control Section') {
                        $empys = DB::SELECT("SELECT
                            *
                            FROM
                            employee_syncs
                            JOIN employees ON employees.employee_id = employee_syncs.employee_id
                            WHERE
                            employee_syncs.end_date IS NULL
                            AND employees.remark != 'OFC'
                            " . $departmentin . "
                            " . $sectionin . "
                            OR employee_syncs.end_date IS NULL
                            AND employees.remark IS NULL
                            " . $departmentin . "
                            " . $sectionin . "
                            or
                            (employee_syncs.employee_id = 'PI9707008')
                            or
                            (employee_syncs.employee_id = 'PI9902018')");
                    } else {
                        $empys = DB::SELECT("SELECT
                            *
                            FROM
                            employee_syncs
                            JOIN employees ON employees.employee_id = employee_syncs.employee_id
                            WHERE
                            employee_syncs.end_date IS NULL
                            AND employees.remark != 'OFC'
                            " . $departmentin . "
                            " . $sectionin . "
                            OR employee_syncs.end_date IS NULL
                            AND employees.remark IS NULL
                            " . $departmentin . "
                            " . $sectionin . "");
                    }
                } else {
                    $empys = DB::SELECT("SELECT
                        *
                        FROM
                        employee_syncs
                        JOIN employees ON employees.employee_id = employee_syncs.employee_id
                        WHERE
                        employee_syncs.end_date IS NULL");
                }
            }

            $response = array(
                'status' => true,
                'emp' => $empys,
                'sisa_kuota' => $sisa_kuota,
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

    public function fetchLiveCookingCheckEmployees(Request $request)
    {
        try {
            // if ($request->get('roles') == 'prod') {
            //     $emp = DB::SELECT("SELECT
            //         *
            //         FROM
            //         employee_syncs
            //         JOIN employees ON employees.employee_id = employee_syncs.employee_id
            //         WHERE
            //         ( employee_syncs.end_date IS NULL AND remark != 'OFC' AND department = '".$request->get('department')."' AND section = '".$request->get('section')."' )
            //         OR (
            //         employee_syncs.end_date IS NULL
            //         AND remark IS NULL
            //         AND department = '".$request->get('department')."'
            //         AND section = '".$request->get('section')."')");
            // }else if($request->get('roles') == 'ga'){
            //     $emp = EmployeeSync::select('employee_id','name')->where('employee_syncs.end_date',null)->get();
            // }else if($request->get('roles') == 'ofc'){
            //     $dept = '';
            //     if($roles->department != null){
            //         $depts =  explode(",", $roles->department);
            //         for ($i=0; $i < count($depts); $i++) {
            //             $dept = $dept."'".$depts[$i]."'";
            //             if($i != (count($depts)-1)){
            //                 $dept = $dept.',';
            //             }
            //         }
            //         $deptin = " and `department` in (".$dept.") ";
            //     }
            //     else{
            //         $deptin = "";
            //     }
            //     $emp = DB::SELECT("SELECT
            //         *
            //         FROM
            //         employee_syncs
            //         JOIN employees ON employees.employee_id = employee_syncs.employee_id
            //         WHERE
            //         employee_syncs.end_date IS NULL
            //         ".$deptin."
            //         AND remark = 'OFC'");
            // }else if($request->get('roles') == 'all'){
            //     $emp = DB::SELECT("SELECT
            //         *
            //         FROM
            //         employee_syncs
            //         JOIN employees ON employees.employee_id = employee_syncs.employee_id
            //         WHERE
            //         employee_syncs.end_date IS NULL");
            // }

            $emp = DB::SELECT("SELECT
              *
              FROM
              canteen_live_cookings
              WHERE
              due_date = '" . $request->get('due_date') . "'
              AND order_for = '" . $request->get('employee_id') . "'");

            // if ($request->get('department') == 'all') {
            //     $empys = DB::SELECT("SELECT
            //             *
            //         FROM
            //             employee_syncs
            //             JOIN employees ON employees.employee_id = employee_syncs.employee_id
            //         WHERE
            //             employee_syncs.end_date IS NULL");
            // }else{
            //     $empys = DB::SELECT("SELECT
            //             *
            //         FROM
            //             employee_syncs
            //             JOIN employees ON employees.employee_id = employee_syncs.employee_id
            //         WHERE
            //             employee_syncs.end_date IS NULL
            //             AND employees.remark != 'OFC'
            //             AND department = '".$request->get('department')."'
            //             AND section = '".$request->get('section')."'
            //             OR employee_syncs.end_date IS NULL
            //             AND employees.remark IS NULL
            //             AND department = '".$request->get('department')."'
            //             AND section = '".$request->get('section')."' ");
            // }

            if (count($emp) > 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Karyawan sudah mendapat Live Cooking pada tanggal tersebut.',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => true,
                    'emp' => $emp,
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

    public function inputLiveCookingOrder(Request $request)
    {
        try {
            $order_lists = $request->get('order_list');
            $order_by = $request->get('order_by');
            $due_date = $request->get('due_date');
            $department = $request->get('department');
            $section = $request->get('section');

            if ($department != 'Berbayar') {

                if ($section == null) {
                    $quota = CanteenLiveCookingAdmin::where('department', $department)->where('section', null)->where('remark', 1)->first();
                }else{
                    $quota = CanteenLiveCookingAdmin::where('department', $department)->where('section', 'like','%'.$section.'%')->where('remark', 1)->first();
                }

                if ($quota != null) {
                    if ($quota->live_cooking_role == 'ofc') {
                        if ($department == 'General Process Control Department') {
                            $cek = CanteenLiveCooking::join('employee_syncs', 'employee_syncs.employee_id', 'canteen_live_cookings.order_for')->where('employee_syncs.department', $department)->join('employees', 'employees.employee_id', 'employee_syncs.employee_id')->where('due_date', $due_date)->where('canteen_live_cookings.remark', null)->where('employees.remark', 'OFC')->get();
                        }else{
                            $cek = CanteenLiveCooking::join('employee_syncs', 'employee_syncs.employee_id', 'canteen_live_cookings.order_for')->where('employee_syncs.department', $department)->where('due_date', $due_date)->where('remark', null)->get();
                        }
                    } else {
                        if ($department == 'General Process Control Department') {
                            $cek = CanteenLiveCooking::join('employee_syncs', 'employee_syncs.employee_id', 'canteen_live_cookings.order_for')->where('employee_syncs.department', $department)->join('employees', 'employees.employee_id', 'employee_syncs.employee_id')->where('employee_syncs.section', 'like','%'.$section.'%')->where('due_date', $due_date)->where('canteen_live_cookings.remark', null)->where(
                                   function($query) {
                                     return $query
                                            ->where('employees.remark', 'NOT LIKE', '%OFC%')
                                            ->orWhere('employees.remark', '=', null);
                                    })->get();
                        }else{
                            $cek = CanteenLiveCooking::join('employee_syncs', 'employee_syncs.employee_id', 'canteen_live_cookings.order_for')->where('employee_syncs.department', $department)->where('employee_syncs.section', 'like','%'.$section.'%')->where('due_date', $due_date)->where('remark', null)->get();
                        }
                    }

                    if (count($cek) == $quota->order_quota) {
                        $response = array(
                            'status' => false,
                            'message' => 'Karyawan untuk Dept & Sect yang Anda pilih tgl ' . date('d M Y', strtotime($due_date)) . ' sudah penuh.',
                        );
                        return Response::json($response);
                    }
                }

                $count_menu = 0;

                for ($i = 0; $i < count($order_lists); $i++) {
                    $order = explode("_", $order_lists[$i]);
                    $live_cooking = CanteenLiveCooking::create(
                        [
                            'order_by' => strtoupper($request->get('order_by')),
                            'due_date' => $due_date,
                            'order_for' => strtoupper($order[0]),
                            'status' => 'Confirmed',
                            'whatsapp_status' => '0',
                            'attendance_generate_status' => '0',
                            'created_by' => Auth::id(),
                        ]
                    );
                    $live_cooking->save();

                    $empys = Employee::where('employee_id', strtoupper($order[0]))->first();
                    $empys->live_cooking = 1;
                    $empys->save();

                    $count_menu++;
                }

                $updateQuota = CanteenLiveCookingMenu::where('due_date', $due_date)->first();
                $updateQuota->serving_ordered = $updateQuota->serving_ordered + $count_menu;
                $updateQuota->save();

                $response = array(
                    'status' => true,
                    'message' => 'Order berhasil dibuat.',
                );
                return Response::json($response);
            } else {
                $count_cek = 0;
                for ($i = 0; $i < count($order_lists); $i++) {
                    $order = explode("_", $order_lists[$i]);
                    $cek = CanteenLiveCooking::where('order_for', strtoupper($order[0]))->where('due_date', $due_date)->first();

                    if ($cek) {
                        $cont_cek++;
                    }
                }

                if ($count_cek > 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Salah 1 Karyawan sudah mendapat Live Cooking ditanggal tersebut.',
                    );
                    return Response::json($response);
                }

                $cek_kuota = CanteenLiveCookingMenu::where('due_date', $due_date)->first();

                if (count($order_lists) > 20) {
                    $response = array(
                        'status' => false,
                        'message' => 'Jumlah Karyawan melebihi kuota. Kuota tersisa ' . (20 - $cek_kuota->serving_ordered_pay),
                    );
                    return Response::json($response);
                }

                $count_menu = 0;

                for ($i = 0; $i < count($order_lists); $i++) {
                    $order = explode("_", $order_lists[$i]);
                    $live_cooking = CanteenLiveCooking::create(
                        [
                            'order_by' => strtoupper($request->get('order_by')),
                            'due_date' => $due_date,
                            'order_for' => strtoupper($order[0]),
                            'status' => 'Requested',
                            'whatsapp_status' => '0',
                            'attendance_generate_status' => '0',
                            'remark' => 'Berbayar',
                            'created_by' => Auth::id(),
                        ]
                    );
                    $live_cooking->save();

                    $empys = Employee::where('employee_id', strtoupper($order[0]))->first();
                    $empys->live_cooking = 1;
                    $empys->save();

                    $count_menu++;
                }

                $updateQuota = CanteenLiveCookingMenu::where('due_date', $due_date)->first();
                $updateQuota->serving_ordered = $updateQuota->serving_ordered + $count_menu;
                $updateQuota->serving_ordered_pay = $updateQuota->serving_ordered_pay + $count_menu;
                $updateQuota->save();

                $response = array(
                    'status' => true,
                    'message' => 'Order berhasil dibuat.',
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

    public function inputLiveCookingOrderExtra(Request $request)
    {
        try {
            // $quotas = CanteenLiveCookingMenu::where('due_date',$request->get('due_date'))->first();
            // if ($quotas->serving_ordered < $quotas->serving_ordered ) {

            $order_lists = $request->get('order_list');
            $order_by = $request->get('order_by');
            $due_date = $request->get('due_date');

            for ($i = 0; $i < count($order_lists); $i++) {
                $order = explode("_", $order_lists[$i]);
                $live_cooking = CanteenLiveCooking::create(
                    [
                        'order_by' => strtoupper($request->get('order_by')),
                        'due_date' => $due_date,
                        'order_for' => strtoupper($order[0]),
                        'status' => 'Confirmed',
                        'whatsapp_status' => '0',
                        'attendance_generate_status' => '0',
                        'remark' => 'Additional',
                        'created_by' => Auth::id(),
                    ]
                );
                $live_cooking->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Extra Order berhasil dibuat.',
            );
            return Response::json($response);
            // }
            // else{
            //     $response = array(
            //         'status' => false,
            //         'message' => 'Order Anda pada tanggal '.$request->get('quota').' telah melebihi kuota.',
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

    public function randomLiveCooking(Request $request)
    {
        try {
            $periode = $request->get('menuDateRandom');
            $dateFrom = $request->get('dateFromRandom');
            $dateTo = $request->get('dateToRandom');

            if ($periode != date('Y-m')) {
                $empys = Employee::get();
                for ($i = 0; $i < count($empys); $i++) {
                    $empedit = Employee::where('id', $empys[$i]->id)->first();
                    $empedit->live_cooking = 0;
                    $empedit->save();
                }
            }

            if ($dateFrom <= date('Y-m-d')) {
                $response = array(
                    'status' => false,
                    'message' => 'Tanggal Random harus lebih dari tanggal ' . date('Y-m-d'),
                );
                return Response::json($response);
            } else {
                $menus = CanteenLiveCookingMenu::where('periode', $periode)->where('due_date', '>=', $dateFrom)->where('due_date', '<=', $dateTo)->get();
                foreach ($menus as $val) {
                    $countMenu = 0;
                    $datanow = CanteenLiveCooking::where('due_date', $val->due_date)->get();
                    if (count($datanow) > 0) {
                        foreach ($datanow as $keys) {
                            $empys = Employee::where('employee_id', $keys->order_for)->first();
                            $empys->live_cooking = 0;
                            $empys->save();

                            CanteenLiveCooking::where('id', $keys->id)->forceDelete();
                        }
                    }
                    $emp = DB::SELECT("SELECT
                        employees.id,
                        employee_syncs.employee_id,
                        employee_syncs.`name`,
                        employee_syncs.`phone`,
                        sunfish_shift_syncs.shiftdaily_code,
                        attend_code
                        FROM
                        employee_syncs
                        LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id
                        LEFT JOIN sunfish_shift_syncs ON sunfish_shift_syncs.employee_id = employee_syncs.employee_id
                        WHERE
                        employees.live_cooking = 0
                        AND employee_syncs.end_date IS NULL
                        AND employees.remark != 'Jps'
                        AND sunfish_shift_syncs.shift_date = '" . $val->due_date . "'
                        AND shiftdaily_code LIKE '%Shift_1%'
                        AND ( attend_code LIKE '%PRS%' OR attend_code LIKE '%ABS%' OR attend_code IS NULL )
                        ORDER BY
                        RAND()
                        LIMIT " . $val->serving_quota . "");

                    foreach ($emp as $key) {
                        $live_cooking = CanteenLiveCooking::create(
                            [
                                'order_by' => $key->employee_id,
                                'due_date' => $val->due_date,
                                'order_for' => $key->employee_id,
                                'status' => 'Confirmed',
                                'whatsapp_status' => '0',
                                'attendance_generate_status' => '0',
                                'created_by' => Auth::id(),
                            ]
                        );
                        $live_cooking->save();

                        $empys = Employee::where('id', $key->id)->first();
                        $empys->live_cooking = 1;
                        $empys->save();

                        $countMenu++;
                    }

                    $updateQuota = CanteenLiveCookingMenu::where('id', $val->id)->first();
                    $updateQuota->serving_ordered = $countMenu;
                    $updateQuota->save();
                }
                $response = array(
                    'status' => true,
                    'message' => 'Success Randomize',
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

    public function editLiveCookingOrder(Request $request)
    {
        try {
            if ($request->get('status') == 'edit') {
                $orders = CanteenLiveCooking::where('due_date', $request->get('due_date'))->get();
                $quota = CanteenLiveCookingMenu::where('due_date', $request->get('due_date'))->first();
                $live_cooking = CanteenLiveCooking::where('id', $request->get('id'))->first();
                $check = CanteenLiveCooking::where('due_date', $request->get('due_date'))->where('order_for', $request->get('order_for'))->first();
                if ($check != null) {
                    $status = false;
                    $message = 'Karyawan sudah ada di list.';
                } else {
                    if ($live_cooking->due_date == $request->get('due_date')) {

                        if ($live_cooking->remark == null) {
                            $emplama = Employee::where('employee_id', $live_cooking->order_for)->first();
                            $emplama->live_cooking = 0;
                            $emplama->save();

                            $empbaru = Employee::where('employee_id', $request->get('order_for'))->first();
                            $empbaru->live_cooking = 1;
                            $empbaru->save();
                            // $live_cooking->order_by = $request->get('order_by');
                        }
                        $attendance = GeneralAttendance::where('purpose_code', 'Live Cooking')->where('due_date', $live_cooking->due_date)->where('employee_id', $live_cooking->order_for)->first();
                        if ($attendance != null) {
                            $attendance->employee_id = $request->get('order_for');
                            $attendance->save();
                        }
                        $live_cooking->order_for = $request->get('order_for');
                        $live_cooking->save();
                        $status = true;
                        $message = 'Update Data Berhasil';
                    } else {
                        if ($quota->serving_ordered < $quota->serving_quota) {

                            if ($live_cooking->remark == null) {
                                // $live_cooking->order_by = $request->get('order_by');
                                $emplama = Employee::where('employee_id', $live_cooking->order_for)->first();
                                $emplama->live_cooking = 0;
                                $emplama->save();

                                $empbaru = Employee::where('employee_id', $request->get('order_for'))->first();
                                $empbaru->live_cooking = 1;
                                $empbaru->save();

                                $quotalama = CanteenLiveCookingMenu::where('due_date', $live_cooking->due_date)->first();
                                $quotalama->serving_ordered = $quotalama->serving_ordered - 1;
                                $quotalama->save();

                                $quota->serving_ordered = $quota->serving_ordered + 1;
                                $quota->save();
                            }
                            $attendance = GeneralAttendance::where('purpose_code', 'Live Cooking')->where('due_date', $live_cooking->due_date)->where('employee_id', $live_cooking->order_for)->first();
                            if ($attendance != null) {
                                $attendance->due_date = $request->get('due_date');
                                $attendance->employee_id = $request->get('order_for');
                                $attendance->save();
                            }
                            $live_cooking->order_for = $request->get('order_for');
                            $live_cooking->due_date = $request->get('due_date');
                            $live_cooking->save();

                            $status = true;
                            $message = 'Update Data Berhasil';
                        } else {
                            $status = false;
                            $message = 'Kuota pada tanggal ' . date('d F Y', strtotime($request->get('due_date'))) . ' telah penuh.';
                        }
                    }
                }
            } else {
                $live_cooking = CanteenLiveCooking::where('id', $request->get('id'))->first();

                $quota = CanteenLiveCookingMenu::where('due_date', $live_cooking->due_date)->first();
                $quota->serving_ordered = $quota->serving_ordered - 1;
                if ($live_cooking->remark == 'Berbayar') {
                    $quota->serving_ordered_pay = $quota->serving_ordered_pay - 1;
                }
                $quota->save();

                $attendance = GeneralAttendance::where('purpose_code', 'Live Cooking')->where('due_date', $live_cooking->due_date)->where('employee_id', $live_cooking->order_for)->first();
                if ($attendance != null) {
                    $attendance->forceDelete();
                }
                $live_cooking->forceDelete();
                $status = true;
                $message = 'Delete Data Berhasil';
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

    public function detailLiveCooking(Request $request)
    {
        try {

            $datas = CanteenLiveCooking::select(DB::RAW('DATE_FORMAT(DATE(NOW()),"%Y-%m-%d") as date_now'), 'canteen_live_cookings.id as id_live', 'canteen_live_cookings.*', 'employee_syncs.*', 'canteen_live_cooking_menus.*', 'canteen_live_cookings.remark as additional', 'departments.department_shortname')
                ->where('canteen_live_cookings.due_date', $request->get('due_date'))
                ->join('employee_syncs', 'employee_syncs.employee_id', 'canteen_live_cookings.order_for')
                ->join('departments', 'departments.department_name', 'employee_syncs.department')
                ->join('canteen_live_cooking_menus', 'canteen_live_cooking_menus.due_date', 'canteen_live_cookings.due_date')
                ->get();

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

    public function reportLiveCooking(Request $request)
    {
        try {
            $datefrom = $request->get('datefrom');
            $dateto = $request->get('dateto');
            if ($request->get('month') == '') {
                # code...
            }

            if ($datefrom == '') {
                $datefrom = date('Y-m-d');
            } else {
                $datefrom = $request->get('datefrom');
            }

            if ($dateto == '') {
                $dateto = date('Y-m-d');
            } else {
                $dateto = $request->get('dateto');
            }

            $datas = CanteenLiveCooking::select('canteen_live_cookings.id as id_live', 'canteen_live_cookings.*', 'employee_syncs.*', 'canteen_live_cooking_menus.*', 'general_attendances.attend_date', 'canteen_live_cookings.remark as additional')
            // ->where('canteen_live_cookings.due_date',$request->get('due_date'))
                ->join('employee_syncs', 'employee_syncs.employee_id', 'canteen_live_cookings.order_for')
                ->join('canteen_live_cooking_menus', 'canteen_live_cooking_menus.due_date', 'canteen_live_cookings.due_date')
                ->leftjoin("general_attendances", function ($join) {
                    $join->on("general_attendances.employee_id", "=", "canteen_live_cookings.order_for")
                        ->on("general_attendances.due_date", "=", "canteen_live_cookings.due_date");
                })
                ->where('canteen_live_cookings.due_date', '>=', $datefrom)
                ->where('canteen_live_cookings.due_date', '<=', $dateto)
                ->orderby('general_attendances.attend_date', 'desc')
                ->get();

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

    public function reportLiveCookingPay(Request $request)
    {
        try {
            $month = date('Y-m');
            if ($request->get('month') != '') {
                $month = $request->get('month');
            }

            $calendar = WeeklyCalendar::where(DB::RAW('DATE_FORMAT(week_date,"%Y-%m")'), $month)->select('weekly_calendars.*', DB::RAW("DATE_FORMAT(week_date,'%d') as dates"))->get();

            if (str_contains(Auth::user()->role_code, 'GA') || str_contains(Auth::user()->role_code, 'MIS')) {
                $report = CanteenLiveCooking::where('remark', 'Berbayar')->where(DB::RAW("DATE_FORMAT(due_date,'%Y-%m')"), $month)->where('status', 'Confirmed')->get();
            } else {
                $report = CanteenLiveCooking::where('remark', 'Berbayar')->where(DB::RAW("DATE_FORMAT(due_date,'%Y-%m')"), $month)->where('order_by', strtoupper(Auth::user()->username))->where('status', 'Confirmed')->get();
            }

            $emp = EmployeeSync::get();

            $response = array(
                'status' => true,
                'calendar' => $calendar,
                'report' => $report,
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

    // CANTEEN ORDER -> PR PO RECEIVE //
    // CANTEEN ORDER -> PR PO RECEIVE //
    // CANTEEN ORDER -> PR PO RECEIVE //
    // CANTEEN ORDER -> PR PO RECEIVE //

    public function canteen_purchase_requisition()
    {
        $title = 'Purchase Requisition Canteen';
        $title_jp = '食堂の購入依頼';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
            ->first();

        $staff = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
         where end_date is null and (position like '%Staff%')");

        $items = db::select("select kode_item, kategori, deskripsi from canteen_items where deleted_at is null");
        $dept = db::select("select department_name from departments where deleted_at is null");

        return view('general_affairs.report.canteen_purchase_requisition', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'items' => $items,
            'dept' => $dept,
            'staff' => $staff,
            'uom' => $this->uom,
        ))
            ->with('page', 'Purchase Requisition Canteen')
            ->with('head', 'PR');
    }

    public function fetch_canteen_purchase_requisition(Request $request)
    {
        $tanggal = "";
        $restrict_dept = "";

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and A.submission_date >= '" . $datefrom . " 00:00:00' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and A.submission_date  <= '" . $dateto . " 23:59:59' ";
            }
        }

        //Get Employee Department
        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('department')
            ->first();

        if (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'ACC') || str_contains(Auth::user()->role_code, 'PCH') || strpos($emp_dept->department, 'Procurement Department') !== false || strpos($emp_dept->department, 'Purchasing Control Department') !== false) {
            $restrict_dept = "";
        } else {
            $restrict_dept = "and department like '%" . $emp_dept->department . "%'";
        }

        $qry = "SELECT  * FROM canteen_purchase_requisitions A WHERE A.deleted_at IS NULL " . $tanggal . "" . $restrict_dept . " order by A.id DESC";

        $pr = DB::select($qry);

        return DataTables::of($pr)
            ->editColumn('submission_date', function ($pr) {
                return date('d M Y', strtotime($pr->submission_date));
            })
            ->editColumn('note', function ($pr) {
                $note = "";
                if ($pr->note != null) {
                    $note = $pr->note;
                } else {
                    $note = '-';
                }

                return $note;
            })
            ->editColumn('status', function ($pr) {
                $id = $pr->id;

                if ($pr->posisi == "user" && $pr->status == "approval") {
                    return '<label class="label label-danger">Not Sent</a>';
                } else if ($pr->posisi == "manager" && $pr->status == "approval") {
                    return '<label class="label label-warning">Approval Manager</a>';
                } else if ($pr->posisi == "dgm" && $pr->status == "approval") {
                    return '<label class="label label-warning">Approval DGM</a>';
                } else if ($pr->posisi == "gm" && $pr->status == "approval") {
                    return '<label class="label label-warning">Approval GM</a>';
                } else if ($pr->status == "approval_acc") {
                    return '<label class="label label-info">Diverifikasi Purchasing</a>';
                } else if ($pr->status == "received") {
                    return '<label class="label label-success">Diterima Purchasing</a>';
                }

            })
            ->addColumn('action', function ($pr) {
                $id = $pr->id;
                if ($pr->posisi == "user" && $pr->status == "approval") {
                    return '
                <button class="btn btn-xs btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;"  onclick="sendEmail(' . $id . ')"><i class="fa fa-envelope"></i> Send Email</button>
                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="editPR(' . $id . ')" data-toggle="tooltip" title="Edit PR"><i class="fa fa-edit"></i> Edit PR</a>
                <a href="purchase_requisition/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
                <a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="deleteConfirmationPR(' . $id . ')" data-toggle="modal" data-target="#modalDeletePR"  title="Delete PR"><i class="fa fa-trash"></i> Delete PR</a>
                ';
                } else {
                    if ($pr->status == "approval") {
                        return '<a href="purchase_requisition/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a> <button class="btn btn-xs btn-primary" data-toggle="tooltip" title="Resend Email" style="margin-right:5px;"  onclick="ResendEmail(' . $id . ')"><i class="fa fa-envelope"></i> Email Reminder</button>';
                    } else {
                        return '
                        <a href="purchase_requisition/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a><button class="btn btn-xs btn-info" data-toggle="tooltip" title="Tracing Item" style="margin-right:5px;"  onclick="tracing('.$id.')"><i class="fa fa-search"></i> Tracing</button>
                    ';
                    }
                }

            })
            ->editColumn('file', function ($pr) {

                $data = json_decode($pr->file);

                $fl = "";

                if ($pr->file != null) {
                    for ($i = 0; $i < count($data); $i++) {
                        $fl .= '<a href="../files/pr_kantin/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
                    }
                } else {
                    $fl = '-';
                }

                return $fl;
            })
            ->rawColumns(['status' => 'status', 'action' => 'action', 'file' => 'file'])
            ->make(true);
    }

    public function pr_resend_email(Request $request)
    {
        $pr = CanteenPurchaseRequisition::find($request->get('id'));

        try {
            if ($pr->posisi == "manager") {
                $mails = "select distinct email from canteen_purchase_requisitions join users on canteen_purchase_requisitions.manager = users.username where canteen_purchase_requisitions.id = " . $request->get('id');
                $mailtoo = DB::select($mails);
            } else if ($pr->posisi == "gm") {
                $mails = "select distinct email from canteen_purchase_requisitions join users on canteen_purchase_requisitions.gm = users.username where canteen_purchase_requisitions.id = " . $request->get('id');
                $mailtoo = DB::select($mails);
            }

            $isimail = "select canteen_purchase_requisitions.*,canteen_purchase_requisition_items.item_desc, canteen_purchase_requisition_items.item_request_date, canteen_purchase_requisition_items.item_qty, canteen_purchase_requisition_items.item_uom, canteen_purchase_requisition_items.item_price, canteen_purchase_requisition_items.item_amount FROM canteen_purchase_requisitions join canteen_purchase_requisition_items on canteen_purchase_requisitions.no_pr = canteen_purchase_requisition_items.no_pr where canteen_purchase_requisitions.id = " . $request->get('id');
            $purchaserequisition = db::select($isimail);

            Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($purchaserequisition, 'canteen_purchase_requisition'));

            $response = array(
                'status' => true,
                'datas' => "Berhasil",
            );

            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'datas' => "Gagal",
            );
            return Response::json($response);
        }
    }


    public function tracing_purchase_requisition(Request $request)
    {
        $purchase_requistion_item = CanteenPurchaseRequisition::select('canteen_purchase_requisition_items.*','canteen_budget_histories.status as status_pr','canteen_budget_histories.po_number','canteen_receives.date_receive as tanggal_diterima')
        ->leftJoin('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
        ->leftJoin('canteen_budget_histories', function($join) {
         $join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
         $join->on('canteen_budget_histories.no_item','=', 'canteen_purchase_requisition_items.item_desc');
        })
        ->leftJoin('canteen_receives', function($join) {
         $join->on('canteen_receives.no_po', '=', 'canteen_budget_histories.po_number');
         $join->on('canteen_receives.nama_item','=', 'canteen_budget_histories.no_item');
        })
        ->where('canteen_purchase_requisitions.id', '=', $request->get('id'))
        ->get();

        $response = array(
            'status' => true,
            'purchase_requisition_item' => $purchase_requistion_item
        );
        return Response::json($response);
    }

    public function fetch_item_list(Request $request)
    {
        $items = CanteenItem::select('kode_item', 'deskripsi')
            ->get();

        $response = array(
            'status' => true,
            'item' => $items,
        );
        return Response::json($response);
    }

    public function prgetitemdesc(Request $request)
    {
        $html = array();
        $kode_item = CanteenItem::where('kode_item', $request->kode_item)
            ->get();
        foreach ($kode_item as $item) {
            $html = array(
                'deskripsi' => $item->deskripsi,
                'uom' => $item->uom,
                'price' => $item->harga,
                'currency' => $item->currency,
                'moq' => $item->moq,
            );

        }

        return json_encode($html);
    }

    //==================================//
    //            Master Item           //
    //==================================//
    public function master_item()
    {
        $title = 'Food Item';
        $title_jp = '';

        $item_categories = CanteenItemCategory::select('canteen_item_categories.*')->whereNull('canteen_item_categories.deleted_at')
            ->get();

        return view('general_affairs.report.canteen_purchase_item', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'uom' => $this->uom,
            'item_category' => $item_categories,
        ))->with('page', 'Food Item')
            ->with('head', 'Food Item');
    }

    public function fetch_item(Request $request)
    {
        $items = CanteenItem::select('canteen_items.id', 'canteen_items.kode_item', 'canteen_items.kategori', 'canteen_items.deskripsi', 'canteen_items.uom', 'canteen_items.harga', 'canteen_items.currency');

        if ($request->get('keyword') != null) {
            $items = $items->where('deskripsi', 'like', '%' . $request->get('keyword') . '%');
        }

        if ($request->get('category') != null) {
            $items = $items->where('canteen_items.kategori', $request->get('category'));
        }

        if ($request->get('uom') != null) {
            $items = $items->whereIn('canteen_items.uom', $request->get('uom'));
        }

        $items = $items->orderBy('canteen_items.id', 'ASC')
            ->get();

        return DataTables::of($items)
            ->addColumn('action', function ($items) {
                $id = $items->id;

                if (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'PCH')) {
                    return '
                <a href="purchase_item/update/' . $id . '" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Edit</a>
                <a href="purchase_item/delete/' . $id . '" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</a>
                ';
                } else {
                    return '-';
                }
            })
            ->addColumn('image', function ($items) {
                $item_code = $items->kode_item;

                if (file_exists(public_path() . '/images/purchase_item/' . $item_code . '.jpg')) {
                    return '<img src="' . url('images/purchase_item') . '/' . $item_code . '.jpg" width="250">';
                } else if (file_exists(public_path() . '/images/purchase_item/' . $item_code . '.png')) {
                    return '<img src="' . url('images/purchase_item') . '/' . $item_code . '.png" width="250">';
                } else {
                    return '-';
                }

            })
            ->rawColumns(['action' => 'action', 'image' => 'image'])
            ->make(true);
    }

    public function create_item()
    {
        $title = 'Create Item';
        $title_jp = '購入アイテムを作成';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
            ->first();

        $item_categories = CanteenItemCategory::select('canteen_item_categories.*')->whereNull('canteen_item_categories.deleted_at')
            ->get();

        return view('general_affairs.report.canteen_create_purchase_item', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'item_category' => $item_categories,
            'uom' => $this->uom,
        ))
            ->with('page', 'Purchase Item');
    }

    public function create_item_post(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $item = CanteenItem::create([
                'kode_item' => $request->get('item_code'),
                'kategori' => $request->get('item_category'),
                'deskripsi' => $request->get('item_desc'),
                'uom' => $request->get('item_uom'),
                'harga' => $request->get('item_price'),
                'currency' => $request->get('item_currency'),
                'created_by' => $id_user,
            ]);

            $item->save();

            $response = array(
                'status' => true,
                'datas' => "Berhasil",
                'id' => $item->id,
            );
            return Response::json($response);

        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function update_item($id)
    {
        $title = 'Edit Food Item';
        $title_jp = '購入アイテムを編集';

        $item = CanteenItem::find($id);

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
            ->first();

        $item_categories = CanteenItemCategory::select('canteen_item_categories.*')->whereNull('canteen_item_categories.deleted_at')
            ->get();

        return view('general_affairs.report.canteen_edit_purchase_item', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'item' => $item,
            'employee' => $emp,
            'item_category' => $item_categories,
            'uom' => $this->uom,
        ))
            ->with('page', 'Purchase Item');
    }

    public function update_item_post(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $inv = CanteenItem::where('id', $request->get('id'))
                ->update([
                    'kode_item' => $request->get('item_code'),
                    'kategori' => $request->get('item_category'),
                    'deskripsi' => $request->get('item_desc'),
                    'uom' => $request->get('item_uom'),
                    'harga' => $request->get('item_price'),
                    'currency' => $request->get('item_currency'),
                    'created_by' => $id_user,
                ]);

            $response = array(
                'status' => true,
                'datas' => "Berhasil",
            );
            return Response::json($response);

        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function delete_item($id)
    {
        $items = CanteenItem::find($id);
        $items->delete();

        return redirect('/canteen/purchase_item')
            ->with('status', 'Food Item has been deleted.')
            ->with('page', 'Food Item');
    }

    public function get_kode_item(Request $request)
    {
        $kategori = $request->kategori;

        $query = "SELECT kode_item FROM `canteen_items` where kategori='$kategori' order by kode_item DESC LIMIT 1";
        $nomorurut = DB::select($query);

        if ($nomorurut != null) {
            $nomor = substr($nomorurut[0]->kode_item, -3);
            $nomor = $nomor + 1;
            $nomor = sprintf('%03d', $nomor);

        } else {
            $nomor = "001";
        }

        $result['no_urut'] = $nomor;

        return json_encode($result);
    }

    //==================================//
    //       Create Item Category       //
    //==================================//
    public function create_item_category()
    {
        $title = 'Create Item Category';
        $title_jp = '購入アイテムの種類を作成';

        return view('general_affairs.report.canteen_create_category_item', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Food Item');
    }

    public function create_item_category_post(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $item_category = CanteenItemCategory::create([
                'category_id' => $request->get('category_id'),
                'category_name' => $request->get('category_name'),
                'created_by' => $id_user,
            ]);

            $item_category->save();

            $response = array(
                'status' => true,
                'datas' => "Berhasil",
            );
            return Response::json($response);
        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function get_nomor_pr(Request $request)
    {
        $datenow = date('Y-m-d');
        $tahun = date('y');
        $bulan = date('m');

        $query = "SELECT no_pr FROM `canteen_purchase_requisitions` where DATE_FORMAT(submission_date, '%y') = '$tahun' and month(submission_date) = '$bulan' order by id DESC LIMIT 1";
        $nomorurut = DB::select($query);

        if ($nomorurut != null) {
            $nomor = substr($nomorurut[0]->no_pr, -3);
            $nomor = $nomor + 1;
            $nomor = sprintf('%03d', $nomor);
        } else {
            $nomor = "001";
        }

        $result['tahun'] = $tahun;
        $result['bulan'] = $bulan;
        $result['dept'] = 'CA';
        $result['no_urut'] = $nomor;

        return json_encode($result);
    }

    public function create_purchase_requisition(Request $request)
    {
        $id = Auth::id();

        $lop = $request->get('lop');

        try
        {
            $staff = null;
            $manager = null;
            $manager_name = null;
            $posisi = null;
            $gm = null;

            //Jika GA pak arief
            if ($request->get('department') == "General Affairs Department") {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Human Resources Department' and position like 'manager%'");
            } else {
                // Get Manager
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '" . $request->get('department') . "' and position like 'manager%'");
            }

            if ($manag != null) {
                $posisi = "user";

                foreach ($manag as $mg) {
                    $manager = $mg->employee_id;
                    $manager_name = $mg->name;
                }
            } else {
                $posisi = "user";
            }

            //Cek File
            $files = array();
            $file = new CanteenPurchaseRequisition();
            if ($request->file('reportAttachment') != null) {
                if ($files = $request->file('reportAttachment')) {
                    foreach ($files as $file) {
                        $nama = $file->getClientOriginalName();
                        $file->move('files/pr_kantin', $nama);
                        $data[] = $nama;
                    }
                }
                $file->filename = json_encode($data);
            } else {
                $file->filename = null;
            }

            if ($request->get('department') == "Human Resources Department" || $request->get('department') == "General Affairs Department") {
                //GM Pak Arief
                $getgm = EmployeeSync::select('employee_id', 'name', 'position')
                    ->where('employee_id', '=', 'PI9709001')
                    ->first();

                $gm = $getgm->employee_id;
            }
            //if accounting maka GM Pak IDA
            else if ($request->get('department') == "Accounting Department") {
                $gm = $this->gm_acc;
            }
            //Selain Itu GM Pak Budhi
            else {
                $gm = $this->dgm;
            }

            $data = new CanteenPurchaseRequisition([
                'no_pr' => $request->get('no_pr'),
                'emp_id' => $request->get('emp_id'),
                'emp_name' => $request->get('emp_name'),
                'department' => $request->get('department'),
                'section' => $request->get('section'),
                'submission_date' => $request->get('submission_date'),
                'note' => $request->get('note'),
                'file' => $file->filename,
                'file_pdf' => 'PR' . $request->get('no_pr') . '.pdf',
                'posisi' => $posisi,
                'status' => 'approval',
                'no_budget' => $request->get('budget_no'),
                'staff' => $staff,
                'manager' => $manager,
                'manager_name' => $manager_name,
                'gm' => $gm,
                'created_by' => $id,
            ]);

            $data->save();

            for ($i = 1; $i <= $lop; $i++) {
                $item_code = "item_code" . $i;
                $item_desc = "item_desc" . $i;
                $item_request_date = "req_date" . $i;
                $item_currency = "item_currency" . $i;
                $item_currency_text = "item_currency_text" . $i;
                $item_price = "item_price" . $i;
                $item_qty = "qty" . $i;
                $item_uom = "uom" . $i;
                $item_amount = "amount" . $i;
                $status = "";
                if ($request->get($item_code) == "kosong") {
                    $request->get($item_code) == "";
                }

                if ($request->get($item_code) != null) {
                    $status = "fixed";
                } else {
                    $status = "sementara";
                }

                if ($request->get($item_currency) != "") {
                    $current = $request->get($item_currency);
                } else if ($request->get($item_currency_text) != "") {
                    $current = $request->get($item_currency_text);
                }

                $data2 = new CanteenPurchaseRequisitionItem([
                    'no_pr' => $request->get('no_pr'),
                    'item_code' => $request->get($item_code),
                    'item_desc' => $request->get($item_desc),
                    'item_request_date' => $request->get($item_request_date),
                    'item_currency' => $current,
                    'item_price' => $request->get($item_price),
                    'item_qty' => $request->get($item_qty),
                    'item_uom' => $request->get($item_uom),
                    'item_amount' => $request->get($item_amount),
                    'created_by' => $id,
                ]);

                $data2->save();

                $dollar = "konversi_dollar" . $i;

                $getbulan = AccBudget::select('budget_no', 'periode')
                    ->where('budget_no', $request->get('budget_no'))
                    ->first();

                if ($getbulan->periode == "FY200") {
                    $month = strtolower(date('M'));
                } else {
                    $month = "apr";
                }

                $data3 = new CanteenBudgetHistory([
                    'budget' => $request->get('budget_no'),
                    'budget_month' => $month,
                    'budget_date' => date('Y-m-d'),
                    'category_number' => $request->get('no_pr'),
                    'no_item' => $request->get($item_desc),
                    'beg_bal' => $request->get('budget'),
                    'amount' => $request->get($dollar),
                    'status' => 'PR',
                    'created_by' => $id,
                ]);

                $data3->save();
            }

            $totalPembelian = $request->get('TotalPembelian');
            if ($totalPembelian != null) {
                $getbulan = AccBudget::select('budget_no', 'periode')
                    ->where('budget_no', $request->get('budget_no'))
                    ->first();

                if ($getbulan->periode == "FY200") {
                    $bulan = strtolower(date('M'));
                    $fiscal = "FY200";
                } else {
                    $bulan = "apr";
                    $fiscal = "FY201";
                }

                $sisa_bulan = $bulan . '_sisa_budget';
                //get Data Budget Based On Periode Dan Nomor
                $budget = AccBudget::where('budget_no', '=', $request->get('budget_no'))->first();
                //perhitungan
                $total = $budget->$sisa_bulan - $totalPembelian;

                if ($total < 0) {
                    return false;
                }

                $dataupdate = AccBudget::where('budget_no', $request->get('budget_no'))->update([
                    $sisa_bulan => $total,
                ]);
            }

            $detail_pr = CanteenPurchaseRequisition::select('canteen_purchase_requisitions.*', 'canteen_purchase_requisition_items.*', 'canteen_budget_histories.beg_bal', 'canteen_budget_histories.amount', DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = canteen_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
                ->leftJoin('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
                ->join('canteen_budget_histories', function ($join) {
                    $join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
                    $join->on('canteen_budget_histories.no_item', '=', 'canteen_purchase_requisition_items.item_desc');
                })
                ->where('canteen_purchase_requisitions.id', '=', $data->id)
                ->distinct()
                ->get();

            $exchange_rate = AccExchangeRate::select('*')
                ->where('periode', '=', date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
                ->where('currency', '!=', 'USD')
                ->orderBy('currency', 'ASC')
                ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('general_affairs.report.report_pr', array(
                'pr' => $detail_pr,
                'rate' => $exchange_rate,
            ));

            $pdf->save(public_path() . "/kantin/pr_list/PR" . $detail_pr[0]->no_pr . ".pdf");

            return redirect('/canteen/purchase_requisition')->with('status', 'PR Berhasil Dibuat')
                ->with('page', 'Purchase Requisition Canteen');
        } catch (QueryException $e) {
            return redirect('/canteen/purchase_requisition')->with('error', $e->getMessage())
                ->with('page', 'Purchase Requisition Canteen');
        }
    }

    public function edit_purchase_requisition(Request $request)
    {
        $purchase_requistion = CanteenPurchaseRequisition::find($request->get('id'));
        $purchase_requistion_item = CanteenPurchaseRequisition::select('canteen_purchase_requisition_items.*', 'canteen_budget_histories.budget', 'canteen_budget_histories.budget_month', 'canteen_budget_histories.budget_date', 'canteen_budget_histories.category_number', 'canteen_budget_histories.no_item', 'canteen_budget_histories.amount', 'canteen_budget_histories.beg_bal')
            ->join('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
            ->join('canteen_budget_histories', function ($join) {
                $join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
                $join->on('canteen_budget_histories.no_item', '=', 'canteen_purchase_requisition_items.item_desc');
            })
            ->where('canteen_purchase_requisitions.id', '=', $request->get('id'))
            ->whereNull('canteen_purchase_requisition_items.sudah_po')
            ->get();

        $response = array(
            'status' => true,
            'purchase_requisition' => $purchase_requistion,
            'purchase_requisition_item' => $purchase_requistion_item,
        );
        return Response::json($response);
    }

    public function update_purchase_requisition(Request $request)
    {
        $id = Auth::id();
        $lop2 = $request->get('lop2');
        $lop = explode(',', $request->get('looping'));
        try
        {
            foreach ($lop as $lp) {
                $item_code = "item_code_edit" . $lp;
                $item_desc = "item_desc_edit" . $lp;
                $item_uom = "uom_edit" . $lp;
                $item_req = "req_date_edit" . $lp;
                $item_qty = "qty_edit" . $lp;
                $item_price = "item_price_edit" . $lp;
                $item_amount = "amount_edit" . $lp;

                // $amount = preg_replace('/[^0-9]/', '', $request->get($item_amount));

                $data2 = CanteenPurchaseRequisitionItem::where('id', $lp)->update([
                    'item_code' => $request->get($item_code),
                    'item_desc' => $request->get($item_desc),
                    'item_uom' => $request->get($item_uom),
                    'item_request_date' => $request->get($item_req),
                    'item_qty' => $request->get($item_qty),
                    'item_price' => $request->get($item_price),
                    'item_amount' => $request->get($item_amount),
                    'created_by' => $id,
                ]);

            }

            for ($i = 2; $i <= $lop2; $i++) {

                $item_code = "item_code" . $i;
                $item_desc = "item_desc" . $i;
                $item_req = "req_date" . $i;
                $item_currency = "item_currency" . $i;
                $item_currency_text = "item_currency_text" . $i;
                $item_price = "item_price" . $i;
                $item_qty = "qty" . $i;
                $item_uom = "uom" . $i;
                $item_amount = "amount" . $i;
                $dollar = "konversi_dollar" . $i;
                $status = "";

                //Jika ada value kosong
                if ($request->get($item_code) == "kosong") {
                    $request->get($item_code) == "";
                }

                //Jika item kosong
                if ($request->get($item_code) != null) {
                    $status = "fixed";
                } else {
                    $status = "sementara";
                }

                if ($request->get($item_currency) != "") {
                    $current = $request->get($item_currency);
                } else if ($request->get($item_currency_text) != "") {
                    $current = $request->get($item_currency_text);
                }

                $data2 = new CanteenPurchaseRequisitionItem([
                    'no_pr' => $request->get('no_pr_edit'),
                    'item_code' => $request->get($item_code),
                    'item_desc' => $request->get($item_desc),
                    'item_request_date' => $request->get($item_req),
                    'item_currency' => $current,
                    'item_price' => $request->get($item_price),
                    'item_qty' => $request->get($item_qty),
                    'item_uom' => $request->get($item_uom),
                    'item_amount' => $request->get($item_amount),
                    'status' => $status,
                    'created_by' => $id,
                ]);

                $data2->save();

                $getbulan = AccBudget::select('budget_no', 'periode')
                    ->where('budget_no', $request->get('no_budget_edit'))
                    ->first();

                if ($getbulan->periode == "FY200") {
                    $bulan = strtolower(date('M'));
                } else {
                    $bulan = "apr";
                }

                $sisa_bulan = $bulan . '_sisa_budget';

                //get Data Budget Based On Periode Dan Nomor
                $budgetdata = AccBudget::where('budget_no', '=', $request->get('no_budget_edit'))->first();

                //Get Amount Di PO
                $total_dollar = $request->get($dollar);

                $totalminusPO = $budgetdata->$sisa_bulan - $total_dollar;

                // Setelah itu update data budgetnya dengan yang actual
                $dataupdate = AccBudget::where('budget_no', $request->get('no_budget_edit'))
                    ->update([
                        $sisa_bulan => $totalminusPO,
                    ]);

                // $month = strtolower(date("M",strtotime($request->get('tgl_pengajuan_edit'))));
                $begbal = $request->get('SisaBudgetEdit') + $request->get('TotalPembelianEdit');

                $getbulan = AccBudget::select('budget_no', 'periode')
                    ->where('budget_no', $request->get('no_budget_edit'))
                    ->first();

                if ($getbulan->periode == "FY200") {
                    $month = strtolower(date('M'));
                } else {
                    $month = "apr";
                }

                $data3 = new CanteenBudgetHistory([
                    'budget' => $request->get('no_budget_edit'),
                    'budget_month' => $month,
                    'budget_date' => date('Y-m-d'),
                    'category_number' => $request->get('no_pr_edit'),
                    'beg_bal' => $begbal,
                    'no_item' => $request->get($item_desc),
                    'amount' => $request->get($dollar),
                    'status' => 'PR',
                    'created_by' => $id,
                ]);

                $data3->save();
            }

            $detail_pr = CanteenPurchaseRequisition::select('canteen_purchase_requisitions.*', 'canteen_purchase_requisition_items.*', 'canteen_budget_histories.beg_bal', 'canteen_budget_histories.amount', DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = canteen_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
                ->leftJoin('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
                ->join('canteen_budget_histories', function ($join) {
                    $join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
                    $join->on('canteen_budget_histories.no_item', '=', 'canteen_purchase_requisition_items.item_desc');
                })
                ->where('canteen_purchase_requisitions.id', '=', $request->get('id_edit_pr'))
                ->distinct()
                ->get();

            $exchange_rate = AccExchangeRate::select('*')
                ->where('periode', '=', date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
                ->where('currency', '!=', 'USD')
                ->orderBy('currency', 'ASC')
                ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('general_affairs.report.report_pr', array(
                'pr' => $detail_pr,
                'rate' => $exchange_rate,
            ));

            $pdf->save(public_path() . "/kantin/pr_list/PR" . $detail_pr[0]->no_pr . ".pdf");

            return redirect('/canteen/purchase_requisition')
                ->with('status', 'Purchase Requisition Berhasil Dirubah')
                ->with('page', 'Purchase Requisition Canteen');
        } catch (QueryException $e) {
            return redirect('/canteen/purchase_requisition')->with('error', $e->getMessage())
                ->with('page', 'Purchase Requisition Canteen');
        }
    }

    public function delete_purchase_requisition(Request $request)
    {
        try
        {
            $pr = CanteenPurchaseRequisition::find($request->get('id'));

            $budget_log = CanteenBudgetHistory::where('category_number', '=', $pr->no_pr)
                ->get();

            foreach ($budget_log as $log) {
                $sisa_bulan = $log->budget_month . '_sisa_budget';
                $budget = AccBudget::where('budget_no', $log->budget)->first();

                $total = $budget->$sisa_bulan + $log->amount; //add total
                $dataupdate = AccBudget::where('budget_no', $log->budget)->update([
                    $sisa_bulan => $total,
                ]);
            }

            $delete_budget_log = CanteenBudgetHistory::where('category_number', '=', $pr->no_pr)->delete();
            $delete_pr_item = CanteenPurchaseRequisitionItem::where('no_pr', '=', $pr->no_pr)->delete();
            $delete_pr = CanteenPurchaseRequisition::where('no_pr', '=', $pr->no_pr)->delete();

            $response = array(
                'status' => true,
                'message' => 'Data Berhasil Dihapus',
            );

            return Response::json($response);
        } catch (QueryException $e) {

            $response = array(
                'status' => true,
                'message' => $e->getMessage(),
            );

            return Response::json($response);

            // return redirect('/canteen/purchase_requisition')->with('error', $e->getMessage())
            // ->with('page', 'Purchase Requisition Canteen');
        }
    }

    public function delete_item_pr(Request $request)
    {
        try
        {
            $master_item = CanteenPurchaseRequisitionItem::find($request->get('id'));

            $budget_log = CanteenBudgetHistory::where('no_item', '=', $master_item->item_desc)
                ->where('category_number', '=', $master_item->no_pr)
                ->first();

            $sisa_bulan = $budget_log->budget_month . '_sisa_budget';

            $budget = AccBudget::where('budget_no', $budget_log->budget)->first();

            $total = $budget->$sisa_bulan + $budget_log->amount; //add total

            $dataupdate = AccBudget::where('budget_no', $budget_log->budget)->update([
                $sisa_bulan => $total,
            ]);

            $delete_budget_log = CanteenBudgetHistory::where('no_item', '=', $master_item->item_desc)
                ->where('category_number', '=', $master_item->no_pr)
                ->delete();

            $delete_item = CanteenPurchaseRequisitionItem::where('id', '=', $request->get('id'))->delete();

            $response = array(
                'status' => true,
            );

            return Response::json($response);

        } catch (QueryException $e) {
            return redirect('/canteen/purchase_requisition')->with('error', $e->getMessage())
                ->with('page', 'Purchase Requisition Canteen');
        }

    }

    //==================================//
    //          Report PR               //
    //==================================//
    public function report_purchase_requisition($id)
    {

        $detail_pr = CanteenPurchaseRequisition::select('canteen_purchase_requisitions.*', 'canteen_purchase_requisition_items.*', 'canteen_budget_histories.beg_bal', 'canteen_budget_histories.amount', DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = canteen_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
            ->leftJoin('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
            ->join('canteen_budget_histories', function ($join) {
                $join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
                $join->on('canteen_budget_histories.no_item', '=', 'canteen_purchase_requisition_items.item_desc');
            })
            ->where('canteen_purchase_requisitions.id', '=', $id)
            ->distinct()
            ->get();

        $exchange_rate = AccExchangeRate::select('*')
            ->where('periode', '=', date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
            ->where('currency', '!=', 'USD')
            ->orderBy('currency', 'ASC')
            ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('general_affairs.report.report_pr', array(
            'pr' => $detail_pr,
            'rate' => $exchange_rate,
        ));

        $path = "kantin/pr_list/" . $detail_pr[0]->no_pr . ".pdf";
        return $pdf->stream("PR" . $detail_pr[0]->no_pr . ".pdf");

        // return view('general_affairs.report.report_pr', array(
        //  'pr' => $detail_pr,
        // ))->with('page', 'PR')->with('head', 'PR List');
    }

    public function pr_send_email(Request $request)
    {
        $pr = CanteenPurchaseRequisition::find($request->get('id'));

        try {
            if ($pr->posisi == "user") {
                //ke manager
                $mails = "select distinct email from canteen_purchase_requisitions join users on canteen_purchase_requisitions.manager = users.username where canteen_purchase_requisitions.id = " . $request->get('id');
                $mailtoo = DB::select($mails);
                $pr->posisi = "manager";
            }

            $pr->save();

            $isimail = "select canteen_purchase_requisitions.*,canteen_purchase_requisition_items.item_desc, canteen_purchase_requisition_items.item_request_date, canteen_purchase_requisition_items.item_qty, canteen_purchase_requisition_items.item_uom, canteen_purchase_requisition_items.item_price, canteen_purchase_requisition_items.item_amount FROM canteen_purchase_requisitions join canteen_purchase_requisition_items on canteen_purchase_requisitions.no_pr = canteen_purchase_requisition_items.no_pr where canteen_purchase_requisitions.id = " . $request->get('id');
            $purchaserequisition = db::select($isimail);

            Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($purchaserequisition, 'canteen_purchase_requisition'));

            $response = array(
                'status' => true,
                'datas' => "Berhasil",
            );

            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'datas' => "Gagal",
            );
            return Response::json($response);
        }
    }

    public function indexUniformStock()
    {
        $uniform = UniformStock::get();

        return view('general_affairs.uniform_stock', array(
            'uniforms' => $uniform,
        ))->with('page', 'uniform');
    }

    public function fetchUniformStock()
    {
        $resume = UniformStock::whereNull('deleted_at')
            ->select('gender', 'category', 'size', 'qty')
            ->get();

        $response = array(
            'status' => true,
            'resume' => $resume,
        );
        return Response::json($response);
    }

    public function fetchUniformLog()
    {
        $uniform_data = db::select("SELECT uniform_logs.* FROM uniform_logs order by id desc ");

        $response = array(
            'status' => true,
            'uniform_data' => $uniform_data,
        );
        return Response::json($response);
    }

    public function editUniformStock(Request $request)
    {

        $detail = UniformStock::whereNull('deleted_at')
            ->where('uniform_stocks.gender', $request->get('gender'))
            ->where('uniform_stocks.category', $request->get('category'))
            ->where('uniform_stocks.size', $request->get('size'))
            ->select('uniform_stocks.qty')
            ->first();

        $response = array(
            'status' => true,
            'detail' => $detail,
        );
        return Response::json($response);
    }

    public function updateUniformStock(Request $request)
    {
        try {

            $stock_update = UniformStock::where('gender', '=', $request->get('gender'))
                ->where('category', '=', $request->get('category'))
                ->where('size', '=', $request->get('size'))
                ->update([
                    'qty' => $request->get('qty'),
                ]);

            $response = array(
                'status' => true,
                'datas' => "Berhasil",
            );
            return Response::json($response);
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                $response = array(
                    'status' => false,
                    'datas' => "Uniform Already Exist",
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'datas' => "Update Uniform Error.",
                );
                return Response::json($response);
            }
        }
    }

    public function indexUniformAttendance()
    {
        $employee = DB::SELECT("SELECT * FROM employee_syncs where end_date is null");

        $title = 'Uniform Attendance';
        $title_jp = '';

        return view('general_affairs.uniform_attendance', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $employee,
        ))->with('page', 'uniform')->with('head', 'uniform');
    }

    public function fetchUniformAttendance(Request $request)
    {

        try {
            $emp = Employee::select('uniform_attendances.*')
                ->where('tag', $request->get('tag'))
                ->join('uniform_attendances', 'uniform_attendances.employee_id', 'employees.employee_id')
                ->where(DB::RAW('DATE_FORMAT(periode,"%Y")'), date('Y'))
                ->whereNotNull('uniform_attendances.size')
                ->first();

            if ($emp != null) {
                if ($emp->attend_date != null) {
                    $response = array(
                        'status' => false,
                        'message' => 'Anda Sudah Mengambil Seragam Periode Ini',
                    );
                    return Response::json($response);
                } else {
                    $stock_uniform = UniformStock::where('gender', '=', $emp->gender)
                        ->where('category', '=', $emp->category)
                        ->where('size', '=', $emp->size)
                        ->first();

                    if ($stock_uniform->qty <= 0) {
                        $response = array(
                            'status' => false,
                            'message' => 'Stock Habis, Silahkan Hubungi GA',
                        );
                        return Response::json($response);
                    } else {
                        $stock_update = UniformStock::where('gender', '=', $emp->gender)
                            ->where('category', '=', $emp->category)
                            ->where('size', '=', $emp->size)
                            ->update([
                                'qty' => $stock_uniform->qty - 1,
                            ]);
                    }

                    $stat = DB::SELECT("UPDATE uniform_attendances SET attend_date = '" . date('Y-m-d H:i:s') . "' where employee_id = '" . $emp->employee_id . "' and date_format(periode,'%Y') = " . date('Y'));

                    $log = UniformLog::create([
                        'date_uniform' => date('Y-m-d'),
                        'employee_id' => $emp->employee_id,
                        'name' => $emp->name,
                        'gender' => $emp->gender,
                        'category' => $emp->category,
                        'size' => $emp->size,
                        'qty' => 1,
                        'created_by' => Auth::id(),
                    ]);

                    $log->save();

                    $response = array(
                        'status' => true,
                        'emp' => $emp,
                    );
                    return Response::json($response);
                }
            } else {

                // $emp = Employee::select('employees.*')
                // ->where('tag',$request->get('tag'))
                // ->first();

                // $attendance = UniformAttendance::create([
                //        'periode' => date('Y-m-d'),
                //        'employee_id' => $emp->employee_id,
                //        'name' => $emp->name,
                //        'gender' => $emp->gender,
                //        'category' => $emp->category,
                //        'size' => $emp->size,
                //        'qty' => 1,
                //        'created_by' => Auth::id()
                //    ]);

                // $attendance->save();

                $response = array(
                    'status' => false,
                    'message' => 'Anda Sudah Pernah Scan atau Tidak Terdaftar Pada Periode Ini ',
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

    public function fetchUniformAttendanceQueue()
    {
        try {

            $emp = DB::SELECT("SELECT
	        *
               FROM
               uniform_attendances
               ORDER BY
               `attend_date`
               DESC");

            $response = array(
                'status' => true,
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

    public function inputUniformAttendance(Request $request)
    {
        try {
            $id = Auth::user()->username;

            $nik = explode("_", $request->get('nik'));
            $gender = $request->get('gender');
            $category = $request->get('category');
            $size = $request->get('size');
            $qty = $request->get('qty');

            $stock_uniform = UniformStock::where('gender', '=', $gender)
                ->where('category', '=', $category)
                ->where('size', '=', $size)
                ->first();

            if ($stock_uniform->qty <= 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Stock Habis, Silahkan Hubungi GA',
                );
                return Response::json($response);
            } else {
                $stock_update = UniformStock::where('gender', '=', $gender)
                    ->where('category', '=', $category)
                    ->where('size', '=', $size)
                    ->update([
                        'qty' => $stock_uniform->qty - 1,
                    ]);
            }

            $log = UniformLog::create([
                'date_uniform' => date('Y-m-d'),
                'employee_id' => $nik[0],
                'name' => $nik[1],
                'gender' => $gender,
                'category' => $category,
                'size' => $size,
                'qty' => $qty,
                'created_by' => Auth::id(),
            ]);

            $log->save();

            $response = array(
                'status' => true,
                'request' => $request,
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

    // ----New Attendance Uniform----

    public function indexUniformAttendanceNew()
    {
        $employee = DB::SELECT("SELECT * FROM employee_syncs where end_date is null");

        $title = 'Uniform Attendance All New';
        $title_jp = '';

        return view('general_affairs.uniform_attendance_new', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $employee,
        ))->with('page', 'uniform')->with('head', 'uniform');
    }

    public function fetchUniformAttendanceNew(Request $request)
    {

        try {
            $data_all = $request->get('data_all');
            $emp_tag_master = $request->get('emp_tag_master');
            $name_tag_master = $request->get('name_tag_master');

            for ($i = 0; $i < count($data_all); $i++) {

                $stock_uniform = UniformStock::where('gender', '=', $data_all[$i]['gender'])
                    ->where('category', '=', $data_all[$i]['category'])
                    ->where('size', '=', $data_all[$i]['size'])
                    ->first();

                if ($stock_uniform->qty <= 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Stock Habis, Silahkan Hubungi GA',
                    );
                    return Response::json($response);
                } else {
                    $stock_update = UniformStock::where('gender', '=', $data_all[$i]['gender'])
                        ->where('category', '=', $data_all[$i]['category'])
                        ->where('size', '=', $data_all[$i]['size'])
                        ->update([
                            'qty' => $stock_uniform->qty - 1,
                        ]);
                }

                $stat = DB::SELECT("UPDATE uniform_attendances SET attend_date = '" . date('Y-m-d H:i:s') . "', employee_id_master = '" . $emp_tag_master . "', name_master = \"" . $name_tag_master . "\" where employee_id = '" . $data_all[$i]['employee_id'] . "' and date_format(periode,'%Y') = '" . date('Y') . "'");

                $log = UniformLog::create([
                    'date_uniform' => date('Y-m-d'),
                    'employee_id_master' => $emp_tag_master,
                    'name_master' => $name_tag_master,
                    'employee_id' => $data_all[$i]['employee_id'],
                    'name' => $data_all[$i]['name'],
                    'gender' => $data_all[$i]['gender'],
                    'category' => $data_all[$i]['category'],
                    'size' => $data_all[$i]['size'],
                    'qty' => 1,
                    'created_by' => Auth::id(),
                ]);

                $log->save();

                $this->printUniform($emp_tag_master, $name_tag_master, $data_all[$i]['employee_id'], $data_all[$i]['name'], $data_all[$i]['gender'], $data_all[$i]['category'], $data_all[$i]['size'], 'MIS');
            }

            $response = array(
                'status' => true,
                'message' => 'Data Berhasil Disimpan',
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

    public function printUniform($master_id, $master_name, $employee_id, $name, $gender, $category, $size, $printer_name)
    {
        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(3, 1);
        $printer->feed(1);
        $printer->text($employee_id . "\n");
        $printer->setTextSize(1, 1);
        $printer->text($name . "\n");
        $printer->feed(1);
        $printer->setTextSize(2, 1);
        $printer->text($gender . '-' . $category . '-' . $size . "\n");
        $printer->setTextSize(1, 1);
        $printer->text('Taken By: ' . $master_name . "\n");
        $printer->feed(1);
        $printer->setTextSize(1, 1);
        $printer->text(date("d-M-Y H:i:s") . "\n");
        $printer->cut();
        $printer->close();
    }

    public function scanUniformOperator(Request $request)
    {

        $nik = $request->get('tag');

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

    public function scanUniformOperatorFix(Request $request)
    {

        try {
            $emp = Employee::select('uniform_attendances.*')
                ->where('tag', $request->get('tag_penerima'))
                ->Orwhere('uniform_attendances.employee_id', $request->get('tag_penerima'))
                ->join('uniform_attendances', 'uniform_attendances.employee_id', 'employees.employee_id')
                ->where(DB::RAW('DATE_FORMAT(periode,"%Y")'), date('Y'))
                ->whereNotNull('uniform_attendances.size')
                ->first();

            if ($emp != null) {
                if ($emp->attend_date != null) {
                    $response = array(
                        'status' => false,
                        'message' => 'Anda Sudah Mengambil Seragam Periode Ini',
                    );
                    return Response::json($response);
                } else {
                    $response = array(
                        'status' => true,
                        'emp' => $emp,
                    );
                    return Response::json($response);
                }
            } else {

                $response = array(
                    'status' => false,
                    'message' => 'Anda Sudah Pernah Scan atau Tidak Terdaftar Pada Periode Ini ',
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

    public function fetchUniformAttendanceQueueNew()
    {
        try {

            $emp = DB::SELECT("SELECT
	        *
               FROM
               uniform_attendances
               ORDER BY
               `attend_date`
               DESC");

            $response = array(
                'status' => true,
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

    public function inputUniformAttendanceNew(Request $request)
    {
        try {
            $id = Auth::user()->username;

            $nik = explode("_", $request->get('nik'));
            $gender = $request->get('gender');
            $category = $request->get('category');
            $size = $request->get('size');
            $qty = $request->get('qty');

            $stock_uniform = UniformStock::where('gender', '=', $gender)
                ->where('category', '=', $category)
                ->where('size', '=', $size)
                ->first();

            if ($stock_uniform->qty <= 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Stock Habis, Silahkan Hubungi GA',
                );
                return Response::json($response);
            } else {
                $stock_update = UniformStock::where('gender', '=', $gender)
                    ->where('category', '=', $category)
                    ->where('size', '=', $size)
                    ->update([
                        'qty' => $stock_uniform->qty - 1,
                    ]);
            }

            $log = UniformLog::create([
                'date_uniform' => date('Y-m-d'),
                'employee_id' => $nik[0],
                'name' => $nik[1],
                'gender' => $gender,
                'category' => $category,
                'size' => $size,
                'qty' => $qty,
                'created_by' => Auth::id(),
            ]);

            $log->save();

            $response = array(
                'status' => true,
                'request' => $request,
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

//---------------------------MCU-----------------------//

    public function indexMcu()
    {
        $title = 'Medical Check Up';
        $title_jp = '健康診断';

        return view('general_affairs.mcu.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'MCU')->with('head', 'MCU');
    }

    public function indexPhysicalCheck($inspector)
    {
        $title = 'Cek Fisik Karyawan - ' . strtoupper($inspector);
        $title_jp = '従業員健康診断';
        $mcu_group = DB::CONNECTION('ympimis_2')
            ->table('mcu_groups')
            ->select('code', DB::RAW('GROUP_CONCAT( category ) AS category'), 'remark')
            ->GROUPBY('code', 'remark')
            ->get();

        $mcu_periode = DB::CONNECTION('ympimis_2')
            ->table('mcus')
            ->select('periode')
            ->DISTINCT()
            ->orderBy('periode', 'desc')
            ->get();

        $username = Auth::user()->username;

        if (str_contains(join(',', $this->mcu), strtoupper($username))) {
            $view = 'general_affairs.mcu.index_physical';
        } else {
            $view = '404';
        }

        return view($view, array(
            'title' => $title,
            'title_jp' => $title_jp,
            'inspector' => $inspector,
            'mcu_group' => $mcu_group,
            'mcu_periode' => $mcu_periode,
        ))->with('page', 'Cek Fisik Karyawan')->with('head', 'Cek Fisik Karyawan');
    }

    public function indexReportPhysicalCheck()
    {
        $title = 'Report Cek Fisik Karyawan';
        $title_jp = '従業員健康診断';
        $mcu_group = DB::CONNECTION('ympimis_2')
            ->table('mcu_groups')
            ->select('code', DB::RAW('GROUP_CONCAT( category ) AS category'))
            ->GROUPBY('code')
            ->get();

        $mcu_periode = DB::CONNECTION('ympimis_2')
            ->table('mcus')
            ->select('periode')
            ->DISTINCT()
            ->get();

        $username = Auth::user()->username;

        if (str_contains(join(',', $this->mcu), strtoupper($username))) {
            $view = 'general_affairs.mcu.report_physical';
        } else {
            $view = '404';
        }

        return view($view, array(
            'title' => $title,
            'title_jp' => $title_jp,
            'mcu_group' => $mcu_group,
            'mcu_periode' => $mcu_periode,
        ))->with('page', 'Report Cek Fisik Karyawan')->with('head', 'Report Cek Fisik Karyawan');
    }

    public function pdfReportPhysicalCheck($id)
    {
        $physical = DB::connection('ympimis_2')->table('mcus')->select('mcus.*', DB::RAW('DATE_FORMAT(updated_at,"%d-%b-%Y") as dates'))->where('id', $id)->first();
        $empsync = DB::table('employee_syncs')->get();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $pdf->loadView('general_affairs.mcu.pdf', array(
            'physical' => $physical,
            'emp' => $empsync,
        ));

        return $pdf->stream("Cek Fisik " . $physical->periode . " (" . $physical->employee_id . ' - ' . $physical->name . ").pdf");
    }

    public function indexReportMcuAttendance()
    {
        $title = 'Report MCU Attendance';
        $title_jp = '健康診断';
        $mcu_group = DB::CONNECTION('ympimis_2')
            ->table('mcu_groups')
            ->select('code', DB::RAW('GROUP_CONCAT( category ) AS category'))
            ->GROUPBY('code')
            ->get();

        $mcu_periode = DB::CONNECTION('ympimis_2')
            ->table('mcus')
            ->select('periode')
            ->DISTINCT()
            ->get();

        $username = Auth::user()->username;

        if (str_contains(join(',', $this->mcu), strtoupper($username))) {
            $view = 'general_affairs.mcu.report_attendance';
        } else {
            $view = '404';
        }

        return view($view, array(
            'title' => $title,
            'title_jp' => $title_jp,
            'mcu_group' => $mcu_group,
            'mcu_periode' => $mcu_periode,
        ))->with('page', 'Report MCU Attendance')->with('head', 'Report MCU Attendance');
    }

    public function fetchReportMcuAttendance(Request $request)
    {
        try {
            $periodes = $request->get('periode');
            if ($periodes == '') {
                $fy = DB::SELECT("Select DISTINCT(fiscal_year) from weekly_calendars where week_date = DATE(NOW())");
                $periodes = $fy[0]->fiscal_year;
            }
            $report = DB::connection('ympimis_2')->table('mcu_attendances')->where('periode', $periodes)->get();
            $response = array(
                'status' => true,
                'report' => $report,
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

    public function indexPhysicalMonitoring()
    {
        $title = 'Monitoring Cek Fisik';
        $title_jp = '健康診断の監視';

        $mcu_periode = DB::CONNECTION('ympimis_2')
            ->table('mcus')
            ->select('periode')
            ->DISTINCT()
            ->get();

        return view('general_affairs.mcu.monitoring_physical', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'mcu_periode' => $mcu_periode,
        ))->with('page', 'Monitoring Cek Fisik')->with('head', 'Monitoring Cek Fisik');
    }

    public function scanPhysicalCheck(Request $request)
    {
        try {
            if (is_numeric($request->get('tag')) == 1) {
                $employees = Employee::where('tag', $request->get('tag'))->join('employee_syncs', 'employee_syncs.employee_id', 'employees.employee_id')->leftjoin('departments', 'departments.department_name', 'employee_syncs.department')->first();
            } else {
                $employees = Employee::where('employees.employee_id', $request->get('tag'))->join('employee_syncs', 'employee_syncs.employee_id', 'employees.employee_id')->leftjoin('departments', 'departments.department_name', 'employee_syncs.department')->first();
            }

            if ($employees != null) {
                $mcu_periode = DB::CONNECTION('ympimis_2')
                    ->table('mcus')
                    ->select('periode')
                    ->DISTINCT()
                    ->orderBy('periode', 'desc')
                    ->get();

                if ($request->get('inspector') == 'clinic') {
                    $mcu = db::connection('ympimis_2')
                        ->table('mcus')
                        ->where('employee_id', '=', $employees->employee_id)
                        ->where('periode', $mcu_periode[0]->periode)
                    // ->where('clinic_status',null)
                    // ->where('doctor_status',null)
                        ->limit(1)->first();
                    // if (count($mcu) > 0) {
                    $mcu_before = db::connection('ympimis_2')
                        ->table('mcus')
                        ->where('employee_id', '=', $employees->employee_id)
                        ->where('periode', $mcu_periode[1]->periode)
                        ->limit(1)->first();
                    if ($mcu) {
                        $response = array(
                            'status' => true,
                            'employees' => $employees,
                            'mcu' => $mcu,
                            'mcu_before' => $mcu_before,
                            'emp_status' => 'clinic',
                            'message' => 'Scan Berhasil',
                        );
                        return Response::json($response);
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Nama Anda tidak ada dalam schedule',
                        );
                        return Response::json($response);
                    }
                } else {
                    $mcu = db::connection('ympimis_2')
                        ->table('mcus')
                        ->where('employee_id', '=', $employees->employee_id)
                        ->where('periode', $mcu_periode[0]->periode)
                        ->where('clinic_status', '!=', null)
                    // ->where('doctor_status',null)
                        ->limit(1)->first();
                    $mcu_before = db::connection('ympimis_2')
                        ->table('mcus')
                        ->where('employee_id', '=', $employees->employee_id)
                        ->where('periode', $mcu_periode[1]->periode)
                        ->limit(1)->first();
                    if ($mcu) {
                        $response = array(
                            'status' => true,
                            'employees' => $employees,
                            'mcu' => $mcu,
                            'mcu_before' => $mcu_before,
                            'emp_status' => 'doctor',
                            'message' => 'Scan Berhasil',
                        );
                        return Response::json($response);
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Belum melalui Cek Petugas Klinik / Sudah melalui Cek Dokter',
                        );
                        return Response::json($response);
                    }
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Tag Invalid',
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

    public function inputPhysicalCheck(Request $request)
    {
        try {
            $schedule_id = $request->get('schedule_id');
            if ($request->get('inspector') == 'clinic') {
                $height = $request->get('height');
                $weight = $request->get('weight');
                $blood_1 = $request->get('blood_1');
                $blood_2 = $request->get('blood_2');
                $pulse = $request->get('pulse');
                $visus_od_1 = $request->get('visus_od_1');
                $visus_od_2 = $request->get('visus_od_2');
                $visus_os_1 = $request->get('visus_os_1');
                $visus_os_2 = $request->get('visus_os_2');
                $visus_os = $request->get('visus_os');
                $visus_od = $request->get('visus_od');
                $imt = $request->get('imt');
                $respiration = $request->get('respiration');
                $color_blind = $request->get('color_blind');
                $age = $request->get('age');

                $mcus = DB::connection('ympimis_2')
                    ->table('mcus')
                    ->where('id', $schedule_id)
                    ->update([
                        'height' => $height,
                        'weight' => $weight,
                        'age' => $age,
                        'blood_pressure' => $blood_1 . '/' . $blood_2,
                        'pulse' => $pulse,
                        'respiration' => $respiration,
                        'color_blind' => $color_blind,
                        'imt' => $imt,
                        'visus_os' => $visus_os_1 . '/' . $visus_os_2,
                        'visus_od' => $visus_od_1 . '/' . $visus_od_2,
                        'visus_os_status' => $visus_os,
                        'visus_od_status' => $visus_od,
                        'clinic_status' => 'Done',
                        'paramedic' => Auth::user()->name,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $mcus = DB::connection('ympimis_2')
                    ->table('mcus')
                    ->where('id', $schedule_id)
                    ->first();
            } else {
                $nose = $request->get('nose');
                $complaint = $request->get('complaint');
                $disease_history = $request->get('disease_history');
                $mcu_group_code = $request->get('mcu_group_code');
                $symmetry = $request->get('symmetry');
                $tht = $request->get('tht');
                $tooth = $request->get('tooth');
                $head = $request->get('head');
                $heart = $request->get('heart');
                $lungs = $request->get('lungs');
                $abdomen = $request->get('abdomen');
                $hepar = $request->get('hepar');

                $limbs = $request->get('limbs');
                $arm = $request->get('arm');
                $joint = $request->get('joint');
                $skin = $request->get('skin');
                $thorax = $request->get('thorax');

                $mcus = DB::connection('ympimis_2')
                    ->table('mcus')
                    ->where('id', $schedule_id)
                    ->update([
                        'complaint' => $complaint,
                        'disease_history' => $disease_history,
                        'mcu_group_code' => $mcu_group_code,
                        'tht' => $tht,
                        'symmetry' => $symmetry,
                        'tooth' => $tooth,
                        'head' => $head,
                        'heart' => $heart,
                        'lungs' => $lungs,
                        'abdomen' => $abdomen,
                        'hepar' => $hepar,
                        'limbs' => $limbs,
                        'arm' => $arm,
                        'joint' => $joint,
                        'skin' => $skin,
                        'thorax' => $thorax,
                        'doctor_status' => 'Done',
                        'doctor' => Auth::user()->name,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $mcus = DB::connection('ympimis_2')
                    ->table('mcus')
                    ->where('id', $schedule_id)
                    ->first();
            }
            $response = array(
                'status' => true,
                'message' => 'Sukses Input Data Cek Fisik',
                'mcus' => $mcus,
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

    public function fetchReportPhysicalCheck(Request $request)
    {
        try {
            $mcu_group_code = $request->get('mcu_group');
            $mcu_periode = $request->get('mcu_periode');

            $mcus = DB::connection('ympimis_2')
                ->table('mcus')
                ->select('mcus.*', DB::RAW('DATE(mcus.updated_at) as created'));

            if ($mcu_group_code == '') {

            } else if ($mcu_group_code == 'Belum Cek') {
                $mcus = $mcus->where('clinic_status', null);
            } else {
                $mcus = $mcus->where('mcu_group_code', $mcu_group_code);
            }

            if ($mcu_periode != '') {
                $mcus = $mcus->where('periode', $mcu_periode);
            } else {
                $fy = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
                $mcus = $mcus->where('periode', $fy->fiscal_year);
            }

            $mcus = $mcus->orderby('mcus.updated_at', 'desc')->get();

            $empsync = EmployeeSync::select('employee_syncs.*', 'departments.department_shortname')->leftjoin('departments', 'employee_syncs.department', 'departments.department_name')->get();
            $response = array(
                'status' => true,
                'mcus' => $mcus,
                'empsync' => $empsync,
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

    public function fetchPhysicalMonitoring(Request $request)
    {
        try {
            $mcu_periode = $request->get('mcu_periode');
            $fy = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();

            if ($mcu_periode != '') {
                $mcu_resume_periode = " where mcus.periode = '" . $mcu_periode . "'";
                $periode = $mcu_periode;
            } else {
                $mcu_resume_periode = " where mcus.periode = '" . $fy->fiscal_year . "'";
                $periode = $fy->fiscal_year;
            }

            $mcus = DB::connection('ympimis_2')
                ->table('mcus')
                ->where('periode', $periode)
                ->get();

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
                'mcus' => $mcus,
                'department' => $department,
                'employees' => $employees,
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

    public function downloadSchedulePhysical()
    {
        $file_path = public_path('data_file/ga/physical/TemplateSchedulePhysical.xlsx');
        return response()->download($file_path);
    }

    public function indexReportPhysicalCheckFormat()
    {
        $title = 'Report Cek Fisik Karyawan';
        $title_jp = '従業員健康診断';
        $mcu_group = DB::CONNECTION('ympimis_2')
            ->table('mcu_groups')
            ->select('code', DB::RAW('GROUP_CONCAT( category ) AS category'))
            ->GROUPBY('code')
            ->get();

        $mcu_periode = DB::CONNECTION('ympimis_2')
            ->table('mcus')
            ->select('periode')
            ->DISTINCT()
            ->get();

        $mcu_group_category = DB::connection('ympimis_2')
            ->table('mcu_groups')
            ->select('category')
            ->distinct()
            ->orderBy('category', 'asc')
            ->get();

        $username = Auth::user()->username;

        if (str_contains(join(',', $this->mcu), strtoupper($username))) {
            $view = 'general_affairs.mcu.report_physical_format';
        } else {
            $view = '404';
        }

        return view($view, array(
            'title' => $title,
            'title_jp' => $title_jp,
            'mcu_group' => $mcu_group,
            'mcu_group_category' => $mcu_group_category,
            'mcu_periode' => $mcu_periode,
        ))->with('page', 'Report Cek Fisik Karyawan')->with('head', 'Report Cek Fisik Karyawan');
    }

    public function fetchReportPhysicalCheckFormat(Request $request)
    {
        try {
            $mcu_group_code = $request->get('mcu_group');
            $mcu_periode = $request->get('mcu_periode');

            $mcus = DB::connection('ympimis_2')
                ->table('mcus')
                ->select('mcus.*', DB::RAW('DATE(mcus.updated_at) as created'));

            if ($mcu_group_code == '') {

            } else if ($mcu_group_code == 'Belum Cek') {
                $mcus = $mcus->where('clinic_status', null);
            } else {
                $mcus = $mcus->where('mcu_group_code', $mcu_group_code);
            }

            if ($mcu_periode != '') {
                $mcus = $mcus->where('periode', $mcu_periode);
                $periodes = $mcu_periode;
            } else {
                $fy = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
                $mcus = $mcus->where('periode', $fy->fiscal_year);
                $periodes = $fy->fiscal_year;
            }

            $mcus = $mcus->orderby('mcus.updated_at', 'descs')->get();

            $location = DB::connection('ympimis_2')->table('mcu_locations')->where('periode', $periodes)->get();

            $empsync = EmployeeSync::select('employee_syncs.*', 'departments.department_shortname')->leftjoin('departments', 'employee_syncs.department', 'departments.department_name')->get();

            $mcu_groups = DB::connection('ympimis_2')
                ->table('mcu_groups')
                ->get();

            $mcu_group_category = DB::connection('ympimis_2')
                ->table('mcu_groups')
                ->select('category')
                ->distinct()
                ->orderBy('category', 'asc')
                ->get();

            $response = array(
                'status' => true,
                'mcus' => $mcus,
                'empsync' => $empsync,
                'location' => $location,
                'mcu_groups' => $mcu_groups,
                'mcu_group_category' => $mcu_group_category,
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

    public function indexMcuAttendance($periode, $location)
    {
        $title = 'Medical Check Up Attendance - ' . str_replace('_', ' ', strtoupper($location));
        $title_jp = '';
        return view('general_affairs.mcu.mcu_attendance', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $location,
            'periode' => $periode,
        ))->with('page', 'MCU Attendance')->with('head', 'MCU Attendance');
    }

    public function fetchMcuAttendance($periode, $location)
    {
        try {
            $codein = '';
            $audio = '';
            if ($location == 'ambil_darah_dan_urine') {
                $codess = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
            } else if ($location == 'thorax') {
                $codess = ['A', 'B', 'C', 'D', 'E', 'F'];
            } else if ($location == 'ecg_treadmill_usg') {
                $codess = ['A', 'B', 'F', 'G'];
            } else if ($location == 'audiometri') {
                $codess = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                $audio = " AND mcus.audiometri = 'YA'";
            }

            $code = '';
            // $codess =  explode(",", $codesss);
            for ($i = 0; $i < count($codess); $i++) {
                $code = $code . "'" . $codess[$i] . "'";
                if ($i != (count($codess) - 1)) {
                    $code = $code . ',';
                }
            }
            $codein = " AND mcus.mcu_group_code IN (" . $code . ")";

            $queue = DB::connection('ympimis_2')->SELECT("SELECT
                mcus.*,
                mcu_attendances.attendance_date,
                mcu_attendances.attendance_status
            FROM
                `mcus`
                LEFT JOIN mcu_attendances ON mcu_attendances.employee_id = mcus.employee_id
                AND mcu_attendances.periode = mcus.periode
                AND mcu_attendances.flow_name = '" . strtoupper($location) . "'
            WHERE
                mcus.periode = '" . $periode . "'
                " . $codein . "
                " . $audio . "
                order by mcu_attendances.attendance_date desc");

            $response = array(
                'status' => true,
                'queue' => $queue,
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

    public function scanMcuAttendance(Request $request)
    {
        try {
            $emp_reg = $request->get('employee_id');
            if (is_numeric($request->get('tag'))) {
                $emp = Employee::where('tag', $request->get('tag'))->first();
            } else {
                $emp = Employee::where('employee_id', $request->get('tag'))->first();
            }
            if ($emp) {
                if (!str_contains($emp_reg, $emp->employee_id)) {
                    $response = array(
                        'status' => false,
                        'message' => 'NIK Tidak Terdaftar di Proses ini',
                    );
                    return Response::json($response);
                }
                $mcu = DB::connection('ympimis_2')->table('mcus')
                    ->where('periode', strtoupper($request->get('periode')))
                    ->where('employee_id', $emp->employee_id)
                    ->where('mcu_group_code', '!=', null);
                if (strtoupper($request->get('location')) == 'AUDIOMETRI') {
                    $mcu = $mcu->where('audiometri', 'YA');
                    $mcu = $mcu->first();
                    if (!$mcu) {
                        $response = array(
                            'status' => false,
                            'message' => 'NIK Tidak Terdaftar di Proses ini',
                        );
                        return Response::json($response);
                    }
                } else {
                    $mcu = $mcu->first();
                }
                if ($mcu) {
                    $employee = DB::connection('ympimis_2')
                        ->table('mcu_attendances')
                        ->where('periode', strtoupper($request->get('periode')))
                        ->where('employee_id', $emp->employee_id)
                        ->where('flow_name', strtoupper($request->get('location')))
                        ->first();
                    if ($employee) {
                        $response = array(
                            'status' => false,
                            'message' => 'NIK Sudah Hadir di Proses ' . str_replace('_', ' ', strtoupper($request->get('location'))),
                        );
                        return Response::json($response);
                    } else {
                        $insert = DB::connection('ympimis_2')->table('mcu_attendances')->insert([
                            'periode' => strtoupper($request->get('periode')),
                            'employee_id' => $emp->employee_id,
                            'name' => $emp->name,
                            'schedule_date' => date('Y-m-d'),
                            'mcu_group_code' => $mcu->mcu_group_code,
                            'flow_index' => '1',
                            'flow_name' => strtoupper($request->get('location')),
                            'attendance_date' => date('Y-m-d H:i:s'),
                            'attendance_status' => 'Hadir',
                            'created_by' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $updates = DB::connection('ympimis_2')->table('mcus')->where('periode', strtoupper($request->get('periode')))->where('employee_id', $emp->employee_id)->first();
                        if ($updates) {
                            if ($updates->mcu_attendance_status == null) {
                                $statusess = strtoupper($request->get('location'));
                            } else {
                                $statusess = $updates->mcu_attendance_status . ',' . strtoupper($request->get('location'));
                            }
                            $update = DB::connection('ympimis_2')->table('mcus')->where('periode', strtoupper($request->get('periode')))->where('employee_id', $emp->employee_id)->update([
                                'mcu_attendance_status' => $statusess,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                        $response = array(
                            'status' => true,
                            'employee' => $emp,
                            'next_location' => '',
                            'message' => 'Scan Berhasil. Silahkan menjalani proses ' . str_replace('_', ' ', strtoupper($request->get('location'))) . '.',
                        );
                        return Response::json($response);
                    }
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'NIK Belum Mendapatkan Group MCU. Silahkan menghubungi bagian GA.',
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Tag Invalid / NIK Salah.<br>Hubungi bagian HR.',
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

    public function indexMcuQueue()
    {
        $title = "Medical Check Up Queue";
        $title_jp = "健康診断待ち行列";

        return view('general_affairs.mcu.mcu_queue', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ));
    }

    public function fetchMcuQueue(Request $request)
    {
        try {
            $auth = EmployeeSync::where('employee_syncs.employee_id', Auth::user()->username)->first();
            $now = date('Y-m-d');
            $data_registrasi = DB::connection('ympimis_2')
                ->table("mcu_attendances")
            // ->leftjoin('ympimis.sunfish_shift_syncs','ympimis.sunfish_shift_syncs.employee_id','mcu_attendances.employee_id')
                ->where('flow_name', 'REGISTRASI')
                ->where('priority', 1)
                ->where('schedule_date', $now)
            // ->where('shift_date',$now)
            //          ->where('sunfish_shift_syncs.attend_code','not like','%CUTI%')
            //          ->where('sunfish_shift_syncs.attend_code','not like','%SAKIT%')
            //          ->where('sunfish_shift_syncs.attend_code','not like','%CK%')
            // ->where('attendance_status',null)
                ->limit(30)
                ->get();

            $data_clinic = DB::connection('ympimis_2')
                ->table("mcu_attendances")
            // ->leftjoin('ympimis.sunfish_shift_syncs','ympimis.sunfish_shift_syncs.employee_id','mcu_attendances.employee_id')
                ->where('flow_name', 'CLINIC')
                ->where('priority', 1)
                ->where('schedule_date', $now)
            //          ->where('shift_date',$now)
            //          ->where('sunfish_shift_syncs.attend_code','not like','%CUTI%')
            //          ->where('sunfish_shift_syncs.attend_code','not like','%SAKIT%')
            //          ->where('sunfish_shift_syncs.attend_code','not like','%CK%')
            // ->where('attendance_status',null)
                ->limit(30)
                ->get();

            $data_thorax = DB::connection('ympimis_2')
                ->table("mcu_attendances")
            // ->leftjoin('ympimis.sunfish_shift_syncs','ympimis.sunfish_shift_syncs.employee_id','mcu_attendances.employee_id')
                ->where('flow_name', 'THORAX')
                ->where('priority', 1)
                ->where('schedule_date', $now)
            //          ->where('shift_date',$now)
            //          ->where('sunfish_shift_syncs.attend_code','not like','%CUTI%')
            //          ->where('sunfish_shift_syncs.attend_code','not like','%SAKIT%')
            //          ->where('sunfish_shift_syncs.attend_code','not like','%CK%')
            // ->where('attendance_status',null)
                ->limit(30)
                ->get();

            $data_audiometri = DB::connection('ympimis_2')
                ->table("mcu_attendances")
            // ->leftjoin('ympimis.sunfish_shift_syncs','ympimis.sunfish_shift_syncs.employee_id','mcu_attendances.employee_id')
                ->where('flow_name', 'AUDIOMETRI')
                ->where('priority', 1)
                ->where('schedule_date', $now)
            //          ->where('shift_date',$now)
            //          ->where('sunfish_shift_syncs.attend_code','not like','%CUTI%')
            //          ->where('sunfish_shift_syncs.attend_code','not like','%SAKIT%')
            //          ->where('sunfish_shift_syncs.attend_code','not like','%CK%')
            // ->where('attendance_status',null)
                ->limit(30)
                ->get();

            $employee = EmployeeSync::
                join('departments', 'departments.department_name', 'employee_syncs.department')
                ->where('end_date', null)
                ->get();

            $shift_schedule = SunfishShiftSync::
                where('shift_date', $now)
                ->get();

            if ($auth != null) {
                $response = array(
                    'status' => true,
                    'data_thorax' => $data_thorax,
                    'data_audiometri' => $data_audiometri,
                    'data_clinic' => $data_clinic,
                    'data_registrasi' => $data_registrasi,
                    'employee' => $employee,
                    'shift_schedule' => $shift_schedule,
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
                    'shift_schedule' => $shift_schedule,
                    'employee' => $employee,
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

    public function fetchCekListGS(Request $request)
    {

        $get_area = DB::connection('ympimis_2')->Select("SELECT DISTINCT category,area
          FROM
          `gs_list_job_masters`
          ");

        $get_list_master = DB::connection('ympimis_2')->Select("SELECT *
          FROM
          `gs_list_job_masters`
          ");

        $response = array(
            'status' => true,
            'areas' => $get_area,
            'datas' => $get_list_master,
        );
        return Response::json($response);

    }

    public function indexGSNew()
    {
        $title = "GS Control";
        $title_jp = "";

        $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
        $pics = db::connection('ympimis_2')->table('gs_operators')->whereNull('deleted_at')->get();
        $user = User::where('id', '=', Auth::id())->first();

        $categorys = DB::connection('ympimis_2')->Select("SELECT DISTINCT
          category
          FROM
          `gs_list_job_masters`
          ORDER BY category ASC
          ");

        return view('ga_control.gs_control.index_gs_new', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'departments' => $departments,
            'pics' => $pics,
            'user' => $user,
            'category' => $categorys,
        ))->with('page', 'gscontrol');
    }

    public function fetchjoblistIndex(Request $request)
    {
        try {
            $user = $request->get('emp_id');

            $cek_dailyjob = db::connection('ympimis_2')->table('gs_daily_jobs')->where('operator_gs', $user)->where('status', '!=', 1)->whereNull('deleted_at')->get();

            $data_all = DB::connection('ympimis_2')
                ->select("SELECT
                gs_actual_jobs.id,
                st_gs1.id,
                gs_actual_jobs.employee_id,
                st_gs1.name_gs,
                gs_actual_jobs.`status`,
                st_gs1.list_job,
                gs_actual_jobs.request_at,
                gs_actual_jobs.finished_at,
                IFNULL(
                    DATE_FORMAT( gs_actual_jobs.finished_at, '%M %d %Y %H:%i:%s' ),
                    DATE_FORMAT( NOW(), '%M %d %Y %H:%i:%s' )) AS end_job,
                DATE_FORMAT( gs_actual_jobs.request_at, '%M %d %Y %H:%i:%s' ) AS dt
                FROM
                gs_actual_jobs
                LEFT JOIN ( SELECT * FROM gs_joblist_logs ) st_gs1 ON gs_actual_jobs.user_id = st_gs1.id");
            $cek_listjob_progress = db::connection('ympimis_2')->table('gs_joblist_logs')->where('nik_gs', $user)->where('status', '!=', 1)->get();

            $job_finished = db::connection('ympimis_2')->table('gs_joblist_logs')->where('nik_gs', $user)->where('status', '=', 1)->get();

            $response = array(
                'status' => true,
                'cek_dailyjob' => $cek_dailyjob,
                'username' => $user,
                'cek_listjob_progress' => $cek_listjob_progress,
                'job_finished' => $job_finished,
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

    public function fetchCheckOP(Request $request)
    {

        $op_ids = $request->get('id');

        try {

            $cek_op = db::connection('ympimis_2')->table('gs_operators')->where('employee_id', $op_ids)->whereNull('deleted_at')->first();

            $response = array(
                'status' => true,
                'cek_op' => $cek_op,
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

    public function CreateJobsGS(Request $request)
    {
        try {
            $ids = $request->input("id");
            for ($i = 0; $i < count($ids); $i++) {
                $jobs = db::connection('ympimis_2')->table('gs_daily_jobs')->where('id', $ids[$i])->whereNull('deleted_at')->get();
                $ops = db::connection('ympimis_2')->table('gs_operators')->where('employee_id', $jobs[0]->operator_gs)->whereNull('deleted_at')->first();

                $input_job = DB::connection('ympimis_2')->table('gs_joblist_logs')->insert([
                    'nik_gs' => $jobs[0]->operator_gs,
                    'name_gs' => $ops->employee_name,
                    'category' => $jobs[0]->category,
                    'lokasi' => $jobs[0]->area,
                    'list_job' => $jobs[0]->list_job,
                    'request_at' => date('Y-m-d H:i:s'),
                    'finished_at' => null,
                    'status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $update = DB::connection('ympimis_2')->table('gs_daily_jobs')->where('id', $ids[$i])->update([
                    'status' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $firsts = date('Y-m-d');

                $getdata = db::connection('ympimis_2')->table('gs_joblist_logs')->where('list_job', $jobs[0]->list_job)->whereNull('deleted_at')->first();

                $getdata2 = db::connection('ympimis_2')->table('gs_actual_jobs')
                    ->where(db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)
                    ->where('status', '=', 'idle')
                    ->where('employee_id', '=', $jobs[0]->operator_gs)
                    ->whereNull('finished_at')->first();

                if ($getdata2 == null) {
                    $input_actual_job = DB::connection('ympimis_2')->table('gs_actual_jobs')->insert([
                        'user_id' => $getdata->id,
                        'employee_id' => $getdata->nik_gs,
                        'status' => $getdata->category,
                        'request_at' => $getdata->request_at,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                } else {
                    $update_actual = DB::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id', $jobs[0]->operator_gs)->where('status', '=', 'idle')->update([
                        'finished_at' => $getdata->request_at,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $input_actual_job1 = DB::connection('ympimis_2')->table('gs_actual_jobs')->insert([
                        'user_id' => $getdata->id,
                        'employee_id' => $getdata->nik_gs,
                        'status' => $getdata->category,
                        'request_at' => $getdata->request_at,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                }

            }

            $response = array(
                'status' => true,
                'message' => 'Tambah Pekerjaan Success',

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

    public function indexGSControlNew()
    {
        $title = "GS";
        $title_jp = "翻訳管理システム";

        $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
        $pics = db::connection('ympimis_2')->table('gs_operators')->whereNull('deleted_at')->get();
        $user = User::where('id', '=', Auth::id())->first();
        $employee_sync = db::table('employee_syncs')->leftJoin('departments', 'departments.department_name', '=', 'employee_syncs.department')->select('departments.department_name', 'departments.department_shortname')->where('employee_id', '=', Auth::user()->username)->first();

        $categorys = DB::connection('ympimis_2')->Select("SELECT DISTINCT
      category
      FROM
      `gs_list_job_masters`
      ORDER BY category ASC
      ");

        return view('ga_control.gs_control.index_gs', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'departments' => $departments,
            'pics' => $pics,
            'user' => $user,
            'employee_sync' => $employee_sync,
            'category' => $categorys,
        ))->with('page', 'GS_Control_New')->with('head', 'GS_Control_New');
    }

    public function indexGSControl()
    {
        $title = "GS Control";
        $title_jp = "";

        $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
        $pics = db::connection('ympimis_2')->table('gs_operators')->where('employee_id', '=', Auth::user()->username)->whereNull('deleted_at')->get();
        $user = User::where('id', '=', Auth::id())->first();
        $employee_sync = db::table('employee_syncs')->leftJoin('departments', 'departments.department_name', '=', 'employee_syncs.department')->select('departments.department_name', 'departments.department_shortname')->where('employee_id', '=', Auth::user()->username)->first();

        $categorys = DB::connection('ympimis_2')->Select("SELECT DISTINCT
      category
      FROM
      `gs_list_job_masters`
      ORDER BY category ASC
      ");

        return view('ga_control.gs_control.gs_index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'departments' => $departments,
            'pics' => $pics,
            'user' => $user,
            'employee_sync' => $employee_sync,
            'category' => $categorys,
        ))->with('page', 'GS_Control')->with('head', 'GS_Control');
    }

    public function inputJobGS(Request $request)
    {
        try {

            $get_op = db::connection('ympimis_2')->table('gs_operators')
                ->where('status', '=', null)->where('employee_id', '=', Auth::user()->username)->first();

            if ($get_op != null) {
                $insert_jobs = db::connection('ympimis_2')->table('gs_jobs')
                    ->insert([
                        'lokasi' => $request->input("location"),
                        'category' => $request->input("category"),
                        'list_job' => $request->input("list_job"),
                        'request_date' => date('Y-m-d H:i:s'),
                        'pic_id' => Auth::user()->username,
                        'pic_name' => Auth::user()->name,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $insert_st_op = db::connection('ympimis_2')->table('gs_operators')
                    ->where('employee_id', '=', Auth::user()->username)
                    ->update([
                        'status' => 'work',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $response = array(
                    'status' => true,
                    'message' => 'Request pekerjaan berhasil tersimpan',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Operator Sedang Bekerja',
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

    public function fetchJoblistGS(Request $request)
    {
        $joblist_gs = db::connection('ympimis_2')->table('gs_jobs')
            ->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        $translation_meetings = db::connection('ympimis_2')->table('translations')
            ->where('category', '=', 'meeting')
            ->whereNull('deleted_at');

        $translation_pics = db::connection('ympimis_2')
            ->select("SELECT *
        FROM
        gs_operators
        WHERE
        deleted_at IS NULL");

        $response = array(
            'status' => true,
            // 'weekly_calendars' => $weekly_calendars,
            'joblist_gs' => $joblist_gs,
        );
        return Response::json($response);
    }

    public function updateJobGS(Request $request)
    {
        try {

            $tujuan_upload = 'images/ga/gs_control';
            $filename_after = "";
            $filename_before = "";

            // if (count($request->file('attachment_foto_after')) > 0) {
            //     $file_after = $request->file('attachment_foto_after');
            //     $nama_after = $file_after->getClientOriginalName();
            //     $filename_after = pathinfo($nama_after, PATHINFO_FILENAME);
            //     $extension_after = pathinfo($nama_after, PATHINFO_EXTENSION);
            //     $filename_after = md5($filename_after) . '.' . $extension_after;
            //     $file_after->move($tujuan_upload, $filename_after);

            //      $insert_data = db::connection('ympimis_2')->table('gs_joblist_logs')
            //     ->where('id', '=', $request->get('id'))
            //     ->where('deleted_at', '=', null)
            //     ->update([
            //         'status' => 1,
            //         'img_before' => $filename_after,
            //         'finished_at' => date('Y-m-d H:i:s'),
            //         'updated_at' => date('Y-m-d H:i:s'),
            //     ]);
            // }

            // if (count($request->file('attachment_foto_before')) > 0) {

            //     $file_before = $request->file('attachment_foto_before');
            //     $nama_before = $file_after->getClientOriginalName();
            //     $filename_before = pathinfo($nama_before, PATHINFO_FILENAME);
            //     $extension_before = pathinfo($nama_before, PATHINFO_EXTENSION);
            //     $filename_before = md5($filename_after) . '.' . $extension_before;
            //     $file_before->move($tujuan_upload, $filename_before);

            //     $insert_data = db::connection('ympimis_2')->table('gs_joblist_logs')
            //     ->where('id', '=', $request->get('id'))
            //     ->where('deleted_at', '=', null)
            //     ->update([
            //         'status' => 1,
            //         'img_before' => $filename_before,
            //         'created_at' => date('Y-m-d H:i:s'),
            //         'updated_at' => date('Y-m-d H:i:s'),
            //     ]);
            // }
            $jobs = db::connection('ympimis_2')->table('gs_daily_jobs')->where('id', $request->get('id'))->whereNull('deleted_at')->first();

            if ($request->get('status') == 'before') {

                $input_job = DB::connection('ympimis_2')->table('gs_joblist_logs')->insert([
                    'nik_gs' => $jobs->operator_gs,
                    'name_gs' => $request->get('names'),
                    'category' => $jobs->category,
                    'lokasi' => $jobs->area,
                    'list_job' => $jobs->list_job,
                    'request_at' => date('Y-m-d H:i:s'),
                    'finished_at' => null,
                    'status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $update = DB::connection('ympimis_2')->table('gs_daily_jobs')->where('id', $request->get('id'))->update([
                    'status' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $firsts = date('Y-m-d');

            $getdata = db::connection('ympimis_2')->table('gs_joblist_logs')->where('list_job', $jobs->list_job)->whereNull('deleted_at')->first();

            $data_off = db::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id', '=', $jobs->operator_gs)
                ->where('status', '=', 'idle')->where(db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)->whereNull('finished_at')->first();

            if ($data_off != null) {

                $update_actual = DB::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id', $jobs->operator_gs)->where('status', '=', 'idle')->update([
                    'finished_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $input_actual_job = DB::connection('ympimis_2')->table('gs_actual_jobs')
                    ->where(db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)->insert([
                    'user_id' => $getdata->id,
                    'employee_id' => $getdata->nik_gs,
                    'status' => $getdata->category,
                    'request_at' => $getdata->request_at,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {

                $update_actual = DB::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id', $jobs->operator_gs)->where('status', '=', 'idle')->update([
                    'finished_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            }

            $response = array(
                'status' => true,
                'message' => 'Upload Pekerjaan berhasil tersimpan',
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

    public function fetchDataGS(Request $request)
    {
        $get_data = db::connection('ympimis_2')->table('gs_jobs')
            ->whereNull('deleted_at');

        $first = date('Y-m-01');
        $last = date('Y-m-t');

        // if($request->get('cat') == 'resume'){
        //     if($request->get('date_from') != ""){
        //         $first = date('Y-m-d', strtotime($request->get('date_from')));
        //         $last = date('Y-m-d', strtotime($request->get('date_to')));
        //     }

        //     $translation_translates = $translation_translates->where('status', '=', 'Finished')
        //     ->where('finished_at', '>=', $first)
        //     ->where('finished_at', '<=', $last);

        //     $translation_meetings = $translation_meetings->where('status', '=', 'Finished')
        //     ->where('finished_at', '>=', $first)
        //     ->where('finished_at', '<=', $last);
        // }

        $get_data = $get_data->orderBy('id', 'DESC')->get();

        // $translations = array();

        // foreach($translation_translates as $translation_translate){
        //     array_push($translations, [
        //         'id' => $translation_translate->id,
        //         'translation_id' => $translation_translate->translation_id,
        //         'category' => $translation_translate->category,
        //         'document_type' => $translation_translate->document_type,
        //         'title' => $translation_translate->title,
        //         'number_page' => $translation_translate->number_page,
        //         'request_date' => $translation_translate->request_date,
        //         'finished_at' => $translation_translate->finished_at,
        //         'request_date_from' => $translation_translate->request_date,
        //         'request_date_to' => $translation_translate->request_date,
        //         'request_time_from' => $translation_translate->request_date,
        //         'request_time_to' => $translation_translate->request_date,
        //         'std_time' => $translation_translate->std_time,
        //         'load_time' => $translation_translate->load_time,
        //         'requester_id' => $translation_translate->requester_id,
        //         'requester_name' => $translation_translate->requester_name,
        //         'requester_email' => $translation_translate->requester_email,
        //         'department_name' => $translation_translate->department_name,
        //         'department_shortname' => $translation_translate->department_shortname,
        //         'translation_request' => $translation_translate->translation_request,
        //         'translation_result' => $translation_translate->translation_result,
        //         'pic_id' => $translation_translate->pic_id,
        //         'pic_name' => $translation_translate->pic_name,
        //         'status' => $translation_translate->status,
        //         'remark' => $translation_translate->remark,
        //         'deleted_at' => $translation_translate->deleted_at,
        //         'created_at' => $translation_translate->created_at,
        //         'updated_at' => $translation_translate->updated_at
        //     ]);
        // }

        $weekly_calendars = db::table('weekly_calendars')->where('week_date', '>=', $first)
            ->where('week_date', '<=', $last)
            ->select('week_date', db::raw('date_format(week_date, "%d") as day_date'))
            ->orderBy('week_date', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'weekly_calendars' => $weekly_calendars,
            'get_data' => $get_data,
        );
        return Response::json($response);
    }

    public function fetchLoadGS()
    {
        $now = date('Y-m-d');

        $translation_pics = db::connection('ympimis_2')
            ->select("SELECT *
        FROM
        gs_operators
        WHERE
        deleted_at IS NULL");

        $translations = db::connection('ympimis_2')
            ->select("SELECT
        pic_id,
        pic_name,
        sum( times ) AS load_time
        FROM
        gs_jobs
        WHERE
        deleted_at IS NULL
        GROUP BY pic_id
        ");

        $gs_jobs = db::connection('ympimis_2')
            ->select("SELECT
        pic_id,
        pic_name,
        sum( times ) AS load_time,
        category,
        lokasi,
        list_job
        FROM
        gs_jobs
        WHERE
        deleted_at IS NULL
        GROUP BY pic_id
        ");

        $tot_jobs = db::connection('ympimis_2')
            ->select("SELECT
           *
        FROM
        gs_jobs
        WHERE
        deleted_at IS NULL
        ");

        $loads = array();

        foreach ($translation_pics as $translation_pic) {
            $load_time_translation = 0;

            foreach ($translations as $translation) {
                if ($translation->pic_id == $translation_pic->employee_id) {
                    $load_time_translation += $translation->load_time;
                }
            }

            array_push($loads, [
                'pic_id' => $translation_pic->employee_id,
                'pic_name' => $translation_pic->employee_name,
                'load_time_translation' => $load_time_translation,
            ]);
        }

        $response = array(
            'status' => true,
            'loads' => $loads,
            'gs_jobs' => $gs_jobs,
            'tot_jobs' => $tot_jobs,
        );
        return Response::json($response);
    }

//Kontrol Obat

    public function indexKontrolObat()
    {
        $title = 'Control Medicine Monitoring';
        // $title_jp = 'IK・DM・DLの管理';
        $role_user = User::where('username', Auth::user()->username)->first();

        return view('_index', array(
            'title' => $title,
            'role_user' => $role_user,
        ))->with('page', 'Control Medicine Monitoring');
    }

    public function indexGym()
    {
        $title = 'YMPI GYM Reservation';
        $title_jp = 'スポーツジムの予約';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();

        return view('general_affairs.gym.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'emp' => $emp,
            'role' => Auth::user()->role_code,
        ))->with('page', 'YMPI GYM');
    }

    public function fetchGym(Request $request)
    {
        try {

            $periode = date('Y-m');
            $nextPeriode = date('Y-m', strtotime('+ 1 month'));

            $quotas = DB::connection('ympimis_2')->SELECT("SELECT
                id,
                date_format( date, '%a, %d %b %Y' ) AS date_name,
                date AS dates,
                start_time,
                end_time,
                gender,
                capacity,
                `order`
            FROM
                general_gym_quotas
            WHERE
                DATE_FORMAT(date,'%Y-%m') >= '" . $periode . "'
                AND DATE_FORMAT(date,'%Y-%m') <= '" . $nextPeriode . "'
            GROUP BY
                date,
                id,
                `order`,
                capacity,
                gender,
                start_time,
                end_time
            ORDER BY dates,start_time");

            $calendars = WeeklyCalendar::where(db::raw('date_format(week_date, "%Y-%m")'), '>=', $periode)
                ->where(db::raw('date_format(week_date, "%Y-%m")'), '<=', $nextPeriode)
                ->select('week_date as date', db::raw('date_format(week_date, "%d") as header'), 'remark')
                ->get();

            $resumes = DB::connection('ympimis_2')->select("SELECT
                general_gyms.id AS id_gym,
                schedule_id,
                order_by_id,
                order_by_name,
                employee_id,
                CONCAT( SPLIT_STRING ( `name`, ' ', 1 ), ' ', SPLIT_STRING ( `name`, ' ', 2 ) ) AS `name`,
                date,
                start_time,
                end_time,
                remark
            FROM
                general_gyms
            ORDER BY
                date");

            $response = array(
                'status' => true,
                'quota' => $quotas,
                'calendars' => $calendars,
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

    public function inputGym(Request $request)
    {
        try {
            $id = $request->get('id');
            $date = $request->get('date');
            $ordered_by_id = $request->get('ordered_by_id');
            $ordered_by_name = $request->get('ordered_by_name');

            $quota = DB::connection('ympimis_2')->table('general_gym_quotas')->where('id', $id)->first();
            if ($quota->capacity == $quota->order) {
                $response = array(
                    'status' => false,
                    'message' => 'Mohon Maaf, Kuota GYM telah Penuh',
                );
                return Response::json($response);
            }

            $cek = DB::connection('ympimis_2')->table('general_gyms')->where('date', $date)->where('employee_id', $ordered_by_id)->where('remark', '!=', 'Canceled')->first();
            if ($cek) {
                $response = array(
                    'status' => false,
                    'message' => 'Mohon Maaf, Nama Anda Sudah Ada di List Tanggal ' . $date,
                );
                return Response::json($response);
            }

            $cek = DB::connection('ympimis_2')->table('general_gyms')->where('week_name', $quota->week_name)->where('employee_id', $ordered_by_id)->where('remark', '!=', 'Canceled')->get();
            if (count($cek) == 2) {
                $response = array(
                    'status' => false,
                    'message' => 'Mohon Maaf, Anda Hanya Berhak 2 Kali Order di Minggu Ini',
                );
                return Response::json($response);
            }

            $input = DB::connection('ympimis_2')->table('general_gyms')->insert([
                'schedule_id' => $id,
                'date' => $date,
                'start_time' => $quota->start_time,
                'end_time' => $quota->end_time,
                'order_by_id' => $ordered_by_id,
                'order_by_name' => $ordered_by_name,
                'employee_id' => $ordered_by_id,
                'name' => $ordered_by_name,
                'gender' => $quota->gender,
                'week_name' => $quota->week_name,
                'remark' => 'Registered',
                'created_by' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $updatequota = DB::connection('ympimis_2')->table('general_gym_quotas')->where('id', $id)->update([
                'order' => $quota->order + 1,
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

    public function editGym(Request $request)
    {
        try {
            $gym = DB::connection('ympimis_2')->table('general_gyms')->where('id', $request->get('id'))->first();
            $response = array(
                'status' => true,
                'gym' => $gym,
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

    public function deleteGym(Request $request)
    {
        try {
            $gym = DB::connection('ympimis_2')->table('general_gyms')->where('id', $request->get('id'))->first();

            $quota = DB::connection('ympimis_2')->table('general_gym_quotas')->where('id', $gym->schedule_id)->first();

            $updatequota = DB::connection('ympimis_2')->table('general_gym_quotas')->where('id', $gym->schedule_id)->update([
                'order' => $quota->order - 1,
            ]);

            $deletegym = DB::connection('ympimis_2')->table('general_gyms')->where('id', $request->get('id'))->update([
                'remark' => 'Canceled',
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

    public function indexGymSchedule()
    {
        $title = 'YMPI GYM Schedule';
        $title_jp = 'スポーツジムの予約';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();

        return view('general_affairs.gym.schedule', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'emp' => $emp,
        ))->with('page', 'YMPI GYM Schedule');
    }

    public function fetchGymSchedule()
    {
        try {
            $schedule = DB::connection('ympimis_2')->table('general_gym_schedules')->get();
            $response = array(
                'status' => true,
                'schedule' => $schedule,
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

    public function indexGymAttendance()
    {
        $title = 'YMPI GYM Attendance';
        $title_jp = 'スポーツジムの予約';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();

        return view('general_affairs.gym.attendance2', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'emp' => $emp,
        ))->with('page', 'YMPI GYM');
    }

    public function fetchGymAttendance(Request $request)
    {
        try {
            $gym = DB::connection('ympimis_2')->table('general_gyms')->where('remark', 'Attended')->get();
            $response = array(
                'status' => true,
                'gym' => $gym,
                'datetime' => date('Y-m-d H:i:s'),
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

    public function sendGymWhatsapp(Request $request)
    {
        try {
            $gym = DB::connection('ympimis_2')->table('general_gyms')->where('id', $request->get('id'))->first();
            if ($gym) {
                if ($gym->whatsapp_time_out == null) {
                    $messages = $gym->name . ' Melebihi Jam Berakhir Schedule GYM. Seharusnya selesai jam ' . $gym->end_time;

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
                        CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));

                    curl_exec($curl);

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
                        CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));

                    curl_exec($curl);

                    $updategym = DB::connection('ympimis_2')->table('general_gyms')->where('id', $request->get('id'))->update([
                        'whatsapp_time_out' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
            $response = array(
                'status' => true,
                'gym' => $gym,
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

    public function scanGymAttendance(Request $request)
    {
        try {
            if ($request->get('tag') != '') {
                $emp = Employee::where('tag', $request->get('tag'))->first();
                if ($emp) {
                    $empsync = EmployeeSync::where('employee_id', $emp->employee_id)->first();
                    if (in_array($empsync->employee_id, $this->gym) || str_contains($empsync->position, 'Manager') || $empsync->grade_code == 'J0-') {
                        $quota = DB::connection('ympimis_2')->table('general_gym_quotas')->where('date', date('Y-m-d'))->first();
                        $gym = DB::connection('ympimis_2')->table('general_gyms')->where('date', date('Y-m-d'))->where('employee_id', $empsync->employee_id)->where('remark', 'Attended')->first();
                        if ($gym) {
                            $updategym = DB::connection('ympimis_2')->table('general_gyms')->where('date', date('Y-m-d'))->where('employee_id', $empsync->employee_id)->where('remark', 'Attended')->update([
                                'actual_end_time' => date('Y-m-d H:i:s'),
                                'remark' => 'Closed',
                            ]);
                            $response = array(
                                'status' => true,
                                'message' => 'Thank You for Your Attend',
                            );
                            return Response::json($response);
                        } else {
                            $input = DB::connection('ympimis_2')->table('general_gyms')->insert([
                                'schedule_id' => 0,
                                'date' => date('Y-m-d'),
                                'start_time' => date('Y-m-d H:i:s'),
                                'end_time' => date('Y-m-d 20:00:00'),
                                'order_by_id' => $empsync->employee_id,
                                'order_by_name' => $empsync->name,
                                'employee_id' => $empsync->employee_id,
                                'name' => $empsync->name,
                                'gender' => $empsync->gender,
                                'week_name' => $quota->week_name,
                                'actual_start_time' => date('Y-m-d H:i:s'),
                                'remark' => 'Attended',
                                'created_by' => Auth::user()->id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                            $gym_progress = DB::connection('ympimis_2')->table('general_gym_progresses')->where('employee_id', $empsync->employee_id)->where(DB::RAW('DATE_FORMAT(date,"%Y-%m")'), date('Y-m'))->get();

                            $response = array(
                                'status' => true,
                                'gym_progress' => $gym_progress,
                                'employee_id' => $empsync->employee_id,
                                'name' => $empsync->name,
                                'message' => 'Attended.',
                            );
                            return Response::json($response);
                        }
                    } else {
                        $data = DB::connection('ympimis_2')->table('general_gyms')->where('employee_id', $emp->employee_id)->where('date', date('Y-m-d'))->where(
                            function ($query) {
                                return $query
                                    ->where('remark', 'Registered')
                                    ->orWhere('remark', 'Attended');
                            })->first();
                        if ($data) {
                            if ($data->actual_start_time == null) {
                                if (date('H:i:s') >= $data->start_time && date('H:i:s') < $data->end_time) {
                                    $data = DB::connection('ympimis_2')->table('general_gyms')->where('employee_id', $emp->employee_id)->where('date', date('Y-m-d'))->where('remark', 'Registered')->update([
                                        'actual_start_time' => date('Y-m-d H:i:s'),
                                        'remark' => 'Attended',
                                    ]);
                                    $gym_progress = DB::connection('ympimis_2')->table('general_gym_progresses')->where('employee_id', $empsync->employee_id)->where(DB::RAW('DATE_FORMAT(date,"%Y-%m")'), date('Y-m'))->get();
                                    $response = array(
                                        'status' => true,
                                        'gym_progress' => $gym_progress,
                                        'employee_id' => $empsync->employee_id,
                                        'name' => $empsync->name,
                                        'message' => 'Silahkan melakukan GYM, patuhi peraturan yang ditetapkan.',
                                    );
                                    return Response::json($response);
                                } else {
                                    $messages = $emp->name . ' Memasuki ruang GYM di luar jam schedule.';

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
                                        CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                                        CURLOPT_HTTPHEADER => array(
                                            'Accept: application/json',
                                            'Content-Type: application/x-www-form-urlencoded',
                                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                                        ),
                                    ));

                                    curl_exec($curl);

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
                                        CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                                        CURLOPT_HTTPHEADER => array(
                                            'Accept: application/json',
                                            'Content-Type: application/x-www-form-urlencoded',
                                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                                        ),
                                    ));

                                    curl_exec($curl);

                                    $response = array(
                                        'status' => false,
                                        'message' => 'Maaf, Silahkan datang pada jam yang telah Anda pilih. ' . $data->start_time . '-' . $data->end_time,
                                    );
                                    return Response::json($response);
                                }
                            } else {
                                $data = DB::connection('ympimis_2')->table('general_gyms')->where('employee_id', $emp->employee_id)->where('date', date('Y-m-d'))->where('remark', 'Attended')->update([
                                    'actual_end_time' => date('Y-m-d H:i:s'),
                                    'remark' => 'Closed',
                                ]);

                                $response = array(
                                    'status' => true,
                                    'message' => 'Terimakasih Telah Berolahraga hari ini.',
                                );
                                return Response::json($response);
                            }
                        } else {
                            $messages = $emp->name . ' Memasuki ruang GYM tanpa order.';

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
                                CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                                CURLOPT_HTTPHEADER => array(
                                    'Accept: application/json',
                                    'Content-Type: application/x-www-form-urlencoded',
                                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                                ),
                            ));

                            curl_exec($curl);

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
                                CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                                CURLOPT_HTTPHEADER => array(
                                    'Accept: application/json',
                                    'Content-Type: application/x-www-form-urlencoded',
                                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                                ),
                            ));

                            curl_exec($curl);

                            $response = array(
                                'status' => false,
                                'message' => 'Schedule Anda Belum Tersedia',
                            );
                            return Response::json($response);
                        }
                    }
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Tag Invalid',
                    );
                    return Response::json($response);

                }
            } else {
                $id = $request->get('id');

                $data = DB::connection('ympimis_2')->table('general_gyms')->where('id', $id)->update([
                    'actual_end_time' => date('Y-m-d H:i:s'),
                    'remark' => 'Closed',
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

    public function inputGymProgress(Request $request)
    {
        try {
            $date = date('Y-m-d H:i:s');
            $employee_id = $request->get('employee_id');
            $name = $request->get('name');
            $berat_baran = $request->get('berat_baran');

            $input = DB::connection('ympimis_2')->table('general_gym_progresses')->insert([
                'date' => $date,
                'employee_id' => $employee_id,
                'name' => $name,
                'type' => 'Berat Baran',
                'result_check' => $berat_baran,
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

    public function updateGymSchedule(Request $request)
    {
        try {
            $id = $request->get('id');
            $start_time = $request->get('start_time');
            $end_time = $request->get('end_time');
            $capacity = $request->get('capacity');
            $remark = $request->get('remark');

            DB::connection('ympimis_2')->table('general_gym_schedules')->where('id', $id)->update([
                'start_time' => $start_time . ':00',
                'end_time' => $end_time . ':00',
                'capacity' => $capacity,
                'remark' => $remark,
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

    public function indexAuditSup()
    {
        $title = 'S-UP Control & Monitoring';
        $title_jp = '';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();

        $fy_all = DB::SELECT("SELECT DISTINCT
              ( fiscal_year )
              FROM
              weekly_calendars");

        return view('ga_control.sup.index_sup', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'emp' => $emp,
            'fy_all' => $fy_all,

        ))->with('page', 'SUP Control');
    }

    public function fetchCheckDataSup(Request $request)
    {
        try {
            if ($request->get('fy') == '') {
                $fys = "(
                SELECT DISTINCT
                ( fiscal_year )
                FROM
                weekly_calendars
                WHERE
                week_date = DATE(
                NOW()))";
            } else {
                $fys = "'" . $request->get('fy') . "'";
            }
            $fy = DB::SELECT("SELECT DISTINCT
                (
                  DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
                DATE_FORMAT( week_date, '%b %Y' ) AS month_name
                FROM
                weekly_calendars
                WHERE
                fiscal_year = " . $fys . "
                ORDER BY
                week_date");

            $data_group = db::connection('ympimis_2')->table('sup_groups')->whereNull('deleted_at')->get();
            $data_check_point = db::connection('ympimis_2')->table('sup_point_checks')->where('step', '1')->whereNull('deleted_at')->get();

            $response = array(
                'status' => true,
                'data_group' => $data_group,
                'fy_all' => $fy,
                'data_check_point' => $data_check_point,

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

    public function auditSupIndex(Request $request)
    {
        $id = 1;
        $emp_id = strtoupper(Auth::user()->username);
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);
        $schedule = DB::CONNECTION('ympimis_2')->table('sup_process_audit_schedules')->where('id', $id)->first();
        $point_check = DB::CONNECTION('ympimis_2')->table('sup_point_checks')->where('step', $schedule->step)->get();
        $emp = EmployeeSync::whereNull('end_date')->get();
        return view('ga_control.sup.index_point')
            ->with('title', 'S-UP Audit')
            ->with('title_jp', '')
            ->with('page', 'Quality Assurance')
            ->with('schedule', $schedule)
            ->with('point_check', $point_check)
            ->with('emp', $emp)
            ->with('role', Auth::user()->role_code)
            ->with('employee_id', Auth::user()->username)
            ->with('jpn', '品保');
    }

    public function auditSup()
    {
        $title = "S-UP";
        $title_jp = "";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'position', 'department')->first();

        return view('ga_control.sup.index_check_point', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
        ))->with('page', 'S-UP');
    }

    public function fetchAuditPoint(Request $request)
    {
        try {

            $point = DB::connection('ympimis_2')->Select("SELECT *
            FROM
            `sup_point_checks`
            WHERE step = 2
            ");

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

    public function inputSupEvidence(Request $request)
    {
        $checklist_id = $request->input('checklist_id');
        $photo_id = $request->input('photo_id');

        try {
            $directory = 'files\sup';

            $file = $request->file('file_datas');
            dd($file);
            $name = $file->getClientOriginalName();
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $filename = $checklist_id . '_' . $photo_id . '_' . uniqid() . '.' . $extension;
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

    public function indexGymRegulation()
    {
        $title = 'YMPI GYM Regulation';
        $title_jp = 'スポーツジムの予約';

        return view('general_affairs.gym.regulation', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'YMPI GYM Regulation');
    }

    public function indexApprovalPresidentDirector()
    {
        $user = EmployeeSync::where('employee_id',Auth::user()->username)->first();
        
        if($user){
            if ($user->department == 'General Affairs Department' || $user->department == 'Management Information System Department' || $user->department == null) {
                $emp = EmployeeSync::where('end_date',null)->get();
            }
            else{
                $emp = EmployeeSync::where('department',$user->department)->where('end_date',null)->get();
            }
        }
        else{
            $emp = EmployeeSync::where('end_date',null)->get();
            $user = array();
        }

        $department = DB::table('departments')
        ->select('department_name','department_shortname')        
        ->get();

        $division = DB::table('divisions')
        ->select('division_name')
        ->get();

        // $user = EmployeeSync::where('employee_id', Auth::user()->username)->first();
        $general_manager = DB::table('approvers')->where('remark', 'General Manager')->first();

        return view('general_affairs.approval_president_director.index', array(
            'title' => 'Pengajuan Approval President Director', 
            'title_jp' => '社長承認申請',
            'employees' => $emp,
            'user' => $user,
            'user2' => $user,
            'general_manager' => $general_manager,
            'role_code' => Auth::user()->role_code,
            'departments' => $department,
            'divisions' => $division,
        ))->with('page', 'General Affairs Approval President Director');
    }

    public function indexApprovalPresidentDirectorReport()
    {
        $user = EmployeeSync::where('employee_id',Auth::user()->username)->first();        
        
        if($user){
            if ($user->employee_id !== 'PI1212001' /* Eko Junaedi */ && $user->employee_id !== 'PI2301032' /* Hentong */ ) {
                abort(404);
            }            
        }

        $department = DB::table('departments')
        ->select('department_name','department_shortname')
        ->get();

        $division = DB::table('divisions')
        ->select('division_name')
        ->get();

        return view ('general_affairs.approval_president_director.report', array(
            'title' => 'Pengajuan Approval President Director Report', 
            'title_jp' => '社長承認申請',
            'departments' => $department,
            'divisions' => $division,
        ))->with('page', 'General Affairs Approval President Director Report');
    }

    public function indexApprovalPresidentDirectorStatus($request_id)
    {                
        $request_id = $request_id;

        $approval = DB::connection('ympimis_2')->table('ga_president_director_approvals');
                

        return view('general_affairs.approval_president_director.index_status', array(
            'title' => 'Approval President Director', 
            'title_jp' => '??',            
            'request_id' => $request_id,
        ))->with('page', 'General Affairs Approval President Director');
    }

    public function fetchApprovalPresidentDirector(Request $request)
    {                
        $applicant = $request->get('applicant');

        try {
            $query = DB::connection('ympimis_2')->table('ga_president_director')
            ->select('ga_president_director.request_id', 'ga_president_director.status', 'ga_president_director.applicant' , 'ga_president_director.department' , 'ga_president_director.department_shortname', 'ga_president_director.document_name', 'ga_president_director.purpose', 'ga_president_director.recipient', 'ga_president_director.remark', 'ga_president_director.hardcopy_total', 'ga_president_director.created_by', 'ga_president_director.created_at', 'ga_president_director.updated_at', DB::raw("DATE_FORMAT(ga_president_director.created_at, '%d-%b-%Y') as date"), DB::raw('GROUP_CONCAT(docs_name SEPARATOR "|") as docs_name'), DB::raw('GROUP_CONCAT(url SEPARATOR "|") as url'))
            ->leftJoin('ga_president_director_files', 'ga_president_director.request_id', '=', 'ga_president_director_files.request_id');

            $query2 = clone $query;

            if ($applicant == 'PI1212001' /* Eko Junaedi */ || $applicant == 'PI230103d' /* Hentong */) {
                $presdir_request = $query->where('ga_president_director.remark', 'Requested')
                ->groupBy('ga_president_director.request_id', 'ga_president_director.status', 'ga_president_director.applicant', 'ga_president_director.department', 'ga_president_director.department_shortname', 'ga_president_director.document_name', 'ga_president_director.purpose', 'ga_president_director.recipient', 'ga_president_director.remark', 'ga_president_director.hardcopy_total', 'ga_president_director.created_by', 'ga_president_director.created_at', 'ga_president_director.updated_at')
                ->get();

                $presdir_request_completed = $query2->where('ga_president_director.remark', '!=', 'Requested')
                ->groupBy('ga_president_director.request_id', 'ga_president_director.status', 'ga_president_director.applicant', 'ga_president_director.department', 'ga_president_director.department_shortname', 'ga_president_director.document_name', 'ga_president_director.purpose', 'ga_president_director.recipient', 'ga_president_director.remark', 'ga_president_director.hardcopy_total', 'ga_president_director.created_by', 'ga_president_director.created_at', 'ga_president_director.updated_at')
                ->orderBy('updated_at', 'desc')
                ->get();
                
            } else {
                $presdir_request = $query->where('ga_president_director.remark', 'Requested')->where('applicant', $applicant)
                ->groupBy('ga_president_director.request_id', 'ga_president_director.status', 'ga_president_director.applicant', 'ga_president_director.department', 'ga_president_director.department_shortname', 'ga_president_director.document_name', 'ga_president_director.purpose', 'ga_president_director.recipient', 'ga_president_director.remark', 'ga_president_director.hardcopy_total', 'ga_president_director.created_by', 'ga_president_director.created_at', 'ga_president_director.updated_at')
                ->get();                

                $presdir_request_completed = $query2->where('ga_president_director.remark', '!=', 'Requested')->where('applicant', $applicant)
                ->groupBy('ga_president_director.request_id', 'ga_president_director.status', 'ga_president_director.applicant', 'ga_president_director.department', 'ga_president_director.department_shortname', 'ga_president_director.document_name', 'ga_president_director.purpose', 'ga_president_director.recipient', 'ga_president_director.remark', 'ga_president_director.hardcopy_total', 'ga_president_director.created_by', 'ga_president_director.created_at', 'ga_president_director.updated_at')
                ->orderBy('updated_at', 'desc')
                ->get();                            
            }

            $presdir_approvals = DB::connection('ympimis_2')->table('ga_president_director_approvals')
                ->select('ga_president_director_approvals.*', DB::raw("DATE_FORMAT(approved_at, '%d-%b-%Y') as date"), DB::raw("TIME_FORMAT(approved_at, '%H:%i:%s') as time"))        
                ->get();
        } catch (\Throwable $th) {
            $response = array(
                'status' => false,
                'message' => $th->getMessage(),
            );
            return Response::json($response);
        }
                

        $response = array(
            'status' => true,
            'presdir_request' => $presdir_request,            
            'presdir_approvals' => $presdir_approvals,
            'presdir_request_completed' => $presdir_request_completed,            
            'presdir_approvals_completed' => $presdir_approvals,
        );        

        return Response::json($response);
    }

    public function fetchApprovalPresidentDirectorReport(Request $request){
        try {            
            $presdir_request = DB::connection('ympimis_2')->table('ga_president_director')            
            ->leftJoin('ga_president_director_files', 'ga_president_director.request_id', '=', 'ga_president_director_files.request_id')
            ->select('ga_president_director.request_id', 'ga_president_director.status' , 'ga_president_director.department' , 'ga_president_director.department_shortname', 'ga_president_director.document_name', 'ga_president_director.purpose', 'ga_president_director.recipient', 'ga_president_director.remark', 'ga_president_director.hardcopy_total', 'ga_president_director.created_by', 'ga_president_director.created_at', 'ga_president_director.updated_at', DB::raw("DATE_FORMAT(ga_president_director.created_at, '%d-%b-%Y') as date"), DB::raw('GROUP_CONCAT(docs_name SEPARATOR "|") as docs_name'), DB::raw('GROUP_CONCAT(url SEPARATOR "|") as url'))            
            ->groupBy('ga_president_director.request_id', 'ga_president_director.status', 'ga_president_director.department', 'ga_president_director.department_shortname', 'ga_president_director.document_name', 'ga_president_director.purpose', 'ga_president_director.recipient', 'ga_president_director.remark', 'ga_president_director.hardcopy_total', 'ga_president_director.created_by', 'ga_president_director.created_at', 'ga_president_director.updated_at');

            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');

            $department = $request->get('department');
            $status = $request->get('status');

            if ($date_from != null && $date_to != null) {
                $presdir_request = $presdir_request->whereBetween('ga_president_director.created_at', [$date_from, $date_to]);
            }

            if ($department != null) {
                $presdir_request = $presdir_request->where('ga_president_director.department', $department);
            }

            if ($status != null) {
                $presdir_request = $presdir_request->where('ga_president_director.status', $status);
            }            

            $presdir_request = $presdir_request->get();

            $presdir_approvals = DB::connection('ympimis_2')->table('ga_president_director_approvals')
            ->select('ga_president_director_approvals.*', DB::raw("DATE_FORMAT(approved_at, '%d-%b-%Y') as date"), DB::raw("TIME_FORMAT(approved_at, '%H:%i:%s') as time"))        
            ->get();
            
        } catch (\Throwable $th) {
            $response = array(
                'status' => false,
                'message' => $th->getMessage(),
            );
            return Response::json($response);
        }
                

        $response = array(
            'status' => true,
            'presdir_request' => $presdir_request,            
            'presdir_approvals' => $presdir_approvals,            
        );

        return Response::json($response);
    }

    public $FOLDER_PATH = '/files/ga/ga_secretary/attachment/';

    public function inputApprovalPresidentDirector(Request $request){                                

        DB::beginTransaction();
        try {                        
            // $validate = Validator::make($request->all(), [                
            //     'applicant_id' => 'required',
            //     'applicant_name' => 'required',
            //     'recipient' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            //     'document_name' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            //     'purpose' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            //     'hardcopy_total' => 'required',
            // ]);

            // if ($validate->fails()) {
            //     $response = array(
            //         'status' => false,
            //         'message' => $validate->errors(),
            //     );
            //     return Response::json($response);
            // }            

            $code_generator = CodeGenerator::where('note', '=', 'approval_president')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $request_id = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $status = 'Requested';

            $emp_id = $request->get('applicant_id');

            // $document_name = $request->get('document_name');            
            // $purpose = $request->get('purpose');
            // $recipient = $request->get('recipient');
            $document_name = str_replace(array('"', "'",';', '`'), ' ', $request->get('document_name')); //regex double tick, quotation, semicolon, backtick
            $purpose = str_replace(array('"', "'",';', '`'), ' ', $request->get('purpose')); //regex double tick, quotation, semicolon, backtick
            $recipient = str_replace(array('"', "'",';', '`'), ' ', $request->get('recipient')); //regex double tick, quotation, semicolon, backtick

            $hardcopy_total = $request->get('hardcopy_total');

            // Department
            $find_department = EmployeeSync::select('employee_syncs.department', 'employee_syncs.position')
            ->where('employee_syncs.employee_id', $emp_id)
            ->first();                    

            $department = $find_department->department;
            
            // Department Shortname
            $department_shortname = Department::select('department_shortname')
            ->where('departments.department_name', $department)
            ->first();
            $department_shortname = $department_shortname->department_shortname;               

            $approval_president = ApprovalPresidentDirector::firstOrCreate([
                'request_id' => $request_id,
                'status' => $status,
                'applicant' => $emp_id,
                'department' => $department,
                'department_shortname' => $department_shortname,
                'document_name' => $document_name,
                'purpose' => $purpose,
                'recipient' => $recipient,
                'remark' => 'Requested',
                'hardcopy_total' => $hardcopy_total,
                'created_by' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Applicant
            $applicant_id = $request->get('applicant_id');
            $applicant_name = $request->get('applicant_name');
            $get_email = User::select('email')->where('username', $applicant_id)->first();
            $applicant_email = $get_email->email;            

            // Approver
            // $General_manager = db::table('approvers')->where('department', $department)->where('remark', 'General Manager')->first();
            // dd($General_manager);
            
            // if ($General_manager == null) {
            //     $General_manager = db::table('approvers')->where('department', $department)->where('remark', 'Manager')->first();
            // }

            $division = DB::table('departments')->leftJoin('divisions', 'departments.id_division', '=', 'divisions.id')
            ->select('divisions.division_name')
            ->where('departments.department_name', $department)
            ->first()->division_name;
            
            $division = strtoupper($division);            
            $division = ucwords(strtolower($division));

            $approver_id = $division ;
            $approver_name = $division ;
            $approver_email = $division ;            

            // $approver_id = $General_manager->approver_id;
            // $approver_name = $General_manager->approver_name;
            // $approver_email = $General_manager->approver_email;            
                                    
            
            $person = [
                [
                    'remark' => 'Applicant',
                    'person_id' => $applicant_id,
                    'person_name' => $applicant_name,
                    'person_email' => $applicant_email,
                    'status' => 'Approved',
                    'approved_at' => date('Y-m-d H:i:s'),
                    'deleted_at' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],                
                [
                    'remark' => 'Approver',
                    'person_id' => $approver_id,
                    'person_name' => $approver_name,
                    'person_email' => $approver_email,
                    'status' => null,
                    'approved_at' => null,
                    'deleted_at' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]                
            ];

            foreach ($person as $pi) {
                $approval = ApprovalPresidentDirectorApprove::firstOrCreate([
                    'request_id' => $request_id,
                    'remark' => $pi['remark'],
                    'person_id' => $pi['person_id'],
                    'person_name' => $pi['person_name'],
                    'person_email' => $pi['person_email'],
                    'status' => $pi['status'],
                    'approved_at' => $pi['approved_at'],
                    'deleted_at' => $pi['deleted_at'],
                    'created_at' => $pi['created_at'],
                    'updated_at' => $pi['updated_at'],
                ]);
            }

            $file_destination = $this->FOLDER_PATH;
            $docsnames = array();

            if($request->get('att_count') > 0) {         
                try {                    
                    
                    for ($i=0; $i < $request->get('att_count'); $i++) { 
                        $file = $request->file('file_upload_' . $i);    

                        $name_origin = $file->getClientOriginalName();
                        
                        $docsname = preg_replace('/[^A-Za-z0-9\s](?=\.[^.]+$)/', '',  $name_origin);                        
                        $docsname = $request_id . '_'. $docsname;                                                  

                        $file = $file->move(public_path($file_destination), $docsname);                                                                    

                        $docs_attachments = db::connection('ympimis_2')->table('ga_president_director_files')
                        ->insert([
                            'request_id' => $request_id,                            
                            'docs_name' => $docsname,                                                                                                      
                            'url' => $file_destination . $docsname,                                   
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);                                                                            
                    }
                    $arr_files = json_encode($docsnames);
                    $code_generator->index = $code_generator->index + 1;

                } catch (\Throwable $th) {
                    dd($th->getMessage());
                }
            } else {
                $arr_files = null;
            }

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Approval President Director Request Created'
            );
        } catch (\Exception $e) {
            DB::rollback();
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
        }

        return Response::json($response);
    }    

    public function editApprovalPresidentDirector(Request $request){                

        try {
            $request_id = $request->get('request_id');
            $new_recipient = $request->get('recipient');
            $new_document_name = $request->get('document_name');
            $new_purpose = $request->get('purpose');

            $update_request = ApprovalPresidentDirector::where('request_id', $request_id)->update([
                'recipient' => $new_recipient,
                'document_name' => $new_document_name,
                'purpose' => $new_purpose,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        
            $response = array(
                'status' => true,
                'message' => 'Approval President Director Request Updated'
            );   
        } catch (\Throwable $th) {
            $response = array(
                'status' => false,
                'message' => $th->getMessage()
            );            
        }        

        return Response::json($response);
    }


    public function approvalPresdir($request_id, $remark ,$status, Request $request) {            

        // dd($request_id, $remark, $status, $request->get('user'));

        $request_id = $request_id;

        if($request->get('user')) {
            $user = $request->get('user');
        }        

        $approvalData = array(            
            'remark' => $remark,
            'status' => $status,            
        );        

        try {
            switch ($approvalData) {
                // Applicant
                case array('remark' => 'Applicant', 'status' => 'Approved'):
                    # code...
                    break;
    
                // TODO Approver    
                case array('remark' => 'Approver', 'status' => 'Approve'):
                    $approved = ApprovalPresidentDirector::where('request_id', $request_id)
                    ->update([
                        'status' => 'Approved',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $approvedApprove = ApprovalPresidentDirectorApprove::where('request_id', $request_id)
                    ->where('remark', 'Approver')
                    ->update([
                        'status' => 'Approved',
                        'approved_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    break;
    
                case array('remark' => 'Approver', 'status' => 'Reject'):
                    $rejected = ApprovalPresidentDirector::where('request_id', $request_id)
                    ->update([
                        'status' => 'Rejected',
                        'remark' => 'Rejected',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $rejectedApprove = ApprovalPresidentDirectorApprove::where('request_id', $request_id)
                    ->where('remark', 'Approver')
                    ->update([
                        'status' => 'Rejected',
                        'approved_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);                    

                    break;               

                case array('remark' => 'Approver', 'status' => 'Revoke'):
                    $revoke = ApprovalPresidentDirector::where('request_id', $request_id)
                    ->update([
                        'status' => 'Requesting',
                        'remark' => 'Requested',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $rejectedApprove = ApprovalPresidentDirectorApprove::where('request_id', $request_id)
                    ->where('remark', 'Approver')
                    ->update([
                        'status' => null,
                        'approved_at' => null,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);                    
                    break;               
                
                default:                                    
                    return redirect()->route('indexApprovalPresidentDirector')
                    ->with('error', 'Approval President Director Request Not Updated');
                    break;
            }

            

        } catch (\Throwable $th) {            
            return redirect()->route('indexApprovalPresidentDirector')
            ->with('error', $th->getMessage());
        }        

        // status translate
        // $approvalText = '';

        // switch ($approvalData['status']) {
        //     case 'Approved':
        //         $approvalText = 'Telah berhasil di setujui';
        //         break;
        //     case 'Rejected':
        //         $approvalText = 'Telah di tolak';
        //         break;            
        //     default:
        //         $approvalText = '??';
        //         break;
        // }

        return redirect()->route('indexApprovalPresidentDirector');        
    }

    public function completePresdir(Request $request)
    {
        $request_id = $request->get('request_id');        

        DB::beginTransaction();
        try {
            $complete = ApprovalPresidentDirector::where('request_id', $request_id)
            ->update([
                'status' => 'Completed',
                'remark' => 'Completed',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $completeApprove = ApprovalPresidentDirectorApprove::where('request_id', $request_id)
            ->update([
                'status' => 'Approved',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $applicant = ApprovalPresidentDirector::where('request_id', $request_id)->first();
            $applicant = $applicant->applicant;

            $get_applicant_email = User::where('username', $applicant)->first();
            $applicant = $get_applicant_email->email;

            $data_detail = ApprovalPresidentDirector::where('request_id', $request_id)->first();

            $data = array(
                'title' => '[Approval President Director] Permohonan telah disetujui',
                'email_subject' => '[Approval President Director] Permohonan telah disetujui', 
                'request_id' => $request_id,                
                'status' => 'Completed',             
                'approved_at' => date('Y-m-d H:i:s'),                
                'data_detail' => $data_detail,
            );                    

            Mail::to($applicant)
            ->bcc(['fakhrizal.ihza.mahendra@music.yamaha.com',])  
            ->send(new SendEmail($data, 'pengajuan_approval_presdir'));
            
            $respond = [
                'status' => 'success',
                'message' => 'Permohonan '. $request_id .' telah selesai',
            ];
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            $respond = [
                'status' => 'error',
                'message' => $th->getMessage(),
            ];
        }        
        return response()->json($respond);
    }

    public function cancelRequest(Request $request)
    {
        $request_id = $request->get('request_id');

        $cancel = ApprovalPresidentDirector::where('request_id', $request_id)
        ->update([
            'status' => 'Cancelled',
            'remark' => 'Completed',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $cancelApprove = ApprovalPresidentDirectorApprove::where('request_id', $request_id)
        ->update([
            'status' => 'Cancelled',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $respond = [
            'status' => 'success',
            'message' => 'Permohonan '. $request_id .' telah dibatalkan',
        ];
        return response()->json($respond);
        
        
    }

}
