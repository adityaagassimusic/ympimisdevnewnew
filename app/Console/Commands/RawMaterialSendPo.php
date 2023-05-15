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

class RawMaterialSendPo extends Command
{
    protected $signature = 'raw_material:send_po';

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

            $po_un_send = MaterialPlanDelivery::where('po_send', '0')
                ->where('issue_date', '>=', '2023-01-01')
                ->select('po_number')
                ->distinct()
                ->get();

            for ($i = 0; $i < count($po_un_send); $i++) {
                $material = MaterialPlanDelivery::leftJoin('material_controls', 'material_controls.material_number', '=', 'material_plan_deliveries.material_number')
                    ->leftJoin('users AS buyer_proc', 'buyer_proc.username', '=', 'material_controls.pic')
                    ->leftJoin('users AS control_proc', 'control_proc.username', '=', 'material_controls.control')
                    ->where('po_number', $po_un_send[$i]->po_number)
                    ->whereNotNull('material_plan_deliveries.vendor_code')
                    ->select(
                        'material_plan_deliveries.vendor_code',
                        db::raw('buyer_proc.email AS buyer_email'),
                        db::raw('control_proc.email AS control_email')
                    )
                    ->first();

                if ($material) {

                    $file = $po_un_send[$i]->po_number . '_' . $material->vendor_code . '.pdf';
                    $filename = 'http://10.109.52.4/mirai/public/po_list/sap/' . $file;
                    // $po_exist = file_exists($filename);

                    $ch = curl_init($filename);
                    curl_setopt($ch, CURLOPT_NOBODY, true);
                    curl_exec($ch);
                    $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($response == 200) {
                        if ($limit >= 5) {
                            break;
                        }
                        $limit++;

                        $plan_delivery = MaterialPlanDelivery::leftJoin('material_controls', 'material_controls.material_number', '=', 'material_plan_deliveries.material_number')
                            ->where('material_plan_deliveries.po_number', $po_un_send[$i]->po_number)
                            ->whereNotNull('material_plan_deliveries.vendor_code')
                            ->select(
                                'material_plan_deliveries.material_number',
                                'material_controls.material_description',
                                'material_plan_deliveries.vendor_code',
                                'material_controls.vendor_name',
                                'material_plan_deliveries.po_number',
                                'material_plan_deliveries.item_line',
                                'material_plan_deliveries.issue_date',
                                'material_plan_deliveries.eta_date',
                                'material_plan_deliveries.quantity'
                            )
                            ->orderBy('material_plan_deliveries.item_line', 'ASC')
                            ->get();

                        try {
                            $update = MaterialPlanDelivery::where('material_plan_deliveries.po_number', $po_un_send[$i]->po_number)
                                ->update([
                                    'po_send' => 1,
                                    'po_send_at' => date('Y-m-d H:i:s'),
                                ]);

                            $attention = '';
                            $to = [];
                            $cc = [$material->buyer_email];
                            array_push($cc, $material->buyer_email, $material->control_email);

                            $vendor = db::table('acc_suppliers')
                                ->where('vendor_code', $plan_delivery[0]->vendor_code)
                                ->first();

                            $vendor_mails = VendorMail::where('vendor_code', $plan_delivery[0]->vendor_code)->get();
                            for ($j = 0; $j < count($vendor_mails); $j++) {
                                if ($vendor_mails[$j]->remark == 'to') {
                                    $attention = $vendor_mails[$j]->name;
                                    $to[] = $vendor_mails[$j]->email;
                                } else {
                                    $cc[] = $vendor_mails[$j]->email;
                                }
                            }

                            // If Yamaha Group
                            if (in_array($plan_delivery[0]->vendor_code, $exclude)) {
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
                                'po_number' => $po_un_send[$i]->po_number,
                                'attention' => $attention,
                                'vendor_name' => $vendor->supplier_name,
                                'subject' => 'PO#' . $po_un_send[$i]->po_number . '_' . $vendor->supplier_name,
                                'attachment' => $po_un_send[$i]->po_number . '_' . $material->vendor_code . '.pdf',
                                'plan_delivery' => $plan_delivery,
                            ];

                            Mail::to($to)
                                ->cc($cc)
                                ->bcc($bcc)
                                ->send(new SendEmail($data, 'raw_material_send_po'));

                        } catch (Exception $e) {

                            $error_log = new ErrorLog([
                                'error_message' => '[ERRORPO] : ' . $po_un_send[$i]->po_number . '_' . $e->getMessage(),
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
