<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;

class CarDocumentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:car_document';

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
        // $remind_date_one = date('Y-m-d',strtotime(date('Y-m-t').' - 14 days'));
        // $remind_date_two = date('Y-m-d',strtotime(date('Y-m-t').' - 1 days'));
        $now = date('Y-m-d');

        // if ($now == $remind_date_one || $now == $remind_date_two) {
            $reminder = DB::SELECT("
                SELECT DISTINCT
                    qc_car_documents.*,qc_cars.pic
                FROM
                    qc_cars JOIN qc_car_documents ON qc_cars.cpar_no = qc_car_documents.cpar_no
                WHERE
                    qc_car_documents.file is null
            ");

            if (count($reminder) > 0) {
                $mail_to = [];
                for ($i=0; $i < count($reminder); $i++) {
                    if ($now > $reminder[$i]->due_date) {
                        $mails = DB::SELECT("select email from users where username = '".$reminder[$i]->pic."'");
                        if (count($mails) > 0) {
                            array_push($mail_to, $mails[0]->email);
                        }
                    } 
                }

                $data = [
                    'reminder' => $reminder
                ];

                if (count($mail_to) > 0) {
                    Mail::to($mail_to)
                    ->bcc(['rio.irvansyah@music.yamaha.com'])
                    ->send(new SendEmail($data, 'car_document'));
                }

                
            }
        // }
    }
}
