<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;

class AuditExternalClaimCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:audit_claim';

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
        $remind_date = date('Y-m-d',strtotime(date('Y-m-t').' - 3 days'));
        $now = date('Y-m-d');
        if ($now == $remind_date) {
            $reminder = DB::SELECT("SELECT DISTINCT
                    ( audit_external_claim_schedules.audit_id ),
                    audit_external_claim_points.audit_title,
                    audit_external_claim_schedules.employee_id,
                    employee_syncs.`name`,
                    schedule_status,
                    audit_external_claim_points.area,
                    audit_external_claim_points.product
                FROM
                    audit_external_claim_schedules
                    JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claim_schedules.audit_id
                    JOIN employee_syncs ON employee_syncs.employee_id = audit_external_claim_schedules.employee_id 
                WHERE
                    DATE_FORMAT( schedule_date, '%Y-%m' ) = DATE_FORMAT( NOW(), '%Y-%m' ) 
                    AND schedule_status = 'Belum Dikerjakan'");

            if (count($reminder) > 0) {
                $mail_to = [];
                for ($i=0; $i < count($reminder); $i++) { 
                    $mails = DB::SELECT("select email from users where username = '".$reminder[$i]->employee_id."'");
                    if (count($mails) > 0) {
                        array_push($mail_to, $mails[0]->email);
                    }
                }

                $data = [
                    'reminder' => $reminder,
                    'periode' => date("F Y", strtotime(date('Y-m-d'))),
                ];

                Mail::to($mail_to)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','rio.irvansyah@music.yamaha.com'])
                ->send(new SendEmail($data, 'audit_ng_jelas_reminder'));
            }
        }
    }
}
