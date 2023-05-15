<?php

namespace App\Console\Commands;

use App\Mail\SendEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailAgreement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:agreement';

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
        $agreements = db::select("SELECT
            'notif_agreement' AS cat,
            a.id,
            a.category,
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
            u.email,
            es.`name`
            FROM
            agreements AS a
            LEFT JOIN employee_syncs AS es ON es.employee_id = a.created_by
            LEFT JOIN departments AS d ON d.department_name = a.department
            LEFT JOIN users AS u ON u.username = a.created_by
            WHERE
            category = 'agreement'
            HAVING
            validity = 90
            OR validity = 30");

        if (count($agreements) > 0) {
            foreach ($agreements as $agreement) {
                $manager = db::select("select email from send_emails where remark = '" . $agreement->department . "'");

                Mail::to(['adhi.satya.indradhi@music.yamaha.com', $agreement->email])->cc(['khoirul.umam@music.yamaha.com', $manager[0]->email])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($agreement, 'notif_agreement'));
            }
        }

        $regulations = DB::select("
            SELECT
            'notif_regulation' AS cat,
            a.id,
                        a.category,
            a.department,
            d.department_shortname,
            a.vendor,
            a.description,
            a.valid_from,
            TIMESTAMPDIFF( DAY, date( now()), a.status_due_date ) AS validity,
            a.`status`,
                        a.`status_due_date`,
            a.remark,
            a.analisis,
            a.created_at,
            a.updated_at,
            a.created_by,
            u.email,
            es.`name`
            FROM
            agreements AS a
            LEFT JOIN employee_syncs AS es ON es.employee_id = a.created_by
            LEFT JOIN departments AS d ON d.department_name = a.department
            LEFT JOIN users AS u ON u.username = a.created_by
            WHERE
            category = 'regulation'
            and `status` = 'Belum Implementasi'
            HAVING
            validity = 7
            OR validity = 1
            OR validity <= 0
        ");

        if (count($regulations) > 0) {
            foreach ($regulations as $regulation) {
                $manager = db::select("select email from send_emails where remark = '" . $regulation->department . "'");

                Mail::to($regulation->email)->cc(['adhi.satya.indradhi@music.yamaha.com', $manager[0]->email])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($regulation, 'notif_regulation'));
            }
        } else {
            exit;
        }

    }
}
