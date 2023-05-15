<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mails;
use App\EmployeeSync;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class ReminderThreeMDocument extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:threeMDocument';

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
        $datas = db::select("SELECT sakurentsu_three_ms.id, form_identity_number, form_number, sakurentsu_three_ms.sakurentsu_number, title, product_name, proccess_name, unit, category, started_date, date_note, document_name, document_description, target_date, pic, sakurentsu_three_m_documents.finish_date, DATE_FORMAT(sakurentsu_three_ms.created_at, '%Y-%m-%d') as create_date from sakurentsu_three_ms 
            left join sakurentsu_three_m_documents on sakurentsu_three_ms.id = sakurentsu_three_m_documents.form_id
            where sakurentsu_three_ms.sakurentsu_number = 'KC-761'");

        if (count($datas) > 0) {
            $arr_pos = ['Foreman','Chief', 'Coordinator'];

            foreach ($datas as $doc_data) {

                $data = array(
                    'datas' => $doc_data,
                );

                $arr_doc_dept = [];

                
                if ($doc_data->finish_date == null) {
                    array_push($arr_doc_dept, $doc_data->pic);

                    $email_list = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->whereIn('department', $arr_doc_dept)
                    ->whereIn('position', $arr_pos)
                    ->whereNull('end_date')
                    ->select('email')
                    ->get();

                    $email_mng = Mails::whereIn('remark', $arr_doc_dept)
                    ->select('email')
                    ->get();

                    $sign = array_merge($email_list->toArray(), $email_mng->toArray());

                    $bcc = ['nasiqul.ibat@music.yamaha.com'];
                    // $sign = ['nasiqul.ibat@music.yamaha.com'];
                    Mail::to($sign)->bcc($bcc)->send(new SendEmail($data, '3m_reminder_document'));
                }

            }
        }

        // $data = [
        //     'doc_data' => []
        // ];

        // $bcc = ['nasiqul.ibat@music.yamaha.com'];
        // $sign = ['nasiqul.ibat@music.yamaha.com'];
        // Mail::to($sign)->bcc($bcc)->send(new SendEmail($data, '3m_remider_document'));
    }
}
