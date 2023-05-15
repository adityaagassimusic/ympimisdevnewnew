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

class RawMaterialReminderDelivery extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raw_material:reminder_delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {

        $limit = 0;
        $now = date('Y-m-d');

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

        $search = db::select("SELECT mpd.po_number,
            mpd.item_line,
            mpd.material_number,
            mc.material_description,
            buyer.email AS buyer_email,
            control.email AS control_email,
            mc.vendor_code,
            mc.vendor_name,
            mc.category,
            mpd.quantity,
            mpd.issue_date,
            mpd.due_date,
            mc.second_reminder
            FROM material_plan_deliveries mpd
            LEFT JOIN material_controls mc ON mc.material_number = mpd.material_number
            LEFT JOIN users buyer ON buyer.username = mc.pic
            LEFT JOIN users control ON control.username = mc.control
            WHERE issue_date >= '2022-01-01'
            AND plan < quantity
            AND mc.second_reminder IS NOT NULL
            AND mpd.reminder_confirm_at IS NULL
            AND (mpd.send_reminder_confirm_at IS NULL OR DATE_FORMAT(mpd.send_reminder_confirm_at, '%Y-%m-%d') <> '" . $now . "')
            GROUP BY mpd.po_number,
            mpd.item_line,
            mpd.material_number,
            mc.material_description,
            buyer_email,
            control_email,
            mc.vendor_code,
            mc.vendor_name,
            mc.category,
            mpd.quantity,
            mpd.issue_date,
            mpd.due_date,
            mc.second_reminder
            ORDER BY mpd.due_date ASC");

        $reminder = [];
        for ($i = 0; $i < count($search); $i++) {
            if ($search[$i]->category == 'LOKAL') {
                if (!is_null($search[$i]->second_reminder)) {

                    $date1 = date_create($now);
                    $date2 = date_create($search[$i]->due_date);
                    $date_diff = date_diff($date1, $date2);
                    $diff = intval($date_diff->format("%R%a"));

                    if ($diff <= $search[$i]->second_reminder) {
                        $key = $search[$i]->vendor_code . '_' . $search[$i]->due_date . '_' . $search[$i]->second_reminder;

                        if (!in_array($key, $reminder)) {
                            array_push($reminder, $key);
                        }

                    }
                }
            }
        }

        for ($i = 0; $i < count($reminder); $i++) {

            $dt = explode('_', $reminder[$i]);
            $vendor_code = $dt[0];
            $due_date = $dt[1];
            $second_reminder = $dt[2];

            $diff = WeeklyCalendar::where('remark', '<>', 'H')
                ->where('week_date', '>=', $now)
                ->where('week_date', '<', $due_date)
                ->get();

            if (count($diff) <= $second_reminder) {

                if ($limit >= 30) {
                    break;
                }

                $to = [];
                $cc = [];

                $email_buyer = [];
                $email_control = [];

                $reminder_data = [];
                $po_number = [];
                $attention = '';

                for ($j = 0; $j < count($search); $j++) {
                    if ($vendor_code == $search[$j]->vendor_code && $due_date == $search[$j]->due_date) {
                        $vendor_name = $search[$j]->vendor_name;

                        if (!in_array($search[$j]->buyer_email, $email_buyer)) {
                            array_push($email_buyer, $search[$j]->buyer_email);
                        }

                        if (!in_array($search[$j]->control_email, $email_control)) {
                            array_push($email_control, $search[$j]->control_email);
                        }

                        if (!in_array($search[$j]->po_number, $po_number)) {
                            array_push($po_number, $search[$j]->po_number);
                        }

                        array_push($reminder_data, $search[$j]);
                    }
                }

                if (in_array($vendor_code, $exclude)) {
                    $to = array_merge($email_buyer, $email_control);
                    $cc = [];

                    $vendor_mails = VendorMail::where('vendor_code', $vendor_code)->get();
                    for ($x = 0; $x < count($vendor_mails); $x++) {
                        if ($vendor_mails[$x]->remark == 'to') {
                            $attention = $vendor_mails[$x]->name;
                        }
                    }

                } else {
                    $vendor_mails = VendorMail::where('vendor_code', $vendor_code)->get();
                    for ($x = 0; $x < count($vendor_mails); $x++) {
                        if ($vendor_mails[$x]->remark == 'to') {
                            $attention = $vendor_mails[$x]->name;
                            $to[] = $vendor_mails[$x]->email;
                        } else {
                            $cc[] = $vendor_mails[$x]->email;
                        }
                    }

                    $cc = array_merge($cc, $email_buyer, $email_control);
                }

                try {

                    $update = MaterialPlanDelivery::whereIn('po_number', $po_number)
                        ->where('due_date', $due_date)
                        ->update([
                            'send_reminder_confirm_at' => date('Y-m-d H:i:s'),
                        ]);

                    $data = [
                        'attention' => $attention,
                        'vendor_name' => $vendor_name,
                        'vendor_code' => $vendor_code,
                        'due_date' => $due_date,
                        'email_buyer' => $email_buyer,
                        'email_control' => $email_control,
                        'due_date' => $due_date,
                        'reminder_data' => $reminder_data,
                        'subject' => 'Reminder Document Delivery_' . $vendor_name . '_' . implode(', ', $po_number),
                    ];

                    Mail::to($to)
                        ->cc($cc)
                        ->bcc($bcc)
                        ->send(new SendEmail($data, 'raw_material_reminder_delivery'));

                    $limit++;

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
