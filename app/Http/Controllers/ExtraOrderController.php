<?php

namespace App\Http\Controllers;

use App\Approver;
use App\CodeGenerator;
use App\Destination;
use App\EmployeeSync;
use App\ExtraOrder;
use App\ExtraOrderApproval;
use App\ExtraOrderBuyer;
use App\ExtraOrderDetail;
use App\ExtraOrderDetailSequence;
use App\ExtraOrderDetailSequenceLog;
use App\ExtraOrderMaterial;
use App\ExtraOrderPrice;
use App\ExtraOrderTimeline;
use App\Http\Controllers\Controller;
use App\Inventory;
use App\Mail\SendEmail;
use App\MasterChecksheet;
use App\MaterialPlantDataList;
use App\SendingApplication;
use App\SendingApplicationDetail;
use App\SendingApplicationLog;
use App\ShipmentCondition;
use App\Smbmr;
use App\StorageLocation;
use App\TransactionCompletion;
use App\TransactionTransfer;
use App\User;
use App\WeeklyCalendar;
use DataTables;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Response;
use Exception;

class ExtraOrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->release = true; //Apabila sudah release ke buyer eo
        $this->allowance = 10; //Apabila kirim PO lebih dari 7 hari
        $this->due_date = 6; //5 hari sebelum request date
        $this->lead_time_eoc = 5; //5 hari kerja
        $this->pc = array(
            'mamluatul.atiyah@music.yamaha.com',
            'farizca.nurma@music.yamaha.com',
            'ali.murdani@music.yamaha.com',
        );
        $this->price_pic = array(
            'ade.laksmana.putra@music.yamaha.com',
        );
        $this->warehouse = array(
            'nurul.hidayat@music.yamaha.com',
            'dwi.misnanto@music.yamaha.com',
        );
        $this->exim = array(
            'angga.setiawan@music.yamaha.com',
            'fathor.rahman@music.yamaha.com',
            'triandini@music.yamaha.com',
            'karina.elnusawati@music.yamaha.com',
        );
        $this->deputy_general_manager = array(
            "PI9905001#DeputyGeneralManager" => [
                "approver_id" => "PI9905001",
                "approver_name" => "Mei Rahayu",
                "approver_email" => "mei.rahayu@music.yamaha.com",
                "role" => "Deputy General Manager",
                "remark" => "Deputy General Manager",
            ],
        );
        $this->general_manager = array(
            "PI1206001#GeneralManager" => [
                "approver_id" => "PI1206001",
                "approver_name" => "Yukitaka Hayakawa",
                "approver_email" => "yukitaka.hayakawa@music.yamaha.com",
                "role" => "General Manager",
                "remark" => "General Manager",
            ],
            "PI0109004#GeneralManager" => [
                "approver_id" => "PI0109004",
                "approver_name" => "Budhi Apriyanto",
                "approver_email" => "budhi.apriyanto@music.yamaha.com",
                "role" => "General Manager",
                "remark" => "General Manager",
            ],
        );
        $this->buyer = array(
            "buyer_pc" => [
                "approver_id" => "PI1506003",
                "approver_name" => "Farizca Nurma",
                "approver_email" => "farizca.nurma@music.yamaha.com",
                "role" => "Senior Staff",
                "remark" => "Buyer PC",
            ],
            "buyer_procurement" => [
                "approver_id" => "PI1209002",
                "approver_name" => "Nunik Erwantiningsih",
                "approver_email" => "nunik.erwantiningsih@music.yamaha.com",
                "role" => "Coordinator",
                "remark" => "Buyer Procurement",
            ],
        );
        $this->buyer_procurement = array(
            'PI0904004',
            'PI9803003',
            'PI1209002',
        );
        $this->approval_order = array(
            1 => ["remark" => "Buyer"],
            2 => ["remark" => "Foreman"],
            3 => ["remark" => "Manager"],
            4 => ["remark" => "General Manager"],
        );
        $this->shipment_condition = array(
            'SEA',
            'AIR',
            'TRUCK',
        );
        $this->payment_term = array(
            'T/T REMITTANCE',
            'D/P AT SIGHT',
            'D/A 60 DAYS AFTER BL DATE',
            'FREE OF CHARGE',
            'THE END OF THE FOLLOWING MONTH',
        );
        $this->freight = array(
            'COLLECT',
            'PREPAID BY YMPI',
            'PREPAID BY DESTINATION',
        );
        $this->picking_location = array('SX51', 'CL51', 'FL51');
        $this->output_breakdown = array();
        $this->check = array();
        $this->temp = array();

    }

    public function indexExtraOrderData()
    {
        $title = 'Extra Order Data';
        $title_jp = 'エクストラオーダーデータ';

        $destinations = Destination::orderBy('destination_shortname', 'ASC')->where('destination_shortname', '<>', 'ITM')->get();
        $shipment_conditions = ShipmentCondition::orderBy('shipment_condition_code', 'ASC')->get();
        $buyers = ExtraOrderBuyer::orderBy('attention', 'ASC')->get();

        return view(
            'extra_order.extra_order_data',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'destinations' => $destinations,
                'shipment_conditions' => $shipment_conditions,
                'buyers' => $buyers,
            )
        )->with('page', 'EOC Approval Monitoring')->with('head', 'Extra Order');
    }

    public function indexApprovalMonitoring()
    {
        $title = 'EOC Approval Monitoring';
        $title_jp = 'EOC承認申請監視';

        $approver = db::select("SELECT DISTINCT approver_id, approver_name FROM approvers
           WHERE approver_id <> ''
           AND approver_id IS NOT NULL
           ORDER BY approver_name");

        return view(
            'extra_order.monitoring_approval',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'approvers' => $approver,
            )
        )->with('page', 'EOC Approval Monitoring')->with('head', 'Extra Order');
    }

    public function indexShortageMonitoring()
    {
        $title = 'EO Shortage Monitoring';
        $title_jp = 'エキストラオーダーの不足監視';

        $storage_location = StorageLocation::where('category', 'WIP')->get();

        return view(
            'extra_order.monitoring_shortage',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'storage_locations' => $storage_location,
            )
        )->with('page', 'EOC Approval Monitoring')->with('head', 'Extra Order');
    }

    public function indexSendingApplication()
    {
        $title = 'Extra Order Sending Application';
        $title_jp = 'エキストラオーダー出荷申請書';

        $destination = Destination::where('destination_code', '<>', 'ITM')
            ->whereNull('deleted_at')
            ->get();

        return view(
            'extra_order.sending_application',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'destinations' => $destination,
                'shipment_conditions' => $this->shipment_condition,
                'payment_terms' => $this->payment_term,
                'freights' => $this->freight,
            )
        )->with('page', 'Extra Order Sending Aplication')->with('head', 'Extra Order');
    }

    public function indexCompletionPage()
    {
        $title = 'Extra Order Completion';
        $title_jp = 'エキストラオーダー完了';

        $storage_location = StorageLocation::where('category', 'WIP')->get();
        $volume = db::select("
            SELECT
            materials.material_number,
            materials.material_description,
            materials.hpl,
            materials.kd_name,
            material_volumes.lot_completion,
            material_volumes.lot_carton,
            IF ( (materials.hpl = 'MPRO') OR ( materials.hpl = 'WELDING' AND materials.kd_name = 'KEY POST' ),
            material_volumes.lot_carton,
            material_volumes.lot_completion
            ) AS lot
            FROM materials
            LEFT JOIN material_volumes ON materials.material_number = material_volumes.material_number
            WHERE materials.category = 'KD'
            AND materials.hpl IN ( 'MP', 'SUBASSY-CL', 'VN-ASSY', 'SUBASSY-SX', 'CL-BODY', 'SUBASSY-FL', 'ASSY-SX', 'ZPRO', 'MPRO', 'WELDING', 'BPRO', 'VN-INJECTION', 'RC ASSY' )
            AND ( materials.kd_name IS NULL OR materials.kd_name != 'SINGLE')");

        return view(
            'extra_order.completion_page',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'storage_locations' => $storage_location,
                'volumes' => $volume,
            )
        )->with('page', 'Extra Order Completion')->with('head', 'Extra Order');
    }

    public function indexStuffingPage()
    {
        $title = 'Extra Order Stuffing';
        $title_jp = 'エキストラオーダースタッフィング';

        $container_schedules = MasterChecksheet::leftJoin(
            'shipment_conditions',
            'shipment_conditions.shipment_condition_code',
            '=',
            'master_checksheets.carier'
        )
            ->whereNull('status')
            ->get();

        return view(
            'extra_order.stuffing_page',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'container_schedules' => $container_schedules,
            )
        )->with('page', 'Extra Order Stuffing')->with('head', 'Extra Order');
    }

    public function indexDeliveryPage()
    {
        $title = 'Extra Order Delivery';
        $title_jp = 'エキストラオーダーデリバリー';

        return view(
            'extra_order.delivery_page',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Extra Order Delivery')->with('head', 'Extra Order');
    }

    public function indexExtraOrder()
    {
        $title = 'Extra Order';
        $title_jp = 'エキストラオーダー';

        $now = date('Y-m-d');

        $fiscal_year = WeeklyCalendar::where('week_date', '=', $now)->first();
        $weekly_calendar = WeeklyCalendar::where('fiscal_year', '=', $fiscal_year->fiscal_year)
            ->selectRaw('min(week_date) as first, max(week_date) as last')
            ->first();

        $materials = db::select("SELECT
           eom.material_number,
           eom.material_number_buyer,
           eom.description,
           eom.uom,
           COALESCE(eop.sales_price, 0) AS sales_price,
           eom.storage_location
           FROM
           extra_order_materials AS eom
           LEFT JOIN ( SELECT * FROM extra_order_prices
           WHERE valid_date >= '" . $weekly_calendar->first . "'
           AND valid_date <= '" . $weekly_calendar->last . "' ) AS eop
           ON eom.material_number = eop.material_number
           WHERE eom.material_number <> 'NEW'");

        $prices = db::table('extra_order_prices')->get();
        $buyers = db::table('extra_order_buyers')->get();
        $po_uploaders = db::table('users')
            ->where(function ($group) {
                $group->whereIn('role_code', ['BUYER', 'C-PC', 'S-PC', 'C-PE', 'S-PE'])
                    ->orWhereIn('username', ['PI2111045', 'PI1612005']);
            })
            ->whereNull('deleted_at')
            ->orderBy('role_code')
            ->orderBy('name')
            ->get();

        $destinations = db::table('destinations')->whereNull('deleted_at')->get();

        return view(
            'extra_order.index_new',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'now' => date('Y-m-d'),
                'materials' => $materials,
                'user' => Auth::user(),
                'prices' => $prices,
                'buyers' => $buyers,
                'po_uploaders' => $po_uploaders,
                'destinations' => $destinations,
            )
        )->with('page', 'Extra Order')->with('head', 'Extra Order');
    }

    public function indexUploadPo(Request $request)
    {
        $title = 'Upload PO Extra Order';
        $title_jp = 'エキストラオーダー発注書のアプロード';

        $eo_number = $request->get('eo_number');

        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
        $detail = ExtraOrderDetail::where('eo_number', $eo_number)->get();
        $approval = ExtraOrderApproval::where('eo_number', $eo_number)->get();
        $user = db::table('users')->get();

        $progress = db::select("SELECT eo_number, SUM(IF(material_number = 'NEW', 0, 1)) AS new_bom, SUM(IF(sales_price = 0, 0, 1)) AS new_price, SUM(1) AS total  FROM `extra_order_details`
           WHERE eo_number = '" . $eo_number . "'
           GROUP BY eo_number");

        if (strlen($extra_order->po_number) > 0) {
            return view(
                'extra_order.upload_po_notification',
                array(
                    'title' => 'PO Extra Order',
                    'title_jp' => 'エキストラオーダーの購入依頼書',
                    'code' => '2',
                    'eo_number' => $eo_number,
                )
            )->with('page', 'Extra Order')->with('head', 'PO Extra Order');
        } else {
            return view(
                'extra_order.upload_po',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'extra_order' => $extra_order,
                    'detail' => $detail,
                    'approval' => $approval,
                    'progress' => $progress,
                    'user' => $user,
                )
            )->with('page', 'Extra Order')->with('head', 'Extra Order');
        }
    }

    public function indexPoNotification($eo_number)
    {
        return view(
            'extra_order.upload_po_notification',
            array(
                'title' => 'PO Extra Order',
                'title_jp' => 'エキストラオーダーの購入依頼書',
                'code' => '1',
                'eo_number' => $eo_number,
            )
        )->with('page', 'Extra Order')->with('head', 'PO Extra Order');
    }

    public function indexExtraOrderDetail($eo_number)
    {
        $title = 'Extra Order Detail';
        $title_jp = 'エキストラオーダー詳細';

        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
        $detail = ExtraOrderDetail::where('eo_number', $eo_number)->get();
        $approval = ExtraOrderApproval::where('eo_number', $eo_number)->get();
        $user = db::table('users')->get();
        $timeline = ExtraOrderTimeline::where('eo_number', $eo_number)
            ->select(
                'extra_order_timelines.*',
                db::raw('DATE_FORMAT(extra_order_timelines.updated_at, "%Y-%m-%d %H:%i") AS `time`')
            )
            ->orderBy('updated_at')
            ->get();

        $progress = db::select("SELECT eo_number, SUM(IF(material_number = 'NEW', 0, 1)) AS new_bom, SUM(IF(sales_price = 0, 0, 1)) AS new_price, SUM(1) AS total  FROM `extra_order_details`
           WHERE eo_number = '" . $eo_number . "'
           GROUP BY eo_number");

        return view(
            'extra_order.detail',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'extra_order' => $extra_order,
                'detail' => $detail,
                'timeline' => $timeline,
                'approval' => $approval,
                'progress' => $progress,
                'user' => $user,
                'note' => $string = trim(preg_replace('/\s\s+/', '<br>', $extra_order->remark)),
            )
        )->with('page', 'Extra Order')->with('head', 'Extra Order');
    }

    public function indexEocPdf($eo_number)
    {

        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
        $detail = ExtraOrderDetail::where('eo_number', $eo_number)->get();
        $approval = ExtraOrderApproval::where('eo_number', $eo_number)
            ->orderBy('approval_order', 'DESC')
            ->get();
        $prepared_by = user::where('id', $approval[0]->created_by)->first();

        $kage = EmployeeSync::where('position', 'LIKE', '%Manager%')
            ->whereNull('end_date')
            ->orderBy('division', 'ASC')
            ->orderBy('department', 'ASC')
            ->get();

        $approval_kage = [];
        //Genaral Manager
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'General Manager' && $kage[$i]->division == 'Production Division') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'GM. Prod. Div.';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }

            if ($kage[$i]->position == 'General Manager' && $kage[$i]->division == 'Production Support Division') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'GM. Prod. Support';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //Deputy General Manager
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Deputy General Manager' && $kage[$i]->division == 'Production Support Division') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'DGM. Prod. Support';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //QA
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Manager' && $kage[$i]->department == 'Quality Assurance Department') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'QA';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //Production
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Manager' && $kage[$i]->division == 'Production Division' && $kage[$i]->department != 'Quality Assurance Department') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'Production';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //Logistic
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Manager' && $kage[$i]->department == 'Logistic Department') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'Logistic';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //Proc
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Manager' && $kage[$i]->department == 'Procurement Department') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'Procurement';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //PC
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Manager' && $kage[$i]->department == 'Production Control Department') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'Prod. Control';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        $new_approval = [];
        $prepared_by_inserted = false;

        for ($j = 0; $j < count($approval); $j++) {
            if ($approval[$j]->approval_order <= 2) {
                $row = array();
                $row['approval_order'] = $approval[$j]->approval_order;
                $row['approver_name'] = $this->call_name($approval[$j]->approver_name);
                $row['role'] = $approval[$j]->role;
                $row['remark'] = $approval[$j]->remark;
                $row['status'] = $approval[$j]->status;
                $row['approved_at'] = $approval[$j]->approved_at;
                $new_approval[] = $row;
            }

            if (count($new_approval) == 6) {
                $row = array();
                $row['approval_order'] = 0;
                $row['approver_name'] = $this->call_name($prepared_by->name);
                $row['role'] = 'Staff';
                $row['remark'] = 'Prepared by';
                $row['status'] = 'Prepared';
                $row['approved_at'] = $approval[0]->created_at->format('Y-m-d H:i:s');
                $new_approval[] = $row;

                $prepared_by_inserted = true;
            }
        }

        if (!$prepared_by_inserted) {
            $row = array();
            $row['approval_order'] = 0;
            $row['approver_name'] = $this->call_name($prepared_by->name);
            $row['role'] = 'Staff';
            $row['remark'] = 'Prepared by';
            $row['status'] = 'Prepared';
            $row['approved_at'] = $approval[0]->created_at->format('Y-m-d H:i:s');
            $new_approval[] = $row;
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $pdf->loadView(
            'extra_order.pdf_eoc',
            array(
                'extra_order' => $extra_order,
                'detail' => $detail,
                'approval_kage' => $approval_kage,
                'approval' => $new_approval,
                'prepared_at' => $approval[0]->created_at->format('Y-m-d H:i:s'),
            )
        );
        return $pdf->stream("EOC_" . $extra_order->eo_number . ".pdf");
    }

    public function indexSendAppPdf($send_app_no)
    {

        $send_app = SendingApplication::where('send_app_no', $send_app_no)->first();

        $send_app_detail = SendingApplicationDetail::where('send_app_no', $send_app_no)
            ->orderBy('package_no')
            ->get();

        $send_app_log = SendingApplicationLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sending_application_logs.created_by')
            ->where('send_app_no', $send_app_no)
            ->select('sending_application_logs.*', 'employee_syncs.name')
            ->get();

        $destination = Destination::where('destination_code', $send_app->destination_code)->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        // return view('extra_order.pdf_send_app', array(
        //     'send_app' => $send_app,
        //     'send_app_detail' => $send_app_detail,
        //     'send_app_log' => $send_app_log,
        //     'destination' => $destination,
        // ))->with('page', 'Extra Order')->with('head', 'Extra Order');

        $pdf->loadView(
            'extra_order.pdf_send_app',
            array(
                'send_app' => $send_app,
                'send_app_detail' => $send_app_detail,
                'send_app_log' => $send_app_log,
                'destination' => $destination,
            )
        );
        return $pdf->stream($send_app_no . ".pdf");
    }

    public function indexLabelExtraOrder($eo_number_sequence)
    {

        $eo_number = explode('-', $eo_number_sequence)[0];

        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
        $extra_order_detail = ExtraOrderDetail::where('eo_number', $eo_number)->first();
        $sequences = ExtraOrderDetailSequence::where('eo_number_sequence', $eo_number_sequence)->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $pdf->loadView(
            'extra_order.pdf_label',
            array(
                'eo_number_sequence' => $eo_number_sequence,
                'extra_order' => $extra_order,
                'extra_order_detail' => $extra_order_detail,
                'sequences' => $sequences,
            )
        );
        return $pdf->stream($eo_number_sequence . ".pdf");

        // return view('extra_order.pdf_label', array(
        //     'eo_number_sequence' => $eo_number_sequence,
        //     'extra_order' => $extra_order,
        //     'extra_order_detail' => $extra_order_detail,
        //     'sequences' => $sequences
        // ));
    }

    public function fetchDetailSendingApplication(Request $request)
    {
        $send_app_no = $request->get('send_app');
        $send_app = SendingApplication::where('send_app_no', $send_app_no)
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'sending_applications.destination_code')
            ->select(
                'sending_applications.*',
                'destinations.destination_name',
                'destinations.destination_shortname'
            )
            ->withTrashed()
            ->first();

        $send_app_detail = SendingApplicationDetail::where('send_app_no', $send_app_no)
            ->orderBy('package_no', 'ASC')
            ->orderBy('sequence', 'ASC')
            ->withTrashed()
            ->get();

        $send_app_log = SendingApplicationLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sending_application_logs.created_by')
            ->where('sending_application_logs.send_app_no', $send_app_no)
            ->select(
                'sending_application_logs.send_app_no',
                'sending_application_logs.status',
                'employee_syncs.name',
                db::raw('MAX(sending_application_logs.updated_at) AS updated_at')
            )
            ->groupBy(
                'sending_application_logs.send_app_no',
                'sending_application_logs.status',
                'employee_syncs.name'
            )
            ->get();

        $response = array(
            'status' => true,
            'send_app' => $send_app,
            'send_app_detail' => $send_app_detail,
            'send_app_log' => $send_app_log,
        );
        return Response::json($response);

    }

    public function fetchSendingApplication(Request $request)
    {

        $send_app = SendingApplication::leftJoin('destinations', 'destinations.destination_code', '=', 'sending_applications.destination_code');
        if (strlen($request->get('issue_from')) > 0) {
            $issue_from = date('Y-m-d', strtotime($request->get('issue_from')));
            $send_app = $send_app->where(db::raw('date_format(sending_applications.created_at, "%Y-%m-%d")'), '>=', $issue_from);
        }
        if (strlen($request->get('issue_to')) > 0) {
            $issue_to = date('Y-m-d', strtotime($request->get('issue_to')));
            $send_app = $send_app->where(db::raw('date_format(sending_applications.created_at, "%Y-%m-%d")'), '<=', $issue_to);
        }
        if (strlen($request->get('stuffing_from')) > 0) {
            $st_from = date('Y-m-d', strtotime($request->get('stuffing_from')));
            $send_app = $send_app->where('sending_applications.st_date', '>=', $st_from);
        }
        if (strlen($request->get('stuffing_to')) > 0) {
            $st_to = date('Y-m-d', strtotime($request->get('stuffing_to')));
            $send_app = $send_app->where('sending_applications.st_date', '<=', $st_to);
        }
        if ($request->get('search_status') != null) {
            $send_app = $send_app->whereIn('extra_order_approvals.status', '=', $request->get('search_status'));
        }
        if ($request->get('search_payment_term') != null) {
            $send_app = $send_app->whereIn('extra_order_approvals.payment_term', '=', $request->get('search_payment_term'));
        }
        if ($request->get('search_ship_by') != null) {
            $send_app = $send_app->whereIn('extra_order_approvals.ship_by', '=', $request->get('search_ship_by'));
        }
        if ($request->get('search_destination') != null) {
            $send_app = $send_app->whereIn('extra_order_approvals.destination_code', '=', $request->get('search_destination'));
        }

        $send_app = $send_app->select(
            'sending_applications.*',
            db::raw('DATE_FORMAT(sending_applications.created_at, "%d-%b-%Y") AS submit'),
            db::raw('DATE_FORMAT(sending_applications.st_date, "%d-%b-%Y") AS st'),
            db::raw('DATE_FORMAT(sending_applications.bl_date, "%d-%b-%Y") AS bl'),
            'destinations.destination_shortname'
        )
            ->orderBy('sending_applications.created_at', 'DESC')
            ->limit(100)
            ->withTrashed()
            ->get();

        $response = array(
            'status' => true,
            'send_app' => $send_app,
        );
        return Response::json($response);
    }

    public function fetchExtraOrderData(Request $request)
    {

        $submit = '';
        if (strlen($request->get('submit_from')) > 0) {
            $submit_from = date('Y-m-d', strtotime($request->get('submit_from')));
            $submit .= " AND DATE_FORMAT(extra_orders.created_at, '%Y-%m-%d') >= '" . $submit_from . "' ";

            if (strlen($request->get('submit_to')) > 0) {
                $submit_to = date('Y-m-d', strtotime($request->get('submit_to')));
                $submit .= " AND DATE_FORMAT(extra_orders.created_at, '%Y-%m-%d') <= '" . $submit_to . "' ";
            }
        }

        $stuffing = '';
        if (strlen($request->get('request_from')) > 0) {
            $request_from = date('Y-m-d', strtotime($request->get('request_from')));
            $stuffing .= " AND DATE_FORMAT(extra_order_details.request_date, '%Y-%m-%d') >= '" . $request_from . "' ";

            if (strlen($request->get('request_to')) > 0) {
                $request_to = date('Y-m-d', strtotime($request->get('request_to')));
                $stuffing .= " AND DATE_FORMAT(extra_order_details.request_date, '%Y-%m-%d') <= '" . $request_to . "' ";
            }
        }

        $receipt = '';
        if ($request->get('receipt') != null) {
            $receipts = $request->get('receipt');
            for ($i = 0; $i < count($receipts); $i++) {
                $receipt = $receipt . "'" . $receipts[$i] . "'";
                if ($i != (count($receipts) - 1)) {
                    $receipt = $receipt . ',';
                }
            }
            $receipt = " AND extra_orders.attention IN (" . $receipt . ") ";
        }

        $destination = '';
        if ($request->get('destination') != null) {
            $destinations = $request->get('destination');
            for ($i = 0; $i < count($destinations); $i++) {
                $destination = $destination . "'" . $destinations[$i] . "'";
                if ($i != (count($destinations) - 1)) {
                    $destination = $destination . ',';
                }
            }
            $destination = " AND extra_orders.destination_shortname IN (" . $destination . ") ";
        }

        $shipment_by = '';
        if ($request->get('shipment_by') != null) {
            $shipment_bys = $request->get('shipment_by');
            for ($i = 0; $i < count($shipment_bys); $i++) {
                $shipment_by = $shipment_by . "'" . $shipment_bys[$i] . "'";
                if ($i != (count($shipment_bys) - 1)) {
                    $shipment_by = $shipment_by . ',';
                }
            }
            $shipment_by = " AND extra_order_details.shipment_by IN (" . $shipment_by . ") ";
        }

        $blank = '';
        if (strlen($submit) == 0 && strlen($stuffing) == 0 && strlen($receipt) == 0 && strlen($destination) == 0 && strlen($shipment_by) == 0) {
            $blank = " AND DATE_FORMAT(extra_order_details.request_date, '%Y-%m') = '" . date('Y-m') . "'";
        }

        $data = db::select("SELECT
           DATE_FORMAT(extra_orders.created_at, '%d-%b-%Y') AS submission_date,
           extra_orders.eo_number,
           extra_order_details.id,
           extra_orders.attention,
           extra_orders.destination_shortname,
           extra_order_details.shipment_by,
           extra_order_details.material_number,
           extra_order_details.description,
           extra_order_details.storage_location,
           DATE_FORMAT(extra_order_details.due_date, '%d-%b-%Y') AS due_date,
           DATE_FORMAT(extra_order_details.request_date, '%d-%b-%Y') AS request_date,
           extra_order_details.sales_price,
           extra_order_details.quantity AS quantity,
           SUM(IF(extra_order_detail_sequences.`status` >= 1, extra_order_detail_sequences.quantity, 0)) AS act_prod,
           SUM(IF(extra_order_detail_sequences.`status` >= 2, extra_order_detail_sequences.quantity, 0)) AS act_delivery,
           SUM(IF(extra_order_detail_sequences.`status` >= 3, extra_order_detail_sequences.quantity, 0)) AS act_stuffing,
           MAX(date(extra_order_detail_sequences.updated_at)) AS act_stuffing_date
           FROM extra_order_details
           LEFT JOIN extra_orders ON extra_orders.eo_number = extra_order_details.eo_number
           LEFT JOIN extra_order_detail_sequences ON extra_order_detail_sequences.eo_detail_id = extra_order_details.id
           WHERE extra_order_details.deleted_at IS NULL " .
            $submit . $stuffing . $receipt . $destination . $shipment_by . $blank .
            " GROUP BY extra_orders.created_at,
           extra_orders.eo_number,
           extra_order_details.id,
           extra_orders.attention,
           extra_orders.destination_shortname,
           extra_order_details.shipment_by,
           extra_order_details.material_number,
           extra_order_details.description,
           extra_order_details.storage_location,
           extra_order_details.due_date,
           extra_order_details.request_date,
           extra_order_details.sales_price,
           extra_order_details.quantity
           ");

        $eo_number = [];
        for ($i = 0; $i < count($data); $i++) {
            if (!in_array($data[$i]->eo_number, $eo_number)) {
                array_push($eo_number, $data[$i]->eo_number);
            }
        }

        $container = ExtraOrderDetailSequence::whereIn('eo_number', $eo_number)
            ->whereNotNull('container_id')
            ->select(
                'eo_detail_id',
                'container_id'
            )
            ->distinct()
            ->get();

        $invoice_number = ExtraOrderDetailSequence::whereIn('eo_number', $eo_number)
            ->whereNotNull('invoice_number')
            ->select(
                'eo_detail_id',
                'invoice_number'
            )
            ->distinct()
            ->get();

        $response = array(
            'status' => true,
            'data' => $data,
            'container' => $container,
            'invoice_number' => $invoice_number,
            'last_update' => date('Y-m-d H:i:s'),
        );
        return Response::json($response);
    }

    public function fetchOutstandingSendApp()
    {
        if (Auth::user() !== null) {
            $send_app = [];
            if (in_array(Auth::user()->role_code, ['S-LOG'])) {
                $send_app = SendingApplication::whereIn('status', [3, 5])->get();

            } elseif (in_array(Auth::user()->role_code, ['C-LOG', 'OP-LOG', 'SL-LOG', 'L-LOG', 'L-WH', 'OP-WH-Exim'])) {
                $send_app = SendingApplication::where('status', 1)->get();

            }

            return count($send_app);
        } else {
            return 0;
        }

    }

    public function fetchOutstandingUser()
    {

        if (Auth::user() !== null && str_contains(strtoupper(Auth::user()->username), 'PI')) {

            $outstanding = db::select("SELECT * FROM `extra_order_approvals`
                WHERE approver_id = '" . strtoupper(Auth::user()->username) . "'
                AND approval_status = 1");

            return count($outstanding);
        } else {
            return 0;
        }
    }

    public function fetchApprovalChart(Request $request)
    {

        $approval = ExtraOrder::leftJoin('extra_order_approvals', 'extra_orders.eo_number', '=', 'extra_order_approvals.eo_number');

        if ($request->get('condition') == 'outstanding') {

            $outstanding = ExtraOrder::leftJoin('extra_order_approvals', 'extra_orders.eo_number', '=', 'extra_order_approvals.eo_number')
                ->where('extra_order_approvals.approval_status', '<', 3)
                ->select(
                    'extra_orders.eo_number',
                    db::raw('date_format(extra_orders.created_at, "%Y-%m") AS month')
                )
                ->distinct()
                ->get();

            $months = [];
            for ($i = 0; $i < count($outstanding); $i++) {
                if (!in_array($outstanding[$i]->month, $months)) {
                    array_push($months, $outstanding[$i]->month);
                }
            }

            $max = max($months);
            $min = min($months);
            if ($max == $max) {
                $approval = $approval->where(db::raw('date_format(extra_orders.created_at, "%Y-%m")'), $max);
            } else {
                $approval = $approval->whereBetween(db::raw('date_format(extra_orders.created_at, "%Y-%m")'), [$min, $max]);
            }
        } else {
            if ($request->get('condition') == '1month') {
                $month = date('Y-m');
                $approval = $approval->where(db::raw('date_format(extra_orders.created_at, "%Y-%m")'), '>=', $month);
            } elseif ($request->get('condition') == '3month') {
                $month = date('Y-m', strtotime("-3 Months"));
                $approval = $approval->where(db::raw('date_format(extra_orders.created_at, "%Y-%m")'), '>=', $month);
            } elseif ($request->get('condition') == '6month') {
                $month = date('Y-m', strtotime("-6 Months"));
                $approval = $approval->where(db::raw('date_format(extra_orders.created_at, "%Y-%m")'), '>=', $month);
            }
        }

        $approval = $approval->select(
            db::raw('date_format(extra_orders.created_at, "%Y-%m") AS submit_month'),
            'extra_orders.eo_number',
            'extra_orders.attention',
            'extra_orders.destination_shortname',
            db::raw('COALESCE(COUNT(extra_order_approvals.approver_id),0) AS total'),
            db::raw('COALESCE(SUM(IF(extra_order_approvals.`status` = "Approved", 1, 0)),0) AS approve')
        )
            ->groupBy(
                'submit_month',
                'extra_orders.eo_number',
                'extra_orders.attention',
                'extra_orders.destination_shortname'
            )->get();

        $approval_detail = [];
        $eo_number = [];
        for ($i = 0; $i < count($approval); $i++) {
            if (!in_array($approval[$i]->eo_number, $eo_number)) {
                array_push($eo_number, $approval[$i]->eo_number);
            }
        }

        if (count($eo_number) > 0) {
            $approval_detail = ExtraOrderApproval::whereIn('eo_number', $eo_number)
                ->orderBy('eo_number', 'ASC')
                ->orderBy('approval_order', 'ASC')
                ->orderBy('approval_status', 'DESC')
                ->get();
        }

        $response = array(
            'status' => true,
            'approval' => $approval,
            'approval_detail' => $approval_detail,
        );
        return Response::json($response);
    }

    public function fetchShortageMonitoring()
    {
        $now = date('Y-m-d');

        $target = db::select("
            SELECT eod.id,
                eod.due_date,
                eod.request_date,
                eod.eo_number,
                eod.material_number,
                m.is_completion,
                eod.description,
                eo.destination_shortname,
                eod.uom,
                eod.storage_location,
                eod.quantity,
                eod.production_quantity,
                (eod.production_quantity - eod.quantity) AS target
            FROM extra_order_details eod
            LEFT JOIN extra_orders eo ON eo.eo_number = eod.eo_number
            LEFT JOIN extra_order_materials m ON m.material_number = eod.material_number
            WHERE eod.due_date IS NOT NULL
            AND eo.status != 'Complete'
            ORDER BY eod.due_date ASC,
            target ASC");

        $new_target = [];
        for ($i = 0; $i < count($target); $i++) {

            $transfer_qty = ExtraOrderDetailSequence::where('eo_number', $target[$i]->eo_number)
                ->where('material_number', $target[$i]->material_number)
                ->where('status', '>=', 2)
                ->sum('quantity');

            if ($transfer_qty != $target[$i]->quantity) {

                $row = [];
                $row['id'] = $target[$i]->id;
                $row['due_date'] = $target[$i]->due_date;
                $row['eo_number'] = $target[$i]->eo_number;
                $row['material_number'] = $target[$i]->material_number;
                $row['is_completion'] = $target[$i]->is_completion;
                $row['description'] = $target[$i]->description;
                $row['destination_shortname'] = $target[$i]->destination_shortname;
                $row['uom'] = $target[$i]->uom;
                $row['storage_location'] = $target[$i]->storage_location;
                $row['quantity'] = $target[$i]->quantity;
                $row['production_quantity'] = $target[$i]->production_quantity;
                $row['target'] = $target[$i]->target;
                $row['transfer_qty'] = $transfer_qty;

                if ($target[$i]->due_date == null) {
                    $row['due_date'] = $this->generateDueDate($target[$i]->request_date);
                }

                if ($now > $row['due_date']) {
                    $row['overdue'] = $this->generateOverdue($row['due_date'], $now);
                } else {
                    $row['overdue'] = $this->generateOverdue($now, $row['due_date']) * -1;
                }

                $new_target[] = (object) $row;
            }

        }

        $response = array(
            'status' => true,
            'target' => $new_target,
        );
        return Response::json($response);

    }

    public function fetchApprovalMonitoring(Request $request)
    {

        $approval = ExtraOrderApproval::leftJoin('extra_orders', 'extra_orders.eo_number', '=', 'extra_order_approvals.eo_number');
        if (strlen($request->get('submit_from')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('submit_from')));
            $approval = $approval->where(db::raw('date_format(extra_orders.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }
        if (strlen($request->get('submit_to')) > 0) {
            $date_to = date('Y-m-d', strtotime($request->get('submit_to')));
            $approval = $approval->where(db::raw('date_format(extra_orders.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }
        if (strlen($request->get('approver_id')) > 0) {
            $approval = $approval->where('extra_order_approvals.approver_id', '=', $request->get('approver_id'));
            $approval = $approval->where('extra_order_approvals.approval_status', '=', 1);
        }

        if (strlen($request->get('submit_from')) <= 0 && strlen($request->get('submit_to')) <= 0 && strlen($request->get('approver_id')) <= 0) {
            $approval = $approval->where('extra_order_approvals.approval_status', '=', 1);
        }

        // if(strlen($request->get('approval_status')) > 0){
        //     if($request->get('approval_status') == 0){
        //         $approval = $approval->where('extra_order_approvals.approval_status', '=', 1);
        //     }else{
        //         $approval = $approval->where('extra_order_approvals.approval_status', '=', 3);
        //     }
        // }else{
        //     $approval = $approval->where('extra_order_approvals.approval_status', '<', 3);
        // }

        $approval = $approval->select(
            db::raw('date_format(extra_orders.created_at, "%Y-%m-%d") AS submit_date'),
            'extra_orders.eo_number',
            'extra_orders.attention',
            'extra_orders.destination_shortname',
            db::raw('MAX(extra_order_approvals.approval_status) AS approval_status')
        )
            ->groupBy(
                'submit_date',
                'extra_orders.eo_number',
                'extra_orders.attention',
                'extra_orders.destination_shortname'
            );
        if (strlen($request->get('approval_status')) > 0) {
            if ($request->get('approval_status') == 1) {
                $approval = $approval->having('approval_status', '=', 3);
            } else {
                $approval = $approval->having('approval_status', '<', 3);
            }
        }
        $approval = $approval->get();

        $approval_detail = [];
        $eo_number = [];
        for ($i = 0; $i < count($approval); $i++) {
            if (!in_array($approval[$i]->eo_number, $eo_number)) {
                array_push($eo_number, $approval[$i]->eo_number);
            }
        }

        if (count($eo_number) > 0) {
            $approval_detail = ExtraOrderApproval::whereIn('eo_number', $eo_number)
                ->orderBy('eo_number', 'ASC')
                ->orderBy('approval_order', 'ASC')
                ->orderBy('approval_status', 'DESC')
                ->get();
        }

        $response = array(
            'status' => true,
            'approval' => $approval,
            'approval_detail' => $approval_detail,
        );
        return Response::json($response);
    }

    public function fetchWarehouseStock()
    {

        $stock = db::select("
            SELECT
                extra_order_detail_sequences.id,
                extra_order_detail_sequences.eo_number,
                extra_orders.attention,
                extra_orders.division,
                extra_orders.destination_name,
                extra_orders.destination_shortname,
                extra_order_detail_sequences.eo_number_sequence,
                extra_order_detail_sequences.material_number,
                extra_order_detail_sequences.description,
                extra_order_detail_sequences.uom,
                extra_order_detail_sequences.quantity,
                extra_order_detail_sequences.sales_price
            FROM
                extra_order_detail_sequences
                LEFT JOIN extra_orders ON extra_orders.eo_number = extra_order_detail_sequences.eo_number
            WHERE
                extra_order_detail_sequences.`status` = 2
                AND extra_order_detail_sequences.send_app_no IS NULL
            ORDER BY
                extra_order_detail_sequences.eo_number,
                extra_order_detail_sequences.description");

        $eo_number = [];
        for ($i = 0; $i < count($stock); $i++) {
            if (!in_array($stock[$i]->eo_number, $eo_number)) {
                array_push($eo_number, $stock[$i]->eo_number);
            }
        }

        $percentage = db::select("
            SELECT extra_order_details.eo_number,
                SUM( extra_order_details.quantity ) AS quantity,
                SUM( extra_order_detail_sequences.quantity ) AS result,
                ROUND( SUM( extra_order_detail_sequences.quantity )/ SUM( extra_order_details.quantity )* 100 ) AS percentage
            FROM
                extra_order_details
                LEFT JOIN extra_order_detail_sequences
                ON extra_order_detail_sequences.eo_number = extra_order_details.eo_number
            WHERE
                extra_order_details.eo_number IN ( '" . implode("','", $eo_number) . "' )
                AND extra_order_detail_sequences.`status` >= 2
            GROUP BY
                extra_order_details.eo_number");

        $response = array(
            'status' => true,
            'stock' => $stock,
            'percentage' => $percentage,
        );
        return Response::json($response);
    }

    public function fetchExtraOrderDetail(Request $request)
    {
        $status = $request->get('status');
        $storage_location = $request->get('storage_location');

        $extra_order_details = ExtraOrderDetailSequence::leftJoin('extra_orders', 'extra_orders.eo_number', '=', 'extra_order_detail_sequences.eo_number')
            ->leftJoin('extra_order_details', 'extra_order_details.id', '=', 'extra_order_detail_sequences.eo_detail_id')
            ->leftJoin('storage_locations', 'storage_locations.storage_location', '=', 'extra_order_detail_sequences.storage_location')
            ->where('extra_order_detail_sequences.status', $status);

        if (strlen($storage_location) > 0) {
            $extra_order_details = $extra_order_details->where('extra_order_detail_sequences.storage_location', $storage_location);
        }

        $extra_order_details = $extra_order_details->orderBy('extra_order_detail_sequences.updated_at', 'DESC')
            ->select(
                'extra_order_detail_sequences.eo_number_sequence',
                'extra_order_detail_sequences.material_number',
                'extra_order_detail_sequences.description',
                'extra_order_detail_sequences.invoice_number',
                'extra_order_detail_sequences.container_id',
                'extra_order_detail_sequences.description',
                'storage_locations.location',
                'extra_orders.destination_shortname',
                'extra_order_details.request_date',
                'extra_order_detail_sequences.quantity',
                'extra_order_detail_sequences.updated_at'
            )
            ->get();

        return DataTables::of($extra_order_details)
            ->addColumn('reprint', function ($extra_order_details) {
                return '<a href="javascript:void(0)" class="btn btn-sm btn-info" onClick="reprintDetail(id)" id="' . $extra_order_details->eo_number_sequence . '"><i class="fa fa-print"></i></a>';
            })
            ->addColumn('delete', function ($extra_order_details) {
                return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteDetail(id)" id="' . $extra_order_details->eo_number_sequence . '"><i class="fa fa-trash"></i></a>';
            })
            ->rawColumns([
                'reprint' => 'reprint',
                'delete' => 'delete',
            ])
            ->make(true);
    }

    public function fetchCompletionPage(Request $request)
    {
        $date = date('Y-m-d', strtotime('+60 day'));
        // $storage_location = $request->get('storage_location');

        $target = db::select("
            SELECT eod.id,
                eod.due_date,
                eod.eo_number,
                eod.material_number,
                m.is_completion,
                eod.description,
                eo.destination_shortname,
                eod.uom,
                eod.storage_location,
                eod.quantity,
                eod.production_quantity,
                (eod.quantity - eod.production_quantity) AS target
            FROM extra_order_details eod
            LEFT JOIN extra_orders eo ON eo.eo_number = eod.eo_number
            LEFT JOIN extra_order_materials m ON m.material_number = eod.material_number
            WHERE eod.due_date <= '" . $date . "'
            HAVING target > 0
            ORDER BY eod.due_date ASC");

        $response = array(
            'status' => true,
            'target' => $target,
        );
        return Response::json($response);
    }

    public function fetchExtraOrder(Request $request)
    {
        try {

            $where_buyer = '';
            if (Auth::user()->role_code == 'BUYER') {

                // $where_destination = '';
                // $is_buyer_list = ExtraOrderBuyer::where('attention', Auth::user()->name)->first();
                // if ($is_buyer_list) {
                //     $where_destination = " OR eo.destination_shortname = '" . $is_buyer_list->destination_shortname . "'";
                // }

                // $where_buyer = "WHERE ( eo.order_by = '" . Auth::user()->username . "' OR eo.po_by = '" . Auth::user()->username . "' " . $where_destination . " )";
            }

            $extra_orders = db::select("SELECT eo.eo_number, eo.attention, eo.status, eo.destination_shortname, DATE_FORMAT( eo.created_at, '%d %b %Y' ) AS submit_date, eo.po_number FROM extra_orders AS eo
                " . $where_buyer . "
                ORDER BY eo.eo_number DESC");

            $eo_numbers = [];
            $eo_details = [];
            $eo_approvals = [];
            $eo_invoices = [];
            $eo_buyers = ExtraOrderBuyer::get();

            if (count($extra_orders) > 0) {
                for ($i = 0; $i < count($extra_orders); $i++) {
                    if (!in_array($extra_orders[$i]->eo_number, $eo_numbers)) {
                        array_push($eo_numbers, $extra_orders[$i]->eo_number);
                    }
                }

                $where_eo_number = '';
                for ($i = 0; $i < count($eo_numbers); $i++) {
                    $where_eo_number = $where_eo_number . "'" . $eo_numbers[$i] . "'";
                    if ($i != (count($eo_numbers) - 1)) {
                        $where_eo_number = $where_eo_number . ',';
                    }
                }
                $where_eo_number = " AND eo.eo_number IN (" . $where_eo_number . ") ";

                $eo_details = db::select("SELECT *, DATE_FORMAT( eo.request_date, '%d %b %Y' ) AS request_date_formated FROM extra_order_details AS eo WHERE eo.deleted_at IS NULL" . $where_eo_number . " ORDER BY eo.eo_number DESC");

                $eo_approvals = db::select("SELECT eo_number, approver, approved, IF(approver = 0, 'Not submitted yet', IF(approved = 0,'Waiting for approval', IF(approver = approved, 'Fully approved', 'Partially approved'))) AS `status` FROM
                    (SELECT eo.eo_number, SUM(IF(app.id IS NULL,0,1)) AS approver, SUM(IF(app.`status` = 'Approved',1,0)) AS approved FROM extra_orders eo
                    LEFT JOIN extra_order_approvals app ON eo.eo_number = app.eo_number
                    WHERE eo.deleted_at IS NULL" . $where_eo_number . "
                    GROUP BY eo.eo_number) AS resume");

                $eo_invoices = db::select("SELECT eo_number, GROUP_CONCAT(invoice_number) AS invoice_number FROM
                    (SELECT DISTINCT eo.eo_number, eo.invoice_number FROM extra_order_detail_sequences eo
                    WHERE eo.invoice_number IS NOT NULL
                    " . $where_eo_number . "
                    ) AS resume
                    GROUP BY eo_number");

                $eo_users = db::select("SELECT eo.eo_number, eo.order_by, order_by.`name` AS order_by_name, eo.po_by, po_by.`name` AS po_by_name FROM extra_orders eo
                    LEFT JOIN users order_by ON order_by.username = eo.order_by
                    LEFT JOIN users po_by ON po_by.username = eo.po_by
                    WHERE eo.deleted_at IS NULL" . $where_eo_number);
            }

            $min_date = date("Y-m-d", strtotime("-6 months"));
            $max_date = date("Y-m-d", strtotime("+5 months"));

            $calendars = db::select("SELECT DISTINCT DATE_FORMAT(week_date,'%Y-%m') AS `month`,  DATE_FORMAT(week_date,'%b-%y') AS month_text FROM weekly_calendars
                WHERE week_date BETWEEN '" . $min_date . "' AND '" . $max_date . "'
                ORDER BY `month`");

            $response = array(
                'status' => true,
                'calendars' => $calendars,
                'extra_orders' => $extra_orders,
                'eo_details' => $eo_details,
                'eo_approvals' => $eo_approvals,
                'eo_invoices' => $eo_invoices,
                'eo_users' => $eo_users,
                'eo_buyers' => $eo_buyers,
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

    public function fetchGenerateUploadData(Request $request)
    {

        $upload = $request->get('upload');
        $uploadRows = preg_split("/\r?\n/", $upload);

        $data = [];
        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);

            if ($uploadColumn[0] != 'NEW') {
                if (strlen($uploadColumn[0]) != 7) {
                    $response = array(
                        'status' => false,
                        'row' => count($data) . '_' . $uploadColumn[0] . '_' . strlen($uploadColumn[0]),
                        'message' => 'Data does not match the format',
                    );
                    return Response::json($response);
                }
            }

            if (!is_numeric($uploadColumn[2])) {
                $response = array(
                    'status' => false,
                    'row' => count($data) . '_' . $uploadColumn[0],
                    'message' => 'Qty is not a number',
                );
                return Response::json($response);
            }

            $row = array();
            $row['material_number'] = $uploadColumn[0];
            $row['material_description'] = $uploadColumn[1];
            $row['quantity'] = $uploadColumn[2];
            $data[] = $row;
        }

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchShowExtraOrder(Request $request)
    {
        $eo_number = $request->get('eo_number');

        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
        $detail = ExtraOrderDetail::where('eo_number', $eo_number)->get();

        $now = date('Y-m-d');
        $fiscal_year = WeeklyCalendar::where('week_date', '=', $now)->first();
        $weekly_calendar = WeeklyCalendar::where('fiscal_year', '=', $fiscal_year->fiscal_year)
            ->selectRaw('min(week_date) as first, max(week_date) as last')
            ->first();

        $materials = db::select("SELECT
           eom.material_number,
           eom.material_number_buyer,
           eom.description,
           eom.uom,
           COALESCE(eop.sales_price, 0) AS sales_price,
           eom.storage_location
           FROM
           extra_order_materials AS eom
           LEFT JOIN ( SELECT * FROM extra_order_prices
           WHERE valid_date >= '" . $weekly_calendar->first . "'
           AND valid_date <= '" . $weekly_calendar->last . "' ) AS eop
           ON eom.material_number = eop.material_number
           WHERE eom.material_number <> 'NEW'");

        $response = array(
            'status' => true,
            'extra_order' => $extra_order,
            'detail' => $detail,
            'material' => $materials,
        );
        return Response::json($response);
    }

    public function resendUploadPo($eo_number)
    {

        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
        $extra_order_detail = ExtraOrderDetail::where('eo_number', $eo_number)
            ->select(
                'material_number_buyer',
                'material_number',
                'description',
                'request_date',
                'shipment_by',
                'quantity',
                'uom',
                'sales_price',
                db::raw('(sales_price * quantity) AS amount')
            )
            ->get();

        $order_by = db::table('users')->where('username', $extra_order->order_by)->first();
        $po_by = db::table('users')->where('username', $extra_order->po_by)->first();

        $data = [
            'extra_order' => $extra_order,
            'lists' => $extra_order_detail,
            'order_by' => $order_by,
            'po_by' => $po_by,
        ];

        $to = [];
        $cc = $this->pc;

        $po_by = User::where('username', strtoupper($extra_order->po_by))->first();
        if ($po_by) {
            $to_email = $po_by->email;
            array_push($to, strtolower($po_by->email));
        }

        if ($extra_order->po_by != $extra_order->order_by) {
            $order_by = User::where('username', strtoupper($extra_order->order_by))->first();
            if ($order_by) {
                array_push($cc, strtolower($order_by->email));
            }
        }

        if (($key = array_search(strtolower($to_email), $cc)) !== false) {
            unset($cc[$key]);
        }

        ob_clean();
        Excel::create($extra_order->eo_number, function ($excel) use ($data) {
            $excel->sheet($data['extra_order']->eo_number, function ($sheet) use ($data) {
                return $sheet->loadView('extra_order.po', $data);
            });
        })->store('xlsx', public_path('files/extra_order/po_att'));

        Mail::to($to)
            ->cc($cc)
            ->bcc([
                'ympi-mis-ML@music.yamaha.com',
            ])
            ->send(new SendEmail($data, 'eo_upload_po'));

        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function sendNewPoInformation($eo_number)
    {

        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
        $extra_order_detail = ExtraOrderDetail::leftJoin('extra_order_materials', 'extra_order_details.material_number', '=', 'extra_order_materials.material_number')
            ->select(
                'extra_order_details.*',
                'extra_order_materials.is_completion'
            )
            ->where('extra_order_details.eo_number', $eo_number)
            ->get();

        $case = array(
            'imbang.prasetyo@music.yamaha.com',
            'ardianto@music.yamaha.com',
            'bambang.ferry@music.yamaha.com',
            'wachid.hasyim@music.yamaha.com',
            'ipung.dwi.setiawan@music.yamaha.com',
        );
        $assembly = array(
            'imbang.prasetyo@music.yamaha.com',
            'ardianto@music.yamaha.com',
            'bambang.ferry@music.yamaha.com',
            'wachid.hasyim@music.yamaha.com',
            'ipung.dwi.setiawan@music.yamaha.com',
            'hartono@music.yamaha.com',
            'mawan.sujianto@music.yamaha.com',
            'santo.siswa@music.yamaha.com',
            'fudila.isya.arida@music.yamaha.com',
            'mey.indah.astuti@music.yamaha.com',
            'maruli.sapta.adi@music.yamaha.com',
            'putri.airin.sucin@music.yamaha.com',
            'fattatul.mufidah@music.yamaha.com',
        );
        $surface_treatment = array(
            'hartono@music.yamaha.com',
            'mawan.sujianto@music.yamaha.com',
            'santo.siswa@music.yamaha.com',
            'fudila.isya.arida@music.yamaha.com',
            'mey.indah.astuti@music.yamaha.com',
            'maruli.sapta.adi@music.yamaha.com',
            'putri.airin.sucin@music.yamaha.com',
            'fattatul.mufidah@music.yamaha.com',
            'yudi.abtadipa@music.yamaha.com',
        );
        $welding = array(
            'mey.indah.astuti@music.yamaha.com',
            'maruli.sapta.adi@music.yamaha.com',
            'putri.airin.sucin@music.yamaha.com',
            'yudi.abtadipa@music.yamaha.com',
        );
        $bpp = array(
            'hadi.firmansyah@music.yamaha.com',
            'maruli.sapta.adi@music.yamaha.com',
            'putri.airin.sucin@music.yamaha.com',
            'yudi.abtadipa@music.yamaha.com',
        );
        $kpp = array(
            'slamet.hariadi@music.yamaha.com',
            'hendri.susilo@music.yamaha.com',
            'bagus.nur.hidayat@music.yamaha.com',
            'khoirul.umam@music.yamaha.com',
        );
        $ei = array(
            'eko.prasetyo.wicaksono@music.yamaha.com',
            'hendri.susilo@music.yamaha.com',
            'anang.zahroni@music.yamaha.com',
            'khoirul.umam@music.yamaha.com',
        );
        $pc = array(
            'mamluatul.atiyah@music.yamaha.com',
            'istiqomah@music.yamaha.com',
            'ali.murdani@music.yamaha.com',
            'farizca.nurma@music.yamaha.com',
        );
        $qa = array(
            'yayuk.wahyuni@music.yamaha.com',
            'ratri.sulistyorini@music.yamaha.com',
            'agustina.hayati@music.yamaha.com',
            'abdissalam.saidi@music.yamaha.com',
            'sutrisno@music.yamaha.com',
            'ertikto.singgih@music.yamaha.com',
        );

        $storage_locations = [];
        $areas = [];
        $to = [];
        for ($i = 0; $i < count($extra_order_detail); $i++) {
            if (!in_array($extra_order_detail[$i]->storage_location, $storage_locations)) {
                array_push($storage_locations, $extra_order_detail[$i]->storage_location);
                $storage_location = db::table('storage_locations')->where('storage_location', $extra_order_detail[$i]->storage_location)->first();
                if ($storage_location) {
                    if (!in_array($storage_location->area, $areas)) {
                        array_push($areas, $storage_location->area);

                        if ($storage_location->area == 'ASSEMBLY') {
                            if ($extra_order_detail[$i]->storage_location == 'CS91') {
                                $to = array_merge($to, $case);
                            } else {
                                $to = array_merge($to, $assembly);
                            }
                        }

                        if ($storage_location->area == 'ST') {
                            $to = array_merge($to, $surface_treatment);
                        }

                        if ($storage_location->area == 'WELDING') {
                            $to = array_merge($to, $welding);
                        }

                        if ($storage_location->area == 'BPP') {
                            $to = array_merge($to, $bpp);
                        }

                        if ($storage_location->area == 'KPP') {
                            $to = array_merge($to, $kpp);
                        }

                        if ($storage_location->area == 'EI') {
                            $to = array_merge($to, $ei);
                        }
                    }
                }
            }
        }
        $to = array_merge($to, $qa);
        array_unique($to);

        $data = [
            'extra_order' => $extra_order,
            'extra_order_detail' => $extra_order_detail,
        ];

        Mail::to($to)
            ->cc($pc)
            ->bcc([
                'ympi-mis-ML@music.yamaha.com',
            ])
            ->send(new SendEmail($data, 'eo_new_po_information'));

    }

    public function sendMeasurementInfo($send_app_no)
    {
        $send_app = SendingApplication::leftJoin('destinations', 'destinations.destination_code', '=', 'sending_applications.destination_code')
            ->where('send_app_no', $send_app_no)
            ->select(
                'sending_applications.*',
                'destinations.destination_name',
                'destinations.destination_shortname'
            )
            ->first();

        $send_app_detail = SendingApplicationDetail::where('send_app_no', $send_app_no)->get();

        $eo_numbers = json_decode($send_app->document_number);
        $po_numbers = [];

        for ($i = 0; $i < count($eo_numbers); $i++) {
            $this->generateEocPdf($eo_numbers[$i]);

            $extra_order = ExtraOrder::where('eo_number', $eo_numbers[$i])->first();
            $po = json_decode($extra_order->po_number);

            $po_numbers = array_merge($po_numbers, $po);
        }

        try {

            $data = [
                'eo_numbers' => $eo_numbers,
                'po_numbers' => $po_numbers,
                'send_app_no' => $send_app_no,
                'send_app' => $send_app,
                'send_app_detail' => $send_app_detail,
            ];

            Mail::to($this->exim)
                ->cc($this->pc)
                ->bcc([
                    'ympi-mis-ML@music.yamaha.com',
                ])
                ->send(new SendEmail($data, 'send_app_measurement'));

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

    public function sendApplication(Request $request)
    {

        $send_app_no = $request->get('send_app_no');

        $send_app = SendingApplication::leftJoin('destinations', 'destinations.destination_code', '=', 'sending_applications.destination_code')
            ->where('send_app_no', $send_app_no)
            ->select(
                'sending_applications.*',
                'destinations.destination_name',
                'destinations.destination_shortname'
            )
            ->first();

        $send_app_detail = SendingApplicationDetail::where('send_app_no', $send_app_no)->get();

        $eo_numbers = json_decode($send_app->document_number);
        $po_numbers = [];

        for ($i = 0; $i < count($eo_numbers); $i++) {
            $eoc_filename = public_path() . '/files/extra_order/eoc/EOC_' . $eo_numbers[$i] . '.pdf';
            $eoc_exist = file_exists($eoc_filename);

            if (!$eoc_exist) {
                $this->generateEocPdf($eo_numbers[$i]);
            }

            $extra_order = ExtraOrder::where('eo_number', $eo_numbers[$i])->first();
            $po = json_decode($extra_order->po_number);

            $po_numbers = array_merge($po_numbers, $po);
        }

        if ($send_app->sent_email == '0') {
            // NEW SEND SEND APP
            try {
                $log = new SendingApplicationLog([
                    'send_app_no' => $send_app_no,
                    'status' => 1,
                    'created_by' => Auth::user()->username,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $log->save();

                $send_app->status = 1;
                $send_app->sent_email = 1;
                $send_app->save();

            } catch (Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        try {

            $data = [
                'eo_numbers' => $eo_numbers,
                'po_numbers' => $po_numbers,
                'send_app' => $send_app,
                'send_app_detail' => $send_app_detail,
            ];

            Mail::to($this->warehouse)
                ->cc($this->pc)
                ->bcc([
                    'ympi-mis-ML@music.yamaha.com',
                ])
                ->send(new SendEmail($data, 'sending_application'));

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

    public function sendEmailApproval($approval_id)
    {
        $send = ExtraOrderApproval::where('id', $approval_id)->first();

        $to = [];
        $to[] = $send->approver_email;

        $prepared_by = user::where('id', $send->created_by)->first();
        $approval = ExtraOrderApproval::where('eo_number', $send->eo_number)->orderBy('approval_order', 'ASC')->get();
        $extra_order = ExtraOrder::where('eo_number', $send->eo_number)->first();
        $lists = ExtraOrderDetail::where('eo_number', $send->eo_number)
            ->select(
                'extra_order_details.eo_number',
                'extra_order_details.material_number_buyer',
                'extra_order_details.material_number',
                'extra_order_details.description',
                'extra_order_details.uom',
                'extra_order_details.storage_location',
                'extra_order_details.quantity',
                'extra_order_details.sales_price',
                'extra_order_details.shipment_by',
                'extra_order_details.request_date',
                db::raw('ROUND(extra_order_details.quantity * extra_order_details.sales_price, 3) AS amount'),
                'extra_order_details.created_by'
            )
            ->get();

        $filename = null;
        if (!is_null($extra_order->attachment)) {
            $filename = $extra_order->attachment;
        }

        try {

            $order_by = db::table('users')->where('username', $extra_order->order_by)->first();
            $po_by = db::table('users')->where('username', $extra_order->po_by)->first();
            $valcl = db::table('material_plant_data_lists')->whereIn('valcl', ['9010', '9040'])->get();

            $data = [
                'extra_order' => $extra_order,
                'lists' => $lists,
                'approval' => $approval,
                'approval_id' => $send->id,
                'prepared_by' => $prepared_by,
                'filename' => $filename,
                'order_by' => $order_by,
                'po_by' => $po_by,
                'valcl' => $valcl,
            ];

            Mail::to($to)
                ->cc($this->pc)
                ->bcc([
                    'ympi-mis-ML@music.yamaha.com',
                ])
                ->send(new SendEmail($data, 'eo_approval_eoc'));

            $response = array(
                'status' => true,
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }
    }

    public function indexEmailApproval($approval_id)
    {
        $send = ExtraOrderApproval::where('id', $approval_id)->first();
        $prepared_by = user::where('id', $send->created_by)->first();
        $approval = ExtraOrderApproval::where('eo_number', $send->eo_number)->orderBy('approval_order', 'ASC')->get();
        $extra_order = ExtraOrder::where('eo_number', $send->eo_number)->first();
        $lists = ExtraOrderDetail::where('eo_number', $send->eo_number)
            ->select(
                'extra_order_details.eo_number',
                'extra_order_details.material_number_buyer',
                'extra_order_details.material_number',
                'extra_order_details.description',
                'extra_order_details.uom',
                'extra_order_details.storage_location',
                'extra_order_details.quantity',
                'extra_order_details.sales_price',
                'extra_order_details.shipment_by',
                'extra_order_details.request_date',
                db::raw('ROUND(extra_order_details.quantity * extra_order_details.sales_price, 3) AS amount'),
                'extra_order_details.created_by'
            )
            ->get();

        $order_by = db::table('users')->where('username', $extra_order->order_by)->first();
        $po_by = db::table('users')->where('username', $extra_order->po_by)->first();
        $valcl = db::table('material_plant_data_lists')->whereIn('valcl', ['9010', '9040'])->get();

        $data = [
            'extra_order' => $extra_order,
            'lists' => $lists,
            'approval' => $approval,
            'approval_id' => $send->id,
            'prepared_by' => $prepared_by,
            'order_by' => $order_by,
            'po_by' => $po_by,
            'valcl' => $valcl,
        ];

        return view('extra_order.mail.mail_approval', array('data' => $data));
    }

    public function indexRejectPo(Request $request)
    {
        $eo_number = $request->get('eo_number');
        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();

        if ($extra_order->status == 'Waiting PO') {
            $data = [
                'extra_order' => $extra_order,
            ];
            return view('extra_order.reject_po', array('data' => $data));
        } else {
            return view(
                'extra_order.notification',
                array(
                    'title' => 'PO Extra Order',
                    'title_jp' => 'PO エキストラオーダー',
                    'code' => 102,
                    'approval' => $extra_order,
                )
            )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
        }
    }

    public function indexBomMultiLevel($material_number)
    {

        $boms = db::table('bom_outputs')
            ->where('material_parent', '=', $material_number)
            ->get();

        if (count($boms) == 0) {
            return view(
                'extra_order.bom_multi_level',
                array(
                    'title' => 'Multi-Level BOM Not Found',
                    'title_jp' => '',
                    'code' => 0,
                )
            )->with('page', 'Extra Order')->with('head', 'Multi-Level BOM');
        }

        $mpdl = MaterialPlantDataList::get();
        $mpdl_formated = [];
        for ($i = 0; $i < count($mpdl); $i++) {
            $mpdl_formated[$mpdl[$i]->material_number] = $mpdl[$i];
        }

        $level = 0;
        $parent = $material_number;
        $usage = 1;

        $row = array();
        $row['level'] = $level;
        $row['parent'] = $parent;
        $row['child'] = $parent;
        $row['usage'] = $usage;
        $multilevels[] = (object) $row;

        $checks = array();
        for ($j = 0; $j < count($boms); $j++) {
            $row['parent'] = $parent;
            $row['child'] = $boms[$j]->material_child;
            $row['usage'] = $boms[$j]->usage / $boms[$j]->divider;
            $checks[] = $row;
        }

        while (count($checks) > 0) {
            $level++;
            $temps = array();

            for ($x = 0; $x < count($checks); $x++) {
                $bom = db::table('bom_outputs')
                    ->where('material_parent', '=', $checks[$x]['child'])
                    ->get();

                if (count($bom) > 0) {
                    for ($y = 0; $y < count($bom); $y++) {
                        $row = array();
                        $row['parent'] = $parent;
                        $row['child'] = $bom[$y]->material_child;
                        $row['usage'] = $checks[$x]['usage'] * ($bom[$y]->usage / $bom[$y]->divider);
                        $temps[] = $row;
                    }

                }

                $row = array();
                $row['level'] = $level;
                $row['parent'] = $parent;
                $row['child'] = $checks[$x]['child'];
                $row['usage'] = $checks[$x]['usage'];
                $multilevels[] = (object) $row;

            }

            $checks = [];
            $checks = $temps;
        }

        return view(
            'extra_order.bom_multi_level',
            array(
                'code' => 1,
                'bom' => $multilevels,
                'mpdl' => $mpdl_formated,
            )
        )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');

    }

    public function sendApprovalEoc(Request $request)
    {

        $eo_number = $request->get('eo_number');
        $message = $request->get('message');
        $group = json_decode($request->get('group'));

        // Check LT Approval
        if (!$this->checkIssueEoc($eo_number)) {
            $response = array(
                'status' => false,
                'message' => 'Inadequate LT for Approval',
            );
            return Response::json($response);
        }

        // Check non reguler, note wajib diisi
        $extra_order_detail = ExtraOrderDetail::leftJoin('extra_order_materials', 'extra_order_materials.material_number', '=', 'extra_order_details.material_number')
            ->where('extra_order_details.eo_number', $eo_number)
            ->select(
                'extra_order_details.*',
                'extra_order_materials.remark'
            )
            ->get();

        if (strlen($message) == 0) {
            for ($i = 0; $i < count($extra_order_detail); $i++) {
                if ($extra_order_detail[$i]->remark == 'NON REGULER') {
                    $response = array(
                        'status' => false,
                        'message' => 'For non reguler material, note is required',
                    );
                    return Response::json($response);
                }
            }
        }

        // Check SMBMR
        $check_smbmr = (object) $this->checkSmbmr($eo_number);
        if (!$check_smbmr->status) {
            $response = array(
                'status' => false,
                'message' => 'Check Buyer Procurement NG',
                'undefined' => $check_smbmr->undefined,
                'count' => count($check_smbmr->undefined),
            );
            return Response::json($response);
        }

        // Buyer
        $buyer = [];
        $buyer['buyer_pc'] = $this->buyer['buyer_pc'];
        for ($i = 0; $i < count($check_smbmr->buyer); $i++) {
            if ($this->buyer['buyer_pc']['approver_id'] != $check_smbmr->buyer[$i]->employee_id) {
                $row = [
                    "approver_id" => $check_smbmr->buyer[$i]->employee_id,
                    "approver_name" => $check_smbmr->buyer[$i]->name,
                    "approver_email" => $check_smbmr->buyer[$i]->email,
                    "role" => $check_smbmr->buyer[$i]->position,
                    "remark" => "Buyer Procurement",
                ];
                $buyer['buyer_procurement_' . $check_smbmr->buyer[$i]->employee_id] = $row;

            }
        }

        $approver_list = [];
        $department = ['Quality Assurance Department', 'Logistic Department', 'Procurement Department', 'Production Control Department'];

        // Foreman
        foreach ($group as $row) {
            $like[] = "section LIKE '%" . $row . "%'";
        }
        $like[] = "section LIKE '%QA%'";

        $foreman = db::select("SELECT * FROM approvers WHERE (" . implode(" OR ", $like) . ") AND position NOT LIKE '%Manager%'");

        for ($i = 0; $i < count($foreman); $i++) {
            $key = $foreman[$i]->approver_id . '#' . $foreman[$i]->remark;

            if (!array_key_exists($key, $approver_list)) {
                $emp = EmployeeSync::where('employee_id', $foreman[$i]->approver_id)->first();

                $row = array();
                $row['approver_id'] = $foreman[$i]->approver_id;
                $row['approver_name'] = $foreman[$i]->approver_name;
                $row['approver_email'] = $foreman[$i]->approver_email;
                $row['role'] = $emp->position;
                $row['remark'] = $foreman[$i]->remark;

                $approver_list[$key] = $row;
            }

            if (!in_array($foreman[$i]->department, $department)) {
                $department[] = $foreman[$i]->department;
            }
        }

        // Chief PC
        $chief = Approver::where('department', 'Production Control Department')
            ->where('remark', 'Chief')
            ->first();
        $emp = EmployeeSync::where('employee_id', $chief->approver_id)->first();

        $row = array();
        $row['approver_id'] = $chief->approver_id;
        $row['approver_name'] = $chief->approver_name;
        $row['approver_email'] = $chief->approver_email;
        $row['role'] = $emp->position;
        $row['remark'] = 'Chief';

        $key = $chief->approver_id . '#Chief';
        $approver_list[$key] = $row;

        // Chief Procurement
        $chief = Approver::where('department', 'Procurement Department')
            ->where('remark', 'Chief')
            ->first();
        $emp = EmployeeSync::where('employee_id', $chief->approver_id)->first();

        $row = array();
        $row['approver_id'] = $chief->approver_id;
        $row['approver_name'] = $chief->approver_name;
        $row['approver_email'] = $chief->approver_email;
        $row['role'] = $emp->position;
        $row['remark'] = 'Chief';

        $key = $chief->approver_id . '#Chief';
        $approver_list[$key] = $row;

        // Manager
        $manager = Approver::whereIn('department', $department)
            ->where('remark', 'Manager')
            ->get();

        for ($i = 0; $i < count($manager); $i++) {
            $key = $manager[$i]->approver_id . '#' . $manager[$i]->remark;

            if (!array_key_exists($key, $approver_list)) {
                $row = array();
                $row['approver_id'] = $manager[$i]->approver_id;
                $row['approver_name'] = $manager[$i]->approver_name;
                $row['approver_email'] = $manager[$i]->approver_email;
                $row['role'] = $manager[$i]->position;
                $row['remark'] = $manager[$i]->remark;

                $approver_list[$key] = $row;
            }
        }

        $approver_list = array_merge($buyer, $approver_list, $this->deputy_general_manager, $this->general_manager);

        try {
            DB::beginTransaction();

            $buyer_approval = [];
            // Insert Approver
            foreach ($approver_list as $value) {
                $approval_order = 1;

                switch ($value['remark']) {
                    case "Foreman":
                        $approval_order = 2;
                        break;
                    case "Chief":
                        $approval_order = 2;
                        break;
                    case "Manager":
                        $approval_order = 3;
                        break;
                    case "Deputy General Manager":
                        $approval_order = 4;
                        break;
                    case "General Manager":
                        $approval_order = 5;
                        break;
                }

                $approval = new ExtraOrderApproval([
                    'eo_number' => $eo_number,
                    'approval_order' => $approval_order,
                    'approver_id' => $value['approver_id'],
                    'approver_name' => $value['approver_name'],
                    'approver_email' => $value['approver_email'],
                    'role' => $value['role'],
                    'remark' => $value['remark'],
                    'created_by' => Auth::id(),
                ]);
                $approval->save();

                if (str_contains($value['remark'], 'Buyer')) {
                    $buyer_approval[] = $approval->id;
                }
            }

            $update_approval_status = ExtraOrderApproval::whereIn('id', $buyer_approval)
                ->update([
                    'approval_status' => 1,
                ]);

            $update_extra_order = ExtraOrder::where('eo_number', $eo_number)
                ->update([
                    'remark' => $message,
                ]);

            if ($request->file('attachment')) {
                $file = $request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $filename = 'attachment_' . $eo_number . '.' . $extension;
                $destinationPath = public_path() . '/files/extra_order/attachment/';
                $file->move($destinationPath, $filename);

                $update_extra_order = ExtraOrder::where('eo_number', $eo_number)
                    ->update([
                        'attachment' => $filename,
                    ]);
            }

            $result_email_buyer = true;
            for ($i = 0; $i < count($buyer_approval); $i++) {
                $send = $this->sendEmailApproval($buyer_approval[$i]);
                $send = json_encode($send);
                $send = json_decode($send);

                $result_email_buyer = $result_email_buyer && $send->original->status;
            }

            if ($result_email_buyer) {

                // UPDATE TIMELINE
                $timeline = ExtraOrderTimeline::where('eo_number', $eo_number)
                    ->where('remark', 'APPROVAL_EOC')
                    ->where('created_by', Auth::id())
                    ->where(db::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), date('Y-m-d'))
                    ->first();

                if ($timeline) {

                    $timeline->timeline_body = ucwords(Auth::user()->name) . ' has submitted for EOC approval';
                    $timeline->updated_at = date('Y-m-d H:i:s');
                    $timeline->save();

                } else {
                    $extra_order_timeline = new ExtraOrderTimeline([
                        'eo_number' => $eo_number,
                        'timeline_item_icon' => '<i class="fa fa-send"></i>',
                        'timeline_header' => 'EOC Approval Submitted',
                        'timeline_body' => ucwords(Auth::user()->name) . ' has submitted for EOC approval',
                        'timeline_footer' => '',
                        'remark' => 'APPROVAL_EOC',
                        'created_by' => Auth::id(),
                    ]);
                    $extra_order_timeline->save();

                }

                DB::commit();
                $response = array(
                    'status' => true,
                );
                return Response::json($response);
            } else {
                DB::rollback();

                $response = array(
                    'status' => false,
                    'message' => 'Approval EOC Failed to Send',
                );
                return Response::json($response);
            }
        } catch (Exception $e) {
            DB::rollback();

            $response = array(
                'status' => false,
                'message' => 'Approval EOC Failed to Send',
            );
            return Response::json($response);
        }
    }

    public function checkUpdateDelivery($eo_number)
    {

        $extra_order_details = ExtraOrderDetail::where('eo_number', $eo_number)->get();

        $quantity = 0;
        $shipment = 0;
        for ($i = 0; $i < count($extra_order_details); $i++) {
            $quantity += $extra_order_details[$i]->quantity;
            $shipment += $extra_order_details[$i]->shipment_quantity;
        }

        if ($quantity == $shipment) {
            return true;
        } else {
            return false;
        }
    }

    public function emailCompleteExtraOrder($send_app_no)
    {
        $send_app = SendingApplication::where('send_app_no', $send_app_no)
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'sending_applications.destination_code')
            ->select(
                'sending_applications.*',
                'destinations.destination_name',
                'destinations.destination_shortname'
            )
            ->first();

        $send_app_detail = db::select("
            SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(sequence, '-', 1), '-', -1) AS eo_number, material_number, description, uom, sales_price, SUM(quantity) AS quantity FROM `sending_application_details`
            WHERE send_app_no = '" . $send_app_no . "'
            GROUP BY eo_number, material_number, description, uom, sales_price");

        $eo_numbers = json_decode($send_app->document_number);
        $extra_order = ExtraOrder::where('eo_number', $eo_numbers[0])
            ->leftJoin('users', 'users.username', '=', 'extra_orders.order_by')
            ->select(
                'extra_orders.*',
                'users.email'
            )
            ->first();

        $data = [
            'send_app' => $send_app,
            'send_app_detail' => $send_app_detail,
            'extra_order' => $extra_order,
        ];

        $cc = array_merge($this->pc, $this->exim, ['youichi.oyama@music.yamaha.com']);

        Mail::to([$extra_order->email])
            ->cc($cc)
            ->bcc([
                'ympi-mis-ML@music.yamaha.com',
            ])
            ->send(new SendEmail($data, 'complete_extra_order'));

    }

    public function inputCompleteSending(Request $request)
    {

        $send_app_no = $request->get('send_app_no');
        $bl_date = $request->get('bl_date');
        $invoice_number = $request->get('invoice_number');
        $way_bill = $request->get('way_bill');

        $invoice_number_name = '';
        $way_bill_name = '';

        DB::beginTransaction();
        try {

            if ($request->file('invoice_file') != null) {
                if ($files = $request->file('invoice_file')) {
                    $filename = $request->get('invoice_number') . '.pdf';
                    $invoice_number_name = $filename;
                    $files->move('files/extra_order/invoice', $filename);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Attach invoice file!',
                );
                return Response::json($response);
            }

            if ($request->file('way_bill_file') != null) {
                if ($files = $request->file('way_bill_file')) {
                    $filename = $request->get('way_bill') . '.pdf';
                    $way_bill_name = $filename;
                    $files->move('files/extra_order/way_bill', $filename);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Attach way bill file!',
                );
                return Response::json($response);
            }

            $ship_doc = SendingApplication::where('send_app_no', $send_app_no)
                ->update([
                    'status' => 6,
                    'bl_date' => $bl_date,
                    'invoice_number' => $invoice_number,
                    'way_bill' => $way_bill,
                ]);

            $update_extra_order = ExtraOrderDetailSequence::where('send_app_no', $send_app_no)
                ->update([
                    'bl_date' => $bl_date,
                    'invoice_number' => $invoice_number,
                    'status' => 4,
                ]);

            // EMAIL BUYER
            $this->emailCompleteExtraOrder($send_app_no);

            // SAVE RESUME SALES
            $send_app = SendingApplication::where('send_app_no', $send_app_no)->first();
            $send_app_detail = SendingApplicationDetail::where('send_app_no', $send_app_no)
                ->select(
                    'material_number',
                    'description',
                    'sales_price',
                    db::raw('SUM(quantity) AS quantity')
                )
                ->groupBy(
                    'material_number',
                    'description',
                    'sales_price'
                )
                ->get();

            $destination = Destination::where('destination_code', $send_app->destination_code)->first();

            for ($i = 0; $i < count($send_app_detail); $i++) {
                $insert = DB::table('sales_resumes')
                    ->insert([
                        'material_number' => $send_app_detail[$i]->material_number,
                        'material_description' => $send_app_detail[$i]->description,
                        'category' => 'EXTRA ORDER',
                        'price_category' => 'ALL',
                        'invoice_number' => $send_app->invoice_number,
                        'destination_shortname' => $destination->destination_shortname,
                        'bl_date' => $send_app->bl_date,
                        'quantity' => $send_app_detail[$i]->quantity,
                        'price' => $send_app_detail[$i]->sales_price,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            // UPDATE COMPLETE
            $eo_numbers = json_decode($send_app->document_number);
            for ($i = 0; $i < count($eo_numbers); $i++) {
                $quantity = 0;
                $shipment = 0;
                $check_complete = ExtraOrderDetail::where('eo_number', $eo_numbers[$i])->get();
                for ($x = 0; $x < count($check_complete); $x++) {
                    $quantity += $check_complete[$x]->quantity;
                    $shipment += $check_complete[$x]->shipment_quantity;
                }

                if ($quantity == $shipment) {
                    $update_extra_order = ExtraOrder::where('eo_number', $eo_numbers[$i])
                        ->update([
                            'status' => 'Complete',
                        ]);
                }
            }

            // EXTRA ORDER DETAIL SEQUENCE LOG
            $sequence_completes = ExtraOrderDetailSequence::where('send_app_no', $send_app_no)->get();
            for ($i = 0; $i < count($sequence_completes); $i++) {
                $insert_sequence_log = new ExtraOrderDetailSequenceLog([
                    'eo_number_sequence' => $sequence_completes[$i]->eo_number_sequence,
                    'status' => 4,
                    'created_by' => Auth::id(),
                ]);
                $insert_sequence_log->save();
            }

            // UPDATE SEND APP LOG
            $log = new SendingApplicationLog([
                'send_app_no' => $send_app_no,
                'status' => 6,
                'created_by' => Auth::user()->username,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $log->save();

            // UPDATE TIMELINE
            for ($i = 0; $i < count($eo_numbers); $i++) {

                $target = 0;
                $actual = 0;
                $extra_order_details = ExtraOrderDetail::where('eo_number', $eo_numbers[$i])->get();

                for ($x = 0; $x < count($extra_order_details); $x++) {
                    $target += $extra_order_details[$x]->quantity;
                    $actual += $extra_order_details[$x]->shipment_quantity;
                }

                if ($target == $actual) {
                    $extra_order_timeline = new ExtraOrderTimeline([
                        'eo_number' => $eo_numbers[$i],
                        'timeline_item_icon' => '<i class="fa fa-check-square-o"></i>',
                        'timeline_header' => 'Complete',
                        'timeline_body' => 'Extra order are complete',
                        'timeline_footer' => '<button style="margin: 0px 5px 0px 5px;" class="btn btn-primary btn-xs" id="' . $invoice_number_name . '" onclick="downloadIv(id)"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;&nbsp;' . str_replace('.pdf', '', $invoice_number_name) . '</button><button style="margin: 0px 5px 0px 5px;" class="btn btn-primary btn-xs" id="' . $way_bill_name . '" onclick="downloadWayBill(id)"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;&nbsp;' . str_replace('.pdf', '', $way_bill_name) . '</button>',
                        'remark' => '',
                        'created_by' => Auth::id(),
                    ]);
                    $extra_order_timeline->save();

                } else {
                    $extra_order_timeline = new ExtraOrderTimeline([
                        'eo_number' => $eo_numbers[$i],
                        'timeline_item_icon' => '<i class="fa fa-check-square-o"></i>',
                        'timeline_header' => 'Partially Complete',
                        'timeline_body' => 'Extra order are partially complete',
                        'timeline_footer' => '<button style="margin: 0px 5px 0px 5px;" class="btn btn-primary btn-xs" id="' . $invoice_number_name . '" onclick="downloadIv(id)"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;&nbsp;' . str_replace('.pdf', '', $invoice_number_name) . '</button><button style="margin: 0px 5px 0px 5px;" class="btn btn-primary btn-xs" id="' . $way_bill_name . '" onclick="downloadWayBill(id)"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;&nbsp;' . str_replace('.pdf', '', $way_bill_name) . '</button>',
                        'remark' => '',
                        'created_by' => Auth::id(),
                    ]);
                    $extra_order_timeline->save();

                }

            }

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Shipment complete',
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

    public function inputMeasurement(Request $request)
    {

        $send_app_no = $request->get('send_app_no');
        $check_1 = explode(' - ', $request->get('check_1'))[0];
        $check_2 = explode(' - ', $request->get('check_2'))[0];
        $measurement = $request->get('measurement');

        DB::beginTransaction();
        try {

            $update = SendingApplication::where('send_app_no', $send_app_no)
                ->update([
                    'status' => 3,
                ]);

            for ($i = 0; $i < count($measurement); $i++) {

                $update_detail = SendingApplicationDetail::where('id', $measurement[$i]['id'])
                    ->update([
                        'package_no' => $measurement[$i]['pkg_no'],
                        'package_type' => $measurement[$i]['pkg_type'],
                        'length' => $measurement[$i]['length'],
                        'height' => $measurement[$i]['height'],
                        'width' => $measurement[$i]['width'],
                        'weight' => $measurement[$i]['weight'],
                    ]);
            }

            $log = new SendingApplicationLog([
                'send_app_no' => $send_app_no,
                'status' => 2,
                'created_by' => $check_1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $log->save();

            $log = new SendingApplicationLog([
                'send_app_no' => $send_app_no,
                'status' => 3,
                'created_by' => $check_2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $log->save();

            $this->sendMeasurementInfo($send_app_no);

            // UPDATE TIMELINE
            $send_app = SendingApplication::where('send_app_no', $send_app_no)->first();
            $eo_numbers = json_decode($send_app->document_number);

            $checker_1 = EmployeeSync::where('employee_id', $check_1)->first();
            $checker_2 = EmployeeSync::where('employee_id', $check_2)->first();

            for ($i = 0; $i < count($eo_numbers); $i++) {
                $extra_order_timeline = new ExtraOrderTimeline([
                    'eo_number' => $eo_numbers[$i],
                    'timeline_item_icon' => '<i class="fa fa-th-large"></i>',
                    'timeline_header' => 'Process Delivery',
                    'timeline_body' => $send_app_no . "'s packing dimensions have been measured by " . $checker_1->name . ' and ' . $checker_2->name,
                    'timeline_footer' => '',
                    'remark' => '',
                    'created_by' => Auth::id(),
                ]);
                $extra_order_timeline->save();
            }
            // END UPDATE TIMELINE

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Sending application edited successfully',
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

    public function inputShippingDocument(Request $request)
    {
        $send_app_no = $request->get('send_app_no');
        $container_id = $request->get('container_id');
        $ship_iv = $request->get('ship_iv');

        $ck = db::table('master_checksheets')
            ->where('id_checkSheet', $container_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$ck) {
            $response = array(
                'status' => false,
                'message' => 'The digital checksheet has not yet been created',
            );
            return Response::json($response);
        }

        if (!str_contains($ck->invoice, $ship_iv)) {
            $response = array(
                'status' => false,
                'message' => 'Container ID and invoice number unmatch',
            );
            return Response::json($response);
        }

        try {
            $ship_doc = SendingApplication::where('send_app_no', $send_app_no)
                ->update([
                    'status' => 4,
                    'container_id' => $container_id,
                    'st_date' => $ck->Stuffing_date,
                    'invoice_number' => $ship_iv,
                ]);

            $log = new SendingApplicationLog([
                'send_app_no' => $send_app_no,
                'status' => 4,
                'created_by' => Auth::user()->username,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $log->save();

            $update_extra_order = ExtraOrderDetailSequence::where('send_app_no', $send_app_no)
                ->update([
                    'container_id' => $container_id,
                ]);

            // UPDATE TIMELINE
            $send_app = SendingApplication::where('send_app_no', $send_app_no)->first();
            $eo_numbers = json_decode($send_app->document_number);

            for ($i = 0; $i < count($eo_numbers); $i++) {
                $extra_order_timeline = new ExtraOrderTimeline([
                    'eo_number' => $eo_numbers[$i],
                    'timeline_item_icon' => '<i class="fa fa-calendar-check-o"></i>',
                    'timeline_header' => 'Process Delivery',
                    'timeline_body' => 'The stuffing schedule from ' . $send_app_no . ' has been scheduled by ' . ucwords(Auth::user()->name),
                    'timeline_footer' => '',
                    'remark' => '',
                    'created_by' => Auth::id(),
                ]);
                $extra_order_timeline->save();
            }
            // END UPDATE TIMELINE

            $response = array(
                'status' => true,
                'message' => 'Shipping document submitted successfully',
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

    public function editSendingApplication(Request $request)
    {

        $send_app_no = $request->get('send_app_no');
        $shipment_by = $request->get('shipment_by');
        $payment_term = $request->get('payment_term');
        $freight = $request->get('freight');
        $condition = $request->get('condition');
        $edit_send_app_detail = $request->get('edit_send_app_detail');

        DB::beginTransaction();
        try {

            $update = SendingApplication::where('send_app_no', $send_app_no)
                ->update([
                    'shipment_by' => $shipment_by,
                    'payment_term' => $payment_term,
                    'freight' => $freight,
                    'condition' => $condition,
                ]);

            for ($i = 0; $i < count($edit_send_app_detail); $i++) {
                $update = SendingApplicationDetail::where('id', $edit_send_app_detail[$i]['id'])
                    ->update([
                        'po_number' => $edit_send_app_detail[$i]['po_number'],
                    ]);
            }

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Sending application edited successfully',
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

    public function inputApprovalDeleteSendingApplication(Request $request)
    {

        $send_app_no = $request->get('send_app_no');
        $status = $request->get('status');
        $requester = $request->get('requester');

        $send_app = SendingApplication::leftJoin('destinations', 'destinations.destination_code', '=', 'sending_applications.destination_code')
            ->where('send_app_no', $send_app_no)
            ->select(
                'sending_applications.*',
                'destinations.destination_name',
                'destinations.destination_shortname'
            )
            ->first();
        $send_app_detail = SendingApplicationDetail::where('send_app_no', $send_app_no)->get();

        if ($send_app->status >= 4) {
            // Sudah ada shipment doc
            return view(
                'extra_order.notification',
                array(
                    'title' => 'Extra Order Sending Application',
                    'title_jp' => 'エキストラオーダー出荷申請書',
                    'code' => 206,
                )
            )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
        }

        if (!in_array(strtoupper(Auth::user()->role_code), ['S-LOG', 'C-LOG', 'S-MIS'])) {
            // Tidak ada akses
            return view(
                'extra_order.notification',
                array(
                    'title' => 'Extra Order Sending Application',
                    'title_jp' => 'エキストラオーダー出荷申請書',
                    'code' => 201,
                )
            )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
        }

        if (strtolower($status) == 'approve') {
            // Aprove request
            DB::beginTransaction();
            try {
                $delete = SendingApplication::where('send_app_no', $send_app_no)->delete();
                $delete_detail = SendingApplicationDetail::where('send_app_no', $send_app_no)->delete();
                $log = new SendingApplicationLog([
                    'send_app_no' => $send_app_no,
                    'status' => 'X',
                    'created_by' => strtoupper($requester),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $log->save();

                $sequence = ExtraOrderDetailSequence::where('send_app_no', $send_app_no)
                    ->update([
                        'send_app_no' => null,
                        'container_id' => null,
                        'invoice_number' => null,
                        'bl_date' => null,
                    ]);

                $data = [
                    'send_app' => $send_app,
                    'send_app_detail' => $send_app_detail,
                ];

                // UPDATE TIMELINE
                $eo_number = json_decode($send_app->document_number);
                for ($i = 0; $i < count($eo_number); $i++) {
                    $extra_order_timeline = new ExtraOrderTimeline([
                        'eo_number' => $eo_number[$i],
                        'timeline_item_icon' => '<i class="fa fa-calendar-times-o "></i>',
                        'timeline_header' => 'Process Delivery',
                        'timeline_body' => $send_app_no . ' has been canceled by ' . ucwords(Auth::user()->name),
                        'timeline_footer' => '',
                        'remark' => '',
                        'created_by' => Auth::id(),
                    ]);
                    $extra_order_timeline->save();
                }
                // END UPDATE TIMELINE

                $to = array_merge($this->warehouse, $this->exim);
                Mail::to($to)
                    ->cc($this->pc)
                    ->bcc([
                        'ympi-mis-ML@music.yamaha.com',
                    ])
                    ->send(new SendEmail($data, 'delete_sending_application'));

                DB::commit();
                return view(
                    'extra_order.notification',
                    array(
                        'title' => 'Extra Order Sending Application',
                        'title_jp' => 'エキストラオーダー出荷申請書',
                        'code' => 204,
                    )
                )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');

            } catch (Exception $e) {
                DB::rollback();
                return view(
                    'extra_order.notification',
                    array(
                        'title' => 'Extra Order Sending Application',
                        'title_jp' => 'エキストラオーダー出荷申請書',
                        'code' => 205,
                        'message' => $e->getMessage(),
                    )
                )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');

            }

        } else if (strtolower($status) == 'reject') {
            // Reject request
            $title = "Sending Application Extra Order";
            $body = '';
            $body .= '<center>';
            $body .= '<p style="color: #fc4439; font-weight: bold; font-size: 20pt;">Requests for Deletion Sending Application Rejected!</p>';
            $body .= 'Requests for deletion sending application number <b>' . $send_app_no . '</b><br>Already <b>Rejected by ' . ucwords(Auth::user()->name) . '</b><br>Please contact the related PIC for this problem.';
            $body .= '</center>';

            Mail::raw(
                [],
                function ($message) use ($title, $body) {
                    $message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
                    $message->to($this->pc);
                    $message->cc([Auth::user()->email]);
                    $message->bcc(['ympi-mis-ML@music.yamaha.com']);
                    $message->subject($title);
                    $message->setBody($body, 'text/html');
                }
            );

            return view(
                'extra_order.notification',
                array(
                    'title' => 'Extra Order Sending Application',
                    'title_jp' => 'エキストラオーダー出荷申請書',
                    'code' => 203,
                )
            )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');

        } else {
            // Link tidak valid
            return view(
                'extra_order.notification',
                array(
                    'title' => 'Extra Order Sending Application',
                    'title_jp' => 'エキストラオーダー出荷申請書',
                    'code' => 202,
                )
            )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');

        }

    }

    public function deleteSendingApplication(Request $request)
    {
        $send_app_no = $request->get('send_app_no');
        $reason = $request->get('reason');

        $send_app = SendingApplication::leftJoin('destinations', 'destinations.destination_code', '=', 'sending_applications.destination_code')
            ->where('send_app_no', $send_app_no)
            ->select(
                'sending_applications.*',
                'destinations.destination_name',
                'destinations.destination_shortname'
            )
            ->first();
        $send_app_detail = SendingApplicationDetail::where('send_app_no', $send_app_no)->get();

        if ($send_app->status >= 4) {
            $response = array(
                'status' => false,
                'message' => 'Sorry, sending application cant delete, because shipment doc. already prepared by exim staff',
            );
            return Response::json($response);
        }

        if ($send_app->status == 3) {
            // DELETE KETIKA SUDAH DI CHECK WH
            DB::beginTransaction();
            try {
                $update_reason = SendingApplication::where('send_app_no', $send_app_no)
                    ->update([
                        'reason' => $reason,
                    ]);

                $data = [
                    'send_app' => $send_app,
                    'send_app_detail' => $send_app_detail,
                    'reason' => $reason,
                    'requester' => Auth::user()->username,
                ];

                $to = array_merge($this->warehouse, $this->exim);
                Mail::to($to)
                    ->cc($this->pc)
                    ->bcc([
                        'ympi-mis-ML@music.yamaha.com',
                    ])
                    ->send(new SendEmail($data, 'request_delete_sending_application'));

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Waiting approval from exim staff',
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

        } else {
            // DELETE BARU BUAT
            DB::beginTransaction();
            try {

                $update_reason = SendingApplication::where('send_app_no', $send_app_no)
                    ->update([
                        'reason' => $reason,
                    ]);
                $delete = SendingApplication::where('send_app_no', $send_app_no)->delete();
                $delete_detail = SendingApplicationDetail::where('send_app_no', $send_app_no)->delete();
                $log = new SendingApplicationLog([
                    'send_app_no' => $send_app_no,
                    'status' => 'X',
                    'created_by' => Auth::user()->username,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $log->save();

                $sequence = ExtraOrderDetailSequence::where('send_app_no', $send_app_no)
                    ->update([
                        'send_app_no' => null,
                        'container_id' => null,
                        'invoice_number' => null,
                        'bl_date' => null,
                    ]);

                $data = [
                    'send_app' => $send_app,
                    'send_app_detail' => $send_app_detail,
                ];

                // UPDATE TIMELINE
                $eo_number = json_decode($send_app->document_number);
                for ($i = 0; $i < count($eo_number); $i++) {
                    $extra_order_timeline = new ExtraOrderTimeline([
                        'eo_number' => $eo_number[$i],
                        'timeline_item_icon' => '<i class="fa fa-calendar-times-o "></i>',
                        'timeline_header' => 'Process Delivery',
                        'timeline_body' => $send_app_no . ' has been canceled by ' . ucwords(Auth::user()->name),
                        'timeline_footer' => '',
                        'remark' => '',
                        'created_by' => Auth::id(),
                    ]);
                    $extra_order_timeline->save();
                }
                // END UPDATE TIMELINE

                $to = array_merge($this->warehouse, $this->exim);
                Mail::to($to)
                    ->cc($this->pc)
                    ->bcc([
                        'ympi-mis-ML@music.yamaha.com',
                    ])
                    ->send(new SendEmail($data, 'delete_sending_application'));

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Sending application delete successfully',
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

    }

    public function inputSendingApplication(Request $request)
    {
        $eo_number = $request->get('extra_order');
        $sequence = $request->get('sequence');
        $ship_by = $request->get('ship_by');
        $payment_term = $request->get('payment_term');
        $freight = $request->get('freight');
        $condition = $request->get('condition');
        $regulation = $request->get('regulation');

        $extra_order = ExtraOrder::where('eo_number', $eo_number[0])->first();

        DB::beginTransaction();
        try {

            $po_number = json_decode($extra_order->po_number);
            $po_number = str_replace('.pdf', '', explode('__', $po_number[0])[1]);

            $prefix_now = 'SENDAPP-' . date('y');
            $code_generator = CodeGenerator::where('note', '=', 'sending_application')->first();
            if ($prefix_now != $code_generator->prefix) {
                $code_generator->prefix = $prefix_now;
                $code_generator->index = '0';
                $code_generator->save();
            }
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $send_app_no = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $send_app = new SendingApplication([
                'category' => 'EXTRA ORDER',
                'send_app_no' => $send_app_no,
                'document_number' => json_encode($eo_number),
                'attention' => $extra_order->attention,
                'destination_code' => $extra_order->destination_code,
                'division' => $extra_order->division,
                'payment_term' => $payment_term,
                'shipment_by' => $ship_by,
                'freight' => $freight,
                'condition' => $condition,
                'note' => $regulation,
                'status' => 0,
                'created_by' => Auth::id(),
            ]);
            $send_app->save();

            $log = new SendingApplicationLog([
                'send_app_no' => $send_app_no,
                'status' => 0,
                'created_by' => Auth::user()->username,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $log->save();

            for ($i = 0; $i < count($sequence); $i++) {
                $send_app = new SendingApplicationDetail([
                    'send_app_no' => $send_app_no,
                    'sequence' => $sequence[$i]['eo_number_sequence'],
                    'material_number' => $sequence[$i]['material_number'],
                    'description' => $sequence[$i]['description'],
                    'uom' => $sequence[$i]['uom'],
                    'quantity' => $sequence[$i]['quantity'],
                    'sales_price' => $sequence[$i]['sales_price'],
                    'po_number' => $po_number,
                    'created_by' => Auth::id(),
                ]);
                $send_app->save();

                $upt_seq = db::table('extra_order_detail_sequences')
                    ->where('id', $sequence[$i]['id'])
                    ->update([
                        'send_app_no' => $send_app_no,
                    ]);
            }

            $fa = 'fa fa-paper-plane';
            if ($ship_by == 'SEA') {
                $fa = 'fa fa-ship';
            } elseif ($ship_by == 'AIR') {
                $fa = 'fa fa-rocket';
            } elseif ($ship_by == 'TRUCK') {
                $fa = 'fa fa-truck';
            }

            // UPDATE TIMELINE
            for ($i = 0; $i < count($eo_number); $i++) {
                $target = 0;
                $actual = 0;

                $extra_order_details = ExtraOrderDetail::where('eo_number', $eo_number[$i])->get();
                $extra_order_detail_sequences = ExtraOrderDetailSequence::where('eo_number', $eo_number[$i])->where('status', '>', 1)->get();

                for ($j = 0; $j < count($extra_order_details); $j++) {
                    $target += $extra_order_details[$j]->quantity;
                }
                for ($j = 0; $j < count($extra_order_detail_sequences); $j++) {
                    $actual += $extra_order_detail_sequences[$j]->quantity;
                }

                if ($target == $actual) {
                    // LANGSUNG FINISH
                    $extra_order_timeline = new ExtraOrderTimeline([
                        'eo_number' => $eo_number[$i],
                        'timeline_item_icon' => '<i class="' . $fa . '"></i>',
                        'timeline_header' => 'Process Delivery',
                        'timeline_body' => 'Extra order will be sent with a sending application number of ' . $send_app_no,
                        'timeline_footer' => '',
                        'remark' => '',
                        'created_by' => Auth::id(),
                    ]);
                    $extra_order_timeline->save();

                    $update_status = ExtraOrder::where('eo_number', $eo_number[$i])
                        ->update([
                            'status' => 'Delivery Process',
                        ]);

                } else {
                    // START PRODUCTION
                    $extra_order_timeline = new ExtraOrderTimeline([
                        'eo_number' => $eo_number[$i],
                        'timeline_item_icon' => '<i class="' . $fa . '"></i>',
                        'timeline_header' => 'Process Delivery',
                        'timeline_body' => 'Extra order will be sent partially with a sending application number of ' . $send_app_no,
                        'timeline_footer' => '',
                        'remark' => '',
                        'created_by' => Auth::id(),
                    ]);
                    $extra_order_timeline->save();

                }

            }
            // END UPDATE TIMELINE

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Form sending application submitted successfully',
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

    public function inputCancel(Request $request)
    {
        $status = $request->get('status');
        $eo_number_sequence = $request->get('sequence');
        $sequences = ExtraOrderDetailSequence::where('eo_number_sequence', '=', $eo_number_sequence)
            ->where('status', '=', $status)
            ->get();

        if (count($sequences) <= 0) {
            $response = array(
                'status' => false,
                'message' => 'Extra order data not found',
            );
            return Response::json($response);
        }

        if ($status == 1) {

            DB::beginTransaction();
            try {

                //Extra Order Detail Sequence Log
                $insert_sequence_log = new ExtraOrderDetailSequenceLog([
                    'eo_number_sequence' => $eo_number_sequence,
                    'status' => 0,
                    'created_by' => Auth::id(),
                ]);
                $insert_sequence_log->save();

                foreach ($sequences as $sequence) {

                    //Inventory
                    $inventory = Inventory::firstOrNew([
                        'plant' => '8190',
                        'material_number' => $sequence->material_number,
                        'storage_location' => $sequence->storage_location,
                    ]);
                    $inventory->quantity = $inventory->quantity - $sequence->quantity;
                    $inventory->save();

                    //Update Extra Order Detail
                    $detail = ExtraOrderDetail::where('id', '=', $sequence->eo_detail_id)->first();
                    $detail->production_quantity = $detail->production_quantity - $sequence->quantity;
                    $detail->save();

                    $material = ExtraOrderMaterial::where('material_number', $sequence->material_number)->first();

                    if ($material->is_completion == 1) {
                        //Cancel CS
                        $transaction_completion = new TransactionCompletion([
                            'serial_number' => $sequence->eo_number_sequence,
                            'material_number' => $sequence->material_number,
                            'issue_plant' => '8190',
                            'issue_location' => $sequence->storage_location,
                            'quantity' => $sequence->quantity,
                            'movement_type' => '102',
                            'reference_file' => 'directly_executed_on_sap',
                            'created_by' => Auth::id(),
                        ]);
                        $transaction_completion->save();

                        $material = ExtraOrderMaterial::where('material_number', '=', $sequence->material_number)->first();

                        // YMES CANCEL COMPLETION NEW
                        $category = 'production_result_cancel';
                        $function = 'inputCancel';
                        $action = 'production_result';
                        $result_date = date('Y-m-d H:i:s');
                        $slip_number = $sequence->eo_number_sequence;
                        $serial_number = $sequence->serial_number;
                        $material_number = $sequence->material_number;
                        $material_description = $sequence->description;
                        $issue_location = $sequence->storage_location;
                        $mstation = $material->mstation;
                        $quantity = $sequence->quantity * -1;
                        $remark = 'MIRAI';
                        $created_by = Auth::user()->username;
                        $created_by_name = Auth::user()->name;
                        $synced = date('Y-m-d H:i:s');
                        $synced_by = 'manual';

                        app(YMESController::class)->production_result(
                            $category,
                            $function,
                            $action,
                            $result_date,
                            $slip_number,
                            $serial_number,
                            $material_number,
                            $issue_location,
                            $mstation,
                            $quantity,
                            $remark,
                            $created_by,
                            $created_by_name,
                            $synced,
                            $synced_by,
                            $material_description
                        );
                        // YMES END

                    }
                }

                $sequences = ExtraOrderDetailSequence::where('eo_number_sequence', '=', $eo_number_sequence)->delete();

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Completion extra order canceled successfully',
                );
                return Response::json($response);
            } catch (\Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        } elseif ($status == 2) {

            DB::beginTransaction();
            try {

                //Extra Order Detail Sequence Log
                $insert_sequence_log = new ExtraOrderDetailSequenceLog([
                    'eo_number_sequence' => $eo_number_sequence,
                    'status' => 1,
                    'created_by' => Auth::id(),
                ]);
                $insert_sequence_log->save();

                foreach ($sequences as $sequence) {

                    $material = ExtraOrderMaterial::where('material_number', $sequence->material_number)->first();

                    //Inventory FSTK
                    $inventoryFSTK = Inventory::firstOrNew([
                        'plant' => '8191',
                        'material_number' => $sequence->material_number,
                        'storage_location' => 'FSTK',
                    ]);
                    $inventoryFSTK->quantity = $inventoryFSTK->quantity - $sequence->quantity;
                    $inventoryFSTK->save();

                    //Inventory WIP
                    $inventoryWIP = Inventory::firstOrNew([
                        'plant' => '8190',
                        'material_number' => $sequence->material_number,
                        'storage_location' => $sequence->storage_location,
                    ]);
                    $inventoryWIP->quantity = $inventoryWIP->quantity + $sequence->quantity;
                    $inventoryWIP->save();

                    $transaction_transfer = new TransactionTransfer([
                        'plant' => '8190',
                        'serial_number' => $sequence->eo_number_sequence,
                        'material_number' => $sequence->material_number,
                        'issue_plant' => '8190',
                        'issue_location' => $sequence->storage_location,
                        'receive_plant' => '8191',
                        'receive_location' => 'FSTK',
                        'transaction_code' => 'MB1B',
                        'movement_type' => '9P2',
                        'quantity' => $sequence->quantity,
                        'created_by' => Auth::id(),
                    ]);
                    $transaction_transfer->save();

                    $serial_number = $sequence->serial_number;
                    if ($material->is_completion == 1) {
                        $serial_number = $sequence->serial_number;
                    } else {
                        $serial_number = null;
                    }

                    // YMES CANCEL TRANSFER NEW
                    $category = 'goods_movement';
                    $function = 'inputCancel';
                    $action = 'goods_movement';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = $sequence->eo_number_sequence;
                    $material_number = $sequence->material_number;
                    $material_description = $sequence->description;
                    $issue_location = 'FSTK';
                    $receive_location = $sequence->storage_location;
                    $quantity = $sequence->quantity;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->goods_movement(
                        $category,
                        $function,
                        $action,
                        $result_date,
                        $slip_number,
                        $serial_number,
                        $material_number,
                        $issue_location,
                        $receive_location,
                        $quantity,
                        $remark,
                        $created_by,
                        $created_by_name,
                        $synced,
                        $synced_by,
                        $material_description
                    );
                    // YMES END

                }

                $sequences = ExtraOrderDetailSequence::where('eo_number_sequence', '=', $eo_number_sequence)
                    ->update([
                        'status' => ($status - 1),
                    ]);

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Transfer extra order canceled successfully from FSTK',
                );
                return Response::json($response);
            } catch (\Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        } elseif ($status == 3) {

            DB::beginTransaction();
            try {

                //Extra Order Detail Sequence Log
                $insert_sequence_log = new ExtraOrderDetailSequenceLog([
                    'eo_number_sequence' => $eo_number_sequence,
                    'status' => 2,
                    'created_by' => Auth::id(),
                ]);
                $insert_sequence_log->save();

                foreach ($sequences as $sequence) {

                    //Inventory FSTK
                    $inventoryFSTK = Inventory::firstOrNew([
                        'plant' => '8191',
                        'material_number' => $sequence->material_number,
                        'storage_location' => 'FSTK',
                    ]);
                    $inventoryFSTK->quantity = $inventoryFSTK->quantity + $sequence->quantity;
                    $inventoryFSTK->save();

                    //Update Extra Order Detail
                    $detail = ExtraOrderDetail::where('id', '=', $sequence->eo_detail_id)->first();
                    $detail->shipment_quantity = $detail->shipment_quantity - $sequence->quantity;
                    $detail->save();
                }

                $sequences = ExtraOrderDetailSequence::where('eo_number_sequence', '=', $eo_number_sequence)
                    ->update([
                        'status' => ($status - 1),
                        'container_id' => null,
                        'invoice_number' => null,
                    ]);

                if (!$this->checkUpdateDelivery(explode('-', $eo_number_sequence)[0])) {
                    $extra_order = ExtraOrder::where('eo_number', explode('-', $eo_number_sequence)[0])->first();
                    $extra_order->status = 'Production Process';
                    $extra_order->save();
                }

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Stuffing extra order canceled successfully',
                );
                return Response::json($response);
            } catch (\Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }
    }

    public function inputStuffing(Request $request)
    {

        $id = Auth::id();
        $status = $request->get('status');
        $eo_number_sequence = $request->get('eo_number_sequence');
        $invoice_number = $request->get('invoice_number');
        $container_id = $request->get('container_id');

        $sequences = ExtraOrderDetailSequence::where('eo_number_sequence', '=', $eo_number_sequence)->get();

        if (count($sequences) <= 0) {
            $response = array(
                'status' => false,
                'message' => 'Extra Order number not found',
            );
            return Response::json($response);
        }

        if ($sequences[0]->status != ($status - 1)) {
            if ($sequences[0]->status == $status) {
                $response = array(
                    'status' => false,
                    'message' => 'Extra Order number has been stuff',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Wrong process flow',
                );
                return Response::json($response);
            }
        } else {
            DB::beginTransaction();

            try {

                //Extra Order Detail Sequence Log
                $insert_sequence_log = new ExtraOrderDetailSequenceLog([
                    'eo_number_sequence' => $eo_number_sequence,
                    'status' => 3,
                    'created_by' => Auth::id(),
                ]);
                $insert_sequence_log->save();

                foreach ($sequences as $sequence) {

                    //Check Container ID
                    if ($container_id != $sequence->container_id) {
                        DB::rollback();
                        $response = array(
                            'status' => false,
                            'message' => 'Checksheet ID does not match what was scheduled by exim staff',
                        );
                        return Response::json($response);
                    }

                    //Inventory FSTK
                    $inventoryFSTK = Inventory::firstOrNew([
                        'plant' => '8191',
                        'material_number' => $sequence->material_number,
                        'storage_location' => 'FSTK',
                    ]);
                    $inventoryFSTK->quantity = $inventoryFSTK->quantity - $sequence->quantity;
                    $inventoryFSTK->save();

                    //Update Extra Order Detail
                    $detail = ExtraOrderDetail::where('id', '=', $sequence->eo_detail_id)->first();
                    $detail->shipment_quantity = $detail->shipment_quantity + $sequence->quantity;
                    $detail->save();
                }

                $sequences = ExtraOrderDetailSequence::where('eo_number_sequence', '=', $eo_number_sequence)
                    ->update([
                        'status' => $status,
                        'invoice_number' => $invoice_number,
                        'container_id' => $container_id,
                    ]);

                if ($this->checkUpdateDelivery(explode('-', $eo_number_sequence)[0])) {
                    $extra_order = ExtraOrder::where('eo_number', explode('-', $eo_number_sequence)[0])->first();
                    $extra_order->status = 'Delivery Process';
                    $extra_order->save();
                }

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Extra Order number successfully transferred from FSTK',
                );
                return Response::json($response);
            } catch (\Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }
    }

    public function inputDelivery(Request $request)
    {

        $id = Auth::id();
        $status = $request->get('status');
        $eo_number_sequence = $request->get('eo_number_sequence');

        $sequences = ExtraOrderDetailSequence::where('eo_number_sequence', '=', $eo_number_sequence)->get();

        if (count($sequences) <= 0) {
            $response = array(
                'status' => false,
                'message' => 'Extra Order number not found',
            );
            return Response::json($response);
        }

        if ($sequences[0]->status != ($status - 1)) {
            if ($sequences[0]->status == $status) {
                $response = array(
                    'status' => false,
                    'message' => 'Extra Order number has been scanned',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Wrong process flow',
                );
                return Response::json($response);
            }
        } else {

            DB::beginTransaction();
            try {

                //Extra Order Detail Sequence Log
                $insert_sequence_log = new ExtraOrderDetailSequenceLog([
                    'eo_number_sequence' => $eo_number_sequence,
                    'status' => 2,
                    'created_by' => Auth::id(),
                ]);
                $insert_sequence_log->save();

                foreach ($sequences as $sequence) {

                    $material = ExtraOrderMaterial::where('material_number', $sequence->material_number)->first();

                    //Inventory WIP
                    $inventoryWIP = Inventory::firstOrNew([
                        'plant' => '8190',
                        'material_number' => $sequence->material_number,
                        'storage_location' => $sequence->storage_location,
                    ]);
                    $inventoryWIP->quantity = $inventoryWIP->quantity - $sequence->quantity;
                    $inventoryWIP->save();

                    //Inventory FSTK
                    $inventoryFSTK = Inventory::firstOrNew([
                        'plant' => '8191',
                        'material_number' => $sequence->material_number,
                        'storage_location' => 'FSTK',
                    ]);
                    $inventoryFSTK->quantity = $inventoryFSTK->quantity + $sequence->quantity;
                    $inventoryFSTK->save();

                    //Transaction Transfer
                    $transaction_transfer = new TransactionTransfer([
                        'plant' => '8190',
                        'serial_number' => $sequence->eo_number_sequence,
                        'material_number' => $sequence->material_number,
                        'issue_plant' => '8190',
                        'issue_location' => $sequence->storage_location,
                        'receive_plant' => '8191',
                        'receive_location' => 'FSTK',
                        'transaction_code' => 'MB1B',
                        'movement_type' => '9P1',
                        'quantity' => $sequence->quantity,
                        'created_by' => $id,
                    ]);
                    $transaction_transfer->save();

                    // YMES TRANSFER NEW
                    $category = 'goods_movement';
                    $function = 'inputDelivery';
                    $action = 'goods_movement';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = $sequence->eo_number_sequence;
                    $material_number = $sequence->material_number;
                    $material_description = $sequence->description;
                    $issue_location = $sequence->storage_location;
                    $receive_location = 'FSTK';
                    $quantity = $sequence->quantity;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    if ($material->is_completion == 1) {
                        $serial_number = $sequence->serial_number;
                    } else {
                        $serial_number = null;
                    }

                    app(YMESController::class)->goods_movement(
                        $category,
                        $function,
                        $action,
                        $result_date,
                        $slip_number,
                        $serial_number,
                        $material_number,
                        $issue_location,
                        $receive_location,
                        $quantity,
                        $remark,
                        $created_by,
                        $created_by_name,
                        $synced,
                        $synced_by,
                        $material_description
                    );
                    // YMES END

                }

                $sequences = ExtraOrderDetailSequence::where('eo_number_sequence', '=', $eo_number_sequence)
                    ->update([
                        'status' => $status,
                    ]);

                // UPDATE TIMELINE
                $eo_number = explode('-', $eo_number_sequence);
                $timeline = ExtraOrderTimeline::where('eo_number', $eo_number[0])
                    ->where('remark', 'GOOD_MOVEMENT_FSTK')
                    ->first();

                $target = 0;
                $actual = 0;

                $extra_order_details = ExtraOrderDetail::where('eo_number', $eo_number[0])->get();
                $extra_order_detail_sequences = ExtraOrderDetailSequence::where('eo_number', $eo_number[0])->where('status', '>', 1)->get();

                for ($i = 0; $i < count($extra_order_details); $i++) {
                    $target += $extra_order_details[$i]->quantity;
                }
                for ($i = 0; $i < count($extra_order_detail_sequences); $i++) {
                    $actual += $extra_order_detail_sequences[$i]->quantity;
                }

                if ($timeline) {
                    if ($target == $actual) {
                        if (date('Y-m-d') == date('Y-m-d', strtotime($timeline->updated_at))) {
                            // FINISH PRODUCTION HARI SAMA
                            $update_timeline = ExtraOrderTimeline::where('id', $timeline->id)
                                ->update([
                                    'timeline_body' => 'All goods have been moved to FSTK',
                                    'remark' => 'PRODUCTION_FINISH',
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);

                        } else {
                            // FINISH PRODUCTION HARI BEDA
                            $extra_order_timeline = new ExtraOrderTimeline([
                                'eo_number' => $eo_number[0],
                                'timeline_item_icon' => '<i class="fa fa-shopping-cart"></i>',
                                'timeline_header' => 'Goods Movement to FSTK',
                                'timeline_body' => 'All goods have been moved to FSTK',
                                'timeline_footer' => '',
                                'remark' => 'GOOD_MOVEMENT_FSTK',
                                'created_by' => Auth::id(),
                            ]);
                            $extra_order_timeline->save();

                        }

                    }

                } else {
                    if ($target == $actual) {
                        // LANGSUNG FINISH
                        $extra_order_timeline = new ExtraOrderTimeline([
                            'eo_number' => $eo_number[0],
                            'timeline_item_icon' => '<i class="fa fa-shopping-cart"></i>',
                            'timeline_header' => 'Goods Movement to FSTK',
                            'timeline_body' => 'All goods have been moved to FSTK',
                            'timeline_footer' => '',
                            'remark' => 'GOOD_MOVEMENT_FSTK',
                            'created_by' => Auth::id(),
                        ]);
                        $extra_order_timeline->save();

                    } else {
                        // START PRODUCTION
                        $extra_order_timeline = new ExtraOrderTimeline([
                            'eo_number' => $eo_number[0],
                            'timeline_item_icon' => '<i class="fa fa-shopping-cart"></i>',
                            'timeline_header' => 'Goods Movement to FSTK',
                            'timeline_body' => 'Some requests have been moved to FSTK',
                            'timeline_footer' => '',
                            'remark' => 'GOOD_MOVEMENT_FSTK',
                            'created_by' => Auth::id(),
                        ]);
                        $extra_order_timeline->save();

                    }
                }
                // END UPDATE TIMELINE

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Extra Order number successfully transferred to FSTK',
                );
                return Response::json($response);
            } catch (\Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }
    }

    public function inputCompletion(Request $request)
    {
        $packing_list = $request->get('packing_list');

        //Inisiasi nomor sequence
        $extra_order_detail = ExtraOrderDetail::where('eo_number', $packing_list[0]['eo_number'])->first();
        $sequence = $extra_order_detail->sequence + 1;
        $eo_number_sequence = $extra_order_detail->eo_number . '-' . sprintf("%'.02d", $sequence);

        DB::beginTransaction();
        try {

            for ($i = 0; $i < count($packing_list); $i++) {

                $material = ExtraOrderMaterial::where('material_number', $packing_list[$i]['material_number'])->first();
                $price = ExtraOrderPrice::where('material_number', $packing_list[$i]['material_number'])->first();

                //Extra Order Detail
                $update_detail = ExtraOrderDetail::where('id', $packing_list[$i]['extra_order_detail_id'])->first();
                $update_detail->sequence = $sequence;
                $update_detail->production_quantity = $update_detail->production_quantity + $packing_list[$i]['quantity'];
                $update_detail->save();

                //Inisiasi Serial Number
                $serial_generator = CodeGenerator::where('note', '=', 'eo_serial_number')->first();
                $serial_number = sprintf("%'.0" . $serial_generator->length . "d", $serial_generator->index + 1);
                $serial_number = $serial_generator->prefix . $serial_number . $this->generateRandomString();
                $serial_generator->index = $serial_generator->index + 1;
                $serial_generator->save();

                //Extra Order Detail Sequence
                $insert_sequence = new ExtraOrderDetailSequence([
                    'eo_number_sequence' => $eo_number_sequence,
                    'eo_number' => $packing_list[$i]['eo_number'],
                    'eo_detail_id' => $packing_list[$i]['extra_order_detail_id'],
                    'sequence' => $sequence,
                    'serial_number' => $serial_number,
                    'material_number_buyer' => $material->material_number_buyer,
                    'material_number' => $packing_list[$i]['material_number'],
                    'description' => $packing_list[$i]['material_description'],
                    'uom' => $material->uom,
                    'storage_location' => $material->storage_location,
                    'quantity' => $packing_list[$i]['quantity'],
                    'sales_price' => $price->sales_price,
                    'status' => 1,
                    'created_by' => Auth::id(),
                ]);
                $insert_sequence->save();

                //Extra Order Detail Sequence Log
                $insert_sequence_log = new ExtraOrderDetailSequenceLog([
                    'eo_number_sequence' => $eo_number_sequence,
                    'status' => 1,
                    'created_by' => Auth::id(),
                ]);
                $insert_sequence_log->save();

                //Transaction Completion
                if ($material->is_completion == 1) {
                    $transaction_completion = new TransactionCompletion([
                        'serial_number' => $eo_number_sequence,
                        'material_number' => $packing_list[$i]['material_number'],
                        'issue_plant' => '8190',
                        'issue_location' => $material->storage_location,
                        'quantity' => $packing_list[$i]['quantity'],
                        'movement_type' => '101',
                        'created_by' => Auth::id(),
                    ]);
                    $transaction_completion->save();

                    $material = ExtraOrderMaterial::where('material_number', '=', $packing_list[$i]['material_number'])->first();

                    // YMES COMPLETION NEW
                    $category = 'production_result';
                    $function = 'inputCompletion';
                    $action = 'production_result';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = $eo_number_sequence;
                    $serial_number = $serial_number;
                    $material_number = $packing_list[$i]['material_number'];
                    $material_description = $material->description;
                    $issue_location = $material->storage_location;
                    $mstation = $material->mstation;
                    $quantity = $packing_list[$i]['quantity'];
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->production_result(
                        $category,
                        $function,
                        $action,
                        $result_date,
                        $slip_number,
                        $serial_number,
                        $material_number,
                        $issue_location,
                        $mstation,
                        $quantity,
                        $remark,
                        $created_by,
                        $created_by_name,
                        $synced,
                        $synced_by,
                        $material_description
                    );
                    // YMES END
                }

                //Inventory
                $inventory = Inventory::where('plant', '8190')
                    ->where('material_number', $packing_list[$i]['material_number'])
                    ->where('storage_location', $material->storage_location)
                    ->first();

                if ($inventory) {
                    $inventory->quantity = $inventory->quantity + $packing_list[$i]['quantity'];
                } else {
                    $inventory = new Inventory([
                        'plant' => '8190',
                        'material_number' => $packing_list[$i]['material_number'],
                        'storage_location' => $material->storage_location,
                        'quantity' => $packing_list[$i]['quantity'],
                    ]);
                }
                $inventory->save();
            }

            $update_sequence = ExtraOrderDetail::where('eo_number', $packing_list[0]['eo_number'])
                ->update([
                    'sequence' => $sequence,
                ]);

            // UPDATE TIMELINE
            $timeline = ExtraOrderTimeline::where('eo_number', $packing_list[0]['eo_number'])
                ->where('remark', 'PRODUCTION_START')
                ->first();

            $target = 0;
            $actual = 0;

            $extra_order_details = ExtraOrderDetail::where('eo_number', $packing_list[0]['eo_number'])->get();
            for ($i = 0; $i < count($extra_order_details); $i++) {
                $target += $extra_order_details[$i]->quantity;
                $actual += $extra_order_details[$i]->production_quantity;
            }

            if ($timeline) {
                if ($target == $actual) {
                    if (date('Y-m-d') == date('Y-m-d', strtotime($timeline->updated_at))) {
                        // FINISH PRODUCTION HARI SAMA
                        $update_timeline = ExtraOrderTimeline::where('id', $timeline->id)
                            ->update([
                                'timeline_body' => 'All requests have been produced',
                                'remark' => 'PRODUCTION_FINISH',
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                    } else {
                        // FINISH PRODUCTION HARI BEDA
                        $extra_order_timeline = new ExtraOrderTimeline([
                            'eo_number' => $packing_list[0]['eo_number'],
                            'timeline_item_icon' => '<i class="fa fa-cubes"></i>',
                            'timeline_header' => 'Production Progress',
                            'timeline_body' => 'All requests have been produced',
                            'timeline_footer' => '',
                            'remark' => 'PRODUCTION_FINISH',
                            'created_by' => Auth::id(),
                        ]);
                        $extra_order_timeline->save();

                    }

                }

            } else {
                if ($target == $actual) {
                    // LANGSUNG FINISH
                    $extra_order_timeline = new ExtraOrderTimeline([
                        'eo_number' => $packing_list[0]['eo_number'],
                        'timeline_item_icon' => '<i class="fa fa-cubes"></i>',
                        'timeline_header' => 'Production Progress',
                        'timeline_body' => 'All requests have been produced',
                        'timeline_footer' => '',
                        'remark' => 'PRODUCTION_FINISH',
                        'created_by' => Auth::id(),
                    ]);
                    $extra_order_timeline->save();

                } else {
                    // START PRODUCTION
                    $extra_order_timeline = new ExtraOrderTimeline([
                        'eo_number' => $packing_list[0]['eo_number'],
                        'timeline_item_icon' => '<i class="fa fa-cubes"></i>',
                        'timeline_header' => 'Production Progress',
                        'timeline_body' => 'The first FG has been produced',
                        'timeline_footer' => '',
                        'remark' => 'PRODUCTION_START',
                        'created_by' => Auth::id(),
                    ]);
                    $extra_order_timeline->save();

                }
            }
            // END UPDATE TIMELINE

            DB::commit();
            $response = array(
                'status' => true,
                'eo_number_sequence' => $eo_number_sequence,
                'message' => 'Print Label Success',
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

    public function inputResetEoc($approval_id)
    {
        $approval = ExtraOrderApproval::where('id', $approval_id)->first();

        try {
            $approval->approval_status = 1;
            $approval->status = null;
            $approval->approved_at = null;
            $approval->note = null;
            $approval->comment = null;
            $approval->answer = null;
            $approval->save();

            $response = array(
                'status' => true,
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }
    }

    public function inputCommentEoc(Request $request)
    {

        $message = $request->get('message');
        $approval_id = $request->get('approval_id');
        $approval = ExtraOrderApproval::where('id', $approval_id)->first();

        if ($approval->approver_id == strtoupper(Auth::user()->username)) {
            try {
                $approval->status = 'Hold & Comment';
                $approval->approved_at = date('Y-m-d H:i:s');
                $approval->comment = $message;
                $approval->answer = null;
                $approval->save();

                $data = [
                    'approval' => $approval,
                    'status' => 'comment',
                ];

                Mail::to($this->pc)
                    ->bcc([
                        'ympi-mis-ML@music.yamaha.com',
                    ])
                    ->send(new SendEmail($data, 'hold_comment_eoc'));

                return view(
                    'extra_order.notification',
                    array(
                        'title' => 'Extra Order Confirmation',
                        'title_jp' => 'エキストラオーダー確認',
                        'code' => 6,
                        'approval' => $approval,
                    )
                )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
            } catch (Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => 'Comment Failed to Send',
                );
                return Response::json($response);
            }
        } else {
            try {
                $approval->answer = $message;
                $approval->save();

                $data = [
                    'approval' => $approval,
                    'status' => 'answer',
                ];

                Mail::to($approval->approver_email)
                    ->cc($this->pc)
                    ->bcc([
                        'ympi-mis-ML@music.yamaha.com',
                    ])
                    ->send(new SendEmail($data, 'hold_comment_eoc'));

                return view(
                    'extra_order.notification',
                    array(
                        'title' => 'Extra Order Confirmation',
                        'title_jp' => 'エキストラオーダー確認',
                        'code' => 7,
                        'approval' => $approval,
                    )
                )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
            } catch (Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => 'Answer Failed to Send',
                );
                return Response::json($response);
            }
        }
    }

    public function inputApprovalEoc(Request $request)
    {

        $status = $request->get('status');
        $approval_id = $request->get('approval_id');

        $approval = ExtraOrderApproval::where('id', $approval_id)->first();
        if (!$approval) {
            return view('404');
        }

        //Tidak ada autorisasi
        if ((!str_contains(Auth::user()->role_code, 'MIS')) && (strtoupper(Auth::user()->username) != strtoupper($approval->approver_id))) {
            return view(
                'extra_order.notification',
                array(
                    'title' => 'Extra Order Confirmation',
                    'title_jp' => 'エキストラオーダー確認',
                    'code' => 3,
                    'approval' => $approval,
                )
            )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
        }

        //Sudah approve
        if ($approval->status == 'Approved') {
            return view(
                'extra_order.notification',
                array(
                    'title' => 'Extra Order Confirmation',
                    'title_jp' => 'エキストラオーダー確認',
                    'code' => 2,
                    'approval' => $approval,
                )
            )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
        }

        //Sudah reject
        if ($approval->status == 'Rejected') {
            return view(
                'extra_order.notification',
                array(
                    'title' => 'Extra Order Confirmation',
                    'title_jp' => 'エキストラオーダー確認',
                    'code' => 0,
                    'approval' => $approval,
                )
            )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
        }

        //Approver sebelumnya belum approve
        if ($approval->approval_order > 1) {
            $total = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                ->where('approval_order', ($approval->approval_order - 1))
                ->get();

            $approved = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                ->where('approval_order', ($approval->approval_order - 1))
                ->where('status', 'Approved')
                ->get();

            if (count($total) != count($approved)) {
                return view(
                    'extra_order.notification',
                    array(
                        'title' => 'Extra Order Confirmation',
                        'title_jp' => 'エキストラオーダー確認',
                        'code' => 4,
                        'approval' => $approval,
                    )
                )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
            }
        }

        if ($status == 'Approved') {

            try {
                $approval->status = 'Approved';
                $approval->approval_status = 2;
                $approval->approved_at = date('Y-m-d H:i:s');
                if ((strtoupper(Auth::user()->username) != strtoupper($approval->approver_id))) {
                    $approval->note = 'Approved by ' . ucwords(Auth::user()->name);
                }
                $approval->save();

                $full_approve = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                    ->where('approval_status', 2)
                    ->get();

                $all_approval = ExtraOrderApproval::where('eo_number', $approval->eo_number)->get();

                if (count($all_approval) == count($full_approve)) {
                    $all_approval = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                        ->update([
                            'approval_status' => 3,
                        ]);
                }

                $prepared_by = user::where('id', $approval->created_by)->first();
                $approval_list = ExtraOrderApproval::where('eo_number', $approval->eo_number)->orderBy('approval_order', 'ASC')->get();
                $extra_order = ExtraOrder::where('eo_number', $approval->eo_number)->first();
                $lists = ExtraOrderDetail::where('eo_number', $approval->eo_number)
                    ->select(
                        'extra_order_details.eo_number',
                        'extra_order_details.material_number_buyer',
                        'extra_order_details.material_number',
                        'extra_order_details.description',
                        'extra_order_details.uom',
                        'extra_order_details.storage_location',
                        'extra_order_details.quantity',
                        'extra_order_details.sales_price',
                        'extra_order_details.shipment_by',
                        'extra_order_details.request_date',
                        db::raw('ROUND(extra_order_details.quantity * extra_order_details.sales_price, 3) AS amount'),
                        'extra_order_details.created_by'
                    )
                    ->get();

                $filename = null;
                if (!is_null($extra_order->attachment)) {
                    $filename = $extra_order->attachment;
                }

                $order_by = db::table('users')->where('username', $extra_order->order_by)->first();
                $po_by = db::table('users')->where('username', $extra_order->po_by)->first();
                $valcl = db::table('material_plant_data_lists')->whereIn('valcl', ['9010', '9040'])->get();

                $data = [
                    'extra_order' => $extra_order,
                    'lists' => $lists,
                    'approval' => $approval_list,
                    'prepared_by' => $prepared_by,
                    'filename' => $filename,
                    'order_by' => $order_by,
                    'po_by' => $po_by,
                    'valcl' => $valcl,
                ];

                if ($approval->approval_order == 5) {

                    if (is_null($extra_order->po_number)) {
                        $gm = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                            ->where('approval_order', 5)
                            ->get();

                        $gm_sign = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                            ->where('approval_order', 5)
                            ->where('status', 'Approved')
                            ->get();

                        if (count($gm) == count($gm_sign)) {

                            $extra_order->po_sended_at = date('Y-m-d H:i:s');
                            $extra_order->status = 'Waiting PO';
                            $extra_order->save();

                            $to = [];
                            $cc = $this->pc;

                            $po_by = User::where('username', strtoupper($extra_order->po_by))->first();
                            if ($po_by) {
                                $to_email = $po_by->email;
                                array_push($to, strtolower($po_by->email));
                            }

                            if ($extra_order->po_by != $extra_order->order_by) {
                                $order_by = User::where('username', strtoupper($extra_order->order_by))->first();
                                if ($order_by) {
                                    array_push($cc, strtolower($order_by->email));
                                }
                            }

                            if (($key = array_search(strtolower($to_email), $cc)) !== false) {
                                unset($cc[$key]);
                            }

                            ob_clean();
                            Excel::create($extra_order->eo_number, function ($excel) use ($data) {
                                $excel->sheet($data['extra_order']->eo_number, function ($sheet) use ($data) {
                                    return $sheet->loadView('extra_order.po', $data);
                                });
                            })->store('xlsx', public_path('files/extra_order/po_att'));

                            Mail::to($to)
                                ->cc($cc)
                                ->bcc([
                                    'ympi-mis-ML@music.yamaha.com',
                                ])
                                ->send(new SendEmail($data, 'eo_upload_po'));

                            // UPDATE TIMELINE
                            $timeline = ExtraOrderTimeline::where('eo_number', $extra_order->eo_number)
                                ->where('remark', 'EOC_FULL_APPROVED')
                                ->where(db::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), date('Y-m-d'))
                                ->first();

                            if ($timeline) {

                                $timeline->timeline_body = 'The EOC has been fully approved and a PO upload notification email is automatically sent to the PO uploader';
                                $timeline->updated_at = date('Y-m-d H:i:s');
                                $timeline->save();

                            } else {
                                $extra_order_timeline = new ExtraOrderTimeline([
                                    'eo_number' => $extra_order->eo_number,
                                    'timeline_item_icon' => '<i class="fa fa-check-square-o"></i>',
                                    'timeline_header' => 'EOC Fully Approved',
                                    'timeline_body' => 'The EOC has been fully approved and a PO upload notification email is automatically sent to the PO uploader',
                                    'timeline_footer' => '',
                                    'remark' => 'EOC_FULL_APPROVED',
                                    'created_by' => Auth::id(),
                                ]);
                                $extra_order_timeline->save();

                            }

                        }
                    }
                } else if ($approval->approval_order == 4) {
                    $dgm = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                        ->where('approval_order', 4)
                        ->get();

                    $dgm_sign = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                        ->where('approval_order', 4)
                        ->where('status', 'Approved')
                        ->get();

                    if (count($dgm) == count($dgm_sign)) {
                        $send_gm = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                            ->where('approval_order', 5)
                            ->get();

                        $update_approval_status = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                            ->where('approval_order', 5)
                            ->update([
                                'approval_status' => 1,
                            ]);

                        for ($i = 0; $i < count($send_gm); $i++) {
                            $data['approval_id'] = $send_gm[$i]->id;

                            Mail::to($send_gm[$i]->approver_email)
                                ->cc($this->pc)
                                ->bcc([
                                    'ympi-mis-ML@music.yamaha.com',
                                ])
                                ->send(new SendEmail($data, 'eo_approval_eoc'));
                        }
                    }
                } else if ($approval->approval_order == 3) {
                    $manager = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                        ->where('approval_order', 3)
                        ->get();

                    $manager_sign = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                        ->where('approval_order', 3)
                        ->where('status', 'Approved')
                        ->get();

                    if (count($manager) == count($manager_sign)) {
                        $send_dgm = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                            ->where('approval_order', 4)
                            ->get();

                        $update_approval_status = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                            ->where('approval_order', 4)
                            ->update([
                                'approval_status' => 1,
                            ]);

                        for ($i = 0; $i < count($send_dgm); $i++) {
                            $data['approval_id'] = $send_dgm[$i]->id;

                            Mail::to($send_dgm[$i]->approver_email)
                                ->cc($this->pc)
                                ->bcc([
                                    'ympi-mis-ML@music.yamaha.com',
                                ])
                                ->send(new SendEmail($data, 'eo_approval_eoc'));
                        }
                    }
                } else if ($approval->approval_order == 2) {
                    $foreman = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                        ->where('approval_order', 2)
                        ->get();

                    $foreman_sign = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                        ->where('approval_order', 2)
                        ->where('status', 'Approved')
                        ->get();

                    if (count($foreman) == count($foreman_sign)) {
                        $send_manager = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                            ->where('approval_order', 3)
                            ->get();

                        $update_approval_status = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                            ->where('approval_order', 3)
                            ->update([
                                'approval_status' => 1,
                            ]);

                        for ($i = 0; $i < count($send_manager); $i++) {
                            $data['approval_id'] = $send_manager[$i]->id;

                            Mail::to($send_manager[$i]->approver_email)
                                ->cc($this->pc)
                                ->bcc([
                                    'ympi-mis-ML@music.yamaha.com',
                                ])
                                ->send(new SendEmail($data, 'eo_approval_eoc'));
                        }
                    }
                } else if ($approval->approval_order == 1) {
                    $buyer = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                        ->where('approval_order', 1)
                        ->get();

                    $buyer_sign = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                        ->where('approval_order', 1)
                        ->where('status', 'Approved')
                        ->get();

                    if (count($buyer) == count($buyer_sign)) {
                        $send_foreman = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                            ->where('approval_order', 2)
                            ->get();

                        $update_approval_status = ExtraOrderApproval::where('eo_number', $approval->eo_number)
                            ->where('approval_order', 2)
                            ->update([
                                'approval_status' => 1,
                            ]);

                        for ($i = 0; $i < count($send_foreman); $i++) {
                            $data['approval_id'] = $send_foreman[$i]->id;

                            Mail::to($send_foreman[$i]->approver_email)
                                ->cc($this->pc)
                                ->bcc([
                                    'ympi-mis-ML@music.yamaha.com',
                                ])
                                ->send(new SendEmail($data, 'eo_approval_eoc'));
                        }
                    }
                }

                return view(
                    'extra_order.notification',
                    array(
                        'title' => 'Extra Order Confirmation',
                        'title_jp' => 'エキストラオーダー確認',
                        'code' => 1,
                        'approval' => $approval,
                    )
                )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        } elseif ($status == 'Rejected') {

            try {
                $approval->status = 'Rejected';
                $approval->approval_status = 2;
                $approval->approved_at = date('Y-m-d H:i:s');

                if ($approval->remark == 'Buyer Procurement') {
                    if (!str_contains(Auth::user()->role_code, 'MIS')) {
                        $approval->note = 'Rejected by ' . ucwords(Auth::user()->name);
                    }
                }
                $approval->save();

                $extra_order = ExtraOrder::where('eo_number', $approval->eo_number)->first();
                $lists = ExtraOrderDetail::where('eo_number', $approval->eo_number)
                    ->select(
                        'extra_order_details.eo_number',
                        'extra_order_details.material_number_buyer',
                        'extra_order_details.material_number',
                        'extra_order_details.description',
                        'extra_order_details.uom',
                        'extra_order_details.storage_location',
                        'extra_order_details.quantity',
                        'extra_order_details.sales_price',
                        'extra_order_details.shipment_by',
                        'extra_order_details.request_date',
                        db::raw('ROUND(extra_order_details.quantity * extra_order_details.sales_price, 3) AS amount'),
                        'extra_order_details.created_by'
                    )
                    ->get();

                $data = [
                    'extra_order' => $extra_order,
                    'lists' => $lists,
                ];

                Mail::to($this->pc)
                    ->cc($approval->approver_email)
                    ->bcc([
                        'ympi-mis-ML@music.yamaha.com',
                    ])
                    ->send(new SendEmail($data, 'eo_approval_eoc_reject'));

                return view(
                    'extra_order.notification',
                    array(
                        'title' => 'Extra Order Confirmation',
                        'title_jp' => 'エキストラオーダー確認',
                        'code' => 5,
                        'approval' => $approval,
                    )
                )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
            } catch (Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        } elseif ($status == 'Hold') {
            $data = [
                'approver' => strtoupper(Auth::user()->username),
                'approval' => $approval,
            ];

            return view('extra_order.comment', array('data' => $data));
        }
    }

    public function updateSalesPrice(Request $request)
    {
        try {
            $files = "";

            if ($request->file('sales_file') != null) {
                if ($files = $request->file('sales_file')) {
                    $nama = $request->get('gmc_material') . '.pdf';
                    $files->move('uploads/sales_price', $nama);

                    $file_name = $nama;
                }
            } else {
                $file_name = null;
            }

            $trial_price = ExtraOrderPrice::firstOrNew(array('material_number' => $request->get('gmc_material')));
            $trial_price->sales_price = $request->get('price');
            $trial_price->valid_date = $request->get('valid_date');
            $trial_price->attachment = $file_name;
            $trial_price->status = $request->get('status');
            $trial_price->created_by = Auth::user()->id;
            $trial_price->save();

            $date = WeeklyCalendar::where('week_date', $request->get('valid_date'))->first();

            // SALES PRICE
            $sales_price = db::table('sales_prices')
                ->where('fiscal_year', $date->fiscal_year)
                ->where('material_number', $request->get('gmc_material'))
                ->first();

            if (!$sales_price) {
                $insert_sales_price = db::table('sales_prices')
                    ->insert([
                        'fiscal_year' => $date->fiscal_year,
                        'material_number' => $request->get('gmc_material'),
                        'price' => $request->get('price'),
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

            } else {
                $update_sales_price = db::table('sales_prices')
                    ->where('fiscal_year', $date->fiscal_year)
                    ->where('material_number', $request->get('gmc_material'))
                    ->update([
                        'price' => $request->get('price'),
                        'created_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

            }

            // SALES PRICE BK
            $sales_price_bk = db::table('sales_prices_bk')
                ->where('month', date('Y-m-01', strtotime($request->get('valid_date'))))
                ->where('material_number', $request->get('gmc_material'))
                ->where('sales_category', 'ALL')
                ->where('currency', 'USD')
                ->first();

            if (!$sales_price_bk) {
                $insert_sales_price_bk = db::table('sales_prices_bk')
                    ->insert([
                        'fiscal_year' => $date->fiscal_year,
                        'month' => date('Y-m-01', strtotime($request->get('valid_date'))),
                        'material_number' => $request->get('gmc_material'),
                        'sales_category' => 'ALL',
                        'price' => $request->get('price'),
                        'currency' => 'USD',
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

            } else {
                $update_sales_price_bk = db::table('sales_prices_bk')
                    ->where('month', date('Y-m-01', strtotime($request->get('valid_date'))))
                    ->where('material_number', $request->get('gmc_material'))
                    ->where('sales_category', 'ALL')
                    ->where('currency', 'USD')
                    ->update([
                        'price' => $request->get('price'),
                        'created_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

            }

            $extra_order = ExtraOrder::where('status', 'Confirming')
                ->select('eo_number')
                ->get();

            for ($i = 0; $i < count($extra_order); $i++) {

                $eo_detail = ExtraOrderDetail::where('eo_number', $extra_order[$i]->eo_number)
                    ->get();

                $is_price_ready = ExtraOrderDetail::where('eo_number', $extra_order[$i]->eo_number)
                    ->where('sales_price', '>', 0)
                    ->get();

                $update = db::table('extra_order_details')
                    ->where('material_number', $request->get('gmc_material'))
                    ->where('eo_number', $extra_order[$i]->eo_number)
                    ->where('sales_price', 0)
                    ->update([
                        'sales_price' => $request->get('price'),
                    ]);

                $new_price = ExtraOrderDetail::where('eo_number', $extra_order[$i]->eo_number)
                    ->where('sales_price', '>', 0)
                    ->get();

                if (count($new_price) != count($is_price_ready)) {
                    // UPDATE TIMELINE
                    $timeline = ExtraOrderTimeline::where('eo_number', $extra_order[$i]->eo_number)
                        ->where('remark', 'UPDATE_PRICE')
                        ->where('created_by', Auth::id())
                        ->where(db::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), date('Y-m-d'))
                        ->first();

                    $progress = round(count($new_price) / count($eo_detail) * 100);

                    if ($timeline) {

                        $timeline->timeline_footer = 'Sales Price : <b>' . $progress . '%</b>';
                        $timeline->updated_at = date('Y-m-d H:i:s');
                        $timeline->save();

                    } else {
                        $extra_order_timeline = new ExtraOrderTimeline([
                            'eo_number' => $extra_order[$i]->eo_number,
                            'timeline_item_icon' => '<i class="fa fa-pencil-square-o"></i>',
                            'timeline_header' => 'Update Sales Price',
                            'timeline_body' => ucwords(Auth::user()->name) . ' has updated the material sales price',
                            'timeline_footer' => 'Sales Price : <b>' . $progress . '%</b>',
                            'remark' => 'UPDATE_PRICE',
                            'created_by' => Auth::id(),
                        ]);
                        $extra_order_timeline->save();

                    }

                }

            }

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

    public function updateExistingDescription(Request $request)
    {

        $extra_order = ExtraOrder::where('status', 'Confirming')->get();
        if (count($extra_order) == 0) {
            $response = array(
                'status' => false,
                'message' => 'No data updated',
            );
            return Response::json($response);
        }
        $material = ExtraOrderMaterial::where('material_number', $request->get('material_number'))->first();

        $list_extra_order = [];
        for ($i = 0; $i < count($extra_order); $i++) {
            array_push($list_extra_order, $extra_order[$i]->eo_number);
        }

        try {

            $update = ExtraOrderDetail::whereIn('eo_number', $list_extra_order)
                ->where('material_number', $request->get('material_number'))
                ->update([
                    'description' => $material->description,
                ]);

            $response = array(
                'status' => true,
                'message' => $update . ' Items successfully updated',
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

    public function updateExistingMaterial(Request $request)
    {

        $eo_number = $request->get('existing_id');
        $material_id = $request->get('existing_material_id');
        $material_number = $request->get('existing_gmc');
        $remark = $request->get('remark');
        $valcl = $request->get('valcl');
        $sloc = $request->get('sloc');

        $check = ExtraOrderMaterial::where('material_number', $material_number)->first();

        DB::beginTransaction();
        if (!$check) {

            $ymes_material = db::connection('ymes')->table('vm_item0010')
                ->where('item_code', $material_number)
                ->first();

            $is_completion = 0;
            if ($ymes_material->eval_class_code == '9010') {
                $is_completion = 1;
            }

            $mstation = 'W' . $ymes_material->mrp_ctrl . 'S10';
            if ($ymes_material->eval_class_code == '9010') {
                $workctr_code = 'W' . $ymes_material->mrp_ctrl;
                $scm = db::connection('ymes')->table('vm_item0080')
                    ->leftJoin('vm_proc0070', 'vm_proc0070.scm_type', '=', 'vm_item0080.scm_type')
                    ->where('vm_item0080.item_code', '=', $material_number)
                    ->where('vm_proc0070.workctr_code', '=', $workctr_code)
                    ->first();
                if ($scm) {
                    $mstation = $scm->man_stat_cd;
                }
            }

            try {

                $check = ExtraOrderMaterial::where('id', $material_id)->first();

                $eo_detail = ExtraOrderDetail::where('eo_number', $eo_number)
                    ->where('description', $check->description)
                    ->first();

                // UPDATE EO MATERIAL
                $check->material_number = $material_number;
                $check->material_number_buyer = '-';
                $check->description = $ymes_material->item_name;
                $check->uom = $ymes_material->unit_code;
                $check->storage_location = $sloc;
                $check->mstation = $mstation;
                $check->is_completion = $is_completion;
                $check->eo_number = null;
                $check->remark = $remark;
                $check->updated_at = date('Y-m-d H:i:s');
                $check->save();

                if ($eo_detail) {
                    // UPDATE EO DETAIL
                    $eo_detail->material_number = $material_number;
                    $eo_detail->material_number_buyer = '-';
                    $eo_detail->description = $ymes_material->item_name;
                    $eo_detail->uom = $ymes_material->unit_code;
                    $eo_detail->storage_location = $sloc;
                    $eo_detail->created_by = Auth::id();
                    $eo_detail->updated_at = date('Y-m-d H:i:s');
                    $eo_detail->save();

                    // UPDATE TIMELINE
                    $timeline = ExtraOrderTimeline::where('eo_number', $eo_number)
                        ->where('remark', 'UPDATE_MATERIAL')
                        ->where('created_by', Auth::id())
                        ->where(db::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), date('Y-m-d'))
                        ->first();

                    $detail_material = ExtraOrderDetail::where('eo_number', $eo_number)->get();

                    $exist_material = ExtraOrderDetail::where('eo_number', $eo_number)
                        ->where('material_number', '<>', 'NEW')
                        ->get();

                    $progress = round(count($exist_material) / count($detail_material) * 100);

                    if ($timeline) {

                        $timeline->timeline_footer = 'BOM : <b>' . $progress . '%</b>';
                        $timeline->updated_at = date('Y-m-d H:i:s');
                        $timeline->save();

                    } else {
                        $extra_order_timeline = new ExtraOrderTimeline([
                            'eo_number' => $eo_number,
                            'timeline_item_icon' => '<i class="fa fa-pencil-square-o"></i>',
                            'timeline_header' => 'Update BOM',
                            'timeline_body' => ucwords(Auth::user()->name) . ' has updated the material  BOM',
                            'timeline_footer' => 'BOM : <b>' . $progress . '%</b>',
                            'remark' => 'UPDATE_MATERIAL',
                            'created_by' => Auth::id(),
                        ]);
                        $extra_order_timeline->save();

                    }
                }

                // UPDATE LOCK SMBMR
                $update_lock = db::table('locks')
                    ->where('remark', 'breakdown_smbmr')
                    ->update([
                        'status' => 0,
                    ]);

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
                $delete = ExtraOrderMaterial::where('id', $material_id)->forceDelete();

            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        // DELETE EXISTING DATA
        $new = ExtraOrderMaterial::where('material_number', 'NEW')->get();
        for ($i = 0; $i < count($new); $i++) {

            $exist = ExtraOrderDetail::where('material_number', 'NEW')
                ->where('description', $new[$i]->description)
                ->first();

            if (!$exist) {
                try {
                    $delete = ExtraOrderMaterial::where('id', $new[$i]->id)->forceDelete();

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

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function deleteExtraOrderDetail(Request $request)
    {
        try {
            $delete = ExtraOrderDetail::where('id', $request->get('id'))->forceDelete();

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

    public function updateExtraOrder(Request $request)
    {
        $data = $request->get('data');
        $now = date('Y-m-d H:i:s');

        $first_eo_number = ExtraOrderDetail::where('id', $data[0]['id'])->first();
        $eo_number = $first_eo_number->eo_number;

        $update_etd = false;
        $etd_before = '';
        $etd_after = '';

        DB::beginTransaction();
        for ($i = 0; $i < count($data); $i++) {

            $detail = ExtraOrderDetail::where('id', $data[$i]['id'])->first();
            if ($detail->request_date != $data[$i]['etd']) {
                $update_etd = true;
                $etd_before = $detail->request_date;
                $etd_after = $data[$i]['etd'];
            }

            try {

                if ($data[$i]['material_number_ympi'] == 'NEW') {
                    $update = ExtraOrderDetail::where('id', $data[$i]['id'])
                        ->update([
                            'quantity' => $data[$i]['quantity'],
                            'shipment_by' => $data[$i]['shipment'],
                            'request_date' => $data[$i]['etd'],
                            'updated_at' => $now,
                        ]);
                } else {
                    $update = ExtraOrderDetail::where('id', $data[$i]['id'])
                        ->update([
                            'material_number_buyer' => $data[$i]['material_number_buyer'],
                            'material_number' => $data[$i]['material_number_ympi'],
                            'description' => $data[$i]['description'],
                            'uom' => $data[$i]['uom'],
                            'storage_location' => $data[$i]['storage_location'],
                            'quantity' => $data[$i]['quantity'],
                            'sales_price' => $data[$i]['sales_price'],
                            'shipment_by' => $data[$i]['shipment'],
                            'request_date' => $data[$i]['etd'],
                            'updated_at' => $now,
                        ]);

                    $seq_update = ExtraOrderDetailSequence::where('eo_number', $eo_number)
                        ->where('material_number', $data[$i]['material_number_ympi'])
                        ->update([
                            'sales_price' => $data[$i]['sales_price'],
                        ]);

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

        if ($update_etd) {

            // UPDATE TIMELINE
            $timeline = ExtraOrderTimeline::where('eo_number', $eo_number)
                ->where('remark', 'UPDATE_ETD')
                ->where('created_by', Auth::id())
                ->where(db::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), date('Y-m-d'))
                ->first();

            if ($timeline) {

                $timeline->timeline_body = ucwords(Auth::user()->name) . ' has updated request date from ' . $etd_before . ' to ' . $etd_after;
                $timeline->updated_at = date('Y-m-d H:i:s');
                $timeline->save();

            } else {
                $extra_order_timeline = new ExtraOrderTimeline([
                    'eo_number' => $eo_number,
                    'timeline_item_icon' => '<i class="fa fa-calendar-times-o"></i>',
                    'timeline_header' => 'Update ETD',
                    'timeline_body' => ucwords(Auth::user()->name) . ' has updated request date from ' . $etd_before . ' to ' . $etd_after,
                    'remark' => 'UPDATE_ETDx',
                    'created_by' => Auth::id(),
                ]);
                $extra_order_timeline->save();

            }

        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function extendEtd($extra_order)
    {

        $extend = 0;
        $send = date('Y-m-d', strtotime($extra_order->po_sended_at));
        $now = date('Y-m-d');

        $date = WeeklyCalendar::whereBetween('week_date', [$send, $now])
            ->where('remark', '<>', 'H')
            ->get();

        if (count($date) > $this->allowance) {
            $extend = count($date) - $this->allowance;
        }

        return $extend;
    }

    public function checkSmbmr($eo_number)
    {

        $raw_material = [];
        $undefined = [];

        $eo_detail = ExtraOrderDetail::leftJoin('extra_order_materials', 'extra_order_materials.material_number', '=', 'extra_order_details.material_number')
            ->where('extra_order_details.eo_number', $eo_number)
            ->select(
                'extra_order_details.*',
                'extra_order_materials.is_completion'
            )
            ->get();

        for ($i = 0; $i < count($eo_detail); $i++) {

            if ($eo_detail[$i]->is_completion == 1) {

                $smbmr = db::table('smbmrs')
                    ->where('material_parent', $eo_detail[$i]->material_number)
                    ->get();

                if (count($smbmr) > 0) {
                    for ($x = 0; $x < count($smbmr); $x++) {
                        $mc = db::table('material_controls')
                            ->where('material_number', $smbmr[$x]->raw_material)
                            ->first();

                        if (!$mc) {
                            $undefined[] = array(
                                'material_number' => $smbmr[$x]->raw_material,
                                'material_description' => $smbmr[$x]->raw_material_description,
                                'message' => 'Buyer & PCH Control not found',
                            );
                        } else {
                            $raw_material[] = array(
                                'material_number' => $smbmr[$x]->raw_material,
                                'material_description' => $smbmr[$x]->raw_material_description,
                            );
                        }

                    }

                } else {
                    $undefined[] = array(
                        'material_number' => $eo_detail[$i]->material_number,
                        'material_description' => $eo_detail[$i]->description,
                        'message' => 'SMBMR not found',
                    );

                }

            } else {
                $mc = db::table('material_controls')
                    ->where('material_number', $eo_detail[$i]->material_number)
                    ->first();

                if (!$mc) {
                    $undefined[] = array(
                        'material_number' => $eo_detail[$i]->material_number,
                        'material_description' => $eo_detail[$i]->description,
                        'message' => 'Buyer & PCH Control not found',
                    );
                } else {
                    $raw_material[] = array(
                        'material_number' => $mc->material_number,
                        'material_description' => $mc->material_description,
                    );

                }
            }

        }

        if (count($undefined) > 0) {
            $undefined = array_map("unserialize", array_unique(array_map("serialize", $undefined)));

            $response = array(
                'status' => false,
                'undefined' => $undefined,
            );
            return $response;

        } else {
            $raw_material = array_map("unserialize", array_unique(array_map("serialize", $raw_material)));
            $material_number = array_column($raw_material, 'material_number');

            $buyer = db::table('material_controls')
                ->whereIn('material_number', $material_number)
                ->select('pic')
                ->distinct()
                ->get();

            $buyer_id = [];
            for ($i = 0; $i < count($buyer); $i++) {
                $buyer_id[] = $buyer[$i]->pic;
            }

            $buyer = db::table('employee_syncs')
                ->leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                ->whereIn('employee_id', $buyer_id)
                ->distinct()
                ->get();

            $response = array(
                'status' => true,
                'buyer' => $buyer,
            );
            return $response;

        }

    }

    public function checkIssueEoc($eo_number)
    {

        $check = true;
        $extra_order_detail = ExtraOrderDetail::where('eo_number', $eo_number)->get();

        for ($i = 0; $i < count($extra_order_detail); $i++) {
            $req = $extra_order_detail[$i]->request_date;
            $now = date('Y-m-d');

            $date = WeeklyCalendar::whereBetween('week_date', [$now, $req])
                ->where('remark', '<>', 'H')
                ->get();

            if (count($date) < $this->lead_time_eoc) {
                $check = false;
                break;
            }
        }

        return $check;
    }

    public function inputExtraOrderPoNew(Request $request)
    {
        $eo_number = $request->input('eo_number');
        $count_files = $request->input('count_files');
        $upload_directory = 'files/extra_order/po';

        try {
            $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();

            //Save PO
            for ($i = 0; $i < $count_files; $i++) {
                $file = $request->file('file_data_' . $i);
                $file_original_name = $file->getClientOriginalName();

                $po_number = $request->input('po_number_' . $i);
                $extension = pathinfo($file_original_name, PATHINFO_EXTENSION);
                $filename = $eo_number . '__' . $po_number . '.' . $extension;

                $file->move($upload_directory, $filename);

                $data[] = $filename;
            }

            $file_saved = json_encode($data);

            $extra_order->po_number = $file_saved;
            $extra_order->po_uploaded_at = date('Y-m-d H:i:s');
            $extra_order->save();

            // UPDATE TIMELINE
            $extra_order_timeline = new ExtraOrderTimeline([
                'eo_number' => $eo_number,
                'timeline_item_icon' => '<i class="fa fa-file-pdf-o"></i>',
                'timeline_header' => 'PO Submitted',
                'timeline_body' => ucwords(Auth::user()->name) . ' has submitted new PO',
                'timeline_footer' => '',
                'remark' => 'SUBMIT_PO',
                'created_by' => Auth::id(),
            ]);
            $extra_order_timeline->save();

            $this->sendApprovalPo($eo_number);

            $response = array(
                'status' => true,
                'eo_number' => $eo_number,
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

    public function inputRejectPo(Request $request)
    {

        $eo_number = $request->get('eo_number');
        $message = $request->get('message');

        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
        $extra_order->po_number = null;
        $extra_order->save();

        $extra_order_detail = ExtraOrderDetail::where('eo_number', $eo_number)
            ->select(
                'material_number_buyer',
                'material_number',
                'description',
                'request_date',
                'shipment_by',
                'quantity',
                'uom',
                'sales_price',
                db::raw('(sales_price * quantity) AS amount')
            )
            ->get();

        $data = [
            'extra_order' => $extra_order,
            'lists' => $extra_order_detail,
            'message' => $message,
        ];

        $to = [];

        $order_by = User::where('username', strtoupper($extra_order->order_by))->first();
        if ($order_by) {
            array_push($to, strtolower($order_by->email));
        }

        // UPDATE TIMELINE
        $extra_order_timeline = new ExtraOrderTimeline([
            'eo_number' => $eo_number,
            'timeline_item_icon' => '<i class="fa fa-warning"></i>',
            'timeline_header' => 'PO Rejected',
            'timeline_body' => ucwords(Auth::user()->name) . ' has rejected new PO',
            'timeline_footer' => '',
            'remark' => 'REJECT_PO',
            'created_by' => Auth::id(),
        ]);
        $extra_order_timeline->save();

        $this->generateAttPoReject($eo_number);

        Mail::to($to)
            ->cc($this->pc)
            ->bcc([
                'ympi-mis-ML@music.yamaha.com',
            ])
            ->send(new SendEmail($data, 'eo_reupload_po'));

        return view(
            'extra_order.notification',
            array(
                'title' => 'PO Extra Order',
                'title_jp' => 'PO エキストラオーダー',
                'code' => 103,
                'approval' => $extra_order,
            )
        )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
    }

    public function inputApprovalPo(Request $request)
    {
        $eo_number = $request->get('eo_number');

        try {
            $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
            $extra_order_detail = ExtraOrderDetail::where('eo_number', $extra_order->eo_number)->get();

            if ($extra_order_detail[0]->due_date != null) {
                return view(
                    'extra_order.notification',
                    array(
                        'title' => 'PO Extra Order',
                        'title_jp' => 'PO エキストラオーダー',
                        'code' => 102,
                        'approval' => $extra_order,
                    )
                )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
            }

            //Cek Extend ETD
            $extend = $this->extendEtd($extra_order);

            if ($extend > 0) {
                //Melebihi Waktu Upload

                for ($i = 0; $i < count($extra_order_detail); $i++) {

                    $calendar_req = WeeklyCalendar::where('week_date', '>', $extra_order_detail[$i]->request_date)
                        ->where('remark', '<>', 'H')
                        ->orderBy('week_date', 'ASC')
                        ->limit($extend)
                        ->get();

                    $index = $extend - 1;
                    $extend_date = $calendar_req[$index]->week_date;

                    $calendar_due = WeeklyCalendar::where('week_date', '<', $extend_date)
                        ->where('remark', '<>', 'H')
                        ->orderBy('week_date', 'DESC')
                        ->limit($this->due_date)
                        ->get();

                    $index = $this->due_date - 1;
                    $plan_due_date = $calendar_due[$index]->week_date;

                    $update = ExtraOrderDetail::where('id', $extra_order_detail[$i]->id)
                        ->update([
                            'request_date' => $extend_date,
                            'due_date' => $plan_due_date,
                        ]);
                }
            } else {
                //Sesuai waktu
                for ($i = 0; $i < count($extra_order_detail); $i++) {
                    $calendar_due = WeeklyCalendar::where('week_date', '<', $extra_order_detail[$i]->request_date)
                        ->where('remark', '<>', 'H')
                        ->orderBy('week_date', 'DESC')
                        ->limit($this->due_date)
                        ->get();

                    $index = $this->due_date - 1;
                    $plan_due_date = $calendar_due[$index]->week_date;

                    $update = ExtraOrderDetail::where('id', $extra_order_detail[$i]->id)
                        ->update([
                            'due_date' => $plan_due_date,
                        ]);
                }
            }

            $this->breakdownPickingSchedule($eo_number);

            $extra_order->status = 'Production Process';
            $extra_order->save();

            $this->sendNewPoInformation($eo_number);

            // UPDATE TIMELINE
            $extra_order_timeline = new ExtraOrderTimeline([
                'eo_number' => $eo_number,
                'timeline_item_icon' => '<i class="fa fa-check-square-o"></i>',
                'timeline_header' => 'PO has been verified',
                'timeline_body' => ucwords(Auth::user()->name) . ' has verified the new PO and the production schedule has been published automatically.',
                'timeline_footer' => '',
                'remark' => 'APPROVE_PO',
                'created_by' => Auth::id(),
            ]);
            $extra_order_timeline->save();

            return view(
                'extra_order.notification',
                array(
                    'title' => 'PO Extra Order',
                    'title_jp' => 'PO エキストラオーダー',
                    'code' => 101,
                    'approval' => $extra_order,
                )
            )->with('page', 'Extra Order')->with('head', 'Extra Order Confirmation');
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function inputExtraOrderPo(Request $request)
    {
        $eo_number = $request->input('eo_number');
        $count_files = $request->input('count_files');
        $upload_directory = 'files/extra_order/po';

        try {
            $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
            $extra_order_detail = ExtraOrderDetail::where('eo_number', $extra_order->eo_number)->get();

            //Cek Extend ETD
            $extend = $this->extendEtd($extra_order);

            if ($extend > 0) {
                //Melebihi Waktu Upload

                for ($i = 0; $i < count($extra_order_detail); $i++) {

                    $calendar_req = WeeklyCalendar::where('week_date', '>', $extra_order_detail[$i]->request_date)
                        ->where('remark', '<>', 'H')
                        ->orderBy('week_date', 'ASC')
                        ->limit($extend)
                        ->get();

                    $index = $extend - 1;
                    $extend_date = $calendar_req[$index]->week_date;

                    $calendar_due = WeeklyCalendar::where('week_date', '<', $extend_date)
                        ->where('remark', '<>', 'H')
                        ->orderBy('week_date', 'DESC')
                        ->limit($this->due_date)
                        ->get();

                    $index = $this->due_date - 1;
                    $plan_due_date = $calendar_due[$index]->week_date;

                    $update = ExtraOrderDetail::where('id', $extra_order_detail[$i]->id)
                        ->update([
                            'request_date' => $extend_date,
                            'due_date' => $plan_due_date,
                        ]);
                }
            } else {
                //Sesuai waktu
                for ($i = 0; $i < count($extra_order_detail); $i++) {
                    $calendar_due = WeeklyCalendar::where('week_date', '<', $extra_order_detail[$i]->request_date)
                        ->where('remark', '<>', 'H')
                        ->orderBy('week_date', 'DESC')
                        ->limit($this->due_date)
                        ->get();

                    $index = $this->due_date - 1;
                    $plan_due_date = $calendar_due[$index]->week_date;

                    $update = ExtraOrderDetail::where('id', $extra_order_detail[$i]->id)
                        ->update([
                            'due_date' => $plan_due_date,
                        ]);
                }
            }

            //Save PO
            for ($i = 0; $i < $count_files; $i++) {
                $file = $request->file('file_data_' . $i);
                $file_original_name = $file->getClientOriginalName();

                $po_number = $request->input('po_number_' . $i);
                $extension = pathinfo($file_original_name, PATHINFO_EXTENSION);
                $filename = $eo_number . '__' . $po_number . '.' . $extension;

                $file->move($upload_directory, $filename);

                $data[] = $filename;
            }

            $file_saved = json_encode($data);

            $extra_order->po_number = $file_saved;
            $extra_order->po_uploaded_at = date('Y-m-d H:i:s');
            $extra_order->save();

            $response = array(
                'status' => true,
                'eo_number' => $eo_number,
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

    public function inputExtraOrder(Request $request)
    {

        $prefix_now = 'EO' . date('Y') . date('m');
        $code_generator = CodeGenerator::where('note', '=', 'extra_order')->first();
        if ($prefix_now != $code_generator->prefix) {
            $code_generator->prefix = $prefix_now;
            $code_generator->index = '0';
            $code_generator->save();
        }
        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
        $eo_number = $code_generator->prefix . $number;

        $destination = Destination::where('destination_code', explode(' - ', $request->input('destination_code'))[0])->first();
        $destination_code = $destination->destination_code;
        $destination_name = $destination->destination_name;
        $destination_shortname = $destination->destination_shortname;

        try {
            $filename = null;

            $buyer = explode('!', $request->input('buyer'));
            $extra_order = new ExtraOrder([
                'eo_number' => $eo_number,
                'order_by' => $request->input('order_by_id'),
                'po_by' => $request->input('po_by'),
                'attention' => $buyer[0],
                'division' => $request->input('division'),
                'destination_code' => $destination_code,
                'destination_name' => $destination_name,
                'destination_shortname' => $destination_shortname,
                'currency' => $request->input('currency'),
                'status' => 'Confirming',
                'remark' => $request->input('remark'),
                'created_by' => Auth::id(),
            ]);

            $extra_order_timeline = new ExtraOrderTimeline([
                'eo_number' => $eo_number,
                'timeline_item_icon' => '<i class="fa fa-envelope"></i>',
                'timeline_header' => 'New Extra Order',
                'timeline_body' => ucwords(Auth::user()->name) . ' submitted new extra order with destination to ' . $destination_shortname,
                'timeline_footer' => '',
                'remark' => '',
                'created_by' => Auth::id(),
            ]);

            $is_exist_attention = ExtraOrderBuyer::where('attention', $buyer[0])->first();
            if (!$is_exist_attention) {
                $create_buyer = new ExtraOrderBuyer([
                    'attention' => $buyer[0],
                    'division' => $request->input('division'),
                    'destination_code' => $destination_code,
                    'destination_name' => $destination_name,
                    'destination_shortname' => $destination_shortname,
                    'created_by' => Auth::id(),
                ]);
            }

            $lists = array();
            $send_trial_request = false;
            foreach ($request->input('order_lists') as $order_list) {
                $col = explode('!!', $order_list);

                if ($col[0] != 'NEW') {
                    $material = explode('!', $col[0]);

                    $material_number_buyer = $material[1];
                    $material_number = $material[0];
                    $description = $col[1];
                    $uom = $col[2];
                    $storage_location = $material[6];
                    $quantity = $col[5];
                    $sales_price = $col[3];
                    $shipment_by = $request->input('shipment');
                    $request_date = $col[4];
                    $amount = $col[6];
                    $urgent = $col[7];

                    $date = date('Y-m-d');
                    $now = WeeklyCalendar::where('week_date', $date)->first();
                    $fy = WeeklyCalendar::where('fiscal_year', $now->fiscal_year)->orderBy('week_date', 'ASC')->first();

                    $price = ExtraOrderPrice::where('material_number', $material_number)
                        ->where('valid_date', '>=', $fy->week_date)
                        ->first();
                    if ($price) {
                        $sales_price = $price->sales_price;
                    }

                } else {
                    $send_trial_request = true;

                    $material_number_buyer = 'NEW';
                    $material_number = 'NEW';
                    $description = $col[1];
                    $uom = '';
                    $storage_location = '';
                    $quantity = $col[5];
                    $sales_price = 0;
                    $shipment_by = $request->input('shipment');
                    $request_date = $col[4];
                    $amount = $col[6];
                    $urgent = $col[7];

                    $is_extra_order_material = ExtraOrderMaterial::where('material_number', $material_number)
                        ->where('description', $description)
                        ->first();

                    if (!$is_extra_order_material) {
                        $extra_order_material = new ExtraOrderMaterial([
                            'material_number' => $material_number,
                            'material_number_buyer' => $material_number,
                            'description' => $description,
                            'eo_number' => $eo_number,
                            'created_by' => 1,
                        ]);
                        $extra_order_material->save();
                    }
                }

                $urgent_val = 0;
                if ($urgent == 'true') {
                    $urgent_val = 1;
                }

                $extra_order_detail = new ExtraOrderDetail([
                    'eo_number' => $eo_number,
                    'material_number_buyer' => $material_number_buyer,
                    'material_number' => $material_number,
                    'description' => $description,
                    'uom' => $uom,
                    'storage_location' => $storage_location,
                    'quantity' => $quantity,
                    'sales_price' => $sales_price,
                    'urgent' => $urgent_val,
                    'shipment_by' => $shipment_by,
                    'request_date' => $request_date,
                    'created_by' => Auth::id(),
                ]);

                array_push($lists, [
                    'eo_number' => $eo_number,
                    'material_number_buyer' => $material_number_buyer,
                    'material_number' => $material_number,
                    'description' => $description,
                    'uom' => $uom,
                    'storage_location' => $storage_location,
                    'quantity' => $quantity,
                    'sales_price' => $sales_price,
                    'shipment_by' => $shipment_by,
                    'request_date' => $request_date,
                    'amount' => $amount,
                    'created_by' => Auth::id(),
                ]);

                $extra_order_detail->save();
            }

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();
            $extra_order->save();
            $extra_order_timeline->save();

            if ($request->file('attachment') != null) {
                $filename = "";
                $file_destination = 'files/extra_order/attachment';
                $file = $request->file('attachment');
                $filename = 'attachment_' . $eo_number . '.' . $request->input('extension');

                $file->move($file_destination, $filename);

                $extra_order->attachment = $filename;
                $extra_order->save();
            }

            $order_by = db::table('users')->where('username', $request->input('order_by_id'))->first();
            $po_by = db::table('users')->where('username', $request->input('po_by'))->first();

            $data = [
                'extra_order' => $extra_order,
                'lists' => $lists,
                'filename' => $filename,
                'order_by' => $order_by,
                'po_by' => $po_by,
            ];

            $to = $this->pc;
            $cc = [];
            if (in_array(Auth::user()->email, $to)) {
                $key = array_search(Auth::user()->email, $to);
                unset($to[$key]);
                array_push($cc, Auth::user()->email);
            } else {
                array_push($cc, Auth::user()->email);
            }

            Mail::to($to)
                ->cc($cc)
                ->bcc([
                    'ympi-mis-ML@music.yamaha.com',
                ])
                ->send(new SendEmail($data, 'eo_request_notification'));

            // if($send_trial_request){
            //     $this->sendTrialRequest($eo_number);
            // }

            $response = array(
                'status' => true,
                'message' => 'Your request has been submitted',
                'tes' => $filename,
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

    public function sendTrialRequest($eo_number)
    {
        $eo_attachment = ExtraOrder::where('eo_number', $eo_number)->first();

        $extra_order = ExtraOrderDetail::where('eo_number', $eo_number)
            ->where('material_number', 'NEW')
            ->get();

        $data = [
            'eo_number' => $eo_number,
            'extra_order' => $extra_order,
            'filename' => $eo_attachment->attachment,
        ];

        Mail::to(['darma.bagus@music.yamaha.com', 'nanang.kurniawan@music.yamaha.com'])
            ->cc($this->pc)
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'eo_trial_request'));

        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function sendPriceRequest($eo_number)
    {
        $eo_attachment = ExtraOrder::where('eo_number', $eo_number)->first();

        $extra_order = ExtraOrderDetail::where('eo_number', $eo_number)
            ->where('material_number', '<>', 'NEW')
            ->where('sales_price', '=', 0)
            ->get();

        $data = [
            'eo_number' => $eo_number,
            'extra_order' => $extra_order,
            'filename' => $eo_attachment->attachment,
        ];

        Mail::to($this->price_pic)
            ->cc($this->pc)
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'eo_price_request'));

        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function sendApprovalPo($eo_number)
    {
        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
        $extra_order_detail = ExtraOrderDetail::where('eo_number', $eo_number)
            ->select(
                'material_number_buyer',
                'material_number',
                'description',
                'request_date',
                'shipment_by',
                'quantity',
                'uom',
                'sales_price',
                db::raw('(sales_price * quantity) AS amount')
            )
            ->get();

        $order_by = db::table('users')->where('username', $extra_order->order_by)->first();
        $po_by = db::table('users')->where('username', $extra_order->po_by)->first();

        $data = [
            'eo_number' => $eo_number,
            'extra_order' => $extra_order,
            'lists' => $extra_order_detail,
            'order_by' => $order_by,
            'po_by' => $po_by,
        ];

        Mail::to($this->pc)
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'eo_approval_po'));
    }

    public function downloadAtt(Request $request)
    {
        $name = $request->get('attachment');
        $path = '/files/extra_order/attachment/' . $name;
        $file_path = asset($path);

        $response = array(
            'status' => true,
            'file_path' => $file_path,
        );
        return Response::json($response);
    }

    public function downloadPo(Request $request)
    {
        $name = $request->get('po_number');
        $path = '/files/extra_order/po/' . $name;
        $file_path = asset($path);

        $response = array(
            'status' => true,
            'file_path' => $file_path,
        );
        return Response::json($response);
    }

    public function downloadIv(Request $request)
    {
        $name = $request->get('invoice_number');
        $path = '/files/extra_order/invoice/' . $name;
        $file_path = asset($path);

        $response = array(
            'status' => true,
            'file_path' => $file_path,
        );
        return Response::json($response);
    }

    public function downloadWayBill(Request $request)
    {
        $name = $request->get('way_bill');
        $path = '/files/extra_order/way_bill/' . $name;
        $file_path = asset($path);

        $response = array(
            'status' => true,
            'file_path' => $file_path,
        );
        return Response::json($response);
    }

    public function generateEocPdf($eo_number)
    {

        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
        $detail = ExtraOrderDetail::where('eo_number', $eo_number)->get();
        $approval = ExtraOrderApproval::where('eo_number', $eo_number)
            ->orderBy('approval_order', 'DESC')
            ->get();
        $prepared_by = user::where('id', $approval[0]->created_by)->first();

        $kage = EmployeeSync::where('position', 'LIKE', '%Manager%')
            ->whereNull('end_date')
            ->orderBy('division', 'ASC')
            ->orderBy('department', 'ASC')
            ->get();

        $approval_kage = [];
        //Genaral Manager
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'General Manager' && $kage[$i]->division == 'Production Division') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'GM. Prod. Div.';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }

            if ($kage[$i]->position == 'General Manager' && $kage[$i]->division == 'Production Support Division') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'GM. Prod. Support';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //Deputy General Manager
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Deputy General Manager' && $kage[$i]->division == 'Production Support Division') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'DGM. Prod. Support';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //QA
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Manager' && $kage[$i]->department == 'Quality Assurance Department') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'QA';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //Production
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Manager' && $kage[$i]->division == 'Production Division' && $kage[$i]->department != 'Quality Assurance Department') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'Production';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //Logistic
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Manager' && $kage[$i]->department == 'Logistic Department') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'Logistic';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //Proc
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Manager' && $kage[$i]->department == 'Procurement Department') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'Procurement';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        //PC
        for ($i = 0; $i < count($kage); $i++) {
            $row = array();
            if ($kage[$i]->position == 'Manager' && $kage[$i]->department == 'Production Control Department') {
                for ($j = 0; $j < count($approval); $j++) {
                    if ($kage[$i]->employee_id == $approval[$j]->approver_id) {
                        $row['position'] = 'Prod. Control';
                        $row['approver_name'] = $approval[$j]->approver_name;
                        $row['status'] = $approval[$j]->status;
                        $row['approved_at'] = $approval[$j]->approved_at;
                        $approval_kage[] = $row;
                        break;
                    }
                }
            }
        }

        $new_approval = [];
        $prepared_by_inserted = false;

        for ($j = 0; $j < count($approval); $j++) {
            if ($approval[$j]->approval_order <= 2) {
                $row = array();
                $row['approval_order'] = $approval[$j]->approval_order;
                $row['approver_name'] = $this->call_name($approval[$j]->approver_name);
                $row['role'] = $approval[$j]->role;
                $row['remark'] = $approval[$j]->remark;
                $row['status'] = $approval[$j]->status;
                $row['approved_at'] = $approval[$j]->approved_at;
                $new_approval[] = $row;
            }

            if (count($new_approval) == 6) {
                $row = array();
                $row['approval_order'] = 0;
                $row['approver_name'] = $this->call_name($prepared_by->name);
                $row['role'] = 'Staff';
                $row['remark'] = 'Prepared by';
                $row['status'] = 'Prepared';
                $row['approved_at'] = $approval[0]->created_at->format('Y-m-d H:i:s');
                $new_approval[] = $row;

                $prepared_by_inserted = true;
            }
        }

        if (!$prepared_by_inserted) {
            $row = array();
            $row['approval_order'] = 0;
            $row['approver_name'] = $this->call_name($prepared_by->name);
            $row['role'] = 'Staff';
            $row['remark'] = 'Prepared by';
            $row['status'] = 'Prepared';
            $row['approved_at'] = $approval[0]->created_at->format('Y-m-d H:i:s');
            $new_approval[] = $row;
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'portrait');
        // $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->loadView(
            'extra_order.pdf_eoc',
            array(
                'extra_order' => $extra_order,
                'detail' => $detail,
                'approval_kage' => $approval_kage,
                'approval' => $new_approval,
                'prepared_at' => $approval[0]->created_at->format('Y-m-d H:i:s'),
            )
        );
        $pdf->save(public_path() . "/files/extra_order/eoc/EOC_" . $eo_number . ".pdf");
    }

    public function call_name($name)
    {
        $new_name = '';
        $blok_m = ['M.', 'Moch.', 'Mochammad', 'Moh.', 'Mohamad', 'Mokhamad', 'Much.', 'Muchammad', 'Muhamad', 'Muhammaad', 'Muhammad', 'Mukammad', 'Mukhamad', 'Mukhammad'];

        if (str_contains($name, ' ')) {
            $name = explode(' ', $name);

            if (in_array($name[0], $blok_m)) {
                $new_name = 'M.';
                for ($i = 1; $i < count($name); $i++) {
                    if ($i == 1) {
                        $new_name .= ' ';
                        $new_name .= $name[$i];
                    } else {
                        $new_name .= ' ';
                        $new_name .= substr($name[$i], 0, 1) . '.';
                    }
                }
            } else {
                for ($i = 0; $i < count($name); $i++) {
                    if ($i == 0) {
                        $new_name .= ' ';
                        $new_name .= $name[$i];
                    } else {
                        $new_name .= ' ';
                        $new_name .= substr($name[$i], 0, 1) . '.';
                    }
                }
            }
        } else {
            $new_name = $name;
        }

        return $new_name;
    }

    public function generateRandomString()
    {

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return $characters[rand(0, strlen($characters) - 1)];
    }

    public function breakdownPickingSchedule($eo_number)
    {

        $target = db::select("
            SELECT
            extra_order_details.eo_number,
            extra_order_details.material_number,
            extra_order_details.description,
            extra_order_details.uom,
            extra_order_details.storage_location,
            extra_order_details.due_date,
            extra_order_details.request_date,
            SUM( extra_order_details.quantity ) AS quantity,
            SUM( extra_order_details.production_quantity ) AS production_quantity,
            (SUM( extra_order_details.quantity )-SUM( extra_order_details.production_quantity )) AS target
            FROM
            `extra_order_details`
            WHERE
            extra_order_details.eo_number = '" . $eo_number . "'
            AND extra_order_details.due_date LIKE '%" . date('Y-m') . "%'
            AND extra_order_details.storage_location IN ( 'SX91', 'CL91', 'FL91', 'CLB9' )
            GROUP BY
            extra_order_details.eo_number,
            extra_order_details.material_number,
            extra_order_details.description,
            extra_order_details.uom,
            extra_order_details.storage_location,
            extra_order_details.due_date,
            extra_order_details.request_date
            HAVING
            target > 0
            ORDER BY
            extra_order_details.due_date ASC"
        );

        for ($i = 0; $i < count($target); $i++) {
            $breakdown = db::select("
                SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt, b.storage_location, b.valcl FROM bom_outputs b
                WHERE b.material_parent = '" . $target[$i]->material_number . "'");

            for ($j = 0; $j < count($breakdown); $j++) {
                if (in_array($breakdown[$j]->storage_location, $this->picking_location) && $breakdown[$j]->spt == null && $breakdown[$j]->valcl == '9030') {

                    $mpdl = MaterialPlantDataList::where('material_number', $breakdown[$j]->material_child)->first();

                    $row = array();
                    $row['eo_number'] = $target[$i]->eo_number;
                    $row['material_parent'] = $target[$i]->material_number;
                    $row['material_parent_description'] = $target[$i]->description;
                    $row['material_parent_uom'] = $target[$i]->uom;
                    $row['target'] = $target[$i]->target;
                    $row['due_date'] = $target[$i]->due_date;
                    $row['material_child'] = $breakdown[$j]->material_child;
                    $row['material_child_description'] = $mpdl->material_description;
                    $row['material_child_uom'] = $mpdl->bun;
                    $row['storage_location'] = $mpdl->storage_location;
                    $row['usage'] = $target[$i]->target * $breakdown[$j]->usage / $breakdown[$j]->divider;

                    $this->output_breakdown[] = $row;

                } else {

                    $row = array();
                    $row['eo_number'] = $target[$i]->eo_number;
                    $row['material_parent'] = $target[$i]->material_number;
                    $row['material_parent_description'] = $target[$i]->description;
                    $row['material_parent_uom'] = $target[$i]->uom;
                    $row['target'] = $target[$i]->target;
                    $row['due_date'] = $target[$i]->due_date;
                    $row['material_child'] = $breakdown[$j]->material_child;
                    $row['quantity'] = $target[$i]->target * $breakdown[$j]->usage / $breakdown[$j]->divider;

                    $this->check[] = $row;

                }
            }
        }

        while (count($this->check) > 0) {
            $this->temp = array();

            for ($i = 0; $i < count($this->check); $i++) {
                $breakdown = db::select("
                    SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt, b.storage_location, b.valcl FROM bom_outputs b
                    WHERE b.material_parent = '" . $this->check[$i]['material_child'] . "'");

                for ($j = 0; $j < count($breakdown); $j++) {
                    if (in_array($breakdown[$j]->storage_location, $this->picking_location) && $breakdown[$j]->spt == null && $breakdown[$j]->valcl == '9030') {

                        $mpdl = MaterialPlantDataList::where('material_number', $breakdown[$j]->material_child)->first();

                        $row = array();
                        $row['eo_number'] = $this->check[$i]['eo_number'];
                        $row['material_parent'] = $this->check[$i]['material_parent'];
                        $row['material_parent_description'] = $this->check[$i]['material_parent_description'];
                        $row['material_parent_uom'] = $this->check[$i]['material_parent_uom'];
                        $row['target'] = $this->check[$i]['target'];
                        $row['due_date'] = $this->check[$i]['due_date'];
                        $row['material_child'] = $breakdown[$j]->material_child;
                        $row['material_child_description'] = $mpdl->material_description;
                        $row['material_child_uom'] = $mpdl->bun;
                        $row['storage_location'] = $mpdl->storage_location;
                        $row['usage'] = $this->check[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider);

                        $this->output_breakdown[] = $row;

                    } else {
                        $row = array();
                        $row['eo_number'] = $this->check[$i]['eo_number'];
                        $row['material_parent'] = $this->check[$i]['material_parent'];
                        $row['material_parent_description'] = $this->check[$i]['material_parent_description'];
                        $row['material_parent_uom'] = $this->check[$i]['material_parent_uom'];
                        $row['target'] = $this->check[$i]['target'];
                        $row['due_date'] = $this->check[$i]['due_date'];
                        $row['material_child'] = $breakdown[$j]->material_child;
                        $row['quantity'] = $this->check[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider);

                        $this->temp[] = $row;
                    }
                }
            }

            $this->check = array();
            $this->check = $this->temp;
        }

        if (count($this->output_breakdown) > 0) {

            $picking_material = db::connection('kitto')
                ->select("SELECT material_number, description, location, category FROM materials
                WHERE category IN ('KEY', 'ACC', 'BODY')
                AND location IN ('SX51', 'CL51', 'FL51')");

            for ($i = 0; $i < count($this->output_breakdown); $i++) {

                $is_picking_material = false;
                for ($j = 0; $j < count($picking_material); $j++) {
                    if ($picking_material[$j]->material_number == $this->output_breakdown[$i]['material_child']) {
                        $is_picking_material = true;
                        $category = $picking_material[$j]->category;
                        break;
                    }
                }

                if (!$is_picking_material) {
                    continue;
                }

                $calendar = WeeklyCalendar::whereBetween('week_date', [date('Y-m') . '-01', $this->output_breakdown[$i]['due_date']])
                    ->where('remark', '<>', 'H')
                    ->orderBy('week_date', 'DESC')
                    ->get();

                if ((count($calendar) <= 0)) {
                    if ($category == 'KEY') {
                        $insert = DB::table('assy_picking_schedules')
                            ->insert([
                                'remark' => $this->output_breakdown[$i]['storage_location'],
                                'material_number' => $this->output_breakdown[$i]['material_child'],
                                'due_date' => $this->output_breakdown[$i]['due_date'],
                                'quantity' => $this->output_breakdown[$i]['usage'],
                                'note' => $this->output_breakdown[$i]['eo_number'] . '_' . $this->output_breakdown[$i]['material_parent'] . '_' . $this->output_breakdown[$i]['material_parent_description'],
                                'created_by' => 1,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    } elseif ($category == 'ACC') {
                        $insert = DB::table('assy_acc_schedules')
                            ->insert([
                                'remark' => $this->output_breakdown[$i]['storage_location'],
                                'material_number' => $this->output_breakdown[$i]['material_child'],
                                'due_date' => $this->output_breakdown[$i]['due_date'],
                                'quantity' => $this->output_breakdown[$i]['usage'],
                                'note' => $this->output_breakdown[$i]['eo_number'] . '_' . $this->output_breakdown[$i]['material_parent'] . '_' . $this->output_breakdown[$i]['material_parent_description'],
                                'created_by' => 1,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    } elseif ($category == 'BODY') {
                        $insert = DB::table('assy_body_schedules')
                            ->insert([
                                'remark' => $this->output_breakdown[$i]['storage_location'],
                                'material_number' => $this->output_breakdown[$i]['material_child'],
                                'due_date' => $this->output_breakdown[$i]['due_date'],
                                'quantity' => $this->output_breakdown[$i]['usage'],
                                'note' => $this->output_breakdown[$i]['eo_number'] . '_' . $this->output_breakdown[$i]['material_parent'] . '_' . $this->output_breakdown[$i]['material_parent_description'],
                                'created_by' => 1,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }

                } else {
                    $target = $this->output_breakdown[$i]['usage'];
                    for ($k = 0; $k < count($calendar); $k++) {

                        $mod = $this->output_breakdown[$i]['usage'] % count($calendar);

                        if ($k <= ($mod - 1)) {
                            $quantity = ceil($this->output_breakdown[$i]['usage'] / count($calendar));
                        } else {
                            $quantity = floor($this->output_breakdown[$i]['usage'] / count($calendar));
                        }

                        if ($category == 'KEY') {
                            $insert = DB::table('assy_picking_schedules')
                                ->insert([
                                    'remark' => $this->output_breakdown[$i]['storage_location'],
                                    'material_number' => $this->output_breakdown[$i]['material_child'],
                                    'due_date' => $calendar[$k]->week_date,
                                    'quantity' => $quantity,
                                    'note' => $this->output_breakdown[$i]['eo_number'] . '_' . $this->output_breakdown[$i]['material_parent'] . '_' . $this->output_breakdown[$i]['material_parent_description'],
                                    'created_by' => 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } elseif ($category == 'ACC') {
                            $insert = DB::table('assy_acc_schedules')
                                ->insert([
                                    'remark' => $this->output_breakdown[$i]['storage_location'],
                                    'material_number' => $this->output_breakdown[$i]['material_child'],
                                    'due_date' => $calendar[$k]->week_date,
                                    'quantity' => $quantity,
                                    'note' => $this->output_breakdown[$i]['eo_number'] . '_' . $this->output_breakdown[$i]['material_parent'] . '_' . $this->output_breakdown[$i]['material_parent_description'],
                                    'created_by' => 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } elseif ($category == 'BODY') {
                            $insert = DB::table('assy_body_schedules')
                                ->insert([
                                    'remark' => $this->output_breakdown[$i]['storage_location'],
                                    'material_number' => $this->output_breakdown[$i]['material_child'],
                                    'due_date' => $calendar[$k]->week_date,
                                    'quantity' => $quantity,
                                    'note' => $this->output_breakdown[$i]['eo_number'] . '_' . $this->output_breakdown[$i]['material_parent'] . '_' . $this->output_breakdown[$i]['material_parent_description'],
                                    'created_by' => 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        $target = $target - $quantity;
                        if ($target <= 0) {
                            break;
                        }
                    }
                }
            }
        }

    }

    public function generateDueDate($date)
    {
        $calendar_due = WeeklyCalendar::where('week_date', '<', $date)
            ->where('remark', '<>', 'H')
            ->orderBy('week_date', 'DESC')
            ->limit($this->due_date)
            ->get();

        return $calendar_due[($this->due_date - 1)]->week_date;

    }

    public function generateOverdue($first, $end)
    {
        $date = WeeklyCalendar::whereBetween('week_date', [$first, $end])
            ->where('remark', '<>', 'H')
            ->orderBy('week_date', 'DESC')
            ->limit($this->due_date)
            ->get();

        return count($date);

    }

    public function generateSmbmr(Request $request)
    {

        $mpdl = MaterialPlantDataList::get();

        $mpdl_formated = [];
        for ($i = 0; $i < count($mpdl); $i++) {
            $mpdl_formated[$mpdl[$i]->material_number] = $mpdl[$i];
        }

        $extra_order_detail = ExtraOrderDetail::where('eo_number', $request->get('eo_number'))->get();
        $undefined = [];

        for ($i = 0; $i < count($extra_order_detail); $i++) {
            $smbmr = Smbmr::where('material_parent', $extra_order_detail[$i]->material_number)->get();

            if (count($smbmr) == 0) {
                $row = [];
                $row['material_number'] = $extra_order_detail[$i]->material_number;
                $row['material_description'] = $extra_order_detail[$i]->description;
                $undefined[] = (object) $row;
            }
        }

        for ($i = 0; $i < count($undefined); $i++) {
            $breakdown = db::select("
                SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt, b.valcl FROM bom_outputs b
                WHERE b.material_parent = '" . $undefined[$i]->material_number . "'");

            for ($j = 0; $j < count($breakdown); $j++) {
                if ($breakdown[$j]->valcl != '9040') {
                    $row = array();
                    $row['material_parent'] = $breakdown[$j]->material_parent;
                    $row['material_child'] = $breakdown[$j]->material_child;
                    $row['quantity'] = $breakdown[$j]->usage / $breakdown[$j]->divider;
                    $row['created_at'] = date('Y-m-d H:i:s');
                    $row['updated_at'] = date('Y-m-d H:i:s');

                    $this->check[] = $row;
                } else {
                    $mpdl = $mpdl_formated[$breakdown[$j]->material_child];
                    $pgr = array("G01", "G08", "G15", "999");

                    if (in_array($mpdl->pgr, $pgr)) {
                        $row = array();
                        $row['material_parent'] = $undefined[$i]->material_number;
                        $row['material_parent_description'] = $undefined[$i]->material_description;
                        $row['raw_material'] = $breakdown[$j]->material_child;
                        $row['raw_material_description'] = $mpdl->material_description;
                        $row['uom'] = $mpdl->bun;
                        $row['pgr'] = $mpdl->pgr;
                        $row['usage'] = $breakdown[$j]->usage / $breakdown[$j]->divider;
                        $row['created_by'] = 1;
                        $row['created_at'] = date('Y-m-d H:i:s');
                        $row['updated_at'] = date('Y-m-d H:i:s');

                        $this->output_breakdown[] = $row;
                    }
                }
            }
        }

        while (count($this->check) > 0) {
            $this->temp = array();

            for ($i = 0; $i < count($this->check); $i++) {
                $breakdown = db::select("
                SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt, b.valcl FROM bom_outputs b
                WHERE b.material_parent = '" . $this->check[$i]['material_child'] . "'");

                for ($j = 0; $j < count($breakdown); $j++) {
                    if ($breakdown[$j]->valcl != '9040') {
                        $row = array();
                        $row['material_parent'] = $this->check[$i]['material_parent'];
                        $row['material_child'] = $breakdown[$j]->material_child;
                        $row['quantity'] = round($this->check[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider), 6);
                        $row['created_at'] = date('Y-m-d H:i:s');
                        $row['updated_at'] = date('Y-m-d H:i:s');

                        $this->temp[] = $row;
                    } else {
                        $mpdl = $mpdl_formated[$breakdown[$j]->material_child];
                        $parent = $mpdl_formated[$this->check[$i]['material_parent']];

                        $pgr = array("G01", "G08", "G15", "999");

                        if (in_array($mpdl->pgr, $pgr)) {
                            $row = array();
                            $row['material_parent'] = $this->check[$i]['material_parent'];
                            $row['material_parent_description'] = $parent->material_description;
                            $row['raw_material'] = $breakdown[$j]->material_child;
                            $row['raw_material_description'] = $mpdl->material_description;
                            $row['uom'] = $mpdl->bun;
                            $row['pgr'] = $mpdl->pgr;
                            $row['usage'] = round($this->check[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider), 6);
                            $row['created_by'] = 1;
                            $row['created_at'] = date('Y-m-d H:i:s');
                            $row['updated_at'] = date('Y-m-d H:i:s');

                            $this->output_breakdown[] = $row;
                        }
                    }
                }
            }

            $this->check = array();
            $this->check = $this->temp;

        }

        $insert_breakdown = [];
        for ($i = 0; $i < count($this->output_breakdown); $i++) {
            $key = '';
            $key .= ($this->output_breakdown[$i]['material_parent'] . '#');
            $key .= ($this->output_breakdown[$i]['material_parent_description'] . '#');
            $key .= ($this->output_breakdown[$i]['raw_material'] . '#');
            $key .= ($this->output_breakdown[$i]['raw_material_description'] . '#');
            $key .= ($this->output_breakdown[$i]['uom'] . '#');
            $key .= ($this->output_breakdown[$i]['pgr'] . '#');

            if (!array_key_exists($key, $insert_breakdown)) {
                $row = array();
                $row['material_parent'] = $this->output_breakdown[$i]['material_parent'];
                $row['material_parent_description'] = $this->output_breakdown[$i]['material_parent_description'];
                $row['raw_material'] = $this->output_breakdown[$i]['raw_material'];
                $row['raw_material_description'] = $this->output_breakdown[$i]['raw_material_description'];
                $row['uom'] = $this->output_breakdown[$i]['uom'];
                $row['pgr'] = $this->output_breakdown[$i]['pgr'];
                $row['usage'] = $this->output_breakdown[$i]['usage'];
                $row['created_by'] = 1;
                $row['created_at'] = date('Y-m-d H:i:s');
                $row['updated_at'] = date('Y-m-d H:i:s');

                $insert_breakdown[$key] = $row;
            } else {
                $insert_breakdown[$key]['usage'] = $insert_breakdown[$key]['usage'] + $this->output_breakdown[$i]['usage'];
            }
        }

        foreach (array_chunk($insert_breakdown, 1000) as $t) {
            $output = Smbmr::insert($t);
        }

        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function generateAttPoReject($eo_number)
    {

        $extra_order = ExtraOrder::where('eo_number', $eo_number)->first();
        $extra_order_detail = ExtraOrderDetail::where('eo_number', $eo_number)
            ->select(
                'material_number_buyer',
                'material_number',
                'description',
                'request_date',
                'shipment_by',
                'quantity',
                'uom',
                'sales_price',
                db::raw('(sales_price * quantity) AS amount')
            )
            ->get();

        $data = [
            'extra_order' => $extra_order,
            'lists' => $extra_order_detail,
        ];

        ob_clean();
        Excel::create($extra_order->eo_number, function ($excel) use ($data) {
            $excel->sheet($data['extra_order']->eo_number, function ($sheet) use ($data) {
                return $sheet->loadView('extra_order.po', $data);
            });
        })->store('xlsx', public_path('files/extra_order/po_att'));

    }

}