<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use App\AuditAllResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class PatrolFinding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:patrol';

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
          // $tes = db::select("");
          
          $audit = AuditAllResult::whereNull('email_status')
          ->orderBy('tanggal', 'asc')
          ->get();

          foreach ($audit as $aud) {
            if ($aud->kategori == 'Patrol Vendor') {
                  $mails = "select distinct email from users where name = '".$aud->auditee_name."'";
                  $mailtoo = DB::select($mails);

                  // $mailscc = "select distinct email from users where username = '".$aud->auditor_id."'";
                  // $mailtoocc = DB::select($mailscc);

                  $deptcc = "select department from employee_syncs where name = '".$aud->auditee_name."'";
                  $deptocc = DB::select($deptcc);    

                  $isimail = "select * from audit_all_results where id = ".$aud->id;
                  $auditdata = db::select($isimail);

                  $mailcc = [];
                  array_push($mailcc, 'vidiya.chalista@music.yamaha.com');
                  array_push($mailcc, 'rani.nurdiyana.sari@music.yamaha.com');
                  array_push($mailcc, 'syafrizal.carnov.purwanto@music.yamaha.com');
                  array_push($mailcc, 'widura@music.yamaha.com');
                  array_push($mailcc, 'yayuk.wahyuni@music.yamaha.com');

                  if ($deptocc) {
                    $mailscc = "select distinct email from send_emails where remark = '".$deptocc[0]->department."'";
                    $mailtoocc = DB::select($mailscc);

                    array_push($mailcc, $mailtoocc[0]->email);
                  }

                  Mail::to($mailtoo)->cc($mailcc)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'patrol'));

                  $data2 = AuditAllResult::where('id', $aud->id)
                  ->update([
                    'email_status' => 'close'
                  ]);
            }else{
                if ($aud->remark != 'Positive Finding') {
                  $mails = "select distinct email from users where name = '".$aud->auditee_name."'";
                  $mailtoo = DB::select($mails);

                  $isimail = "select * from audit_all_results where id = ".$aud->id;
                  $auditdata = db::select($isimail);

                  Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'patrol'));

                  $data2 = AuditAllResult::where('id', $aud->id)
                  ->update([
                    'email_status' => 'close'
                  ]);
                }
            }
            
          }

          // $now = date('Y-m-d H:i:s');
          // if ($now > date('Y-m-d').' 15:00:00' && $now < date('Y-m-d').' 17:00:00') {
          //   $latch = DB::connection('ympimis_2')->select("SELECT
          //     * 
          //   FROM
          //     packing_latchs 
          //   WHERE
          //     location = 'Clarinet' 
          //     AND DATE( created_at ) = DATE( NOW( ) ) 
          //     AND latch = 'Ya'");

          //   if (count($latch) > 0) {
          //     $mail_to = [];
          //     array_push($mail_to, 'youichi.oyama@music.yamaha.com');
          //     array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
          //     array_push($mail_to, 'mamluatul.atiyah@music.yamaha.com');
          //     array_push($mail_to, 'bambang.ferry@music.yamaha.com');
          //     array_push($mail_to, 'jihan.rusdi@music.yamaha.com');
          //     array_push($mail_to, 'imbang.prasetyo@music.yamaha.com');

          //     $bcc = [];
          //     array_push($bcc, 'ympi-mis-ML@music.yamaha.com');

          //     $data = array(
          //         'latch' => $latch,
          //         'title' => 'New Reed Clarinet Information (CL付属リード新規切替連絡)',
          //     );

          //     Mail::to($mail_to)
          //       ->bcc($bcc)
          //       ->send(new SendEmail($data, 'packing_documentation'));
          //   }
          // }


    }
}
