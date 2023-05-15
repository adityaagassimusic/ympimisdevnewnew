<?php

namespace App\Console\Commands;

use App\Mail\SendEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailUserDocument extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:user_document';

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
        $safe = db::select("UPDATE user_documents
            SET `condition` = 'Safe'
            WHERE DATEDIFF(valid_to, NOW()) > reminder");

        $at_risk = db::select("UPDATE user_documents
            SET `condition` = 'At Risk'
            WHERE DATEDIFF(valid_to, NOW()) < reminder");

        $expired = db::select("UPDATE user_documents
            SET `condition` = 'Expired'
            WHERE now() > valid_to");

        $user_reminder = db::select("SELECT DISTINCT d.employee_id, u.email FROM user_documents d
            LEFT JOIN users u ON d.employee_id = u.username
            WHERE (d.`condition` = 'At Risk' OR  d.`condition` = 'Expired')
            AND d.`status` = 'Active'
            AND d.notification = 0
            AND u.email like '%music.yamaha.com%'");

        $cc = array();
        for ($x = 0; $x < count($user_reminder); $x++) {
            array_push($cc, $user_reminder[$x]->email);
        }
        array_push($cc, 'khoirul.umam@music.yamaha.com', 'budhi.apriyanto@music.yamaha.com');

        $resume = db::select("SELECT d.category,
            d.document_number,
            d.employee_id,
            u.`name`,
            d.valid_from,
            d.valid_to,
            d.`condition`,
            DATEDIFF(valid_to, NOW()) as diff
            FROM user_documents d
            LEFT JOIN users u ON d.employee_id = u.username
            WHERE (d.`condition` = 'At Risk' OR  d.`condition` = 'Expired')
            AND d.`status` = 'Active'
            AND d.notification = 0
            ORDER BY diff ASC");

        $data = [
            'user_documents' => $resume,
            'jml' => count($resume),
        ];

        $bcc = array();
        array_push($bcc, 'ympi-mis-ML@music.yamaha.com');

        if (count($resume) > 0) {
            Mail::to(['eko.junaedi@music.yamaha.com'])
                ->cc($cc)
                ->bcc($bcc)
                ->send(new SendEmail($data, 'user_document'));

            for ($i = 0; $i < count($resume); $i++) {
                $update = db::table('user_documents')
                    ->where('document_number', $resume[$i]->document_number)
                    ->where('employee_id', $resume[$i]->employee_id)
                    ->update([
                        'notification' => 1,
                    ]);
            }
        }
    }
}
