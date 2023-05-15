<?php

namespace App\Console\Commands;

use App\Mail\SendEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotificationKy extends Command
{
    protected $signature = 'reminder:ky';
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $datas = db::connection('ympimis_2')->select('SELECT
            nama_tim,
            nama,
            GROUP_CONCAT(
                DATE_FORMAT( created_at, "%M" )) AS bulan,
            GROUP_CONCAT(
                DATE_FORMAT( created_at, "%Y-%m" )) AS bulan2,
            count( nama_tim ) jumlah
            FROM
            std_teams 
            WHERE
            remark IS NULL 
            AND posisi = "Ketua" 
            AND soal is not null
            GROUP BY
            nama_tim,
            nama 
            ORDER BY
            jumlah DESC, bulan2 ASC');

        $data = array(
            'datas' => $datas
        );

        $mail_to = [
            'widura@music.yamaha.com'
        ];

        // Mail::to($mail_to)->cc(['yayuk.wahyuni@music.yamaha.com'])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'reminder_ky'));
        Mail::to($mail_to)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'reminder_ky'));
    }
}
