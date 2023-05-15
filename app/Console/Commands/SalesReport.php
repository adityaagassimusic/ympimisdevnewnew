<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use App\WeeklyCalendar;


class SalesReport extends Command
{

    protected $signature = 'report:sales';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $now = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
        if ($now->remark != 'H') {
            $outstanding = db::select("SELECT outstanding.*,
                master_checksheets.destination,
                master_checksheets.Stuffing_date,
                master_checksheets.etd_sub,
                shipment_conditions.shipment_condition_name
                FROM
                (SELECT container_id, GROUP_CONCAT(invoice_number) AS invoice FROM
                    (SELECT DISTINCT container_id, invoice_number FROM
                        (SELECT invoice_number, container_id FROM flos
                            WHERE invoice_number IS NOT NULL
                            AND bl_date IS NULL
                            UNION ALL
                            SELECT invoice_number, container_id FROM knock_downs
                            WHERE invoice_number IS NOT NULL
                            AND bl_date IS NULL
                            ) AS iv) AS iv
                    GROUP BY container_id
                    )AS outstanding
                LEFT JOIN master_checksheets ON master_checksheets.id_checkSheet = outstanding.container_id
                LEFT JOIN shipment_conditions ON shipment_conditions.shipment_condition_code = master_checksheets.carier
                WHERE etd_sub <= '" . date('Y-m-d') . "'");

            if (count($outstanding) > 0) {

                $to = [
                    'fatchur.rozi@music.yamaha.com',
                    'karina.elnusawati@music.yamaha.com',
                    'fathor.rahman@music.yamaha.com',
                    'angga.setiawan@music.yamaha.com',
                    'triandini@music.yamaha.com'
                ];

                $cc = [
                    'imron.faizal@music.yamaha.com',
                    'mamluatul.atiyah@music.yamaha.com',
                    'ade.laksmana.putra@music.yamaha.com'
                ];

                $bcc = [
                    'ympi-mis-ML@music.yamaha.com'
                ];

                $data = [
                    'outstanding' => $outstanding
                ];

                Mail::to($to)
                    ->cc($cc)
                    ->bcc($bcc)
                    ->send(new SendEmail($data, 'sales_report'));
            }

        }
    }
}