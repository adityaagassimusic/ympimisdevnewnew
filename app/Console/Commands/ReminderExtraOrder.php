<?php

namespace App\Console\Commands;

use App\ApprApprovals;
use App\ApprSend;
use App\Mail\SendEmail;
use App\MaterialPlantDataList;
use App\WeeklyCalendar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReminderExtraOrder extends Command
{
    protected $signature = 'reminder:extra_order';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
        $this->assembly = array(
            'imbang.prasetyo@music.yamaha.com',
            'ardianto@music.yamaha.com',
            'bambang.ferry@music.yamaha.com',
            'wachid.hasyim@music.yamaha.com',
            'ipung.dwi.setiawan@music.yamaha.com',
            'hartono@music.yamaha.com',
            'mawan.sujianto@music.yamaha.com',
            'susanti@music.yamaha.com',
            'danang.harianto@music.yamaha.com',
            'srianingsih@music.yamaha.com',
            'mey.indah.astuti@music.yamaha.com',
            'maruli.sapta.adi@music.yamaha.com',
            'putri.airin.sucin@music.yamaha.com',
            'fattatul.mufidah@music.yamaha.com',
        );
        $this->surface_treatment = array(
            'hartono@music.yamaha.com',
            'mawan.sujianto@music.yamaha.com',
            'susanti@music.yamaha.com',
            'danang.harianto@music.yamaha.com',
            'srianingsih@music.yamaha.com',
            'mey.indah.astuti@music.yamaha.com',
            'maruli.sapta.adi@music.yamaha.com',
            'putri.airin.sucin@music.yamaha.com',
            'fattatul.mufidah@music.yamaha.com',
            'yudi.abtadipa@music.yamaha.com',
        );
        $this->welding = array(
            'mey.indah.astuti@music.yamaha.com',
            'maruli.sapta.adi@music.yamaha.com',
            'putri.airin.sucin@music.yamaha.com',
            'yudi.abtadipa@music.yamaha.com',
        );
        $this->bpp = array(
            'hadi.firmansyah@music.yamaha.com',
            'maruli.sapta.adi@music.yamaha.com',
            'putri.airin.sucin@music.yamaha.com',
            'yudi.abtadipa@music.yamaha.com',
        );
        $this->kpp = array(
            'slamet.hariadi@music.yamaha.com',
            'hendri.susilo@music.yamaha.com',
            'bagus.nur.hidayat@music.yamaha.com',
            'khoirul.umam@music.yamaha.com',
        );
        $this->ei = array(
            'eko.prasetyo.wicaksono@music.yamaha.com',
            'hendri.susilo@music.yamaha.com',
            'anang.zahroni@music.yamaha.com',
            'khoirul.umam@music.yamaha.com',
        );
        $this->pc = array(
            'mamluatul.atiyah@music.yamaha.com',
            'istiqomah@music.yamaha.com',
            'ali.murdani@music.yamaha.com',
            'farizca.nurma@music.yamaha.com',
        );
        $this->logistic = array(
            'nurul.hidayat@music.yamaha.com',
            'dwi.misnanto@music.yamaha.com',
            'angga.setiawan@music.yamaha.com',
            'fathor.rahman@music.yamaha.com',
            'triandini@music.yamaha.com',
            'karina.elnusawati@music.yamaha.com',
        );
        $this->qa = array(
            'yayuk.wahyuni@music.yamaha.com',
            'ratri.sulistyorini@music.yamaha.com',
            'agustina.hayati@music.yamaha.com',
            'abdissalam.saidi@music.yamaha.com',
            'sutrisno@music.yamaha.com',
            'ertikto.singgih@music.yamaha.com',
        );
        $this->mis = array(
            'muhammad.ikhlas@music.yamaha.com',
        );
        $this->picking_location = array('SX51', 'CL51', 'FL51', 'VN51');
        $this->output_breakdown = array();
        $this->check = array();
        $this->temp = array();
    }

    public function handle()
    {

        $now = date("Y-m-d");
        $date = db::table('weekly_calendars')->where('week_date', $now)->first();

        // GENRATE PICKING ASSEMEBLY
        if (intval(date('d')) == 3) {
            $last_day = date("Y-m-d", strtotime("last day of this month"));

            $target = db::select("
                SELECT
                extra_order_details.eo_number,
                extra_orders.destination_shortname,
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
                LEFT JOIN
                extra_orders ON extra_order_details.eo_number = extra_orders.eo_number
                WHERE
                extra_order_details.due_date <= '" . $last_day . "'
                AND storage_location IN ( 'SX91', 'CL91', 'FL91', 'CLB9' )
                GROUP BY
                extra_order_details.eo_number,
                extra_orders.destination_shortname,
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

                $title = "Generate Picking Schedule Extra Order";
                $body = "Count Data : " . count($this->output_breakdown) . "<br><br>";
                for ($i = 0; $i < count($this->output_breakdown); $i++) {
                    if ($i == 0) {
                        $body .= "<br><br>";
                        $body .= 'Parent : ';
                        $body .= $this->output_breakdown[$i]['eo_number'] . ' _ ';
                        $body .= $this->output_breakdown[$i]['material_parent'] . ' _ ';
                        $body .= $this->output_breakdown[$i]['material_parent_description'] . ' _ ';
                        $body .= $this->output_breakdown[$i]['target'] . ' ';
                        $body .= $this->output_breakdown[$i]['material_parent_uom'];

                        $body .= "<br><br>";
                    } else {
                        if ($this->output_breakdown[$i]['material_parent'] != $this->output_breakdown[$i - 1]['material_parent']) {
                            $body .= "<br><br>";
                            $body .= 'Parent : ';
                            $body .= $this->output_breakdown[$i]['eo_number'] . ' _ ';
                            $body .= $this->output_breakdown[$i]['material_parent'] . ' _ ';
                            $body .= $this->output_breakdown[$i]['material_parent_description'] . ' _ ';
                            $body .= $this->output_breakdown[$i]['target'] . ' ';
                            $body .= $this->output_breakdown[$i]['material_parent_uom'];

                            $body .= "<br><br>";
                        }
                    }
                    $body .= ' Child : ';
                    $body .= $this->output_breakdown[$i]['material_child'] . ' _ ';
                    $body .= $this->output_breakdown[$i]['material_child_description'] . ' _ ';
                    $body .= $this->output_breakdown[$i]['usage'] . ' ';
                    $body .= $this->output_breakdown[$i]['material_child_uom'];

                    $body .= "<br><br>";
                }

                $to = [
                    'ipung.dwi.setiawan@music.yamaha.com',
                    'hartono@music.yamaha.com',
                    'mawan.sujianto@music.yamaha.com',
                    'susanti@music.yamaha.com',
                    'danang.harianto@music.yamaha.com',
                    'srianingsih@music.yamaha.com',
                ];
                $cc = $this->pc;
                $bcc = $this->mis;

                Mail::raw([], function ($message) use ($title, $body, $to, $cc, $bcc) {
                    $message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
                    $message->to($to);
                    $message->cc($cc);
                    $message->bcc($bcc);
                    $message->subject($title);
                    $message->setBody($body, 'text/html');}
                );

            }
        }

        // REMINDER MINUS WO
        if ($date->remark != 'H') {
            // CHECK FIRM ORDER
            $minus = db::select("
                SELECT
                extra_order_details.material_number,
                extra_order_details.description,
                extra_order_details.storage_location,
                SUM( extra_order_details.production_quantity - extra_order_details.quantity ) AS minus
                FROM
                extra_order_details
                LEFT JOIN extra_order_materials ON extra_order_details.material_number = extra_order_materials.material_number
                WHERE
                extra_order_details.production_quantity < extra_order_details.quantity
                AND extra_order_details.material_number <> 'NEW'
                AND extra_order_details.due_date IS NOT NULL
                AND extra_order_materials.is_completion = 1
                GROUP BY
                extra_order_details.material_number,
                extra_order_details.description,
                extra_order_details.storage_location");

            $ymes_firmed_orders = db::connection('ymes')
                ->select("
                SELECT
                item_code AS material_number,
                SUM ( plan_qty - confirm_qty ) AS available_quantity
                FROM
                vd_sap0010
                WHERE
                firm_type = 'X'
                AND confirm_qty < plan_qty
                GROUP BY
                item_code");

            $minus_wo = [];
            for ($i = 0; $i < count($minus); $i++) {
                $is_wo_found = false;
                for ($j = 0; $j < count($ymes_firmed_orders); $j++) {
                    if ($minus[$i]->material_number == $ymes_firmed_orders[$j]->material_number) {
                        if (($minus[$i]->minus + $ymes_firmed_orders[$j]->available_quantity) < 0) {
                            $row = array();
                            $row['material_number'] = $minus[$i]->material_number;
                            $row['description'] = $minus[$i]->description;
                            $row['storage_location'] = $minus[$i]->storage_location;
                            $row['qty'] = $minus[$i]->minus;
                            $row['available_quantity'] = $ymes_firmed_orders[$j]->available_quantity;
                            $row['minus'] = ($minus[$i]->minus + $ymes_firmed_orders[$j]->available_quantity);
                            $minus_wo[] = $row;
                        }
                        $is_wo_found = true;
                        break;
                    }
                }

                if (!$is_wo_found) {
                    $row = array();
                    $row['material_number'] = $minus[$i]->material_number;
                    $row['description'] = $minus[$i]->description;
                    $row['storage_location'] = $minus[$i]->storage_location;
                    $row['qty'] = $minus[$i]->minus;
                    $row['available_quantity'] = 0;
                    $row['minus'] = $minus[$i]->minus;
                    $minus_wo[] = $row;
                }
            }

            if (count($minus_wo) > 0) {
                $data = [
                    'minus_wo' => $minus_wo,
                ];

                Mail::to($this->pc)
                    ->bcc($this->mis)
                    ->send(new SendEmail($data, 'eo_minus_wo'));

            }
        }

        // REMINDER APPROVAL EOC
        if ($date->remark != 'H') {
            $outstanding = db::select("SELECT approver_id, approver_name, approver_email, role, COUNT(approver_id) AS qty FROM
                (SELECT approver_id, approver_name, approver_email, role, updated_at, TIMESTAMPDIFF(HOUR,updated_at,NOW()) AS diff FROM `extra_order_approvals`
                WHERE approval_status = 1
                AND role NOT IN ('Deputy General Manager', 'General Manager')
                AND approved_at IS NULL
                HAVING diff > 24
                ) AS outstanding
                GROUP BY approver_id, approver_name, approver_email, role
                ORDER BY qty");

            if (count($outstanding) > 0) {
                $to = [];
                for ($i = 0; $i < count($outstanding); $i++) {
                    if (!in_array($outstanding[$i]->approver_email, $to)) {
                        array_push($to, $outstanding[$i]->approver_email);
                    }
                }

                $data = [
                    'outstanding' => $outstanding,
                ];

                Mail::to($to)
                    ->cc($this->pc)
                    ->bcc($this->mis)
                    ->send(new SendEmail($data, 'eo_outstanding_eoc'));

            }
        }

        // REMINDER SEND APP
        if ($date->remark != 'H') {
            $waiting = db::select("
                SELECT
                    sending_applications.send_app_no,
                    sending_applications.attention,
                    destinations.destination_shortname,
                    sending_applications.`condition`,
                    sending_applications.shipment_by,
                    CASE
                        WHEN sending_applications.`status` = 1
                            THEN 'REQUESTED'
                        WHEN sending_applications.`status` = 3
                            THEN 'CHECKED'
                        END AS `status`,
                    CASE
                        WHEN sending_applications.`status` = 1
                            THEN 'WAREHOUSE'
                        WHEN sending_applications.`status` = 3
                            THEN 'EXIM STAFF'
                    END AS pic,
                    sending_application_logs.created_at,
                    TIMESTAMPDIFF( HOUR, sending_application_logs.created_at, NOW())/ 24 AS diff
                FROM
                    sending_applications
                    LEFT JOIN sending_application_logs
                        ON sending_application_logs.send_app_no = sending_applications.send_app_no
                        AND sending_application_logs.`status` = sending_applications.`status`
                    LEFT JOIN destinations
                        ON destinations.destination_code = sending_applications.destination_code
                WHERE
                    sending_applications.`status` IN ( 1, 3 )
                    AND sending_applications.deleted_at IS NULL
                HAVING
                    diff > 7");

            if (count($waiting) > 0) {

                $data = [
                    'waiting' => $waiting,
                ];

                Mail::to($this->logistic)
                    ->cc($this->pc)
                    ->bcc($this->mis)
                    ->send(new SendEmail($data, 'eo_waiting_sendapp'));

            }
        }

        // REMINDER SHORTAGE PRODUCTION (SENIN)
        if (date('w') == 1 || intval(date('d')) == 24) {
            $now = strtotime(date("Y-m-d"));
            $date = date("Y-m-d", strtotime("+1 month", $now));

            $minus = db::select("SELECT extra_order_details.eo_number, extra_orders.destination_shortname, extra_order_details.material_number, extra_order_materials.is_completion, extra_order_details.description, extra_order_details.storage_location, extra_order_details.due_date, extra_order_details.request_date, SUM(extra_order_details.quantity) AS quantity, SUM(extra_order_details.production_quantity) AS production_quantity, (SUM(extra_order_details.production_quantity) - SUM(extra_order_details.quantity)) AS minus  FROM `extra_order_details`
                LEFT JOIN extra_orders ON extra_order_details.eo_number = extra_orders.eo_number
                LEFT JOIN extra_order_materials ON extra_order_details.material_number = extra_order_materials.material_number
                WHERE extra_order_details.due_date <= '" . $date . "'
                GROUP BY extra_order_details.eo_number, extra_orders.destination_shortname, extra_order_details.material_number, extra_order_materials.is_completion, extra_order_details.description, extra_order_details.storage_location, extra_order_details.due_date, extra_order_details.request_date
                HAVING minus < 0
                ORDER BY extra_order_details.due_date ASC");

            if (count($minus) > 0) {
                $storage_locations = [];
                $areas = [];
                $to = [];
                for ($i = 0; $i < count($minus); $i++) {
                    if (!in_array($minus[$i]->storage_location, $storage_locations)) {
                        array_push($storage_locations, $minus[$i]->storage_location);
                        $storage_location = db::table('storage_locations')->where('storage_location', $minus[$i]->storage_location)->first();
                        if ($storage_location) {
                            if (!in_array($storage_location->area, $areas)) {
                                array_push($areas, $storage_location->area);

                                if ($storage_location->area == 'ASSEMBLY') {
                                    $to = array_merge($to, $this->assembly);
                                }

                                if ($storage_location->area == 'ST') {
                                    $to = array_merge($to, $this->surface_treatment);
                                }

                                if ($storage_location->area == 'WELDING') {
                                    $to = array_merge($to, $this->welding);
                                }

                                if ($storage_location->area == 'BPP') {
                                    $to = array_merge($to, $this->bpp);
                                }

                                if ($storage_location->area == 'KPP') {
                                    $to = array_merge($to, $this->kpp);
                                }

                                if ($storage_location->area == 'EI') {
                                    $to = array_merge($to, $this->ei);
                                }
                            }
                        }
                    }
                }
                $to = array_merge($to, $this->qa);
                array_unique($to);

                $data = [
                    'minus' => $minus,
                ];

                Mail::to($to)
                    ->cc($this->pc)
                    ->bcc($this->mis)
                    ->send(new SendEmail($data, 'eo_shortage'));
            }
        }

        // MIRAI APPROVAL REMINDER (SENIN)
        if (date('w') == 123) {
            $cek_appr = db::select('SELECT
                request_id
                FROM
                appr_approvals
                LEFT JOIN appr_sends ON appr_sends.no_transaction = appr_approvals.request_id
                WHERE
                `status` IS NULL
                AND appr_sends.remark = "Open"
                GROUP BY
                request_id
                ORDER BY
                appr_approvals.id ASC');

            for ($i = 0; $i < count($cek_appr); $i++) {
                $mail_to = [];
                $select = ApprApprovals::where('request_id', '=', $cek_appr[$i]->request_id)->wherenull('status')->take(1)->first();
                array_push($mail_to, $select->approver_email);

                $appr_sends = ApprSend::where('no_transaction', '=', $cek_appr[$i]->request_id)
                    ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
                    ->first();

                $appr_approvals = ApprApprovals::where('request_id', '=', $cek_appr[$i]->request_id)
                    ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
                    ->get();

                $data = [
                    'appr_sends' => $appr_sends,
                    'appr_approvals' => $appr_approvals,
                ];

                Mail::to($mail_to)
                    ->bcc(['ympi-mis-ML@music.yamaha.com'])
                    ->send(new SendEmail($data, 'send_email'));
            }
        }
    }
}
