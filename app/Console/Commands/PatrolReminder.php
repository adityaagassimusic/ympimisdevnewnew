<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use App\AuditAllResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class PatrolReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:patrol';

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
        $audit_data = DB::select("
            SELECT
                kategori,
                COUNT( id ) AS jumlah,
                auditee_name
            FROM
                `audit_all_results` 
            WHERE
                (status_ditangani IS NULL or status_ditangani = 'progress')
                AND deleted_at IS NULL 
                AND kategori LIKE '%Patrol%'
                AND auditee_name is not null
                AND (remark != 'Positive Finding' OR remark is null)
            GROUP BY
                kategori,
                auditee_name    
            ORDER BY
                auditee_name
        ");

        $audit_email = DB::select("
            SELECT
                kategori,
                COUNT(audit_all_results.id) AS jumlah,
                auditee_name,
                                email
            FROM
                `audit_all_results` 
            JOIN
                users on users.`name` = auditee_name
            WHERE
                (status_ditangani IS NULL or status_ditangani = 'progress')
                AND audit_all_results.deleted_at IS NULL 
                AND kategori LIKE '%Patrol%'
                AND auditee_name is not null
                AND (remark != 'Positive Finding' OR remark is null)
            GROUP BY
                kategori,
                auditee_name,
                email
            ORDER BY
                auditee_name
        ");

        $kategori = DB::select("
            SELECT
                kategori,
                COUNT( id ) AS jumlah
            FROM
                `audit_all_results` 
            WHERE
                (status_ditangani IS NULL or status_ditangani = 'progress')
                AND deleted_at IS NULL 
                AND kategori LIKE '%Patrol%'
                AND auditee_name is not null
                AND (remark != 'Positive Finding' OR remark is null)
            GROUP BY
                kategori
        ");


        $mail_to = array();
        foreach($audit_email as $audit){
            if(!in_array($audit->email, $mail_to)){
                array_push($mail_to, $audit->email);
            }
        }

        $audits = [
            'audit_data' => $audit_data,
            'category' => $kategori
        ];


        Mail::to($mail_to)->cc(['ympi-manager-ML@music.yamaha.com'])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($audits, 'patrol_reminder'));
    }
}
