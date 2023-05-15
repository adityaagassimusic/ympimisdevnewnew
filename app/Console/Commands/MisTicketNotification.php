<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;


class MisTicketNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:ticket_progress';

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

        $tgl = date('d F Y');

        $ticket_data = DB::select("
            SELECT
                sum( CASE WHEN `status` = 'Waiting' THEN 1 ELSE 0 END ) AS jumlah_belum,
                sum( CASE WHEN `status` = 'InProgress' THEN 1 ELSE 0 END ) AS jumlah_progress,
                sum( CASE WHEN `status` = 'Finished' THEN 1 ELSE 0 END ) AS jumlah_sudah,
                sum( CASE WHEN `status` = 'OnHold' THEN 1 ELSE 0 END ) AS jumlah_tunda
            FROM
                tickets 
            WHERE
                deleted_at IS NULL
        ");

        $ticket_resume = DB::SELECT("
            SELECT
            tickets.ticket_id,
            departments.department_shortname,
            tickets.case_title,
            tickets.pic_id,
            tickets.pic_name,
            ticket_pics.pic_shortname,
            tickets.progress,
            tl.timeline_date,
            TIMESTAMPDIFF(MONTH, timeline_date, NOW()) as timeline_month,
                        TIMESTAMPDIFF(DAY, timeline_date, NOW()) as timeline_day
            FROM
                tickets
                LEFT JOIN departments ON tickets.department = departments.department_name 
                LEFT JOIN (select ticket_id,MIN(timeline_date) as timeline_date from ticket_timelines GROUP BY ticket_id) as tl ON tickets.ticket_id = tl.ticket_id
                LEFT JOIN ticket_pics on tickets.pic_id = ticket_pics.pic_id 
            WHERE
                `status` = 'InProgress' 
                AND tickets.deleted_at IS NULL
                AND progress != 0
                AND tickets.pic_id != 'PI1412008'
            ORDER BY timeline_date, ticket_id ASC
        ");

        $ticket_finish = DB::SELECT("
            SELECT
            tickets.ticket_id,
            departments.department_shortname,
            tickets.case_title,
            tickets.pic_id,
            tickets.pic_name,
            ticket_pics.pic_shortname,
            tickets.progress,
            tl.timeline_date,
            tickets.due_date_to,
            TIMESTAMPDIFF(MONTH, due_date_to, NOW()) as timeline_month,
            TIMESTAMPDIFF(DAY, due_date_to, NOW()) as timeline_day
            FROM
                tickets
                LEFT JOIN departments ON tickets.department = departments.department_name 
                LEFT JOIN (select ticket_id,MIN(timeline_date) as timeline_date from ticket_timelines GROUP BY ticket_id) as tl ON tickets.ticket_id = tl.ticket_id
                LEFT JOIN ticket_pics on tickets.pic_id = ticket_pics.pic_id 
            WHERE
                `status` = 'Finished' 
                AND tickets.deleted_at IS NULL
                AND progress != 0
                AND tickets.pic_id != 'PI1412008'
                AND (TIMESTAMPDIFF(MONTH, due_date_to, NOW()) = 0 || TIMESTAMPDIFF(MONTH, due_date_to, NOW()) = 1) 
            ORDER BY due_date_to, ticket_id ASC
        ");

        $data = array(
            'ticket_data' => $ticket_data,
            'ticket_resume' => $ticket_resume,
            'ticket_finish' => $ticket_finish,
            'now' => $tgl
        );

        $mail_to = ['ympi-mis-ML@music.yamaha.com'];
        // $cc = ['budhi.apriyanto@music.yamaha.com'];
        // ->cc($cc)
        Mail::to($mail_to)->send(new SendEmail($data, 'daily_ticket'));
    }
}
