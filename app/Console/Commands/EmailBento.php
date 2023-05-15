<?php

namespace App\Console\Commands;

use App\BentoQuota;
use App\Mail\SendEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailBento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:bento';

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
        $last = date('Y-m-d', strtotime('+3 Days'));
        $month = date('Y-m', strtotime($last));

        $calendars = BentoQuota::where(db::raw('date_format(due_date, "%Y-%m")'), '=', $month)
            ->select('due_date', db::raw('date_format(due_date, "%d") as header'), 'menu', 'remark')
            ->get();

        $japaneses = db::select("SELECT
            *
            FROM
            japaneses
            WHERE deleted_at IS NULL
            ORDER BY
            id ASC");

        $mail_to = array();
        foreach ($japaneses as $japanese) {
            array_push($mail_to, $japanese->email);
        }

        $bento_lists = db::select("SELECT
        	j.employee_id,
        	j.employee_name,
        	b.due_date,
        	b.revise,
        	b.STATUS,
        	b.location
        FROM
        	japaneses AS j
        	LEFT JOIN ( SELECT * FROM bentos WHERE date_format( due_date, '%Y-%m' ) = '" . $month . "' ) AS b ON b.employee_id = j.employee_id
        ORDER BY
        	j.id ASC");

        $datas = [
            'bento_lists' => $bento_lists,
            'calendars' => $calendars,
            'month' => $month,
            'japaneses' => $japaneses,
            'remark' => 'reminder',
        ];

        Mail::to(
            $mail_to
        )->cc([
            'rianita.widiastuti@music.yamaha.com',
            'putri.sukma.riyanti@music.yamaha.com',
            'ninik.islami@music.yamaha.com',
            'novita.siswindarti@music.yamaha.com',
            'ilmi.fauziah@music.yamaha.com',
        ])->bcc([
            'ympi-mis-ML@music.yamaha.com',
        ])->send(new SendEmail($datas, 'bento_information'));
    }
}
