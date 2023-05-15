<?php

namespace App\Console\Commands;

use App\ErrorLog;
use App\Mail\SendEmail;
use App\MaterialPlanDelivery;
use App\VendorMail;
use App\WeeklyCalendar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RawMaterialReminderPo extends Command
{
    protected $signature = 'raw_material:reminder_po';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $limit = 0;

        $exclude = [
            'Y10053',
            'Y31520',
            'Y81811',

            'Y31504',
            'Y10022',
            'Y81801',
        ];

        $bcc = [
            'ympi-mis-ML@music.yamaha.com',
        ];

        $now = date('Y-m-d');
        $now = WeeklyCalendar::where('week_date', $now)->first();

        if ($now->remark != 'H') {

            $po_un_confirm = db::select("SELECT material_plan_deliveries.po_number, material_controls.controlling_group, MIN(material_plan_deliveries.po_send_at) AS po_send_at, MAX(material_plan_deliveries.po_reminder_at) AS po_reminder_at FROM material_plan_deliveries
                LEFT JOIN material_controls ON material_controls.material_number = material_plan_deliveries.material_number
                WHERE material_plan_deliveries.po_send = 1
                AND material_plan_deliveries.po_confirm = 0
                GROUP BY material_plan_deliveries.po_number, material_controls.controlling_group
                ORDER BY material_plan_deliveries.po_send_at ASC");

            for ($i = 0; $i < count($po_un_confirm); $i++) {

                if (date('Y-m-d', strtotime($po_un_confirm[$i]->po_reminder_at)) != $now->week_date) {

                    $calendar = WeeklyCalendar::where('week_date', '<=', $now->week_date)
                        ->where('week_date', '>', date('Y-m-d', strtotime($po_un_confirm[$i]->po_send_at)))
                        ->where('remark', '<>', 'H')
                        ->get();

                    $reminder = false;

                    if ($po_un_confirm[$i]->controlling_group == 'INDIRECT') {
                        if (count($calendar) > 1) {
                            $reminder = true;
                        }
                    } else {
                        if (count($calendar) > 2) {
                            $reminder = true;
                        }
                    }

                    if ($reminder) {

                        $material = MaterialPlanDelivery::leftJoin('material_controls', 'material_controls.material_number', '=', 'material_plan_deliveries.material_number')
                            ->leftJoin('users AS buyer_proc', 'buyer_proc.username', '=', 'material_controls.pic')
                            ->leftJoin('users AS control_proc', 'control_proc.username', '=', 'material_controls.control')
                            ->where('po_number', $po_un_confirm[$i]->po_number)
                            ->select(
                                'material_controls.vendor_code',
                                'material_controls.vendor_name',
                                db::raw('buyer_proc.email AS buyer_email'),
                                db::raw('control_proc.email AS control_email')
                            )
                            ->first();

                        if ($limit >= 1000) {
                            break;
                        }
                        $limit++;

                        try {

                            $update = MaterialPlanDelivery::where('material_plan_deliveries.po_number', $po_un_confirm[$i]->po_number)
                                ->update([
                                    'po_reminder_at' => date('Y-m-d H:i:s'),
                                ]);

                            $attention = '';
                            $to = [];
                            $cc = [$material->buyer_email];
                            if ($material->buyer_email == 'nunik.erwantiningsih@music.yamaha.com') {
                                array_push($cc, 'erlangga.kharisma@music.yamaha.com', 'amelia.novrinta@music.yamaha.com');
                            } else {
                                array_push($cc, $material->control_email);
                            }

                            $vendor_mails = VendorMail::where('vendor_code', $material->vendor_code)->get();
                            for ($j = 0; $j < count($vendor_mails); $j++) {
                                if ($vendor_mails[$j]->remark == 'to') {
                                    $attention = $vendor_mails[$j]->name;
                                    $to[] = $vendor_mails[$j]->email;
                                } else {
                                    $cc[] = $vendor_mails[$j]->email;
                                }
                            }

                            // If Yamaha Group
                            if (in_array($material->vendor_code, $exclude)) {
                                $to = [];
                                $cc = [];

                                $to = [$material->buyer_email];
                                if ($material->buyer_email == 'nunik.erwantiningsih@music.yamaha.com') {
                                    array_push($to, 'erlangga.kharisma@music.yamaha.com', 'amelia.novrinta@music.yamaha.com');
                                } else {
                                    array_push($to, $material->control_email);
                                }
                            }

                            $data = [
                                'buyer' => $material->buyer_email,
                                'control' => $material->control_email,
                                'po_number' => $po_un_confirm[$i]->po_number,
                                'po_send_at' => $po_un_confirm[$i]->po_send_at,
                                'attention' => $attention,
                                'vendor_name' => $material->vendor_name,
                                'subject' => 'PO#' . $po_un_confirm[$i]->po_number . '_' . $material->vendor_name . ' - Reminder PO Confirmation',
                            ];

                            Mail::to($to)
                                ->cc($cc)
                                ->bcc($bcc)
                                ->send(new SendEmail($data, 'raw_material_reminder_po'));

                        } catch (Exception $e) {

                            $error_log = new ErrorLog([
                                'error_message' => '[ERRORPOREMINDER] : ' . $po_un_confirm[$i]->po_number . '_' . $e->getMessage(),
                                'created_by' => 1,
                            ]);
                            $error_log->save();
                        }

                    }
                }
            }
        }
    }
}
