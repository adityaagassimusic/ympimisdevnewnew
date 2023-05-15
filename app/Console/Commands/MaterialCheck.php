<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class MaterialCheck extends Command
{

    protected $signature = 'material:check';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $first = date('Y-m-d H:i:s', strtotime('-30 Minutes'));
        $last = date('Y-m-d H:i:s');

        // db::connection('ympimis_2')->table('material_checks')->truncate();

        $materials = db::table('material_controls')
            ->where('incoming', '=', 1)
            ->whereNull('deleted_at')
            ->get();

        $item_codes = array();

        foreach ($materials as $material) {
            array_push($item_codes, $material->material_number);
        }

        $goods_receives = db::connection('ymes')->table('vd_mes0020')
            ->where('move_type', '=', 'GR01')
            ->whereIn('item_code', $item_codes)
            ->where('instdt', '>=', $first)
            ->where('instdt', '<', $last)
            ->get();

        $data = array();

        // $approvers = db::select("SELECT
        //     a.approver_email, d.department_shortname_2
        //     FROM
        //     approvers AS a
        //     LEFT JOIN departments AS d ON d.department_name = a.department
        //     WHERE
        //     a.remark = 'Foreman'");

        // $mail_to = array();

        foreach ($goods_receives as $goods_receive) {
            $employee = db::table('employee_syncs')->where('employee_id', '=', $goods_receive->instid)->first();
            $inout_no = $goods_receive->inout_no;
            $entry_date = $goods_receive->instdt;
            $posting_date = $goods_receive->inout_date;
            $material_number = $goods_receive->item_code;
            $material_description = "";
            $receive_qty = $goods_receive->inout_qty;
            $sample_qty = 0;
            $uom = $goods_receive->unit_code;
            $location = "";
            $received_by = $goods_receive->instid;
            $received_by_name = $goods_receive->instid;
            if ($employee) {
                $received_by_name = $employee->name;
            }
            $vendor_code = "";
            $vendor_name = "";

            foreach ($materials as $material) {
                if ($material->material_number == $goods_receive->item_code) {
                    $material_description = $material->material_description;
                    $sample_qty = $material->minimum_order;
                    if ($material->sample_qty > 0) {
                        $sample_qty = $material->sample_qty;
                    }
                    $location = $material->location;
                    $vendor_code = $material->vendor_code;
                    $vendor_name = $material->vendor_shortname;
                    break;
                }
            }

            // foreach ($approvers as $approver) {
            //     if ($approver->department_shortname_2 == $location) {
            //         if (!in_array($approver->approver_email, $mail_to)) {
            //             array_push($mail_to, $approver->approver_email);
            //         }
            //     }
            // }

            db::connection('ympimis_2')->table('material_checks')->insert([
                'inout_no' => $inout_no,
                'entry_date' => $entry_date,
                'posting_date' => $posting_date,
                'material_number' => $material_number,
                'material_description' => $material_description,
                'receive_qty' => $receive_qty,
                'sample_qty' => $sample_qty,
                'uom' => $uom,
                'location' => $location,
                'status' => 'Waiting',
                'received_by' => $received_by,
                'received_by_name' => $received_by_name,
                'vendor_code' => $vendor_code,
                'vendor_name' => $vendor_name,
                'created_at' => $last,
                'updated_at' => $last,
            ]);

            $connector = new WindowsPrintConnector('FLO Printer LOG');
            $printer = new Printer($connector);

            $date = date('l, d F Y', strtotime($posting_date));

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->setUnderline(true);
            $printer->text('INDIRECT INCOMING CHECK');
            $printer->feed(2);
            $printer->qrCode($inout_no, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
            $printer->feed(1);
            $printer->initialize();
            $printer->setTextSize(2, 1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text($material_number);
            $printer->feed(1);
            $printer->text($material_description);
            $printer->setEmphasis(false);
            $printer->feed(2);
            $printer->text('Sample Qty: ' . $sample_qty . " " . $uom);
            $printer->feed(1);
            $printer->text('Lokasi Cek: ' . $location);
            $printer->feed(2);
            $printer->setTextSize(1, 1);
            $printer->text('Kedatangan: ' . $date);
            $printer->feed(2);
            $printer->cut();
            $printer->close();

            // array_push($data, [
            //     'inout_no' => $inout_no,
            //     'entry_date' => $entry_date,
            //     'posting_date' => $posting_date,
            //     'material_number' => $material_number,
            //     'material_description' => $material_description,
            //     'receive_qty' => $receive_qty,
            //     'sample_qty' => $sample_qty,
            //     'uom' => $uom,
            //     'location' => $location,
            //     'status' => 'Warehouse',
            //     'created_at' => $last,
            //     'updated_at' => $last,
            // ]);

        }

        // if (count($data) > 0) {
        //     Mail::to($mail_to)
        //         ->cc(['dwi.misnanto@music.yamaha.com', 'fatchur.rozi@music.yamaha.com'])
        //         ->bcc(['ympi-mis-ML@music.yamaha.com'])
        //         ->queue(new SendEmail($data, 'material_check_notification'));
        // }
    }
}
