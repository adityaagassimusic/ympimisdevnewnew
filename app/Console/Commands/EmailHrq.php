<?php

namespace App\Console\Commands;

use App\Mail\SendEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailHrq extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:hrq';

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
        $qry = "select category, SUM(IF(remark = 1,1,0)) as unanswer from hr_question_logs group by category having unanswer > 0";

        $data = db::select($qry);

        if ($data != null) {
            Mail::to([
                'achmad.riski.bayu@music.yamaha.com',
                'mahendra.putra@music.yamaha.com',
                'dicky.kurniawan@music.yamaha.com',
                'khoirul.umam@music.yamaha.com',
            ])->bcc(['ympi-mis-ML@music.yamaha.com'])
                ->send(new SendEmail($data, 'hrq'));
        }
    }
}
