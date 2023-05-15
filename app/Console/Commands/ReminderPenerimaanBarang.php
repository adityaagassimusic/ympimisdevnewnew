<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\AccReceive;

class ReminderPenerimaanBarang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:penerimaan_barang';

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
        $receive = DB::select("
           SELECT DISTINCT left(no_pr,2) as department from acc_receives where date_receive = '".date('Y-m-d')."'
        ");

        foreach ($receive as $rev) {
            if ($rev->department != 'N2') {
                
                $dept = "";

                $data_email = DB::select("
                   SELECT * FROM acc_receives where date_receive = '".date('Y-m-d')."' and LEFT(no_pr,2) = '".$rev->department."'
                ");

                if ($rev->department == "IT")
                {
                    $dept = "Management Information System Department";
                }
                else if ($rev->department == "AC")
                {
                    $dept = "Accounting Department";
                }
                else if ($rev->department == "AS")
                {
                    $dept = "Woodwind Instrument - Assembly (WI-A) Department";
                }
                else if ($rev->department == "EI")
                {
                    $dept = "Educational Instrument (EI) Department";
                }
                else if ($rev->department == "GA")
                {
                    $dept = "General Affairs Department";
                }
                else if ($rev->department == "HR")
                {
                    $dept = "Human Resources Department";
                }
                else if ($rev->department == "LG")
                {
                    $dept = "Logistic Department";
                }
                else if ($rev->department == "PM" || $rev->department == "CM")
                {
                    $dept = "Maintenance Department";
                }
                else if ($rev->department == "KP")
                {
                    $dept = "Woodwind Instrument - Parts Process (WI-PP) Department";
                }
                else if ($rev->department == "BP")
                {
                    $dept = "Woodwind Instrument - Parts Process (WI-PP) Department";
                }
                else if ($rev->department == "PH")
                {
                    $dept = "Procurement Department";
                }
                else if ($rev->department == "PC")
                {
                    $dept = "Production Control Department";
                }
                else if ($rev->department == "PE")
                {
                    $dept = "Production Engineering Department";
                }
                else if ($rev->department == "QC" || $rev->department == "SR")
                {
                    $dept = "Standardization Department";
                }
                else if ($rev->department == "WP")
                {
                    $dept = "Woodwind Instrument - Welding Process (WI-WP) Department";
                }
                else if ($rev->department == "ST")
                {
                    $dept = "Woodwind Instrument - Surface Treatment (WI-ST) Department";
                }
                else if ($rev->department == "PP")
                {
                    $dept = "Woodwind Instrument - Parts Process (WI-PP) Department";
                }

                $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = '".$dept."' and position like '%staff%' and end_date is null";
                $mailtoo = DB::select($mails);

                //kirim email ke Mas Shega & Mas Hamzah
                $mailccs = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and (employee_id = 'PI1810020'  or employee_id = 'PI0904006' or employee_id = 'PI1506001')";
                $mailtoocc = DB::select($mailccs);
                
                if (count($data_email) > 0 && ($dept != null || $dept != "")) {
                  Mail::to($mailtoo)->cc($mailtoocc)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($data_email, 'penerimaan_barang_equipment'));
                }
            }
        }
    }
}
