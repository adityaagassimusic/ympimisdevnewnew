<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mails;
use App\SakurentsuThreeM;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class ReminderThreeM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:threeM';

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
        $data = SakurentsuThreeM::where('notif_date', '=', date('Y-m-d'))
        ->where('remark', '<', '7')
        ->select('id', 'form_identity_number', 'sakurentsu_number', 'title', 'product_name', 'proccess_name', db::raw('DATE_FORMAT(created_at, "%d %M %Y") AS create_date'), db::raw('DATE_FORMAT(started_date, "%d %M %Y") AS target_date'), 'unit', 'category', 'remark', 'created_by', 'related_department')
        ->get();

        foreach ($data as $dt) {
            $email = [];
            $rel_dep = explode(',', $dt->related_department);

            $mail_rel = Mails::whereIn('remark', $rel_dep)->select('email')->get();

            $datas = array(
                'datas' => $dt,
            );


            $mail = db::select('SELECT email from users where username = "'.$dt->created_by.'"');

            array_push($email, $mail[0]->email);
            // foreach ($mail_rel as $mr) {
            //     array_push($email, $mr->email);
            // }

            if (strtoupper($dt->created_by) == 'PI0812002') {
                array_push($email, 'farizca.nurma@music.yamaha.com');
            }

            Mail::to($email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($datas, '3m_reminder'));
        }

    }
}
