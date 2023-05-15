<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\EmployeeSync;
use App\ApprSend;
use App\ApprApprovals;

class MAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'm_app:schedule';

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
        $nt = db::select('select * from
            (
            select request_id, count(request_id) as a, count(`status`) as b from appr_approvals group by request_id
            ) a
            where a.a <> a.b');

        if (count($nt) > 0) {
            for ($i=0; $i < count($nt); $i++) { 
                $email = ApprApprovals::where('request_id', $nt[$i]->request_id)
                ->wherenull('status')
                ->select('request_id','approver_email')
                ->first();
                
                $appr_sends = ApprSend::where('no_transaction', $email->request_id)
                ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
                ->first();

                $appr_approvals = ApprApprovals::where('request_id', $email->request_id)
                ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
                ->get();

                $data = [
                    'appr_sends' => $appr_sends,
                    'appr_approvals' => $appr_approvals
                ];

                Mail::to($email->approver_email)->bcc(['lukmannul.arif@music.yamaha.com', 'aditya.agassi@music.yamaha.com'])->send(new SendEmail($data, 'send_email'));
            }            
        }
    }
}
